#!/bin/bash

# Skrypt do tworzenia backupu bazy danych
# Użycie: ./backup.sh

# Konfiguracja
BACKUP_DIR="/tmp/crm_backups"
CONTAINER_NAME="crm_mastermet_db"
DB_NAME="crm_mastermet"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/backup_${DB_NAME}_${TIMESTAMP}.sql.gz"

# Pobierz hasła z .env (jeśli istnieje)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="${SCRIPT_DIR}/../../.env"

if [ -f "$ENV_FILE" ]; then
    # Pobierz wartości z .env - użyj source lub eval aby poprawnie obsłużyć znaki specjalne
    # Usuń komentarze i puste linie, potem source
    ROOT_PASSWORD=$(grep "^MYSQL_ROOT_PASSWORD=" "$ENV_FILE" | cut -d '=' -f2- | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')
    USER_PASSWORD=$(grep "^MYSQL_PASSWORD=" "$ENV_FILE" | cut -d '=' -f2- | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')
    DB_USER=$(grep "^MYSQL_USER=" "$ENV_FILE" | cut -d '=' -f2- | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')
    
    # Jeśli puste, użyj domyślnych
    if [ -z "$ROOT_PASSWORD" ]; then
        ROOT_PASSWORD="root_password"
    fi
    if [ -z "$USER_PASSWORD" ]; then
        USER_PASSWORD="crm_password"
    fi
    if [ -z "$DB_USER" ]; then
        DB_USER="crm_user"
    fi
else
    ROOT_PASSWORD="root_password"
    USER_PASSWORD="crm_password"
    DB_USER="crm_user"
fi

# Debug - pokaż jakie dane są używane (bez wyświetlania haseł)
echo "Używanie danych z: $([ -f "$ENV_FILE" ] && echo ".env" || echo "domyślne")"
echo "Użytkownik: $DB_USER"

# Utwórz katalog backupu jeśli nie istnieje
mkdir -p "$BACKUP_DIR"

# Utwórz backup
echo "Tworzenie backupu bazy danych..."
echo "Kontener: $CONTAINER_NAME"
echo "Baza: $DB_NAME"

# Sprawdź czy kontener działa
if ! docker ps | grep -q "$CONTAINER_NAME"; then
    echo "Błąd: Kontener $CONTAINER_NAME nie jest uruchomiony!"
    exit 1
fi

# Utwórz backup - najpierw spróbuj z użytkownikiem crm_user, potem root
echo "Próba backupu z użytkownikiem $DB_USER..."
# Użyj --password= zamiast -p aby uniknąć problemów ze znakami specjalnymi
docker exec "$CONTAINER_NAME" mariadb-dump -u"$DB_USER" --password="$USER_PASSWORD" -h127.0.0.1 "$DB_NAME" 2>&1 | gzip > "$BACKUP_FILE"
BACKUP_EXIT_CODE=${PIPESTATUS[0]}

# Jeśli nie udało się z crm_user, spróbuj z root
if [ $BACKUP_EXIT_CODE -ne 0 ]; then
    echo "Backup z użytkownikiem $DB_USER nie powiódł się, próba z root..."
    docker exec "$CONTAINER_NAME" mariadb-dump -uroot --password="$ROOT_PASSWORD" -h127.0.0.1 "$DB_NAME" 2>&1 | gzip > "$BACKUP_FILE"
    BACKUP_EXIT_CODE=${PIPESTATUS[0]}
fi

if [ $BACKUP_EXIT_CODE -eq 0 ]; then
    echo "Backup utworzony: $BACKUP_FILE"
    
    # Sprawdź rozmiar pliku
    FILE_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo "Rozmiar backupu: $FILE_SIZE"
    
    # Usuń stare backupy (starsze niż 7 dni)
    echo "Usuwanie starych backupów (starszych niż 7 dni)..."
    find "$BACKUP_DIR" -name "backup_${DB_NAME}_*.sql.gz" -mtime +7 -delete
    
    echo "Backup zakończony pomyślnie!"
else
    echo "Błąd podczas tworzenia backupu!"
    echo "Sprawdź czy hasła są poprawne w pliku .env"
    echo "Możesz przetestować połączenie:"
    echo "docker exec $CONTAINER_NAME mariadb -u$DB_USER --password='$USER_PASSWORD' -h127.0.0.1 -e 'SELECT 1;'"
    echo "lub"
    echo "docker exec $CONTAINER_NAME mariadb -uroot --password='$ROOT_PASSWORD' -h127.0.0.1 -e 'SELECT 1;'"
    
    # Usuń pusty plik backupu
    rm -f "$BACKUP_FILE"
    exit 1
fi
