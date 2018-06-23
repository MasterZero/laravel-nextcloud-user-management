<?php
namespace MasterZero\Nextcloud;

use MasterZero\Nextcloud\Exceptions\XMLParseException;

/**
* class MasterZero\Nextcloud\Response
* statuses for nextcloud response
*/
abstract class Status
{

    /**
     * User list endpoint
     */
    const USERLIST_OK               = 100; // successful

    /**
     * User create endpoint
     */
    const CREATEUSER_OK             = 100; // successful
    const CREATEUSER_INVALID_INPUT  = 101; // invalid input data
    const CREATEUSER_EXIST          = 102; // username already exists
    const CREATEUSER_UNKNOWN        = 103; // unknown error occurred whilst adding the user

    /**
     * User edit endpoint
     */
    const EDITUSER_OK               = 100; // successful
    const EDITUSER_NOT_EXIST        = 101; // user not found
    const EDITUSER_INVALID_INPUT    = 102; // invalid input data

    /**
     * User disable endpoint
     */
    const DISABLEUSER_OK            = 100; // successful
    const DISABLEUSER_FAILURE       = 101; // failure

    /**
     * User enable endpoint
     */
    const ENABLEUSER_OK             = 100; // successful
    const ENABLEUSER_FAILURE        = 101; // failure

    /**
     * errors
     */
    const ERROR_AUTH                = 997; // bad userid/password data. permission deny.


}
