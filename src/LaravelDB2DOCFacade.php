<?php

namespace CleaniqueCoders\LaravelDB2DOC;

/*
 * This file is part of laravel-db2doc
 *
 * @license MIT
 * @package laravel-db2doc
 */

use Illuminate\Support\Facades\Facade;

class LaravelDB2DOCFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'LaravelDB2DOC';
    }
}
