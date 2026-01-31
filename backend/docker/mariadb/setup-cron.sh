#!/bin/bash

# Skrypt do konfiguracji cron dla automatycznych backupów
# Użycie: ./setup-cron.sh [godzina] [minuta] [sciezka_do_klucza_ssh] [user@host:/sciezka/na/pulpit]

# Domyślne wartości
HOUR=${1:-2}
MINUTE=${2:-0}
SSH_KEY=${3:-""}
REMOTE_PATH=${4:-""}

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_SCRIPT="${SCRIPT_DIR}/backup.sh"

# Sprawdź czy skrypt backupu istnieje
if [ ! -f "$BACKUP_SCRIPT" ]; then
    echo "Błąd: Nie znaleziono skryptu backup.sh"
    exit 1
fi

# Utwórz komendę cron
if [ -n "$SSH_KEY" ] && [ -n "$REMOTE_PATH" ]; then
    CRON_CMD="$MINUTE $HOUR * * * $BACKUP_SCRIPT \"$SSH_KEY\" \"$REMOTE_PATH\" >> /var/log/crm_backup.log 2>&1"
else
    CRON_CMD="$MINUTE $HOUR * * * $BACKUP_SCRIPT >> /var/log/crm_backup.log 2>&1"
fi

# Sprawdź czy już istnieje wpis w cron
CRON_EXISTS=$(crontab -l 2>/dev/null | grep -c "$BACKUP_SCRIPT")

if [ "$CRON_EXISTS" -gt 0 ]; then
    echo "Usuwanie istniejącego wpisu cron..."
    crontab -l 2>/dev/null | grep -v "$BACKUP_SCRIPT" | crontab -
fi

# Dodaj nowy wpis do cron
echo "Dodawanie wpisu do cron (codziennie o ${HOUR}:${MINUTE})..."
(crontab -l 2>/dev/null; echo "$CRON_CMD") | crontab -

echo "Cron skonfigurowany pomyślnie!"
echo "Backup będzie uruchamiany codziennie o ${HOUR}:${MINUTE}"

# Pokaż aktualne wpisy cron
echo ""
echo "Aktualne wpisy cron:"
crontab -l | grep "$BACKUP_SCRIPT"
