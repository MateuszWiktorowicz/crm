#!/bin/bash
# Skrypt do naprawy uprawnień użytkownika w istniejącej bazie danych MariaDB

docker exec -i crm_mastermet_db mysql -uroot -proot_password <<EOF
-- Usuń istniejącego użytkownika (jeśli istnieje tylko dla localhost)
DROP USER IF EXISTS 'crm_user'@'localhost';
DROP USER IF EXISTS 'crm_user'@'%';

-- Utwórz użytkownika z uprawnieniami do połączenia z dowolnego hosta
CREATE USER 'crm_user'@'%' IDENTIFIED BY 'crm_password';
GRANT ALL PRIVILEGES ON crm_mastermet.* TO 'crm_user'@'%';
FLUSH PRIVILEGES;

-- Pokaż użytkowników dla weryfikacji
SELECT User, Host FROM mysql.user WHERE User = 'crm_user';
EOF

echo "Uprawnienia zostały zaktualizowane!"
