# Kontekst Aplikacji CRM Mastermet

## 📋 Przegląd Aplikacji

**CRM Mastermet** to system CRM do zarządzania ofertami dla firmy produkującej narzędzia skrawające. Aplikacja umożliwia tworzenie, edycję i zarządzanie ofertami handlowymi, klientami, narzędziami oraz pokryciami.

### Główne Funkcjonalności
- Zarządzanie klientami (CRUD)
- Tworzenie i edycja ofert handlowych
- Zarządzanie narzędziami i ich geometriami
- Zarządzanie pokryciami (coatings)
- Generowanie PDF ofert
- Dashboard ze statystykami
- Import klientów z plików Excel
- System uprawnień oparty na rolach

---

## 🏗️ Architektura

### Stack Technologiczny

**Backend:**
- **Framework:** Laravel 11.31
- **PHP:** 8.2+
- **Baza danych:** MySQL/PostgreSQL (konfigurowalna)
- **Autentykacja:** Laravel Sanctum
- **PDF:** barryvdh/laravel-dompdf
- **Excel:** maatwebsite/excel

**Frontend:**
- **Framework:** Vue 3.5.13
- **Language:** TypeScript 5.8.3
- **Build Tool:** Vite 6.1.0
- **State Management:** Pinia 2.3.1
- **Routing:** Vue Router 4.5.0
- **Styling:** Tailwind CSS 4.0.4
- **Charts:** Chart.js 4.5.1 + vue-chartjs
- **HTTP Client:** Axios 1.7.9
- **UI Components:** Headless UI Vue

### Struktura Projektu

```
crm-mastermet/
├── backend/          # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   ├── Services/
│   │   └── ...
│   ├── routes/
│   ├── database/
│   └── resources/views/
└── frontend/         # Vue 3 SPA
    └── src/
        ├── Pages/
        ├── components/
        ├── store/
        ├── composables/
        └── services/
```

---

## 🗄️ Model Danych i Relacje

### Główne Modele

#### 1. **User** (Użytkownik/Pracownik)
- `id`, `name`, `email`, `password`, `marker`, `roles` (array)
- **Rola:** Authenticatable
- **Metody:** `hasRole($role)` - sprawdza czy użytkownik ma daną rolę
- **Relacje:**
  - `hasMany` Offer (jako created_by i changed_by)

#### 2. **Customer** (Klient)
- `id`, `code`, `name`, `nip`, `zip_code`, `city`, `address`, `saler_marker`, `description`
- **Relacje:**
  - `hasMany` Offer

#### 3. **Offer** (Oferta)
- `id`, `customer_id`, `status_id`, `total_price`, `created_by`, `changed_by`, `offer_number`, `created_at`, `updated_at`
- **Relacje:**
  - `belongsTo` Customer
  - `belongsTo` Status
  - `belongsTo` User (createdBy, changedBy)
  - `hasMany` OfferDetail
  - `hasOne` OfferPdfInfo

#### 4. **OfferDetail** (Szczegół Oferty)
- `id`, `offer_id`, `tool_type_id`, `tool_geometry_id`, `quantity`, `discount`, `tool_net_price`, `coating_price_id`, `coating_net_price`, `radius`, `regrinding_option`, `description`, `symbol`, `file_id`
- **Relacje:**
  - `belongsTo` Offer
  - `belongsTo` ToolType
  - `belongsTo` ToolGeometry
  - `belongsTo` CoatingPrice
  - `belongsTo` Tool (file_id)

#### 5. **Status** (Status Oferty)
- `id`, `name`
- **Wartości:** "Robocza", "Wysłana", "Zamówienie", "Odrzucona"
- **Relacje:**
  - `hasMany` Offer

#### 6. **Tool** (Narzędzie/Kartoteka)
- `id`, `code`, `name`, `price`, `diameter`
- **Relacje:**
  - `hasMany` OfferDetail (jako file_id)

#### 7. **ToolType** (Typ Narzędzia)
- `id`, `tool_type_name`
- **Relacje:**
  - `hasMany` OfferDetail

#### 8. **ToolGeometry** (Geometria Narzędzia)
- `id`, `tool_type_id`, `diameter`, `flutes_number`, `length`, `shank_diameter`, `overall_length`
- **Relacje:**
  - `belongsTo` ToolType
  - `hasMany` OfferDetail

