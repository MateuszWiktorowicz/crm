# Docker - Konfiguracja bazy danych

## Wprowadzenie

Aplikacja CRM Mastermet używa MariaDB uruchomionej w kontenerze Docker. Ta konfiguracja zapewnia spójne środowisko deweloperskie i łatwe zarządzanie bazą danych.

## Wymagania

- Docker Desktop (lub Docker Engine + Docker Compose)
- PHP 8.2+ z rozszerzeniem PDO MySQL/MariaDB

## Konfiguracja

### 1. Uruchomienie bazy danych

Aby uruchomić kontener z bazą danych MariaDB, wykonaj w katalogu `backend/`:

```bash
cd backend
docker-compose up -d
```

To polecenie:
- Pobierze obraz MariaDB 11 (jeśli nie jest dostępny lokalnie)
- Utworzy kontener `crm_mastermet_db`
- Uruchomi bazę danych w tle
- Utworzy wolumen `mariadb_data` dla persystencji danych

### 2. Konfiguracja aplikacji Laravel

1. Skopiuj plik `.env.example` do `.env` w katalogu `backend/`:

```bash
cd backend
cp .env.example .env
```

2. Wygeneruj klucz aplikacji:

```bash
php artisan key:generate
```

3. Sprawdź, czy zmienne środowiskowe bazy danych są poprawnie skonfigurowane w pliku `.env`:

```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_mastermet
DB_USERNAME=crm_user
DB_PASSWORD=crm_password
```

### 3. Wykonanie migracji

Po uruchomieniu kontenera i skonfigurowaniu `.env`, wykonaj migracje:

```bash
cd backend
php artisan migrate
```

To polecenie utworzy wszystkie tabele w bazie danych bez wypełniania ich danymi.

### 4. Uruchomienie seederów (opcjonalne)

Aby wypełnić bazę danych danymi początkowymi:

```bash
php artisan db:seed
```

Lub wykonaj migracje i seedery jednocześnie:

```bash
php artisan migrate --seed
```

## Zarządzanie kontenerem

### Sprawdzenie statusu

```bash
cd backend
docker-compose ps
```

### Zatrzymanie kontenera

```bash
cd backend
docker-compose down
```

To polecenie zatrzyma kontener, ale **nie usunie danych** (dane są przechowywane w wolumenie Docker).

### Zatrzymanie i usunięcie danych

Jeśli chcesz całkowicie usunąć bazę danych i zacząć od nowa:

```bash
docker-compose down -v
```

**UWAGA:** To polecenie usunie wszystkie dane w bazie!

### Restart kontenera

```bash
cd backend
docker-compose restart
```

### Wyświetlenie logów

```bash
cd backend
docker-compose logs -f mariadb
```

## Połączenie z bazą danych

### Przez phpMyAdmin

phpMyAdmin jest dostępne pod adresem: **http://localhost:8081**

- **Serwer:** `mariadb` (lub `127.0.0.1`)
- **Użytkownik:** `crm_user`
- **Hasło:** `crm_password`

### Z poziomu aplikacji Laravel

Aplikacja automatycznie łączy się z bazą danych używając konfiguracji z pliku `.env`.

### Z zewnętrznego klienta

Możesz połączyć się z bazą danych używając dowolnego klienta MySQL/MariaDB:

- **Host:** `127.0.0.1` lub `localhost`
- **Port:** `3306`
- **Database:** `crm_mastermet`
- **Username:** `crm_user`
- **Password:** `crm_password`

Przykład użycia `mysql` CLI:

```bash
mysql -h 127.0.0.1 -P 3306 -u crm_user -pcrm_password crm_mastermet
```

### Z poziomu kontenera

Możesz również połączyć się bezpośrednio z kontenera:

```bash
cd backend
docker-compose exec mariadb mysql -u crm_user -pcrm_password crm_mastermet
```

## Bezpieczeństwo

**WAŻNE:** Domyślne hasła w `backend/docker-compose.yml` są przeznaczone tylko do środowiska deweloperskiego. 

Przed wdrożeniem na produkcję:
1. Zmień wszystkie hasła w `backend/docker-compose.yml`
2. Zaktualizuj odpowiednie zmienne w pliku `.env`
3. Rozważ użycie Docker secrets lub zmiennych środowiskowych z pliku `.env` dla haseł

## Rozwiązywanie problemów

### Kontener nie uruchamia się

Sprawdź, czy port 3306 nie jest już zajęty:

```bash
lsof -i :3306
```

Jeśli port 3306 jest zajęty przez inny kontener, możesz:
1. Zatrzymać konfliktujący kontener: `docker stop <nazwa_kontenera>`
2. Lub zmienić mapowanie portu w `backend/docker-compose.yml` na inny port (np. 3307)

### Błąd połączenia z bazą danych

1. Sprawdź, czy kontener jest uruchomiony: `cd backend && docker-compose ps`
2. Sprawdź logi: `cd backend && docker-compose logs mariadb`
3. Zweryfikuj konfigurację w pliku `.env`
4. Upewnij się, że używasz `127.0.0.1` zamiast `localhost` (w niektórych systemach `localhost` może nie działać poprawnie)

### Reset bazy danych

Jeśli chcesz całkowicie zresetować bazę danych:

```bash
# Przejdź do katalogu backend
cd backend

# Zatrzymaj i usuń kontener oraz wolumen
docker-compose down -v

# Uruchom ponownie
docker-compose up -d

# Wykonaj migracje i seedery
php artisan migrate --seed
```

## Dodatkowe informacje

- Wolumen Docker `mariadb_data` przechowuje dane bazy danych, więc dane przetrwają restart kontenera
- Healthcheck w `backend/docker-compose.yml` zapewnia, że kontener jest gotowy przed użyciem
- Kontener automatycznie się restartuje przy awarii (opcja `restart: unless-stopped`)

