<?php

namespace Svr\Raw;

use OpenAdminCore\Admin\Admin;
use OpenAdminCore\Admin\Auth\Database\Menu;
use OpenAdminCore\Admin\Extension;

class RawManager extends Extension
{


    public $name = 'srv-raw';

    public $menu = [
        'title' => 'SVR',
        'path'  => 'SVR',
        'icon'  => 'icon-wrench',
    ];

    /**
     * Bootstrap this package.
     *
     * @return void
     */
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('SVR', __CLASS__);
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

            $router->resource('svr/raw_milk', 'Svr\Raw\Controllers\FromSelexSheepController');
            $router->resource('svr/raw_beef', 'Svr\Raw\Controllers\FromSelexBeefController');
            $router->resource('svr/raw_sheep', 'Svr\Raw\Controllers\FromSelexMilkController');
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


        $menus = [
            [
                'title'     => 'RAW SHEEP',
                'icon'      => 'icon-database',
                'uri'       => 'svr/raw_sheep',
            ],
            [
                'title'     => 'RAW BEEF',
                'icon'      => 'icon-database',
                'uri'       => 'svr/raw_beef',
            ],
            [
                'title'     => 'RAW MILK',
                'icon'      => 'icon-database',
                'uri'       => 'svr/raw_milk',
            ],
        ];

        foreach ($menus as $menu) {
            $menu['parent_id'] = $root->id;
            $menu['order'] = $lastOrder++;

            Menu::create($menu);
        }

        parent::createPermission('Exceptions SVR-RAW', 'svr.raw', 'svr/*');
    }
}