#### 9. **CoatingType** (Typ Pokrycia)
- `id`, `mastermet_code`, `mastermet_name`
- **Relacje:**
  - `hasMany` CoatingPrice

#### 10. **CoatingPrice** (Cena Pokrycia)
- `id`, `coating_type_id`, `diameter`, `price`
- **Relacje:**
  - `belongsTo` CoatingType
  - `hasMany` OfferDetail

#### 11. **OfferPdfInfo** (Informacje PDF)
- `id`, `offer_id`, `delivery_time`, `offer_validity`, `payment_terms`, `display_discount`
- **Relacje:**
  - `belongsTo` Offer

#### 12. **Settings** (Ustawienia)
- `id`, `offer_number` (aktualny numer oferty)

### Diagram Relacji

```
User (created_by, changed_by)
  └── hasMany Offer

Customer
  └── hasMany Offer

Status
  └── hasMany Offer

Offer
  ├── belongsTo Customer
  ├── belongsTo Status
  ├── belongsTo User (createdBy, changedBy)
  ├── hasMany OfferDetail
  └── hasOne OfferPdfInfo

OfferDetail
  ├── belongsTo Offer
  ├── belongsTo ToolType
  ├── belongsTo ToolGeometry
  ├── belongsTo CoatingPrice
  └── belongsTo Tool (file_id)

ToolType
  └── hasMany ToolGeometry

CoatingType
  └── hasMany CoatingPrice
```

---

## 🔌 API Endpoints

### Autentykacja
Wszystkie endpointy (oprócz auth) wymagają middleware `auth:sanctum`.

### Klienci (`/api/klienci`)
- `GET /api/klienci` - lista klientów (z filtrowaniem)
- `POST /api/klienci` - utworzenie klienta
- `PUT /api/klienci/{customer}` - aktualizacja klienta
- `DELETE /api/klienci/{customer}` - usunięcie klienta
- `POST /api/klienci/import` - import z Excel

### Pracownicy (`/api/pracownicy`)
- `GET /api/pracownicy` - lista pracowników
- `POST /api/pracownicy` - utworzenie pracownika
- `PUT /api/pracownicy/{user}` - aktualizacja pracownika
- `DELETE /api/pracownicy/{user}` - usunięcie pracownika

### Oferty (`/api/offers`)
- `GET /api/offers` - lista ofert (z filtrowaniem i paginacją)
- `GET /api/offers/{id}` - szczegóły oferty
- `POST /api/offers` - utworzenie oferty
- `PUT /api/offers/{offer}` - aktualizacja oferty
- `DELETE /api/offers/{offer}` - usunięcie oferty (tylko status "Robocza")
- `POST /api/offers/{offer}/generate-pdf` - generowanie PDF
- `PUT /api/offers/{id}/update-number` - aktualizacja numeru oferty

### Dashboard (`/api/offers/dashboard`)
- `GET /api/offers/dashboard/stats` - statystyki dashboardu
- `GET /api/offers/dashboard/markers` - lista znaczników handlowców
- `GET /api/offers/dashboard/popular-tools` - najpopularniejsze narzędzia

### Słowniki (`/api/dictionaries`)
- `GET /api/dictionaries` - słowniki (narzędzia, pokrycia, statusy)

### Narzędzia i Pokrycia
- `GET /api/tools` - lista narzędzi
- `GET /api/coatings` - lista pokryć

### Ustawienia (`/api/settings`)
- `GET /api/settings` - pobranie ustawień
- `POST /api/settings` - utworzenie ustawień
- `PUT /api/settings/{setting}` - aktualizacja ustawień

### Użytkownik
- `GET /api/user` - aktualny zalogowany użytkownik

---

## 🔐 System Uprawnień

### Role Użytkowników
- **admin** - pełny dostęp do wszystkich funkcji
- **regeneration** - dostęp do regeneracji i zarządzania
- **standard** (domyślna) - dostęp tylko do własnych ofert

### Logika Uprawnień

**W kontrolerach:**
```php
if (!$user->hasRole('admin') && !$user->hasRole('regeneration')) {
    $query->where('created_by', $user->id);
}
```

