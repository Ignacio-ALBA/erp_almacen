
ALTER TABLE articulos
ADD COLUMN tipo_articulo ENUM('materia_prima', 'producto_terminado') NULL DEFAULT NULL;

ubicacion_almacen (nueva para segmentar ubicaciones)
CREATE TABLE ubicacion_almacen (
  id_ubicacion INT PRIMARY KEY AUTO_INCREMENT,
  kid_almacen INT,
  codigo_localizacion VARCHAR(100),
  descripcion VARCHAR(200),
  kid_creacion int(11) NOT NULL,		
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,	
  kid_estatus int(11) NOT NULL,
FOREIGN KEY (kid_creacion) REFERENCES colaboradores(id_colaborador),
FOREIGN KEY (kid_estatus) REFERENCES estatus(id_estatus),
  FOREIGN KEY (id_almacen) REFERENCES almacenes(id_almacen)
);

Tabla produccion
Esta tabla representa una orden de producción o lote que se va a fabricar.

CREATE TABLE produccion (
    id_produccion INT PRIMARY KEY AUTO_INCREMENT,
    fecha_produccion DATETIME NOT NULL,
    kid_articulo INT NOT NULL,               -- Producto que se produce
    cantidad_producida DECIMAL(10,2) NOT NULL,
    kid_almacen INT NOT NULL,                -- Almacén donde se realiza la producción
    kid_creacion	int(11)	NOT NULL,	
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,	
    kid_estatus	int(11)	NOT NULL,
FOREIGN KEY (kid_creacion) REFERENCES colaboradores(id_colaborador),
    FOREIGN KEY (kid_articulo) REFERENCES articulos(id_articulo),
FOREIGN KEY (kid_estatus) REFERENCES estatus(id_estatus),
    FOREIGN KEY (kid_almacen) REFERENCES almacenes(id_almacen)
);
Tabla detalle_produccion
Relaciona la materia prima utilizada para fabricar un producto.
CREATE TABLE detalle_produccion (
    id_detalle_produccion INT PRIMARY KEY AUTO_INCREMENT,
    kid_produccion INT NOT NULL,
    kid_articulo INT NOT NULL,               -- Materia prima utilizada
    cantidad_usada DECIMAL(10,2) NOT NULL,
    kid_ubicacion INT,                       -- Ubicación interna del almacén donde se sacó la materia prima
    kid_creacion	int(11)	NOT NULL,		
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,	
    kid_estatus	int(11)	NOT NULL,
    codigo_qr VARCHAR(255),                 -- Código QR generado para la producción
FOREIGN KEY (kid_creacion) REFERENCES colaboradores(id_colaborador),
    FOREIGN KEY (kid_produccion) REFERENCES produccion(id_produccion),
    FOREIGN KEY (kid_articulo) REFERENCES articulos(id_articulo),
FOREIGN KEY (kid_estatus) REFERENCES estatus(id_estatus),
    FOREIGN KEY (kid_ubicacion) REFERENCES ubicacion_almacen(id_ubicacion)
);
 Tabla orden_venta
Representa la orden de venta general (cliente, fecha, etc.).

CREATE TABLE orden_venta (
    id_orden_venta INT PRIMARY KEY AUTO_INCREMENT,
    especificaciones_venta VARCHAR (100),
    fecha_venta DATETIME NOT NULL,
    kid_cliente INT NOT NULL,
    kid_creacion	int(11) NOT NULL,		
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,		
    kid_estatus int(11) NOT NULL,
FOREIGN KEY (kid_creacion) REFERENCES colaboradores(id_colaborador),
FOREIGN KEY (kid_estatus) REFERENCES estatus(id_estatus),
    FOREIGN KEY (kid_cliente) REFERENCES clientes(id_cliente)
);
 

Tabla detalle_venta
Productos vendidos por cada orden de venta.
CREATE TABLE detalle_venta (
    id_detalle_venta INT PRIMARY KEY AUTO_INCREMENT,
    kid_orden_venta INT NOT NULL,
    kid_articulo INT NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
kid_creacion	int(11)	NOT NULL,		
fecha_creacion	DATETIME DEFAULT CURRENT_TIMESTAMP,		
kid_estatus	int(11)	NOT NULL,		
FOREIGN KEY (kid_creacion) REFERENCES colaboradores(id_colaborador),
    FOREIGN KEY (kid_orden_venta) REFERENCES orden_venta(id_orden_venta),
FOREIGN KEY (kid_estatus) REFERENCES estatus(id_estatus),
    FOREIGN KEY (kid_articulo) REFERENCES articulos(id_articulo)
);

CREATE TABLE mermas (
    id_merma INT PRIMARY KEY AUTO_INCREMENT,
    kid_produccion INT NOT NULL,        -- Relación con el lote/producción
    kid_articulo INT NOT NULL,          -- Artículo al que se le hizo merma
    titulo VARCHAR(150) NOT NULL,      -- Título breve de la merma
    descripcion VARCHAR(200),                  -- Descripción detallada
    ckantidad DECIMAL(10,2) NOT NULL,   -- Cantidad merma (en unidades o kg, depende del artículo)
    kid_creacion	int(11)	NOT NULL,		
fecha_creacion	DATETIME DEFAULT CURRENT_TIMESTAMP,		
kid_estatus	int(11)	NOT NULL,
FOREIGN KEY (kid_creacion) REFERENCES colaboradores(id_colaborador),
    FOREIGN KEY (kid_produccion) REFERENCES produccion(id_produccion),
FOREIGN KEY (kid_estatus) REFERENCES estatus(id_estatus),
    FOREIGN KEY (kid_articulo) REFERENCES articulos(id_articulo)
);


5. 	detalles_orden_compra O detalles_recepciones  (ya presentes en la BD)
•	Mejora sugerida: Añadir tres campos para la recepcion de pedido:
	    codigo_qr VARCHAR(255),                 -- Código QR generado para la producción
                  kid_almacen INT,
                  kid_ubicacion_almacen INT,
                  fecha_recepcion DATETIME DEFAULT CURRENT_TIMESTAMP,
                  diferencia DECIMAL (10,2)


ALTER TABLE detalles_ordenes_compras
ADD COLUMN codigo_qr VARCHAR(255) AFTER porcentaje_descuento,
ADD COLUMN kid_almacen INT AFTER codigo_qr,
ADD COLUMN kid_ubicacion_almacen INT AFTER kid_almacen,
ADD COLUMN fecha_recepcion DATETIME DEFAULT CURRENT_TIMESTAMP AFTER kid_ubicacion_almacen,
ADD COLUMN diferencia DECIMAL(10,2) AFTER fecha_recepcion;

ALTER TABLE detalles_ordenes_compras
ADD CONSTRAINT fk_detalle_almacen
    FOREIGN KEY (kid_almacen) REFERENCES almacenes(id_almacen),
ADD CONSTRAINT fk_detalle_ubicacion
    FOREIGN KEY (kid_ubicacion_almacen) REFERENCES ubicacion_almacen(id_ubicacion);

Estos cambios los realice en la tabla de detalles_ordenes_compras
