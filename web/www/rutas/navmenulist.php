<?php
// Asegura que la sesión esté activa
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// DEBUG opcional (elimina después de verificar)
echo "<pre>TIPO USUARIO: " . ($_SESSION['tipo_usuario'] ?? 'SIN SESIÓN') . "</pre>";

$visibilidadPorTipoUsuario = [
  'Superadmin' => ['*'],
  'admin' => ['Compras', 'Almacén MP', 'Almacén Producción', 'Catálogos', 'Configuraciones'],
  'gerente' => ['Compras', 'Catálogos', 'Configuraciones'],
  'MP' => ['*'],
  'p' => ['Almacén Producción']
];

// Normaliza el tipo de usuario (asegura coincidencia con las claves del arreglo)
//$tipoUsuarioActual = strtolower($_SESSION['s_tipo_usuario'] ?? '');
$tipoUsuarioActual = $_SESSION['s_tipo_usuario'] ?? '';


$permisosVisibles = $visibilidadPorTipoUsuario[$tipoUsuarioActual];

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
    ],
    
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
        ]
      ]
    ],*/

  // 1. DASHBOARD - Mantener como está
  /*
    [
      'label' => 'Dashboard',
      'icon' => 'bi bi-speedometer2',
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
        ]
      ]
    ],
    */
  // 2. COMPRAS - Mantener como está
  [
    'label' => 'Compras',
    'icon' => 'bi bi-bag',
    'route' => '/rutas/compras.php',
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
      [
        "label" => "Contenido de Lista de Compras",
        "href" => "/detalles_listas_compras",
        "permiso" => [
          "crear_detalles_listas_compras",
          "editar_detalles_listas_compras",
          "ver_detalles_listas_compras",
          "eliminar_detalles_listas_compras"
        ]
      ],
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
      ]/*,
        [
          "label" => "Pesaje de Producción",
          "href" => "/recepcion_produccion",
          "permiso" => [
            "crear_recepciones_compras",
            "editar_recepciones_compras",
            "ver_recepciones_compras",
            "eliminar_recepciones_compras"
            ]
        ],
        [
          "label" => "Pesaje de Materia Prima",
          "href" => "/recepciones_compras",
          "permiso" => [
            "crear_recepciones_compras",
            "editar_recepciones_compras",
            "ver_recepciones_compras",
            "eliminar_recepciones_compras"
            ]
        ],
        [
          "label" => "Recibir Pedido para Producción",
          "href" => "/recepciones_pedidos",
          "permiso" => [
            "crear_recepciones_compras",
            "editar_recepciones_compras",
            "ver_recepciones_compras",
            "eliminar_recepciones_compras"
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
  ],

  // 3. ALMACEN MP - Nuevo módulo
  [
    'label' => 'Almacén MP',
    'icon' => 'bi bi-box-seam',
    'route' => '/rutas/almacen_mp.php',
    'subitems' => [
      [
        "label" => "Pesaje De Materia Prima",
        "href" => "/recepciones_materia_prima",
        "permiso" => [
          "crear_recepciones_compras",
          "editar_recepciones_compras",
          "ver_recepciones_compras",
          "eliminar_recepciones_compras"
        ]
      ],
      [
        "label" => "Contenido de Recepciones",
        "href" => "/detalles_recepciones_mp",
        "permiso" => [
          "crear_detalles_recepciones_compras",
          "editar_detalles_recepciones_compras",
          "ver_detalles_recepciones_compras",
          "eliminar_detalles_recepciones_compras"
        ]
      ],
      [
        "label" => "Comentarios de Recepciones",
        "href" => "/comentarios_recepciones_mp",
        "permiso" => [
          "crear_comentarios_recepciones",
          "editar_comentarios_recepciones",
          "ver_comentarios_recepciones",
          "eliminar_comentarios_recepciones"
        ]
      ],
      [
        "label" => "Reportes",
        "href" => "/reportes_mp",
        "permiso" => [
          "crear_reportes_mp",
          "editar_reportes_mp",
          "ver_reportes_mp",
          "eliminar_reportes_mp"
        ]
      ],
      [
        "label" => "Mermas",
        "href" => "/mermas_mp",
        "permiso" => [
          "crear_mermas",
          "editar_mermas",
          "ver_mermas",
          "eliminar_mermas"
        ]
      ]
    ]
  ],

  // 4. ALMACEN PRODUCCIÓN - Nuevo módulo
  [
    'label' => 'Almacén Producción',
    'icon' => 'bi bi-box2',
    'route' => '/rutas/almacen_produccion.php',
    'subitems' => [
      [
        "label" => "Recepción MP",
        "href" => "/recepciones_produccion",
        "permiso" => [
          "crear_recepciones_compras",
          "editar_recepciones_compras",
          "ver_recepciones_compras",
          "eliminar_recepciones_compras"
        ]
      ],
      [
        "label" => "Contenido de Recepciones",
        "href" => "/detalles_recepciones_mp",
        "permiso" => [
          "crear_detalles_recepciones_compras",
          "editar_detalles_recepciones_compras",
          "ver_detalles_recepciones_compras",
          "eliminar_detalles_recepciones_compras"
        ]
      ],
      [
        "label" => "Comentarios de Recepciones",
        "href" => "/comentarios_recepciones_mp",
        "permiso" => [
          "crear_comentarios_recepciones",
          "editar_comentarios_recepciones",
          "ver_comentarios_recepciones",
          "eliminar_comentarios_recepciones"
        ]
      ],
      [
        "label" => "Producción",
        "href" => "/produccion",
        "permiso" => [
          "crear_produccion",
          "editar_produccion",
          "ver_produccion",
          "eliminar_produccion"
        ]
      ],
      [
        "label" => "Pesaje Producción",
        "href" => "/pesaje_produccion",
        "permiso" => [
          "crear_pesaje_produccion",
          "editar_pesaje_produccion",
          "ver_pesaje_produccion",
          "eliminar_pesaje_produccion"
        ]
      ],
      [
        "label" => "Producto Terminado",
        "href" => "/producto_terminado",
        "permiso" => [
          "crear_producto_terminado",
          "editar_producto_terminado",
          "ver_producto_terminado",
          "eliminar_producto_terminado"
        ]
      ]
    ]
  ],

  // 5. CENTRAL DE SERVICIOS - Mantener y complementar
  [
    'label' => 'Central de servicios',
    'icon' => 'bi bi-table',
    'route' => '/rutas/central_servicios.php',
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
      ]
    ]
  ],

  // 6. TALENTO HUMANO - Mantener como está
  [
    'label' => 'Talento Humano',
    'icon' => 'bi bi-person-lines-fill',
    'route' => '/rutas/talento_humano.php',
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

  // 7. REGISTRO DE EVENTOS - Actualizar
  [
    'label' => 'Registro de eventos',
    'icon' => 'bi bi-calendar',
    'route' => '/rutas/registro_eventos.php',
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
        "label" => "Eventos de Sistema",
        "href" => "/eventos_sistema",
        "permiso" => [
          "crear_eventos_sistema",
          "editar_eventos_sistema",
          "ver_eventos_sistema",
          "eliminar_eventos_sistema"
        ]
      ]
      /*
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

  // Elimino módulo de producción y lo reemplazo por los nuevos módulos
  /* Comentado por la nueva estructura
    [
      'label' => 'Producción',
      'icon' => 'bi bi-gear',
      'route' =>'/rutas/produccion.php',
      'subitems' => [
        [
          "label" => "Capturar producción",
          "href" => "/capturar_produccion",
          "permiso" => [
            "ver_capturar_produccion",
            "editar_capturar_produccion",
            "eliminar_capturar_produccion",
            "crear_capturar_produccion"
            ]
          ]
          ],
          [
            "label" => "Reporte de producción",
            "href" => "/reporte_produccion",
            "permiso" => [
              "ver_reporte_produccion",
              "editar_reporte_produccion",
              "eliminar_reporte_produccion",
              "crear_reporte_produccion"
              
            ]
            ]
          ],
    */

  // 8. CATÁLOGOS - Mantener como está
  [
    'label' => 'Catálogos',
    'icon' => 'bi bi-card-list',
    'route' => '/rutas/catalogo.php',
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
        'route' => '/rutas/catalogo.php',
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
            "label" => "Materia Prima",
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
          ]/*,
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
        'route' => '/rutas/catalogo.php',
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
              "crear_locaciones_almacen",
              "editar_locaciones_almacen",
              "ver_locaciones_almacen",
              "eliminar_locaciones_almacen"
            ]
          ],/*
            [
              "label" => "Contenido de Almacenes",
              "href" => "/detalles_almacenes",
              "permiso" => [
                "crear_detalles_almacenes",
                "editar_detalles_almacenes",
                "ver_detalles_almacenes",
                "eliminar_detalles_almacenes"
                ]
            ],*/

          [
            "label" => "Tipo de Almacenes",
            "href" => "/tipo_almacenes",
            "permiso" => [
              "crear_tipo_almacenes",
              "editar_tipo_almacenes",
              "ver_tipo_almacenes",
              "eliminar_tipo_almacenes"
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

  // 9. CONFIGURACIONES - Mantener como está
  [
    'label' => 'Configuraciones',
    'icon' => 'bi bi-gear',
    'route' => '/rutas/configuraciones.php',
    'subitems' => [
      [
        "label" => "Permisos",
        "href" => "/permisos",
        "permiso" => [
          "ver_permisos",
          "editar_permisos"
        ]
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
      ]
    ]
  ]
];

// Solo después de definir todo, aplica el filtro
$navItems = array_filter($navItems, function ($item) use ($permisosVisibles) {
  return in_array('*', $permisosVisibles) || in_array($item['label'], $permisosVisibles);
});

// Si deseas reindexar (opcional):
$navItems = array_values($navItems);