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
          "permiso" => [
            "ver_actividades",
            "editar_actividades",
            "agregar_actividades",
            "eliminar_actividades"
          ],
        ],
        [
          "label" => "Contenido de Actividades", 
          "href" => "/detalles_actividades",
          "permiso" => [
            "ver_detalles_actividades",
            "editar_detalles_actividades",
            "agregar_detalles_actividades", 
            "eliminar_detalles_actividades"
          ],
        ],
        [
          "label" => "Justificaciones de Actividades",
          "href" => "/justificaciones_actividades", 
          "permiso" => [
            "ver_justificaciones_actividades",
            "editar_justificaciones_actividades",
            "agregar_justificaciones_actividades",
            "eliminar_justificaciones_actividades"
          ],
        ],
        [
          "label" => "Evidencias de Actividades",
          "href" => "/evidencia_actividades",
          "permiso" => [
            "ver_evidencias_actividades", 
            "editar_evidencias_actividades",
            "agregar_evidencias_actividades",
            "eliminar_evidencias_actividades"
          ],
        ]
      ]
    ],
    [
        'label' => 'Compras',
        'icon' => 'bi bi-bag',
        'route' =>'/rutas/compras.php',
        'subitems' => [
            ['label' => 'Proveedores', 'href' => '/proveedores', "permiso" => [
                "ver_proveedores",
                "editar_proveedores",
                "agregar_proveedores",
                "eliminar_proveedores"
            ]],
            ['label' => 'Comentarios de Proveedores', 'href' => '/comentarios_proveedores', "permiso" => [
                "ver_comentarios_proveedores",
                "editar_comentarios_proveedores",
                "agregar_comentarios_proveedores",
                "eliminar_comentarios_proveedores"
            ]],
            ['label' => 'Listas de Compras', 'href' => '/listas_compras', "permiso" => [
                "ver_listas_compras",
                "editar_listas_compras",
                "agregar_listas_compras",
                "eliminar_listas_compras"
            ]],
            ['label' => 'Contenido de Lista de Compras', 'href' => '/detalles_listas_compras', "permiso" => [
                "ver_detalles_listas_compras",
                "editar_detalles_listas_compras",
                "agregar_detalles_listas_compras",
                "eliminar_detalles_listas_compras"
            ]],
            ['label' => 'Cotizaciones', 'href' => '/cotizaciones_compras', "permiso" => [
                "ver_cotizaciones_compras",
                "editar_cotizaciones_compras",
                "agregar_cotizaciones_compras",
                "eliminar_cotizaciones_compras"
            ]],
            ['label' => 'Contenido de Cotizaciones', 'href' => '/detalles_cotizaciones_compras', "permiso" => [
                "ver_detalles_cotizaciones_compras",
                "editar_detalles_cotizaciones_compras",
                "agregar_detalles_cotizaciones_compras",
                "eliminar_detalles_cotizaciones_compras"
            ]],
            ['label' => 'Ordenes de compras', 'href' => '/ordenes_compras', "permiso" => [
                "ver_ordenes_compras",
                "editar_ordenes_compras",
                "agregar_ordenes_compras",
                "eliminar_ordenes_compras"
            ]],
            ['label' => 'Contenido de Ordenes de compras', 'href' => '/detalles_ordenes_compras', "permiso" => [
                "ver_detalles_ordenes_compras",
                "editar_detalles_ordenes_compras",
                "agregar_detalles_ordenes_compras",
                "eliminar_detalles_ordenes_compras"
            ]],
            ['label' => 'Recepciones', 'href' => '/recepciones_compras', "permiso" => [
                "ver_recepciones_compras",
                "editar_recepciones_compras",
                "agregar_recepciones_compras",
                "eliminar_recepciones_compras"
            ]],
            ['label' => 'Contenido de Recepciones', 'href' => '/detalles_recepciones_compras', "permiso" => [
                "ver_detalles_recepciones_compras",
                "editar_detalles_recepciones_compras",
                "agregar_detalles_recepciones_compras",
                "eliminar_detalles_recepciones_compras"
            ]],
            ['label' => 'Comentarios de Recepciones', 'href' => '/comentarios_recepciones', "permiso" => [
                "ver_comentarios_recepciones",
                "editar_comentarios_recepciones",
                "agregar_comentarios_recepciones",
                "eliminar_comentarios_recepciones"
            ]],
            ['label' => 'Viáticos', 'href' => '/asignacion_viaticos', "permiso" => [
                "ver_asignacion_viaticos",
                "editar_asignacion_viaticos",
                "agregar_asignacion_viaticos",
                "eliminar_asignacion_viaticos"
            ]],
            ['label' => 'Tipos de Viáticos', 'href' => '/tipos_viaticos', "permiso" => [
                "ver_tipos_viaticos",
                "editar_tipos_viaticos",
                "agregar_tipos_viaticos",
                "eliminar_tipos_viaticos"
            ]],
            ['label' => 'Tiempos de Entrega', 'href' => '/tiempos_entregas', "permiso" => [
                "ver_tiempos_entregas",
                "editar_tiempos_entregas",
                "agregar_tiempos_entregas",
                "eliminar_tiempos_entregas"
            ]],
            ['label' => 'Métodos de Pago', 'href' => '/tipos_pagos', "permiso" => [
                "ver_tipos_pagos",
                "editar_tipos_pagos",
                "agregar_tipos_pagos",
                "eliminar_tipos_pagos"
            ]]
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
          "permiso" => [
            "ver_bancos",
            "editar_bancos",
            "agregar_bancos",
            "eliminar_bancos"
          ]
        ],
        [
          "label" => "Tipos de Cuentas Bancarias",
          "href" => "/tipos_cuentas_bancarias",
          "permiso" => [
            "ver_tipos_cuentas_bancarias",
            "editar_tipos_cuentas_bancarias",
            "agregar_tipos_cuentas_bancarias",
            "eliminar_tipos_cuentas_bancarias"
          ]
        ],
        [
          "label" => "Cuentas Bancarias",
          "href" => "/cuentas_bancarias",
          "permiso" => [
            "ver_cuentas_bancarias",
            "editar_cuentas_bancarias",
            "agregar_cuentas_bancarias",
            "eliminar_cuentas_bancarias"
          ]
        ],
        [
          "label" => "Detalles de Cuentas Bancarias",
          "href" => "/detalles_cuentas_bancarias",
          "permiso" => [
            "ver_detalles_cuentas_bancarias",
            "editar_detalles_cuentas_bancarias",
            "agregar_detalles_cuentas_bancarias",
            "eliminar_detalles_cuentas_bancarias"
          ]
        ],
        [
          "label" => "Compras y Cuentas Bancarias",
          "href" => "/compras_cuentas_bancarias",
          "permiso" => [
            "ver_compras_cuentas_bancarias",
            "editar_compras_cuentas_bancarias",
            "agregar_compras_cuentas_bancarias",
            "eliminar_compras_cuentas_bancarias"
          ]
        ],
        [
          "label" => "Facturas de Clientes",
          "href" => "/facturas_clientes",
          "permiso" => [
            "ver_facturas_clientes",
            "editar_facturas_clientes",
            "agregar_facturas_clientes",
            "eliminar_facturas_clientes"
          ]
        ],
        [
          "label" => "Reportes de Cuentas Bancarias",
          "href" => "/reportes_cuentas_bancarias",
          "permiso" => [
            "ver_reportes_cuentas_bancarias",
            "editar_reportes_cuentas_bancarias",
            "agregar_reportes_cuentas_bancarias",
            "eliminar_reportes_cuentas_bancarias"
          ]
        ],
        [
          "label" => "Monedas",
          "href" => "/monedas",
          "permiso" => [
            "ver_monedas",
            "editar_monedas",
            "agregar_monedas",
            "eliminar_monedas"
          ]
        ],
        [
          "label" => "Contenido de Reportes de Cuentas Bancarias",
          "href" => "/detalles_reportes_cb",
          "permiso" => [
            "ver_detalles_reportes_cb",
            "editar_detalles_reportes_cb",
            "agregar_detalles_reportes_cb",
            "eliminar_detalles_reportes_cb"
          ]
        ],
        [
          "label" => "Tipos de Reportes de Cuentas Bancarias",
          "href" => "/tipos_reportes_cb",
          "permiso" => [
            "ver_tipos_reportes_cb",
            "editar_tipos_reportes_cb",
            "agregar_tipos_reportes_cb",
            "eliminar_tipos_reportes_cb"
          ]
        ],
        [
          "label" => "Observaciones de Reportes de Cuentas Bancarias",
          "href" => "/observaciones_reportes_cb",
          "permiso" => [
            "ver_observaciones_reportes_cb",
            "editar_observaciones_reportes_cb",
            "agregar_observaciones_reportes_cb",
            "eliminar_observaciones_reportes_cb"
          ]
        ]
      ]
    ],
    [
        'label' => 'Talento Humano',
        'icon' => 'bi bi-person-lines-fill',
        'route' =>'/rutas/talento_humano.php',
        'subitems' => [
            ['label' => 'Colaboradores', 'href' => '/colaboradores', "permiso" => [
                "ver_colaboradores",
                "editar_colaboradores",
                "agregar_colaboradores",
                "eliminar_colaboradores"
            ]],
            ['label' => 'Ocupaciones', 'href' => '/ocupaciones_talento_humano', "permiso" => [
                "ver_ocupaciones_talento_humano",
                "editar_ocupaciones_talento_humano",
                "agregar_ocupaciones_talento_humano",
                "eliminar_ocupaciones_talento_humano"
            ]],
            ['label' => 'Asistencias', 'href' => '/asistencias_talento_humano', "permiso" => [
                "ver_asistencias_talento_humano",
                "editar_asistencias_talento_humano",
                "agregar_asistencias_talento_humano",
                "eliminar_asistencias_talento_humano"
            ]],
            ['label' => 'Adicionales de Asistencias', 'href' => '/adicionales_asistencias_talento_humano', "permiso" => [
                "ver_adicionales_asistencias_talento_humano",
                "editar_adicionales_asistencias_talento_humano",
                "agregar_adicionales_asistencias_talento_humano",
                "eliminar_adicionales_asistencias_talento_humano"
            ]],
            ['label' => 'Tipos de Adicionales', 'href' => '/tipos_adicionales', "permiso" => [
                "ver_tipos_adicionales",
                "editar_tipos_adicionales",
                "agregar_tipos_adicionales",
                "eliminar_tipos_adicionales"
            ]]
        ]
    ],
    [
      'label' => 'Catálogos',
      'icon' => 'bi bi-card-list',
      'route' =>'/rutas/catalogo.php',
      'subitems' => [
        [
          "label" => "General",
          "permiso" => ["ver_catalogo_general"],
          'route' =>'/rutas/catalogo.php',
          "subitems" => [
          [
              "label" => "Marcas",
              "href" => "/marcas",
              "permiso" => [
                "ver_marcas",
                "editar_marcas",
                "agregar_marcas",
                "eliminar_marcas"
              ]
          ],
          [
              "label" => "Categorías",
              "href" => "/categorias",
              "permiso" => [
                "ver_categorias",
                "editar_categorias", 
                "agregar_categorias",
                "eliminar_categorias"
              ]
          ],
          [
              "label" => "Subcategorías",
              "href" => "/subcategorias",
              "permiso" => [
                "ver_subcategorias",
                "editar_subcategorias",
                "agregar_subcategorias",
                "eliminar_subcategorias"
              ]
          ],
          [
              "label" => "Dimensiones",
              "href" => "/dimensiones",
              "permiso" => [
                "ver_dimensiones",
                "editar_dimensiones",
                "agregar_dimensiones",
                "eliminar_dimensiones"
              ]
          ],
          [
              "label" => "Presentaciones",
              "href" => "/presentaciones",
              "permiso" => [
                "ver_presentaciones",
                "editar_presentaciones",
                "agregar_presentaciones",
                "eliminar_presentaciones"
              ]
          ],
          [
              "label" => "Formatos",
              "href" => "/formatos",
              "permiso" => [
                "ver_formatos",
                "editar_formatos",
                "agregar_formatos",
                "eliminar_formatos"
              ]
          ],
          [
              "label" => "Roles",
              "href" => "/roles",
              "permiso" => [
                "ver_roles",
                "editar_roles",
                "agregar_roles",
                "eliminar_roles"
              ]
          ],
          [
              "label" => "Unidades",
              "href" => "/unidades",
              "permiso" => [
                "ver_unidades",
                "editar_unidades",
                "agregar_unidades",
                "eliminar_unidades"
              ]
          ],
          [
              "label" => "Artículos",
              "href" => "/articulos",
              "permiso" => [
                "ver_articulos",
                "editar_articulos",
                "agregar_articulos",
                "eliminar_articulos"
              ]
          ],
          [
            "label" => "Estados",
            "href" => "/estados",
            "permiso" => [
              "ver_estados",
              "editar_estados",
              "agregar_estados",
              "eliminar_estados"
            ]
          ],
          [
              "label" => "Municipios",
              "href" => "/municipios",
              "permiso" => [
                "ver_municipios",
                "editar_municipios",
                "agregar_municipios",
                "eliminar_municipios"
              ]
          ],
          [
              "label" => "Tipos de Comentarios",
              "href" => "/tipos_comentarios",
              "permiso" => [
                "ver_tipos_comentarios",
                "editar_tipos_comentarios",
                "agregar_tipos_comentarios",
                "eliminar_tipos_comentarios"
              ]
          ],
          [
              "label" => "Tipos de Estados",
              "href" => "/tipos_estados",
              "permiso" => [
                "ver_tipos_estados",
                "editar_tipos_estados",
                "agregar_tipos_estados",
                "eliminar_tipos_estados"
              ]
          ]
          ] 
        ],
        [
          "label" => "Almacenes",
          "permiso" => ["ver_catalogo_almacenes"],
          'route' =>'/rutas/catalogo.php',
          "subitems" => [
            [
              "label" => "Almacenes",
              "href" => "/almacenes",
              "permiso" => [
                "ver_almacenes",
                "editar_almacenes",
                "agregar_almacenes",
                "eliminar_almacenes"
              ]
            ],
            [
              "label" => "Contenido de Almacenes",
              "href" => "/detalles_almacenes",
              "permiso" => [
                "ver_detalles_almacenes",
                "editar_detalles_almacenes",
                "agregar_detalles_almacenes",
                "eliminar_detalles_almacenes"
              ]
            ]
            ,
            [
              "label" => "Comentarios de Almacenes",
              "href" => "/comentarios_almacenes",
              "permiso" => [
                "ver_comentarios_almacenes",
                "editar_comentarios_almacenes",
                "agregar_comentarios_almacenes",
                "eliminar_comentarios_almacenes"
              ]
            ]
          ] 
        ],
        [
          "label" => "Empresas",
          "href" => "/empresas",
          "permiso" => [
            "ver_empresas",
            "editar_empresas",
            "agregar_empresas",
            "eliminar_empresas"
          ]
        ],
        [
          "label" => "Sucursales",
          "href" => "/sucursales",
          "permiso" => [
            "ver_sucursales",
            "editar_sucursales",
            "agregar_sucursales",
            "eliminar_sucursales"
          ]
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