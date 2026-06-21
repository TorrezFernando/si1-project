<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Sistema de Gestión Escolar',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Colegio</b> "Los Angeles"',
    'logo_img' => 'img/Colegio.jpg',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'img/Colegio.jpg',
            'alt' => 'Auth Logo',
            'class' => 'img-circle',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'img/Colegio.jpg',
            'alt' => 'Colegio Los Angeles',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 220,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => '/',
    'logout_url' => 'logout',
    'logout_method' => 'POST',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'admin/password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // ========== PANEL PRINCIPAL ==========
        [
            'text' => 'PANEL PRINCIPAL',
            'icon' => 'fas fa-fw fa-tachometer-alt',
            'url' => 'panel',
            'can' => 'home-panel',
        ],

        // ✅ Agrega este separador SUTIL
        [
            'type' => 'divider',
            'classes' => 'mt-1 mb-2',
        ],
        // ========== USUARIOS ==========
        [
            'text' => 'USUARIOS',
            'icon' => 'fas fa-fw fa-users',
            'icon_color' => 'info',
            'submenu' => [
                /* [
                    'text' => 'Administradores',
                    'url' => 'admin/administradores',
                    'icon' => 'fas fa-fw fa-user-shield',
                    'can' => 'admin.administradores.index'
                ], */
                [
                    'text' => 'Usuarios del Sistema',
                    'url' => 'admin/usuarios',
                    'icon' => 'fas fa-fw fa-users-cog',
                    'can' => 'admin.usuarios.index'
                ],
                [
                    'text' => 'Personal Administrativo',
                    'url' => 'admin/personal-administrativo',
                    'icon' => 'fas fa-fw fa-user-cog',
                    'can' => 'admin.personal-administrativo.index'
                ],
                [
                    'text' => 'Profesores',
                    'url' => 'admin/profesores',
                    'icon' => 'fas fa-fw fa-chalkboard-teacher',
                    'can' => 'admin.profesores.index'
                ],
                [
                    'text' => 'Apoderados',
                    'url' => 'admin/apoderados',
                    'icon' => 'fas fa-fw fa-user-tie',
                    'can' => 'admin.apoderados.index'
                ],
            ],
        ],

        // ========== ACADÉMICO ==========
        [
            'text' => 'ACADÉMICO',
            'icon' => 'fas fa-fw fa-chalkboard-teacher',
            'icon_color' => 'success',
            'submenu' => [
                [
                    'text' => 'Alumnos',
                    'url' => 'admin/alumnos',
                    'icon' => 'fas fa-fw fa-user-graduate',
                    'can' => 'admin.alumnos.index'
                ],
                [
                    'text' => 'Matriculas',
                    'url' => 'admin/matriculas',
                    'icon' => 'fas fa-fw fa-address-card',
                    'icon_color' => 'info',
                    'can' => 'admin.matriculas.index'
                ],
                [
                    'text' => 'Cursos',
                    'url' => 'admin/cursos',
                    'icon' => 'fas fa-fw fa-book',
                    'can' => 'admin.cursos.index'
                ],
                [
                    'text' => 'Materias',
                    'url' => 'admin/materias',
                    'icon' => 'fas fa-fw fa-book-open',
                    'can' => 'admin.materias.index'
                ],
                [
                    'text' => 'Gestionar Notas',
                    'url' => 'admin/notas',
                    'icon' => 'fas fa-fw fa-clipboard-list',
                    'icon_color' => 'primary',
                    'can' => 'admin.notas.index'
                ],
                [
                    'text' => 'Asistencias',
                    'url' => 'admin/asistencias',
                    'icon' => 'fas fa-fw fa-calendar-check',
                    'icon_color' => 'info',
                    'can' => 'admin.asistencias.index'
                ],
                [
                    'text' => 'Fichas Medicas',
                    'url' => 'admin/fichas-medicas',
                    'icon' => 'fas fa-fw fa-notes-medical',
                    'can' => 'admin.fichas-medicas.index'
                ],
                [
                    'text' => 'Infraestructura',
                    'url' => 'admin/infraestructura',
                    'icon' => 'fas fa-fw fa-school',
                    'icon_color' => 'teal',
                    'can' => 'admin.infraestructura.index'
                ],
                [
                    'text' => 'Gestionar Horarios',
                    'url' => 'admin/horarios',
                    'icon' => 'fas fa-fw fa-calendar-check',
                    'icon_color' => 'success',
                    'can' => 'admin.horarios.index'
                ],
                [
                    'text' => 'Años Escolares',
                    'url' => 'admin/gestiones',
                    'icon' => 'fas fa-fw fa-calendar-alt',
                    'can' => 'admin.gestiones.index'
                ],


                [
                    'text' => 'Horario Profesores',
                    'url' => 'profesor/horario',
                    'icon' => 'fas fa-fw fa-calendar-week',
                    'icon_color' => 'purple',
                    'can' => 'profesor.horario'
                ],
                [
                    'text' => 'Consulta de Notas',
                    'url' => 'apoderado/consulta',
                    'icon' => 'fas fa-fw fa-file-alt',
                    'icon_color' => 'info',
                    'can' => 'apoderado.consulta'
                ],
            ],
        ],

        // ========== FINANCIERO ==========
        [
            'text' => 'FINANCIERO',
            'icon' => 'fas fa-fw fa-dollar-sign',
            'icon_color' => 'warning',
            'submenu' => [
                [
                    'text' => 'Pagos',
                    'url' => 'admin/pagos',
                    'icon' => 'fas fa-fw fa-credit-card',
                    'can' => 'admin.pagos.index'
                ],
                [
                    'text' => 'Mensualidades',
                    'url' => 'admin/mensualidades',
                    'icon' => 'fas fa-fw fa-file-invoice-dollar',
                    'icon_color' => 'success',
                    'can' => 'admin.mensualidades.index'
                ],
                [
                    'text' => 'Becas',
                    'url' => 'admin/becas',
                    'icon' => 'fas fa-fw fa-award',
                    'icon_color' => 'success',
                    'can' => 'admin.becas.index'
                ],
                [
                    'text' => 'Reportes Financieros',
                    'url' => 'admin/reportes-financieros',
                    'icon' => 'fas fa-fw fa-chart-line',
                    'can' => 'admin.reportes-financieros.index'
                ],
            ],
        ],

        // ========== REPORTES GENERALES ==========
        [
            'text' => 'Generar Reportes',
            'url' => 'admin/reportes',
            'icon' => 'fas fa-fw fa-file-invoice',
            'icon_color' => 'success',
            'can' => 'admin.reportes.index'
        ],
        [
            'text' => 'Reportes Estáticos',
            'url' => 'admin/reportes-estaticos',
            'icon' => 'fas fa-fw fa-file-alt',
            'icon_color' => 'info',
            'can' => 'admin.reportes_estaticos.index'
        ],

        // ========== SEGURIDAD ==========
        [
            'text' => 'SEGURIDAD',
            'icon' => 'fas fa-fw fa-shield-alt',
            'icon_color' => 'danger',
            'submenu' => [
                [
                    'text' => 'Bitácora',
                    'url' => 'admin/bitacora',
                    'icon' => 'fas fa-fw fa-history',
                    'can' => 'admin.bitacora.index'
                ],
                [
                    'text' => 'Modulos',
                    'url' => 'admin/modulos',
                    'icon' => 'fas fa-fw fa-th-large',
                    'can' => 'admin.modulos.index'
                ],
                [
                    'text' => 'Funcionalidades',
                    'url' => 'admin/funcionalidades',
                    'icon' => 'fas fa-fw fa-tasks',
                    'can' => 'admin.funcionalidades.index'
                ],
                [
                    'text' => 'Permisos por Rol',
                    'url' => 'admin/permisos',
                    'icon' => 'fas fa-fw fa-user-lock',
                    'can' => 'admin.permisos.index'
                ],
            ],
        ],

        // ========== SEPARADOR ==========
        [
            'type' => 'divider',
            'classes' => 'mt-2 mb-2'
        ],

        // ========== PERFIL Y SALIDA ==========
        [
            'text' => 'Mi Perfil',
            'url' => 'profile',
            'icon' => 'fas fa-fw fa-user-circle',
            'icon_color' => 'info'
        ],
        [
            'text' => 'Cerrar Sesión',
            'url' => '#',
            'icon' => 'fas fa-fw fa-sign-out-alt',
            'icon_color' => 'danger',
            'classes' => 'logout-item',
            'id' => 'sidebar-logout'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
