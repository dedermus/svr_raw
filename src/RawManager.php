<?php

namespace Svr\Raw;

use OpenAdminCore\Admin\Extension;

class RawManager extends Extension
{
    public $name = 'env-manager';

    public $menu = [
        'title' => 'SVR',
        'path'  => 'SVR',
        'icon'  => 'icon-wrench',
    ];
}
