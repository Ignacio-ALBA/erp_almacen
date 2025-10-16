
-- Migration: Add/Modify table codigos_qr to match new app structure
-- Date: 2025-10-13 (updated 2025-10-15)

-- New desired structure (fields):
-- id_codigo_qr INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
-- idOrdenCompra INT NULL
-- insumo VARCHAR(255) NOT NULL
-- proveedor VARCHAR(255) NOT NULL
-- fechaHora DATETIME NOT NULL
-- pesoKg DOUBLE NULL
-- pesoTarima DOUBLE NULL

-- If the table does not exist, create it with the new structure:
CREATE TABLE IF NOT EXISTS codigos_qr (
  id_codigo_qr INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  idOrdenCompra INT NULL,
  insumo VARCHAR(255) NOT NULL,
  proveedor VARCHAR(255) NOT NULL,
  fechaHora DATETIME NOT NULL,
  pesoKg DOUBLE NULL,
  pesoTarima DOUBLE NULL,
  KEY idx_idOrdenCompra (idOrdenCompra),
  KEY idx_fechaHora (fechaHora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- If the table already exists with the old schema, apply a best-effort ALTER to adapt it.
-- NOTE: Review this ALTER before running on production. It attempts to:
-- 1) Add new columns if missing
-- 2) Migrate possible existing values from 'codigo' or 'datos' if applicable (best-effort)
-- 3) Drop or keep legacy columns as needed (this script keeps legacy columns to avoid data loss)

-- Example ALTER statements (run manually against the database if needed):
-- Add new columns if they don't exist
ALTER TABLE codigos_qr
  ADD COLUMN IF NOT EXISTS idOrdenCompra INT NULL,
  ADD COLUMN IF NOT EXISTS insumo VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS proveedor VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS fechaHora DATETIME NULL,
  ADD COLUMN IF NOT EXISTS pesoKg DOUBLE NULL,
  ADD COLUMN IF NOT EXISTS pesoTarima DOUBLE NULL;

-- Optional: Try to populate new columns from existing 'datos' JSON if present
-- This assumes 'datos' JSON might contain keys like idOrdenCompra, insumo, proveedor, fechaHora, pesoKg, pesoTarima
-- The JSON functions used here are for MySQL 5.7+/8.0+; adjust if using an earlier version.
UPDATE codigos_qr
SET
  idOrdenCompra = COALESCE(idOrdenCompra, JSON_UNQUOTE(JSON_EXTRACT(datos, '$.idOrdenCompra'))),
  insumo = COALESCE(insumo, JSON_UNQUOTE(JSON_EXTRACT(datos, '$.insumo'))),
  proveedor = COALESCE(proveedor, JSON_UNQUOTE(JSON_EXTRACT(datos, '$.proveedor'))),
  fechaHora = COALESCE(fechaHora, JSON_UNQUOTE(JSON_EXTRACT(datos, '$.fechaHora'))),
  pesoKg = COALESCE(pesoKg, JSON_EXTRACT(datos, '$.pesoKg')),
  pesoTarima = COALESCE(pesoTarima, JSON_EXTRACT(datos, '$.pesoTarima'))
WHERE datos IS NOT NULL;

-- After migration you may want to make columns NOT NULL if appropriate and remove legacy columns (codigo, datos)

-- ------------------------------------------------------------------
-- BACKUP and DROP legacy 'datos' column
-- ------------------------------------------------------------------
-- The following steps will:
-- 1) Create a backup table `codigos_qr_datos_backup` with the original id and datos JSON.
-- 2) Populate the backup table from existing rows (if any).
-- 3) Drop the `datos` column from `codigos_qr`.
-- Run these commands only after verifying the UPDATE migration above has populated new columns correctly.

-- 1) Create backup table (if not exists)
CREATE TABLE IF NOT EXISTS codigos_qr_datos_backup (
  id_codigo_qr INT UNSIGNED PRIMARY KEY,
  datos JSON NULL,
  fecha_backup DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2) Populate backup table with existing datos values (no-op for rows already backed up)
INSERT INTO codigos_qr_datos_backup (id_codigo_qr, datos)
SELECT id_codigo_qr, datos FROM codigos_qr WHERE datos IS NOT NULL
ON DUPLICATE KEY UPDATE datos = VALUES(datos);

-- 3) Drop the datos column from the main table (run only after backup confirmed)
-- IMPORTANT: Uncomment and run the following line manually when ready
ALTER TABLE codigos_qr DROP COLUMN datos;

-- End of migration file
