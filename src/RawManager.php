<?php

namespace Svr\Raw;

use OpenAdminCore\Admin\Admin;
use OpenAdminCore\Admin\Auth\Database\Menu;
use OpenAdminCore\Admin\Extension;

class RawManager extends Extension
{

    /**
     * Bootstrap this package.
     *
     * @return void
     */
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('svr-raw', __CLASS__);
    }


    /**
     * Register routes for open-admin.
     *
     * @return void
     */
    public static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */

            $router->resource('svr/raw/milk', 'Svr\Raw\Controllers\FromSelexSheepController');
            $router->resource('svr/raw/beef', 'Svr\Raw\Controllers\FromSelexBeefController');
            $router->resource('svr/raw/sheep', 'Svr\Raw\Controllers\FromSelexMilkController');
        });
    }


    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        $lastOrder = Menu::max('order');

        $root = [
            'parent_id' => 0,
            'order'     => $lastOrder++,
            'title'     => 'SVR',
            'icon'      => 'icon-cogs',
            'uri'       => '',
        ];

        $root = Menu::create($root);

        $sub_root = [
            'parent_id' => $root->id,
            'order'     => $lastOrder++,
            'title'     => 'RAW',
            'icon'      => 'icon-cogs',
            'uri'       => '',
        ];

        $sub_root = Menu::create($sub_root);


        $menus = [
            [
                'title'     => 'SHEEP',
                'icon'      => 'icon-database',
                'uri'       => 'svr/raw/sheep',
            ],
            [
                'title'     => 'BEEF',
                'icon'      => 'icon-database',
                'uri'       => 'svr/raw/beef',
            ],
            [
                'title'     => 'MILK',
                'icon'      => 'icon-database',
                'uri'       => 'svr/raw/milk',
            ],
        ];

        foreach ($menus as $menu) {
            $menu['parent_id'] = $sub_root->id;
            $menu['order'] = $lastOrder++;

            Menu::create($menu);
        }

        parent::createPermission('Exceptions SVR-RAW', 'svr.raw', 'svr/raw/*');
    }
}