**W routingu frontend:**
- `/pracownicy` - tylko admin i regeneration
- `/ustawienia` - tylko admin i regeneration
- Pozostałe strony - dostępne dla wszystkich zalogowanych

### Metoda Sprawdzania Roli
```php
$user->hasRole('admin') // zwraca true/false
```

---

## 📊 Logika Biznesowa

### Tworzenie Oferty
1. Walidacja danych przez `OfferRequest`
2. Transakcja bazy danych
3. Utworzenie rekordu Offer
4. Utworzenie rekordów OfferDetail (dla każdego narzędzia)
5. Utworzenie rekordu OfferPdfInfo
6. Zwrócenie pełnego obiektu z relacjami

### Generowanie PDF
1. Sprawdzenie czy oferta istnieje i ma szczegóły
2. Aktualizacja/utworzenie OfferPdfInfo
3. Generowanie numeru oferty (jeśli nie istnieje):
   - Pobranie numeru z Settings
   - Zwiększenie o 1
   - Format: `{numer}/{dd/mm/yyyy}`
4. Generowanie PDF z widoku `resources/views/offer/pdf.blade.php`
5. Zwrócenie pliku PDF jako response

### Usuwanie Oferty
- Możliwe tylko dla ofert ze statusem "Robocza"
- Usuwanie kaskadowe: OfferDetail, OfferPdfInfo

### Dashboard - Statystyki
- **Filtrowanie:**
  - Po uprawnieniach użytkownika
  - Po kliencie (customer_id)
  - Po handlowcu (employee_marker z Customer)
  - Po okresie (week, month, year, custom, all)
- **Statystyki:**
  - Liczba ofert w przygotowaniu (Robocza)
  - Liczba wysłanych (Wysłana)
  - Liczba zaakceptowanych (Zamówienie)
  - Liczba odrzuconych (Odrzucona)
  - Wartość wygranych vs. przegranych
  - Wartość miesięczna, kwartalna, roczna
  - Skuteczność ofert (%)

### Najpopularniejsze Narzędzia
- Najpopularniejsze kartoteki (file_id) - po sumie ilości
- Najpopularniejsze kombinacje: tool_type + flutes + diameter
- Najpopularniejsze pokrycia - po sumie ilości

---

## 🎨 Frontend - Struktura

### Store (Pinia)
- **user.ts** - zarządzanie użytkownikami i autentykacją
- **customer.ts** - zarządzanie klientami
- **offer.ts** - zarządzanie ofertami
- **tool.ts** - zarządzanie narzędziami
- **coating.ts** - zarządzanie pokryciami
- **settings.ts** - zarządzanie ustawieniami

