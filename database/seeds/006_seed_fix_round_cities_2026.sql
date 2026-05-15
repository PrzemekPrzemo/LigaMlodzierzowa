-- 006 — Poprawka miast rund 3 i 4 dla edycji 2026 (zamiana Wrocław <-> Bydgoszcz)
-- Idempotentny: bezpieczny do wielokrotnego uruchamiania.

SET @ed26 := (SELECT id FROM editions WHERE year = 2026);

UPDATE rounds
   SET city = 'Bydgoszcz',
       venue = 'Bydgoszcz',
       short_label = 'PP PZSS Bydgoszcz'
 WHERE edition_id = @ed26 AND code = 'PP_PZSS';

UPDATE rounds
   SET city = 'Wrocław',
       venue = 'Wrocław',
       short_label = 'ZM ZK Wrocław'
 WHERE edition_id = @ed26 AND code = 'ZM_ZK';
