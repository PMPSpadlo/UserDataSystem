# Projekt Laravel

## Wymagania wstępne

- PHP >= 8.4
- Composer
- MySQL >= 8.0
- Node.js (opcjonalnie, jeśli używane)
- Redis (do obsługi kolejek)

## Szybki start

1. **Klonowanie repozytorium**
   ```bash
   git clone https://github.com/PMPSpadlo/UserDataSystem.git
   cd UserDataSystem
   ```

2. **Instalacja zależności**
   ```bash
   composer install
   npm install
   ```

3. **Konfiguracja środowiska**
    - Skopiuj plik `.env.example`:
      ```bash
      cp .env.example .env
      ```
    - Uzupełnij dane w pliku `.env` (np. baza danych, Redis):
      ```env
      QUEUE_CONNECTION=redis
      ```
    - Wygeneruj klucz aplikacji:
      ```bash
      php artisan key:generate
      ```

4. **Migracje i seedy**
   Utwórz strukturę bazy danych i wprowadź dane początkowe:
   ```bash
   php artisan migrate --seed
   ```

5. **Uruchomienie serwera**
   ```bash
   php artisan serve
   ```
   Aplikacja będzie dostępna pod adresem: [http://localhost:8000](http://localhost:8000).

6. **Uruchomienie Redisa**
   Upewnij się, że Redis jest uruchomiony. Możesz użyć polecenia:
   ```bash
   redis-server
   ```

7. **Uruchomienie kolejek**
   W oddzielnym terminalu uruchom przetwarzanie jobów:
   ```bash
   php artisan queue:work
   ```

## Domyślne konto administratora
- **Email**: `admin@example.com`
- **Hasło**: `password`

## Komendy dodatkowe
- Kompilacja frontendu (jeśli dotyczy):
  ```bash
  npm run dev
  ```
- Czyszczenie cache:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan view:clear
  ```
