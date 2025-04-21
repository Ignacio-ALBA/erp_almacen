<?php 
$navItems = [
    [
      'label' => 'Administración',
      'icon' => 'bi bi-menu-button-wide',
      'route' =>'/rutas/administracion.php',
      'subitems' => [
        [
          "label" => "Bolsas de Proyectos",
          "href" => "/bolsas_proyectos",
          "permiso" => [
            "ver_bolsas_proyectos",
            "editar_bolsas_proyectos",
            "agregar_bolsas_proyectos",
            "eliminar_bolsas_proyectos"
          ],
        ],
        [
          "label" => "Proyectos",
          "href" => "/proyectos",
          "permiso" => [
            "ver_proyectos",
            "editar_proyectos",
            "agregar_proyectos",
            "eliminar_proyectos"
          ],
        ],
        [
          "label" => "Detalles de Proyectos",
          "href" => "/detalles_proyectos",
          "permiso" => [
            "ver_detalles_proyectos",
            "editar_detalles_proyectos",
            "agregar_detalles_proyectos",
            "eliminar_detalles_proyectos"
          ],
        ]
          // Agrega más subitems según sea necesario
      ]
    ],
    [
      'label' => 'Planeación',
      'icon' => 'bi bi-laptop',
      'route' =>'/rutas/planeacion.php',
      'subitems' => [
        /*[
          "label" => "Clientes",
          "href" => "/clientes",
          "permiso" => [
            "ver_clientes",
            "editar_clientes",
            "agregar_clientes",
            "eliminar_clientes"
          ],
        ],*/
        [
          "label" => "Planeaciónes de Compras",
          "href" => "/planeaciones_compras",
          "permiso" => [
            "ver_planeaciones_compras",
            "editar_planeaciones_compras",
            "agregar_planeaciones_compras",
            "eliminar_planeaciones_compras"
          ],
        ],
        [
          "label" => "Contenido de Planeaciónes de Compras",
          "href" => "/detalles_planeaciones_compras",
          "permiso" => [
            "ver_detalles_planeaciones_compras",
            "editar_detalles_planeaciones_compras",
            "agregar_detalles_planeaciones_compras",
            "eliminar_detalles_planeaciones_compras"
          ],
        ],
        [
          "label" => "Cambios de Planeaciónes de Compras",
          "href" => "/cambios_planeaciones_compras",
          "permiso" => [
            "ver_cambios_planeaciones_compras",
            "editar_cambios_planeaciones_compras",
            "agregar_cambios_planeaciones_compras",
            "eliminar_cambios_planeaciones_compras"
          ],
        ],
        /*[
          "label" => "Tablas",
          "href" => "/tablas",
          "permiso" => [],
        ],
        [
          "label" => "Módulos",
          "href" => "/modulos",
          "permiso" => [],
        ],*/
        [
          "label" => "Planeaciónes de Talento Humanos",
          "href" => "/planeaciones_recursos_humanos",
          "permiso" => [
            "ver_planeaciones_recursos_humanos",
            "editar_planeaciones_recursos_humanos",
            "agregar_planeaciones_recursos_humanos",
            "eliminar_planeaciones_recursos_humanos"
          ],
        ],
        [
          "label" => "Contenido Planeaciones de Talento Humanos",
          "href" => "/detalles_planeaciones_recursos_humanos",
          "permiso" => [
            "ver_detalles_planeaciones_recursos_humanos",
            "editar_detalles_planeaciones_recursos_humanos",
            "agregar_detalles_planeaciones_recursos_humanos",
            "eliminar_detalles_planeaciones_recursos_humanos"
          ],
        ],
        [
          "label" => "Trabajadores Internos Y Externos",
          "href" => "/internos_externos",
          "permiso" => [
            "ver_internos_externos",
            "editar_internos_externos",
            "agregar_internos_externos",
            "eliminar_internos_externos"
          ],
        ],
        [
          "label" => "Tipos de Costos por Trabajo",
          "href" => "/tipos_costos_total",
          "permiso" => [
            "ver_tipos_costos_total",
            "editar_tipos_costos_total",
            "agregar_tipos_costos_total",
            "eliminar_tipos_costos_total"
          ],
        ],
        [
          "label" => "Planeaciónes de Actividades",
          "href" => "/planeaciones_actividades",
          "permiso" => [
            "ver_planeaciones_actividades",
            "editar_planeaciones_actividades",
            "agregar_planeaciones_actividades",
            "eliminar_planeaciones_actividades"
          ],
        ],
        [
          "label" => "Contenido Planeaciónes de Actividades",
          "href" => "/detalles_planeaciones_actividades",
          "permiso" => [
            "ver_detalles_planeaciones_actividades",
            "editar_detalles_planeaciones_actividades",
            "agregar_detalles_planeaciones_actividades",
            "eliminar_detalles_planeaciones_actividades"
          ],
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
          "permiso" => [],
        ],
        [
          "label" => "Contenido de Actividades",
          "href" => "/detalles_actividades",
          "permiso" => [],
        ],
        [
          "label" => "Justificaciones de Actividades",
          "href" => "/justificaciones_actividades",
          "permiso" => [],
        ],
        [
          "label" => "Evidencias de Actividades",
          "href" => "/evidencia_actividades",
          "permiso" => [],
        ]
      ]
    ],
    [
        'label' => 'Compras',
        'icon' => 'bi bi-bag',
        'route' =>'/rutas/compras.php',
        'subitems' => [
            ['label' => 'Proveedores',  'href' => '/proveedores',"permiso" => []],
            ['label' => 'Comentarios de Proveedores',  'href' => '/comentarios_proveedores',"permiso" => []],
            ['label' => 'Listas de Compras',  'href' => '/listas_compras',"permiso" => []],
            ['label' => 'Contenido de Lista de Compras',  'href' => '/detalles_listas_compras',"permiso" => []],
            ['label' => 'Cotizaciones',  'href' => '/cotizaciones_compras',"permiso" => []],
            ['label' => 'Contenido de Cotizaciones',  'href' => '/detalles_cotizaciones_compras',"permiso" => []],
            ['label' => 'Ordenes de compras',  'href' => '/ordenes_compras',"permiso" => []],
            ['label' => 'Contenido de Ordenes de compras',  'href' => '/detalles_ordenes_compras',"permiso" => []],
            ['label' => 'Recepciones',  'href' => '/recepciones_compras',"permiso" => []],
            ['label' => 'Contenido de Recepciones',  'href' => '/detalles_recepciones_compras',"permiso" => []],
            ['label' => 'Comentarios de Recepciones',  'href' => '/comentarios_recepciones',"permiso" => []],
            ['label' => 'Viáticos',  'href' => '/asignacion_viaticos',"permiso" => []],
            ['label' => 'Tipos de Viáticos',  'href' => '/tipos_viaticos',"permiso" => []],
            ['label' => 'Tiempos de Entrega',  'href' => '/tiempos_entregas',"permiso" => []],
            ['label' => 'Métodos de Pago',  'href' => '/tipos_pagos',"permiso" => []],
            // Agrega más subitems según sea necesario
        ]
    ],
    [
      'label' => 'Contabilidad',
      'icon' => 'bi bi-journal-text',
      'route' =>'/rutas/contabilidad.php',
      'subitems' => [
        [
          "label" => "Bancos",
          "href" => "/bancos",
          "permiso" => [],
          "subitems" => [] 
        ],
        [
          "label" => "Tipos de Cuentas Bancarias",
          "href" => "/tipos_cuentas_bancarias",
          "permiso" => [],
          "subitems" => [] 
        ],
        [
          "label" => "Cuentas Bancarias",
          "href" => "/cuentas_bancarias",
          "permiso" => [],
          "subitems" => [] 
        ],
        [
          "label" => "Detalles de Cuentas Bancarias",
          "href" => "/detalles_cuentas_bancarias",
          "permiso" => [],
          "subitems" => [] 
        ],
        [
          "label" => "Compras y Cuentas Bancarias",
          "href" => "/compras_cuentas_bancarias",
          "permiso" => [],
          "subitems" => [] 
        ],
        [
          "label" => "Facturas de Clientes",
          "href" => "/facturas_clientes",
          "permiso" => [],
          "subitems" => [] 
        ],
        [
          "label" => "Reportes de Cuentas Bancarias",
          "href" => "/reportes_cuentas_bancarias",
          "permiso" => [],
          "subitems" => [] 
        ],
        [
          "label" => "Monedas",
          "href" => "/monedas",
          "permiso" => [],
          "subitems" => [] 
        ],
        [
          "label" => "Contenido de Reportes de Cuentas Bancarias",
          "href" => "/detalles_reportes_cb",
          "permiso" => [],
          
        ],
        [
          "label" => "Tipos de Reportes de Cuentas Bancarias",
          "href" => "/tipos_reportes_cb",
          "permiso" => [],
          "subitems" => [] 
        ],
        [
          "label" => "Observaciones de Reportes de Cuentas Bancarias",
          "href" => "/observaciones_reportes_cb",
          "permiso" => [],
          "subitems" => [] 
        ]
      ]
    ],
    [
        'label' => 'Talento Humano',
        'icon' => 'bi bi-person-lines-fill',
        'route' =>'/rutas/talento_humano.php',
        'subitems' => [
            ['label' => 'Colaboradores',  'href' => '/colaboradores',"permiso" => []],
            ['label' => 'Ocupaciones',  'href' => '/ocupaciones_talento_humano',"permiso" => []],
            ['label' => 'Asistencias',  'href' => '/asistencias_talento_humano',"permiso" => []],
            ['label' => 'Adicionales de Asistencias',  'href' => '/adicionales_asistencias_talento_humano',"permiso" => []],
            ['label' => 'Tipos de Adicionales',  'href' => '/tipos_adicionales',"permiso" => []],
            // Agrega más subitems según sea necesario
        ]
    ],
    [
      'label' => 'Catálogos',
      'icon' => 'bi bi-card-list',
      'route' =>'/rutas/catalogo.php',
      'subitems' => [
        [
          "label" => "General",
          "permiso" => [],
          'route' =>'/rutas/catalogo.php',
          "subitems" => [
          [
              "label" => "Marcas",
              "href" => "/marcas",
              "permiso" => [],
              "subitems" => []
          ],
          [
              "label" => "Categorías",
              "href" => "/categorias",
              "permiso" => []
          ],
          [
              "label" => "Subcategorías",
              "href" => "/subcategorias",
              "permiso" => []
          ],
          [
              "label" => "Dimensiones",
              "href" => "/dimensiones",
              "permiso" => []
          ],
          [
              "label" => "Presentaciones",
              "href" => "/presentaciones",
              "permiso" => []
          ],
          [
              "label" => "Formatos",
              "href" => "/formatos",
              "permiso" => []
          ],
          [
              "label" => "Roles",
              "href" => "/roles",
              "permiso" => []
          ],
          [
              "label" => "Unidades",
              "href" => "/unidades",
              "permiso" => []
          ],
          [
              "label" => "Artículos",
              "href" => "/articulos",
              "permiso" => []
          ],
          [
            "label" => "Estados",
            "href" => "/estados",
            "permiso" => []
          ],
          [
              "label" => "Municipios",
              "href" => "/municipios",
              "permiso" => []
          ],
          [
              "label" => "Tipos de Comentarios",
              "href" => "/tipos_comentarios",
              "permiso" => []
          ],
          [
              "label" => "Tipos de Estados",
              "href" => "/tipos_estados",
              "permiso" => []
          ]
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
              "permiso" => []
            ],
            [
              "label" => "Contenido de Almacenes",
              "href" => "/detalles_almacenes",
              "permiso" => []
            ]
            ,
            [
              "label" => "Comentarios de Almacenes",
              "href" => "/comentarios_almacenes",
              "permiso" => []
            ]
          ] 
        ],
        [
          "label" => "Empresas",
          "href" => "/empresas",
          "permiso" => []
        ],
        [
          "label" => "Sucursales",
          "href" => "/sucursales",
          "permiso" => []
        ]
      ]
    ],
    [
      'label' => 'Configuraciones',
      'icon' => 'bi bi-menu-button-wide',
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
          // Agrega más subitems según sea necesario
      ]
    ]
  ];
?>