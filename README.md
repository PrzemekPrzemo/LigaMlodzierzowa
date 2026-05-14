# Liga Młodzieżowa PZSS 2026 — strona dynamiczna

Dynamiczna strona prezentująca Ligę Młodzieżową PZSS do lat 17 — z aktualnymi
wynikami zespołowymi, terminarzem rund i informacjami o Finale 2026 łączonym
ze Strzeleckim Pucharem Gdyni.

## Stack

- **PHP 8.3+** (czyste PHP, bez frameworka — bez Composera)
- **MySQL / MariaDB** (PDO)
- **HTML5 + nowoczesny CSS** (Inter + Barlow), JS bez zależności
- Wdrożenie pod **Plesk** (Apache/nginx + PHP-FPM)

## Struktura

```
public/         # webroot (DocumentRoot w Plesk)
  index.php     # front controller
  .htaccess     # rewrite + cache + headers
  assets/       # css, js, img, docs (PDF)
src/
  Core/         # Bootstrap, Router, Database, View
  Repository/   # EditionRepository, RoundRepository, ResultRepository, ContentRepository
templates/
  layout.php
  partials/     # header, footer
  pages/        # home, wyniki, terminarz, regulamin, final, news_*, kontakt, 404
config/
  config.example.php   # skopiuj do config.php i uzupełnij
database/
  migrations/001_schema.sql
  seeds/001_seed.sql
bin/install.php        # instalator bazy (CLI)
```

## Instalacja na Plesk

1. **Domena / katalog**: ustaw DocumentRoot na `public/`. Pełny katalog projektu
   trzymaj nad webrootem (np. `/var/www/vhosts/twoja-domena.pl/liga/` z DocumentRoot
   `liga/public/`).
2. **PHP**: wybierz PHP **8.3+** w panelu Plesk. Wymagane rozszerzenia: `pdo_mysql`,
   `mbstring`, `intl` (opcjonalnie).
3. **Baza danych**: utwórz bazę MySQL/MariaDB (np. `liga_mlodziezowa`) i użytkownika
   z pełnymi prawami do tej bazy.
4. **Konfiguracja**: skopiuj `config/config.example.php` do `config/config.php`
   i uzupełnij dane bazy + opcjonalny hash hasła administratora.
5. **Instalator**: w panelu Plesk → *Scheduled Tasks* lub przez SSH:
   ```bash
   php bin/install.php
   ```
   Skrypt utworzy tabele i załaduje dane (rundy 2026, wyniki rundy I — ZLOT Orlików).
6. **Pretty URL**: `.htaccess` w `public/` zajmuje się przekierowaniami. Pod nginx
   w Plesk dodaj w *Additional nginx directives*:
   ```nginx
   location / { try_files $uri $uri/ /index.php?$query_string; }
   ```

## Aktualizacja wyników

Wyniki są zapisane w tabeli `team_scores`. Aby dodać kolejne rundy (Puchar
Bydgoszczy, PP PZSS Wrocław, ZM ZK Bydgoszcz):

```sql
SET @ed := (SELECT id FROM editions WHERE year = 2026);
SET @r  := (SELECT id FROM rounds WHERE edition_id = @ed AND code = 'PB');
SET @d  := (SELECT id FROM disciplines WHERE code = 'PPN');

INSERT INTO team_scores (team_id, round_id, score)
SELECT t.id, @r, 1112.00
FROM teams t
WHERE t.edition_id = @ed AND t.discipline_id = @d AND t.display_name = 'DELFIN Tarnów'
ON DUPLICATE KEY UPDATE score = VALUES(score);

UPDATE rounds SET status='finished' WHERE id=@r;
UPDATE editions SET state_as_of = CURDATE() WHERE id=@ed;
```

Ranking wylicza się automatycznie (najwyższy wynik z rund, top 8 do Finału).

## Kolejne kroki

- Edytowalne treści przez prosty panel admina (logowanie hashed-password)
- Import wyników z CSV / XLSX (`bin/import_results.php`)
- Strona partnerska Finału z mapą strzelnicy i programem dnia
- Galeria zdjęć i transmisje wideo z Finału w Gdyni
