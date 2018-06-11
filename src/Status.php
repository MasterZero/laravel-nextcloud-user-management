<?php
namespace MasterZero\Nextcloud;

use MasterZero\Nextcloud\Exceptions\XMLParseException;


abstract class Status
{

    const USERLIST_OK = 100;


    const CREATEUSER_OK             = 100;
    const CREATEUSER_INVALID_INPUT  = 101;
    const CREATEUSER_EXIST          = 102;
    const CREATEUSER_UNKNOWN        = 103;


}

?>