### Pages
- **Dashboard.vue** - główny dashboard ze statystykami
- **Customers/** - zarządzanie klientami
- **Offers/** - zarządzanie ofertami
  - **OfferModal/** - modal z formularzem oferty
    - **OfferForm/** - komponenty formularza
- **Tools/** - zarządzanie narzędziami
- **Coatings/** - zarządzanie pokryciami
- **Employees/** - zarządzanie pracownikami
- **Settings/** - ustawienia systemu
- **Login.vue** - logowanie

### Routing
- `/` - Dashboard (wymaga autentykacji)
- `/klienci` - Klienci
- `/oferty` - Oferty
- `/narzedzia` - Narzędzia
- `/pokrycia` - Pokrycia
- `/pracownicy` - Pracownicy (tylko admin/regeneration)
- `/ustawienia` - Ustawienia (tylko admin/regeneration)
- `/login` - Logowanie

### Komponenty
- Layouty: `DefaultLayout.vue`
- Wspólne komponenty w `components/`
- Formularze w odpowiednich katalogach Pages

### Composables
- Logika wielokrotnego użytku w `composables/`

---

## 🔄 Konwersja Nazewnictwa

### Backend → Frontend
Aplikacja używa konwersji między snake_case (backend) a camelCase (frontend):

**Przykłady:**
- `total_price` → `totalPrice`
- `created_by` → `createdBy`
- `zip_code` → `zipCode`
- `saler_marker` → `salerMarker`
- `offer_number` → `offerNumber`
- `tool_net_price` → `toolNetPrice`
- `coating_net_price` → `coatingNetPrice`
- `regrinding_option` → `regrindingOption`

Modele mają metodę `toArray()` która wykonuje konwersję automatycznie.

---

## 📝 Ważne Konwencje

### Transakcje Bazy Danych
Wszystkie operacje modyfikujące dane (create, update, delete) używają transakcji:
```php
DB::beginTransaction();
try {
    // operacje
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    // obsługa błędu
}
```

### Eager Loading
Kontrolery używają eager loading dla optymalizacji zapytań:
```php
Offer::with([
    'customer',
    'offerDetails.coatingPrice.coatingType',
    'offerDetails.toolType',
    'offerDetails.toolGeometry',
    'offerDetails.tool',
    'status',
    'createdBy',
    'changedBy',
    'pdfInfo'
])
```

### Walidacja
Używa się Request classes (`OfferRequest`, etc.) do walidacji danych.

### Response Format
Wszystkie odpowiedzi API są w formacie JSON:
```json
{
    "data": [...],
    "message": "...",
    "errors": {...}
}
```

---

## 🗂️ Pliki Konfiguracyjne

### Backend
- `config/cors.php` - konfiguracja CORS
- `config/sanctum.php` - konfiguracja autentykacji
- `config/dompdf.php` - konfiguracja PDF
- `config/excel.php` - konfiguracja Excel

### Frontend
- `vite.config.js` - konfiguracja Vite
- `tailwind.config.js` - konfiguracja Tailwind
- `tsconfig.json` - konfiguracja TypeScript
- `axios.ts` - konfiguracja Axios (base URL, interceptors)

---

## 🧪 Testy

### Backend
- PHPUnit
- Testy w `tests/Feature/` i `tests/Unit/`
- Konfiguracja w `phpunit.xml`

---

## 📦 Zależności Kluczowe

### Backend
- `laravel/framework` ^11.31
- `laravel/sanctum` ^4.0
- `barryvdh/laravel-dompdf` ^3.1
- `maatwebsite/excel` ^3.1

### Frontend
- `vue` ^3.5.13
- `pinia` ^2.3.1
- `vue-router` ^4.5.0
- `axios` ^1.7.9
- `tailwindcss` ^4.0.4
- `chart.js` ^4.5.1

---

## 🚀 Uruchomienie

### Backend
```bash
cd backend
composer install
php artisan migrate
php artisan db:seed
php artisan serve
```

### Frontend
```bash
cd frontend
npm install
npm run dev
```

### Docker
Zobacz `docs/DOCKER.md` dla szczegółów.

---

## 📌 Ważne Uwagi dla Agentów

1. **Konwersja nazewnictwa:** Zawsze sprawdzaj czy używasz właściwej konwencji (snake_case vs camelCase)
2. **Transakcje:** Używaj transakcji dla operacji modyfikujących dane
3. **Eager Loading:** Używaj eager loading przy pobieraniu danych z relacjami
4. **Uprawnienia:** Sprawdzaj role użytkownika przed dostępem do zasobów
5. **Walidacja:** Używaj Request classes do walidacji
6. **Statusy:** Usuwanie ofert możliwe tylko dla statusu "Robocza"
7. **Numer oferty:** Generowany automatycznie przy pierwszym PDF, można go później zmienić
8. **Filtrowanie:** Dashboard i listy ofert mają zaawansowane filtrowanie
9. **PDF:** Generowanie PDF wymaga widoku Blade w `resources/views/offer/pdf.blade.php`
10. **Import:** Import klientów z Excel używa `maatwebsite/excel`

---

## 🔍 Gdzie Szukać Informacji

- **Modele:** `backend/app/Models/`
- **Kontrolery:** `backend/app/Http/Controllers/`
- **Requesty:** `backend/app/Http/Requests/`
- **Migrations:** `backend/database/migrations/`
- **Seeders:** `backend/database/seeders/`
- **Routes:** `backend/routes/api.php`
- **Frontend Pages:** `frontend/src/Pages/`
- **Frontend Store:** `frontend/src/store/`
- **Frontend Components:** `frontend/src/components/`
- **Widoki PDF:** `backend/resources/views/offer/`

---

**Ostatnia aktualizacja:** 2025-01-XX
**Wersja dokumentu:** 1.0

