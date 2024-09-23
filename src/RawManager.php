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

            $router->resource('raw/import_selex_milk', 'Svr\Raw\Controllers\FromSelexSheepController');
            $router->resource('raw/import_selex_beef', 'Svr\Raw\Controllers\FromSelexBeefController');
            $router->resource('raw/import_selex_sheep', 'Svr\Raw\Controllers\FromSelexMilkController');
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
            'title'     => 'СВР - отладка импорта',
            'icon'      => 'icon-cogs',
            'uri'       => 'raw',
        ];
//        Если есть пункт меню, удаляем его
        if (Menu::where('uri', 'raw')->exists()) {
            Menu::all()->where('uri', 'raw')->first()->delete();
        }
        $root = Menu::create($root);

        $menus = [
            [
                'title'     => 'Селекс - молоко',
                'icon'      => 'icon-database',
                'uri'       => 'raw/import_selex_milk',
            ],
            [
                'title'     => 'Селекс - мясо',
                'icon'      => 'icon-database',
                'uri'       => 'raw/import_selex_beef',
            ],
            [
                'title'     => 'Селекс - овцы',
                'icon'      => 'icon-database',
                'uri'       => 'raw/import_selex_sheep',
            ],
        ];

        foreach ($menus as $menu) {
            $menu['parent_id'] = $root->id;
            $menu['order'] = $lastOrder++;

            Menu::create($menu);
        }

        parent::createPermission('Exceptions SVR-RAW', 'svr.raw', 'raw/*');
    }
}
