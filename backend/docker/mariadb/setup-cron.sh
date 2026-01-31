#!/bin/bash

# Skrypt do konfiguracji cron dla automatycznych backupów
# Użycie: ./setup-cron.sh [godzina] [minuta]
# Lub uruchom bez argumentów, aby użyć trybu interaktywnego

# Tryb interaktywny jeśli nie podano argumentów
if [ $# -eq 0 ]; then
    echo "=== Konfiguracja automatycznego backupu ==="
    echo ""
    
    read -p "Podaj godzinę backupu (0-23, domyślnie 2): " HOUR
    HOUR=${HOUR:-2}
    
    read -p "Podaj minutę backupu (0-59, domyślnie 0): " MINUTE
    MINUTE=${MINUTE:-0}
else
    # Tryb z argumentami
    HOUR=${1:-2}
    MINUTE=${2:-0}
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_SCRIPT="${SCRIPT_DIR}/backup.sh"

# Sprawdź czy skrypt backupu istnieje
if [ ! -f "$BACKUP_SCRIPT" ]; then
    echo "Błąd: Nie znaleziono skryptu backup.sh"
    exit 1
fi

# Utwórz komendę cron
CRON_CMD="$MINUTE $HOUR * * * $BACKUP_SCRIPT >> /var/log/crm_backup.log 2>&1"

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
