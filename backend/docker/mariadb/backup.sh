#!/bin/bash

# Skrypt do tworzenia backupu bazy danych i wysyłania na pulpit
# Użycie: ./backup.sh [sciezka_do_klucza_ssh] [user@host:/sciezka/na/pulpit]

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
    ROOT_PASSWORD=$(grep MYSQL_ROOT_PASSWORD "$ENV_FILE" | cut -d '=' -f2)
else
    ROOT_PASSWORD="root_password"
fi

# Utwórz katalog backupu jeśli nie istnieje
mkdir -p "$BACKUP_DIR"

# Utwórz backup
echo "Tworzenie backupu bazy danych..."
docker exec "$CONTAINER_NAME" mariadb-dump -uroot -p"$ROOT_PASSWORD" "$DB_NAME" | gzip > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "Backup utworzony: $BACKUP_FILE"
    
    # Sprawdź rozmiar pliku
    FILE_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo "Rozmiar backupu: $FILE_SIZE"
    
    # Jeśli podano parametry SSH, wyślij na pulpit
    if [ $# -ge 2 ]; then
        SSH_KEY="$1"
        REMOTE_PATH="$2"
        
        echo "Wysyłanie backupu na pulpit..."
        
        if [ -f "$SSH_KEY" ]; then
            scp -i "$SSH_KEY" "$BACKUP_FILE" "$REMOTE_PATH"
        else
            scp "$BACKUP_FILE" "$REMOTE_PATH"
        fi
        
        if [ $? -eq 0 ]; then
            echo "Backup wysłany pomyślnie!"
        else
            echo "Błąd podczas wysyłania backupu!"
            exit 1
        fi
    fi
    
    # Usuń stare backupy (starsze niż 7 dni)
    echo "Usuwanie starych backupów (starszych niż 7 dni)..."
    find "$BACKUP_DIR" -name "backup_${DB_NAME}_*.sql.gz" -mtime +7 -delete
    
    echo "Backup zakończony pomyślnie!"
else
    echo "Błąd podczas tworzenia backupu!"
    exit 1
fi
