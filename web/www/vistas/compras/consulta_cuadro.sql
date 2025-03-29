WITH detalles_cotizacion_compra_1 AS (
  SELECT kid_articulo, cantidad
  FROM detalles_cotizaciones_compras
  WHERE kid_cotizacion_compra = 1
),
cotizaciones_compras_similares AS (
  SELECT c.id_cotizacion_compra, COUNT(d.id_detalle_cotizacion_compras) AS num_detalles
  FROM cotizaciones_compras c
  JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
  WHERE c.kid_proyecto = (SELECT kid_proyecto FROM cotizaciones_compras WHERE id_cotizacion_compra = 1)
  AND c.id_cotizacion_compra != 1
  GROUP BY c.id_cotizacion_compra
  HAVING COUNT(d.id_detalle_cotizacion_compras) = (SELECT COUNT(*) FROM detalles_cotizaciones_compras WHERE kid_cotizacion_compra = 1)
),
detalles_cotizaciones_compras_similares AS (
  SELECT c.id_cotizacion_compra, d.kid_articulo, d.cantidad
  FROM cotizaciones_compras c
  JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
  WHERE c.id_cotizacion_compra IN (SELECT id_cotizacion_compra FROM cotizaciones_compras_similares)
)
SELECT c.id_cotizacion_compra
FROM cotizaciones_compras c
WHERE c.id_cotizacion_compra IN (
  SELECT d.id_cotizacion_compra
  FROM detalles_cotizaciones_compras_similares d
  GROUP BY d.id_cotizacion_compra
  HAVING SUM(CASE WHEN (d.kid_articulo, d.cantidad) IN (SELECT kid_articulo, cantidad FROM detalles_cotizacion_compra_1) THEN 1 ELSE 0 END) = (SELECT COUNT(*) FROM detalles_cotizacion_compra_1)
)


WITH detalles_cotizacion_compra_1 AS (
  SELECT kid_articulo, cantidad
  FROM detalles_cotizaciones_compras
  WHERE kid_cotizacion_compra = 1
),
cotizaciones_compras_similares AS (
  SELECT c.id_cotizacion_compra, COUNT(d.id_detalle_cotizacion_compras) AS num_detalles
  FROM cotizaciones_compras c
  JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
  WHERE c.kid_proyecto = (SELECT kid_proyecto FROM cotizaciones_compras WHERE id_cotizacion_compra = 1)
  AND c.id_cotizacion_compra != 1
  GROUP BY c.id_cotizacion_compra
  HAVING COUNT(d.id_detalle_cotizacion_compras) = (SELECT COUNT(*) FROM detalles_cotizaciones_compras WHERE kid_cotizacion_compra = 1)
),
detalles_cotizaciones_compras_similares AS (
  SELECT c.id_cotizacion_compra, d.kid_articulo, d.cantidad
  FROM cotizaciones_compras c
  JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
  WHERE c.id_cotizacion_compra IN (SELECT id_cotizacion_compra FROM cotizaciones_compras_similares)
)
SELECT 
  c.*,
  d.*,
  CASE 
    WHEN c.id_cotizacion_compra IN (
      SELECT d.id_cotizacion_compra
      FROM detalles_cotizaciones_compras_similares d
      GROUP BY d.id_cotizacion_compra
      HAVING SUM(CASE WHEN (d.kid_articulo, d.cantidad) IN (SELECT kid_articulo, cantidad FROM detalles_cotizacion_compra_1) THEN 1 ELSE 0 END) = (SELECT COUNT(*) FROM detalles_cotizacion_compra_1)
    ) THEN 'Similares'
    ELSE 'No Similares'
  END AS condicion
FROM cotizaciones_compras c
JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra



WITH detalles_cotizacion_compra_1 AS (
  SELECT kid_articulo, cantidad
  FROM detalles_cotizaciones_compras
  WHERE kid_cotizacion_compra = 1
),
cotizaciones_compras_similares AS (
  SELECT c.id_cotizacion_compra, COUNT(d.id_detalle_cotizacion_compras) AS num_detalles
  FROM cotizaciones_compras c
  JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
  WHERE c.kid_proyecto = (SELECT kid_proyecto FROM cotizaciones_compras WHERE id_cotizacion_compra = 1)
  AND c.id_cotizacion_compra != 1
  GROUP BY c.id_cotizacion_compra
  HAVING COUNT(d.id_detalle_cotizacion_compras) = (SELECT COUNT(*) FROM detalles_cotizaciones_compras WHERE kid_cotizacion_compra = 1)
),
detalles_cotizaciones_compras_similares AS (
  SELECT c.id_cotizacion_compra, d.kid_articulo, d.cantidad
  FROM cotizaciones_compras c
  JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
  WHERE c.id_cotizacion_compra IN (SELECT id_cotizacion_compra FROM cotizaciones_compras_similares)
),
id_detalles_cotizaciones_compras AS (
  SELECT d.kid_articulo, GROUP_CONCAT(d.id_detalle_cotizacion_compras SEPARATOR ',') AS id_detalles
  FROM detalles_cotizaciones_compras d
  GROUP BY d.kid_articulo
)
SELECT 
  idc.kid_articulo,
  COUNT(c.id_cotizacion_compra) AS num_cotizaciones,
  SUM(CASE 
    WHEN c.id_cotizacion_compra IN (
      SELECT d.id_cotizacion_compra
      FROM detalles_cotizaciones_compras_similares d
      GROUP BY d.id_cotizacion_compra
      HAVING SUM(CASE WHEN (d.kid_articulo, d.cantidad) IN (SELECT kid_articulo, cantidad FROM detalles_cotizacion_compra_1) THEN 1 ELSE 0 END) = (SELECT COUNT(*) FROM detalles_cotizacion_compra_1)
    ) THEN 1 ELSE 0 END) AS num_cotizaciones_similares,
  SUBSTRING_INDEX(idc.id_detalles, ',', 1) AS kid_detalle_1,
  SUBSTRING_INDEX(SUBSTRING_INDEX(idc.id_detalles, ',', 2), ',', -1) AS kid_detalle_2,
  SUBSTRING_INDEX(idc.id_detalles, ',', -1) AS kid_detalle_3
FROM cotizaciones_compras c
JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
JOIN id_detalles_cotizaciones_compras idc ON d.kid_articulo = idc.kid_articulo
GROUP BY idc.kid_articulo, idc.id_detalles