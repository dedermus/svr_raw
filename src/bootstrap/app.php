<?php

namespace Svr\Raw;

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withExceptions(require __DIR__.'/../Exceptions/ExceptionHandler.php')
    ->create();
