<?php
return [
    // Title
    'title' => 'Personal Finance',
    'title_prefix' => '',
    'title_postfix' => ' - Personal Finance',
    // Logo
    'logo' => '<b>Personal</b>Finance',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Personal Finance',
    // User Menu
    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => true,
    'usermenu_desc' => true,
    'usermenu_profile_url' => false,
    // Layout
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,
    // Classes
    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',
    // Menu Items
    'menu' => [
        [
            'text' => 'Dashboard',
            'url'  => 'dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
        [
            'text' => 'Transactions',
            'url'  => 'transactions',
            'icon' => 'fas fa-fw fa-exchange-alt',
        ],
        [
            'text' => 'Accounts',
            'url'  => 'accounts',
            'icon' => 'fas fa-fw fa-wallet',
        ],
        [
            'text' => 'Budget',
            'url'  => 'budgets',
            'icon' => 'fas fa-fw fa-chart-pie',
        ],
        [
            'text' => 'Categories',
            'url'  => 'categories',
            'icon' => 'fas fa-fw fa-tags',
        ],
        [
            'text' => 'Reports',
            'url'  => 'reports',
            'icon' => 'fas fa-fw fa-chart-bar',
            'submenu' => [
                [
                    'text' => 'Monthly Summary',
                    'url'  => 'reports/monthly',
                    'icon' => 'fas fa-fw fa-calendar-alt',
                ],
                [
                    'text' => 'Expense Analysis',
                    'url'  => 'reports/expenses',
                    'icon' => 'fas fa-fw fa-chart-line',
                ],
                [
                    'text' => 'Income Trends',
                    'url'  => 'reports/income',
                    'icon' => 'fas fa-fw fa-arrow-trend-up',
                ],
            ],
        ],
        [
            'text' => 'Goals',
            'url'  => 'goals',
            'icon' => 'fas fa-fw fa-bullseye',
        ],
        [
            'text' => 'Settings',
            'url'  => 'settings',
            'icon' => 'fas fa-fw fa-cogs',
            'submenu' => [
                [
                    'text' => 'Profile',
                    'url'  => 'settings/profile',
                    'icon' => 'fas fa-fw fa-user',
                ],
                [
                    'text' => 'Preferences',
                    'url'  => 'settings/preferences',
                    'icon' => 'fas fa-fw fa-sliders-h',
                ],
                [
                    'text' => 'Notifications',
                    'url'  => 'settings/notifications',
                    'icon' => 'fas fa-fw fa-bell',
                ],
                [
                    'text' => 'Security',
                    'url'  => 'settings/security',
                    'icon' => 'fas fa-fw fa-shield-alt',
                ],
            ],
        ],
    ],
    // Sidebar
    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,
    // Control Sidebar (Right Sidebar)
    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',
    // URLs
    'use_route_url' => false,
    'dashboard_url' => 'dashboard',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    // Pagination
    'pagination_theme' => 'bootstrap-4',
    // Footer
    'footer_right_text' => 'Built with <strong>Laravel</strong> & <strong>AdminLTE</strong>',
    'footer_left_text' => '<strong>Personal Finance</strong> v1.0.0',
];
