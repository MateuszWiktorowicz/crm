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
    # Pobierz hasło, usuwając cudzysłowy i białe znaki
    ROOT_PASSWORD=$(grep MYSQL_ROOT_PASSWORD "$ENV_FILE" | cut -d '=' -f2 | tr -d ' "' | tr -d "'")
    # Jeśli puste, użyj domyślnego
    if [ -z "$ROOT_PASSWORD" ]; then
        ROOT_PASSWORD="root_password"
    fi
else
    ROOT_PASSWORD="root_password"
fi

# Debug - pokaż jakie hasło jest używane (bez wyświetlania hasła)
echo "Używanie hasła z: $([ -f "$ENV_FILE" ] && echo ".env" || echo "domyślne")"

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

# Utwórz backup
docker exec "$CONTAINER_NAME" mariadb-dump -uroot -p"$ROOT_PASSWORD" "$DB_NAME" 2>&1 | gzip > "$BACKUP_FILE"
BACKUP_EXIT_CODE=${PIPESTATUS[0]}

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
    echo "Sprawdź czy hasło root jest poprawne w pliku .env"
    echo "Możesz przetestować połączenie:"
    echo "docker exec $CONTAINER_NAME mariadb -uroot -p'$ROOT_PASSWORD' -e 'SELECT 1;'"
    
    # Usuń pusty plik backupu
    rm -f "$BACKUP_FILE"
    exit 1
fi
