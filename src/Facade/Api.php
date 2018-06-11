<?php
namespace MasterZero\Nextcloud\Facade;

use Illuminate\Support\Facades\Facade;


class Api extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'NextcloudApi';
    }
}


?>