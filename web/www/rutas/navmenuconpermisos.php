<?php 
$navItems = [
    /* Módulo de Administración comentado
    [
      'label' => 'Administración',
      'icon' => 'bi bi-menu-button-wide',
      'route' =>'/rutas/administracion.php',
      'subitems' => [
        // ...existing code...
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
        ],
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
        [
          "label" => "Listas de Compras",
          "href" => "/listas_compras",
          "permiso" => [
            "ver_listas_compras",
            "editar_listas_compras",
            "agregar_listas_compras",
            "eliminar_listas_compras"
          ]
        ],
        [
          "label" => "Contenido de Lista de Compras",
          "href" => "/detalles_listas_compras",
          "permiso" => [
            "ver_detalles_listas_compras",
            "editar_detalles_listas_compras",
            "agregar_detalles_listas_compras",
            "eliminar_detalles_listas_compras"
          ]
        ],
        [
          "label" => "Cotizaciones",
          "href" => "/cotizaciones_compras",
          "permiso" => [
            "ver_cotizaciones_compras",
            "editar_cotizaciones_compras",
            "agregar_cotizaciones_compras",
            "eliminar_cotizaciones_compras"
          ]
        ],
        [
          "label" => "Contenido de Cotizaciones",
          "href" => "/detalles_cotizaciones_compras",
          "permiso" => [
            "ver_detalles_cotizaciones_compras",
            "editar_detalles_cotizaciones_compras",
            "agregar_detalles_cotizaciones_compras",
            "eliminar_detalles_cotizaciones_compras"
          ]
        ],
        [
          "label" => "Ordenes de compras",
          "href" => "/ordenes_compras",
          "permiso" => [
            "ver_ordenes_compras",
            "editar_ordenes_compras",
            "agregar_ordenes_compras",
            "eliminar_ordenes_compras"
          ]
        ],
        [
          "label" => "Contenido de Ordenes de compras",
          "href" => "/detalles_ordenes_compras",
          "permiso" => [
            "ver_detalles_ordenes_compras",
            "editar_detalles_ordenes_compras",
            "agregar_detalles_ordenes_compras",
            "eliminar_detalles_ordenes_compras"
          ]
        ],
        [
          "label" => "Pesaje de Producción",
          "href" => "/recepcion_produccion",
          "permiso" => [
            "ver_recepciones_compras",
            "editar_recepciones_compras",
            "agregar_recepciones_compras",
            "eliminar_recepciones_compras"
          ]
        ],
        [
          "label" => "Pesaje de Materia Prima",
          "href" => "/recepciones_compras",
          "permiso" => [
            "ver_recepciones_compras",
            "editar_recepciones_compras",
            "agregar_recepciones_compras",
            "eliminar_recepciones_compras"
          ]
        ],
        [
          "label" => "Recibir Pedido para Producción",
          "href" => "/recepciones_pedidos",
          "permiso" => [
            "ver_recepciones_compras",
            "editar_recepciones_compras",
            "agregar_recepciones_compras",
            "eliminar_recepciones_compras"
          ]
        ],
        [
          "label" => "Contenido de Recepciones",
          "href" => "/detalles_recepciones_compras",
          "permiso" => [
            "ver_detalles_recepciones_compras",
            "editar_detalles_recepciones_compras",
            "agregar_detalles_recepciones_compras",
            "eliminar_detalles_recepciones_compras"
          ]
        ],
        [
          "label" => "Comentarios de Recepciones",
          "href" => "/comentarios_recepciones",
          "permiso" => [
            "ver_comentarios_recepciones",
            "editar_comentarios_recepciones",
            "agregar_comentarios_recepciones",
            "eliminar_comentarios_recepciones"
          ]
        ]
      ]
    ],
    /* Ventas section commented out
    [
      'label' => 'Ventas',
      'icon' => 'bi bi-cart',
      'route' =>'/rutas/ventas.php',
      'subitems' => [
        // ...existing code...
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
            "ver_capturar_produccion",
            "editar_capturar_produccion",
            "eliminar_capturar_produccion",
            "crear_capturar_produccion"
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
      ]
    ],
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
        ],
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
        ]
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
?>

<?php
require_once('navmenulist.php');

// Filter menu items based on user permissions
$filteredNavItems = filterMenuItems($navItems);

foreach ($filteredNavItems as $item) {
    echo '<li class="nav-item">';
    if (isset($item['subitems'])) {
        echo '<a class="nav-link collapsed" data-bs-target="#' . str_replace(' ', '', strtolower($item['label'])) . '-nav" data-bs-toggle="collapse" href="#" aria-expanded="false">';
        echo '<i class="' . $item['icon'] . '"></i>';
        echo '<span>' . $item['label'] . '</span><i class="bi bi-chevron-down ms-auto"></i>';
        echo '</a>';
        echo '<ul id="' . str_replace(' ', '', strtolower($item['label'])) . '-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">';
        foreach ($item['subitems'] as $subitem) {
            if (isset($subitem['subitems'])) {
                // Handle nested subitems
                echo '<li>';
                echo '<a href="#' . str_replace(' ', '', strtolower($subitem['label'])) . '-subnav" data-bs-toggle="collapse">';
                echo '<i class="bi bi-circle"></i><span>' . $subitem['label'] . '</span><i class="bi bi-chevron-down ms-auto"></i>';
                echo '</a>';
                echo '<ul id="' . str_replace(' ', '', strtolower($subitem['label'])) . '-subnav" class="nav-content collapse">';
                foreach ($subitem['subitems'] as $nestedItem) {
                    echo '<li>';
                    echo '<a href="' . $nestedItem['href'] . '">';
                    echo '<i class="bi bi-circle"></i><span>' . $nestedItem['label'] . '</span>';
                    echo '</a>';
                    echo '</li>';
                }
                echo '</ul>';
                echo '</li>';
            } else {
                // Regular subitem
                echo '<li>';
                echo '<a href="' . $subitem['href'] . '">';
                echo '<i class="bi bi-circle"></i><span>' . $subitem['label'] . '</span>';
                echo '</a>';
                echo '</li>';
            }
        }
        echo '</ul>';
    } else {
        echo '<a class="nav-link collapsed" href="' . $item['href'] . '">';
        echo '<i class="' . $item['icon'] . '"></i>';
        echo '<span>' . $item['label'] . '</span>';
        echo '</a>';
    }
    echo '</li>';
}
?>