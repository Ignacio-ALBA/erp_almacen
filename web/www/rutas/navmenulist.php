<?php 
$navItems = [
    /* Módulo de Administración comentado
    [
      'label' => 'Administración',
      'icon' => 'bi bi-menu-button-wide',
      'route' =>'/rutas/administracion.php',
      'subitems' => [
        [
          "label" => "Bolsas de Proyectos",
          "href" => "/bolsas_proyectos",
          "permiso" => [
            "crear_bolsas_proyectos",
            "editar_bolsas_proyectos",
            "ver_bolsas_proyectos",
            "eliminar_bolsas_proyectos"
            ]
        ],
        [
          "label" => "Proyectos",
          "href" => "/proyectos",
          "permiso" => [
            "crear_proyectos",
            "editar_proyectos",
            "ver_proyectos",
            "eliminar_proyectos"
            ]
        ],
        [
          "label" => "Detalles de Proyectos",
          "href" => "/detalles_proyectos",
          "permiso" => [
            "crear_detalles_proyectos",
            "editar_detalles_proyectos",
            "ver_detalles_proyectos",
            "eliminar_detalles_proyectos"
            ]
        ]
      ]
    ],
    
    [
      'label' => 'Planeación',
      'icon' => 'bi bi-laptop',
      'route' =>'/rutas/planeacion.php',
      'subitems' => [
        [
          "label" => "Clientes",
          "href" => "/clientes",
          "permiso" => [
            "crear_clientes",
            "editar_clientes",
            "ver_clientes",
            "eliminar_clientes"
            ]
        ],
        [
          "label" => "Planeaciónes de Compras",
          "href" => "/planeaciones_compras",
          "permiso" => [
            "crear_planeaciones_compras",
            "editar_planeaciones_compras",
            "ver_planeaciones_compras",
            "eliminar_planeaciones_compras"
            ]
        ],
        [
          "label" => "Contenido de Planeaciónes de Compras",
          "href" => "/detalles_planeaciones_compras",
          "permiso" => [
            "crear_detalles_planeaciones_compras",
            "editar_detalles_planeaciones_compras",
            "ver_detalles_planeaciones_compras",
            "eliminar_detalles_planeaciones_compras"
            ]
        ],
        [
          "label" => "Cambios de Planeaciónes de Compras",
          "href" => "/cambios_planeaciones_compras",
          "permiso" => [
            "crear_cambios_planeaciones_compras",
            "editar_cambios_planeaciones_compras",
            "ver_cambios_planeaciones_compras",
            "eliminar_cambios_planeaciones_compras"
            ]
        ],
        [
          "label" => "Planeaciónes de Talento Humanos",
          "href" => "/planeaciones_recursos_humanos",
          "permiso" => [
            "crear_planeaciones_rrhh",
            "editar_planeaciones_rrhh",
            "ver_planeaciones_rrhh",
            "eliminar_planeaciones_rrhh"
            ]
        ],
        [
          "label" => "Contenido Planeaciones de Talento Humanos",
          "href" => "/detalles_planeaciones_recursos_humanos",
          "permiso" => [
            "crear_detalles_planeaciones_rrhh",
            "editar_detalles_planeaciones_rrhh",
            "ver_detalles_planeaciones_rrhh",
            "eliminar_detalles_planeaciones_rrhh"
            ]
        ],
        [
          "label" => "Trabajadores Internos Y Externos",
          "href" => "/internos_externos",
          "permiso" => [
            "crear_internos_externos",
            "editar_internos_externos",
            "ver_internos_externos",
            "eliminar_internos_externos"
            ]
        ],
        [
          "label" => "Tipos de Costos por Trabajo",
          "href" => "/tipos_costos_total",
          "permiso" => [
            "crear_tipos_costo",
            "editar_tipos_costo",
            "ver_tipos_costo",
            "eliminar_tipos_costo"
            ]
        ],
        [
          "label" => "Planeaciónes de Actividades",
          "href" => "/planeaciones_actividades",
          "permiso" => [
            "crear_planeaciones_actividades",
            "editar_planeaciones_actividades",
            "ver_planeaciones_actividades",
            "eliminar_planeaciones_actividades"
            ]
        ],
        [
          "label" => "Contenido Planeaciones de Actividades",
          "href" => "/detalles_planeaciones_actividades",
          "permiso" => [
            "crear_detalles_planeaciones_actividades",
            "editar_detalles_planeaciones_actividades",
            "ver_detalles_planeaciones_actividades",
            "eliminar_detalles_planeaciones_actividades"
            ]
        ]
      ]
    ],
    [
      'label' => 'Ingeniería de Servicios',
      'icon' => 'bi bi-laptop',
      'route' =>'/rutas/ingenieria_servicios.php',
      'subitems' => [
        [
          "label" => "Actividades",
          "href" => "/actividades",
          "permiso" => [
            "crear_actividades",
            "editar_actividades",
            "ver_actividades",
            "eliminar_actividades"
            ]
        ],
        [
          "label" => "Contenido de Actividades",
          "href" => "/detalles_actividades",
          "permiso" => [
            "crear_detalles_actividades",
            "editar_detalles_actividades",
            "ver_detalles_actividades",
            "eliminar_detalles_actividades"
            ]
        ],
        [
          "label" => "Justificaciones de Actividades",
          "href" => "/justificaciones_actividades",
          "permiso" => [
            "crear_justificaciones_actividades",
            "editar_justificaciones_actividades",
            "ver_justificaciones_actividades",
            "eliminar_justificaciones_actividades"
            ]
        ],
        [
          "label" => "Evidencias de Actividades",
          "href" => "/evidencia_actividades",
          "permiso" => [
            "crear_evidencia_actividades",
            "editar_evidencia_actividades",
            "ver_evidencia_actividades",
            "eliminar_evidencia_actividades"
            ]
        ]
      ]
    ],
    [
      'label' => 'Registro de eventos',
      'icon' => 'bi bi-calendar',
      'route' =>'/rutas/registro_eventos.php',
      'subitems' => [
        [
          "label" => "Registro de Eventos",
          "href" => "/registro_eventos",
          "permiso" => [
            "crear_registro_eventos",
            "editar_registro_eventos",
            "ver_registro_eventos",
            "eliminar_registro_eventos"
            ]
        ],
        [
          "label" => "Contenido de Registro de Eventos",
          "href" => "/detalles_registro_eventos",
          "permiso" => [
            "crear_detalles_registro_eventos",
            "editar_detalles_registro_eventos",
            "ver_detalles_registro_eventos",
            "eliminar_detalles_registro_eventos"
            ]
        ],
        [
          "label" => "Comentarios de Registro de Eventos",
          "href" => "/comentarios_registro_eventos",
          "permiso" => [
            "crear_comentarios_registro_eventos",
            "editar_comentarios_registro_eventos",
            "ver_comentarios_registro_eventos",
            "eliminar_comentarios_registro_eventos"
            ]
        ]
      ]
    ],*/
    /*
    [
      'label' => 'Tiempo Real',
      'icon' => 'bi bi-clock',
      'route' =>'/rutas/tiempo_real.php',
      'subitems' => [
        [
          "label" => "Tiempo Real",
          "href" => "/tiempo_real",
          "permiso" => [
            "crear_tiempo_real",
            "editar_tiempo_real",
            "ver_tiempo_real",
            "eliminar_tiempo_real"
            ]
        ],
        ]
      ],*/
    [
      'label' => 'Central de servicios',
      'icon' => 'bi bi-table',
      'route' =>'/rutas/central_servicios.php',
      'subitems' => [
        [
          "label" => "Central de materia prima",
          "href" => "/central_mp",
          "permiso" => [
            "crear_detalles_almacenes",
            "editar_detalles_almacenes",
            "ver_detalles_almacenes",
            "eliminar_detalles_almacenes"
            ]
        ],/*
        [
          "label" => "Central de pedidos de materia prima",
          "href" => "/central_pedidos_mp",
          "permiso" => [
            "crear_detalles_almacenes",
            "editar_detalles_almacenes",
            "ver_detalles_almacenes",
            "eliminar_detalles_almacenes"
            ]
        ],*/
        [
          "label" => "Central de productos",
          "href" => "/central_productos",
          "permiso" => [
            "crear_detalles_almacenes",
            "editar_detalles_almacenes",
            "ver_detalles_almacenes",
            "eliminar_detalles_almacenes"
            ]
        ],
        ]
       ],
    [
      'label' => 'Compras',
      'icon' => 'bi bi-bag',
      'route' =>'/rutas/compras.php',
      'subitems' => [
          /*
        [
          "label" => "Proveedores",
          "href" => "/proveedores",
          "permiso" => [
            "crear_proveedores",
            "editar_proveedores",
            "ver_proveedores",
            "eliminar_proveedores"
            ]
        ],
        [
          "label" => "Comentarios de Proveedores",
          "href" => "/comentarios_proveedores",
          "permiso" => [
            "crear_comentarios_proveedores",
            "editar_comentarios_proveedores",
            "ver_comentarios_proveedores",
            "eliminar_comentarios_proveedores"
            ]
        ], */
        [
          "label" => "Listas de Compras",
          "href" => "/listas_compras",
          "permiso" => [
            "crear_listas_compras",
            "editar_listas_compras",
            "ver_listas_compras",
            "eliminar_listas_compras"
            ]
        ],
        /*[
          "label" => "Contenido de Lista de Compras",
          "href" => "/detalles_listas_compras",
          "permiso" => [
            "crear_detalles_listas_compras",
            "editar_detalles_listas_compras",
            "ver_detalles_listas_compras",
            "eliminar_detalles_listas_compras"
            ]
        ],*/
        [
          "label" => "Cotizaciones",
          "href" => "/cotizaciones_compras",
          "permiso" => [
            "crear_cotizaciones_compras",
            "editar_cotizaciones_compras",
            "ver_cotizaciones_compras",
            "eliminar_cotizaciones_compras"
            ]
        ],
        [
          "label" => "Contenido de Cotizaciones",
          "href" => "/detalles_cotizaciones_compras",
          "permiso" => [
            "crear_detalles_cotizaciones_compras",
            "editar_detalles_cotizaciones_compras",
            "ver_detalles_cotizaciones_compras",
            "eliminar_detalles_cotizaciones_compras"
            ]
        ],
        [
          "label" => "Ordenes de compras",
          "href" => "/ordenes_compras",
          "permiso" => [
            "crear_ordenes_compras",
            "editar_ordenes_compras",
            "ver_ordenes_compras",
            "eliminar_ordenes_compras"
            ]
        ],
        [
          "label" => "Contenido de Ordenes de compras",
          "href" => "/detalles_ordenes_compras",
          "permiso" => [
            "crear_detalles_ordenes_compras",
            "editar_detalles_ordenes_compras",
            "ver_detalles_ordenes_compras",
            "eliminar_detalles_ordenes_compras"
            ]
        ],
        [
          "label" => "Recibir Orden",
          "href" => "/recepcion_orden",
          "permiso" => [
            "crear_recibir_orden",
            "editar_recibir_orden",
            "ver_recibir_orden",
            "eliminar_recibir_orden"
            ]
        ],
        [
          "label" => "Recepciones",
          "href" => "/recepciones_compras",
          "permiso" => [
            "crear_recepciones_compras",
            "editar_recepciones_compras",
            "ver_recepciones_compras",
            "eliminar_recepciones_compras"
            ]
        ], [
          "label" => "Recibir Pedido",
          "href" => "/recepciones_pedidos",
          "permiso" => [
            "crear_recepciones_pedidos",
            "editar_recepciones_pedidos",
            "ver_recepciones_pedidos",
            "eliminar_recepciones_pedidos"
            ]
        ],
        [
          "label" => "Contenido de Recepciones",
          "href" => "/detalles_recepciones_compras",
          "permiso" => [
            "crear_detalles_recepciones_compras",
            "editar_detalles_recepciones_compras",
            "ver_detalles_recepciones_compras",
            "eliminar_detalles_recepciones_compras"
            ]
        ],
        [
          "label" => "Comentarios de Recepciones",
          "href" => "/comentarios_recepciones",
          "permiso" => [
            "crear_comentarios_recepciones",
            "editar_comentarios_recepciones",
            "ver_comentarios_recepciones",
            "eliminar_comentarios_recepciones"
            ]
        ],
          /*
        [
          "label" => "Viáticos",
          "href" => "/asignacion_viaticos",
          "permiso" => [
            "crear_asignacion_viaticos",
            "editar_asignacion_viaticos",
            "ver_asignacion_viaticos",
            "eliminar_asignacion_viaticos"
            ]
        ],
        [
          "label" => "Tipos de Viáticos",
          "href" => "/tipos_viaticos",
          "permiso" => [
            "crear_tipos_viaticos",
            "editar_tipos_viaticos",
            "ver_tipos_viaticos",
            "eliminar_tipos_viaticos"
            ]
        ], 
        [
          "label" => "Tiempos de Entrega",
          "href" => "/tiempos_entregas",
          "permiso" => [
            "crear_tiempos_entregas",
            "editar_tiempos_entregas",
            "ver_tiempos_entregas",
            "eliminar_tiempos_entregas"
            ]
        ],
        [
          "label" => "Métodos de Pago",
          "href" => "/tipos_pagos",
          "permiso" => [
            "crear_tipos_pagos",
            "editar_tipos_pagos",
            "ver_tipos_pagos",
            "eliminar_tipos_pagos"
            ]
        ]*/
      ]
    ],/*
    [
      'label' => 'Ventas',
      'icon' => 'bi bi-cart',
      'route' =>'/rutas/ventas.php',
      'subitems' => [
        [
          "label" => "Capturar venta",
          "href" => "/capturar_venta",
          "permiso" => [
            "crear_capturar_ventas",
            "ver_capturar_ventas",
            "editar_capturar_ventas",
            "eliminar_capturar_ventas"
          ]
          ],
          [
            "label" => "Reporte de ventas",
            "href" => "/reporte_ventas",
            "permiso" => [
              "ver_reporte_ventas",
              "editar_reporte_ventas",
              "eliminar_reporte_ventas",
              "crear_reporte_ventas"

              ]
            ]
            ]
          ],*/
          [
            'label' => 'Producción',
            'icon' => 'bi bi-gear',
            'route' =>'/rutas/produccion.php',
            'subitems' => [
              [
                "label" => "Capturar producción",
                "href" => "/capturar_produccion",
                "permiso" => [
                  "ver_capturar_produccion"
                ]
                ],
                [
                  "label" => "Reporte de producción",
                  "href" => "/reporte_produccion",
                  "permiso" => [
                    "ver_reporte_produccion"
                    ]
                  ]
                  ]
                ],
      /*
    [
      'label' => 'Contabilidad',
      'icon' => 'bi bi-journal-text',
      'route' =>'/rutas/contabilidad.php',
      'subitems' => [
        [
          "label" => "Bancos",
          "href" => "/bancos",
          "permiso" => [
            "crear_bancos",
            "editar_bancos",
            "ver_bancos",
            "eliminar_bancos"
            ]
        ],
        [
          "label" => "Tipos de Cuentas Bancarias",
          "href" => "/tipos_cuentas_bancarias",
          "permiso" => [
            "crear_tipos_cuentas_bancarias",
            "editar_tipos_cuentas_bancarias",
            "ver_tipos_cuentas_bancarias",
            "eliminar_tipos_cuentas_bancarias"
            ]
        ],
        [
          "label" => "Cuentas Bancarias",
          "href" => "/cuentas_bancarias",
          "permiso" => [
            "crear_cuentas_bancarias",
            "editar_cuentas_bancarias",
            "ver_cuentas_bancarias",
            "eliminar_cuentas_bancarias"
            ]
        ],
        [
          "label" => "Detalles de Cuentas Bancarias",
          "href" => "/detalles_cuentas_bancarias",
          "permiso" => [
            "crear_detalles_cuentas_bancarias",
            "editar_detalles_cuentas_bancarias",
            "ver_detalles_cuentas_bancarias",
            "eliminar_detalles_cuentas_bancarias"
            ]
        ],
        [
          "label" => "Compras y Cuentas Bancarias",
          "href" => "/compras_cuentas_bancarias",
          "permiso" => [
            "crear_compras_cuentas_bancarias",
            "editar_compras_cuentas_bancarias",
            "ver_compras_cuentas_bancarias",
            "eliminar_compras_cuentas_bancarias"
            ]
        ],
        [
          "label" => "Facturas de Clientes",
          "href" => "/facturas_clientes",
          "permiso" => [
            "crear_facturas_clientes",
            "editar_facturas_clientes",
            "ver_facturas_clientes",
            "eliminar_facturas_clientes"
            ]
        ],
        [
          "label" => "Reportes de Cuentas Bancarias",
          "href" => "/reportes_cuentas_bancarias",
          "permiso" => [
            "crear_reportes_cuentas_bancarias",
            "editar_reportes_cuentas_bancarias",
            "ver_reportes_cuentas_bancarias",
            "eliminar_reportes_cuentas_bancarias"
            ]
        ],
        [
          "label" => "Monedas",
          "href" => "/monedas",
          "permiso" => [
            "crear_monedas",
            "editar_monedas",
            "ver_monedas",
            "eliminar_monedas"
            ]
        ],
        [
          "label" => "Contenido de Reportes de Cuentas Bancarias",
          "href" => "/detalles_reportes_cb",
          "permiso" => [
            "crear_detalles_reportes_cb",
            "editar_detalles_reportes_cb",
            "ver_detalles_reportes_cb",
            "eliminar_detalles_reportes_cb"
            ]
        ],
        [
          "label" => "Tipos de Reportes de Cuentas Bancarias",
          "href" => "/tipos_reportes_cb",
          "permiso" => [
            "crear_tipos_reportes_cb",
            "editar_tipos_reportes_cb",
            "ver_tipos_reportes_cb",
            "eliminar_tipos_reportes_cb"
            ]
        ],
        [
          "label" => "Observaciones de Reportes de Cuentas Bancarias",
          "href" => "/observaciones_reportes_cb",
          "permiso" => [
            "crear_observaciones_reportes_cb",
            "editar_observaciones_reportes_cb",
            "ver_observaciones_reportes_cb",
            "eliminar_observaciones_reportes_cb"
            ]
        ]
      ]
    ],*/
    [
      'label' => 'Talento Humano',
      'icon' => 'bi bi-person-lines-fill',
      'route' =>'/rutas/talento_humano.php',
      'subitems' => [
        [
          "label" => "Colaboradores",
          "href" => "/colaboradores",
          "permiso" => [
            "crear_usuarios",
            "editar_usuarios",
            "ver_usuarios",
            "eliminar_usuarios"
            ]
        ],/*
        [
          "label" => "Ocupaciones",
          "href" => "/ocupaciones_talento_humano",
          "permiso" => [
            "crear_ocupaciones_th",
            "editar_ocupaciones_th",
            "ver_ocupaciones_th",
            "eliminar_ocupaciones_th"
            ]
        ],
        [
          "label" => "Asistencias",
          "href" => "/asistencias_talento_humano",
          "permiso" => [
            "crear_asistencias_th",
            "editar_asistencias_th",
            "ver_asistencias_th",
            "eliminar_asistencias_th"
            ]
        ],
        [
          "label" => "Adicionales de Asistencias",
          "href" => "/adicionales_asistencias_talento_humano",
          "permiso" => [
            "crear_adicionales_asistencias_th",
            "editar_adicionales_asistencias_th",
            "ver_adicionales_asistencias_th",
            "eliminar_adicionales_asistencias_th"
            ]
        ],
        [
          "label" => "Tipos de Adicionales",
          "href" => "/tipos_adicionales",
          "permiso" => [
            "crear_tipos_adicionales_th",
            "editar_tipos_adicionales_th",
            "ver_tipos_adicionales_th",
            "eliminar_tipos_adicionales_th"
            ]
        ],*/
        [
          "label" => "Tipos de Colaboradores",
          "href" => "/tipos_usuario",
          "permiso" => [
            "crear_tipos_usuario",
            "editar_tipos_usuario",
            "ver_tipos_usuario",
            "eliminar_tipos_usuario",
            "asignar_permisos_tipos_usuario"
            ]
        ]
      ]
    ],  
    [
      'label' => 'Registro de eventos',
      'icon' => 'bi bi-calendar',
      'route' =>'/rutas/registro_eventos.php',
      'subitems' => [
        [
          "label" => "Registro de Eventos",
          "href" => "/registro_eventos",
          "permiso" => [
            "crear_registro_eventos",
            "editar_registro_eventos",
            "ver_registro_eventos",
            "eliminar_registro_eventos"
            ]
        ],
        [
          "label" => "Contenido de Registro de Eventos",
          "href" => "/detalles_registro_eventos",
          "permiso" => [
            "crear_detalles_registro_eventos",
            "editar_detalles_registro_eventos",
            "ver_detalles_registro_eventos",
            "eliminar_detalles_registro_eventos"
            ]
        ],/*
        [
          "label" => "Comentarios de Registro de Eventos",
          "href" => "/comentarios_registro_eventos",
          "permiso" => [
            "crear_comentarios_registro_eventos",
            "editar_comentarios_registro_eventos",
            "ver_comentarios_registro_eventos",
            "eliminar_comentarios_registro_eventos"
            ]
        ]*/
      ]
    ],
    [
      'label' => 'Catálogos',
      'icon' => 'bi bi-card-list',
      'route' =>'/rutas/catalogo.php',
      'subitems' => [
        [
          "label" => "Clientes",
          "href" => "/clientes",
          "permiso" => [
            "crear_clientes",
            "editar_clientes",
            "ver_clientes",
            "eliminar_clientes"
            ]
        ],
        [
          "label" => "Comentarios de Clientes",
          "href" => "/comentarios_clientes",
          "permiso" => [
            "crear_comentarios_clientes",
            "editar_comentarios_clientes",
            "ver_comentarios_clientes",
            "eliminar_comentarios_clientes"
            ]
        ],
        [
          "label" => "Proveedores",
          "href" => "/proveedores",
          "permiso" => [
            "crear_proveedores",
            "editar_proveedores",
            "ver_proveedores",
            "eliminar_proveedores"
            ]
        ],
        [
          "label" => "Comentarios de Proveedores",
          "href" => "/comentarios_proveedores",
          "permiso" => [
            "crear_comentarios_proveedores",
            "editar_comentarios_proveedores",
            "ver_comentarios_proveedores",
            "eliminar_comentarios_proveedores"
            ]
        ],
        [
          "label" => "General",
          "permiso" => [],
          'route' =>'/rutas/catalogo.php',
          "subitems" => [
            [
              "label" => "Marcas",
              "href" => "/marcas",
              "permiso" => [
                "crear_marcas",
                "editar_marcas",
                "ver_marcas",
                "eliminar_marcas"
                ]
            ],
            [
              "label" => "Categorías",
              "href" => "/categorias",
              "permiso" => [
                "crear_categorias",
                "editar_categorias",
                "ver_categorias",
                "eliminar_categorias"
                ]
            ],
            [
              "label" => "Mermas",
              "href" => "/mermas",
              "permiso" => [
                "crear_mermas",
                "editar_mermas",
                "ver_mermas",
                "eliminar_mermas"
                ]
            ],
            [
              "label" => "Subcategorías",
              "href" => "/subcategorias",
              "permiso" => [
                "crear_subcategorias",
                "editar_subcategorias",
                "ver_subcategorias",
                "eliminar_subcategorias"
                ]
            ],
            [
              "label" => "Dimensiones",
              "href" => "/dimensiones",
              "permiso" => [
                "crear_dimensiones",
                "editar_dimensiones",
                "ver_dimensiones",
                "eliminar_dimensiones"
                ]
            ],
            [
              "label" => "Presentaciones",
              "href" => "/presentaciones",
              "permiso" => [
                "crear_presentaciones",
                "editar_presentaciones",
                "ver_presentaciones",
                "eliminar_presentaciones"
                ]
            ],
            [
              "label" => "Formatos",
              "href" => "/formatos",
              "permiso" => [
                "crear_formatos",
                "editar_formatos",
                "ver_formatos",
                "eliminar_formatos"
                ]
            ],
            [
              "label" => "Unidades",
              "href" => "/unidades",
              "permiso" => [
                "crear_unidades",
                "editar_unidades",
                "ver_unidades",
                "eliminar_unidades"
                ]
            ],
            [
              "label" => "Artículos",
              "href" => "/articulos",
              "permiso" => [
                "crear_articulos",
                "editar_articulos",
                "ver_articulos",
                "eliminar_articulos"
                ]
            ],
            [
              "label" => "Estados",
              "href" => "/estados",
              "permiso" => [
                "crear_estados",
                "editar_estados",
                "ver_estados",
                "eliminar_estados"
                ]
            ],
            [
              "label" => "Municipios",
              "href" => "/municipios",
              "permiso" => [
                "crear_municipios",
                "editar_municipios",
                "ver_municipios",
                "eliminar_municipios"
                ]
            ],/*
            [
              "label" => "Tipos de Comentarios",
              "href" => "/tipos_comentarios",
              "permiso" => [
                "crear_tipos_comentarios",
                "editar_tipos_comentarios",
                "ver_tipos_comentarios",
                "eliminar_tipos_comentarios"
                ]
            ],
            [
              "label" => "Tipos de Estados",
              "href" => "/tipos_estados",
              "permiso" => [
                "crear_estatus",
                "editar_estatus",
                "ver_estatus",
                "eliminar_estatus"
                ]
            ]*/
          ] 
        ],
        [
          "label" => "Almacenes",
          "permiso" => [],
          'route' =>'/rutas/catalogo.php',
          "subitems" => [
            [
              "label" => "Almacenes",
              "href" => "/almacenes",
              "permiso" => [
                "crear_almacenes",
                "editar_almacenes",
                "ver_almacenes",
                "eliminar_almacenes"
                ]
            ],
            [
              "label" => "Locaciones de almacen",
              "href" => "/locaciones",
              "permiso" => [
                "crear_locaciones",
                "editar_locaciones",
                "ver_locaciones",
                "eliminar_locaciones"
                ]
            ],
            [
              "label" => "Contenido de Almacenes",
              "href" => "/detalles_almacenes",
              "permiso" => [
                "crear_detalles_almacenes",
                "editar_detalles_almacenes",
                "ver_detalles_almacenes",
                "eliminar_detalles_almacenes"
                ]
            ],
            [
              "label" => "Comentarios de Almacenes",
              "href" => "/comentarios_almacenes",
              "permiso" => [
                "crear_comentarios_almacenes",
                "editar_comentarios_almacenes",
                "ver_comentarios_almacenes",
                "eliminar_comentarios_almacenes"
                ]
            ]
          ] 
        ],
        [
          "label" => "Empresas",
          "href" => "/empresas",
          "permiso" => [
            "crear_empresas",
            "editar_empresas",
            "ver_empresas",
            "eliminar_empresas"
            ]
        ],
        [
          "label" => "Sucursales",
          "href" => "/sucursales",
          "permiso" => [
            "crear_sucursales",
            "editar_sucursales",
            "ver_sucursales",
            "eliminar_sucursales"
            ]
        ]
      ]
    ],
    [
      'label' => 'Configuraciones',
      'icon' => 'bi bi-gear',
      'route' =>'/rutas/configuraciones.php',
      'subitems' => [
        [
          "label" => "Permisos",
          "href" => "/permisos",
          "permiso" => [
            "ver_permisos",
            "editar_permisos"
          ],
        ],
        [
          "label" => "Tipos de Comentarios",
          "href" => "/tipos_comentarios",
          "permiso" => [
            "crear_tipos_comentarios",
            "editar_tipos_comentarios",
            "ver_tipos_comentarios",
            "eliminar_tipos_comentarios"
            ]
        ],
        [
          "label" => "Tipos de Estados",
          "href" => "/tipos_estados",
          "permiso" => [
            "crear_estatus",
            "editar_estatus",
            "ver_estatus",
            "eliminar_estatus"
            ]
          ],
          // Agrega más subitems según sea necesario
      ]
      ]
  ];
?>