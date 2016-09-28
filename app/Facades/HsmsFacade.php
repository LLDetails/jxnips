<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class HSmsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'HSmsService';
    }
}