# Laravel Nextcloud API User Management
manage your nextcloud users via laravel

# Setup:
1. Use following command in your terminal to install this library. (Currently the library is in development mode):

    `composer require masterzero/nextcloud dev-master`

2. Update the poviders in config/app.php
        
        'providers' => [
            // ...
            MasterZero\Nextcloud\ApiServiceProvider::class,
        ]

3. Update the aliases in config/app.php

        'aliases' => [
            // ...
            'NextcloudApi' => MasterZero\Nextcloud\Facade\Api::class,
        ]

4. Create `config/nextcloud.php` with content:

```php

return [
    'login'=> env('NEXTCLOUD_LOGIN', 'admin'),
    'password'=> env('NEXTCLOUD_PASSWORD', '12345678'),
    'baseUrl'=> env('NEXTCLOUD_BASEURL', 'http://localhost'),
];

```

5. Add these params to `.env` (optional):

```sh
NEXTCLOUD_LOGIN=admin
NEXTCLOUD_PASSWORD=12345678
NEXTCLOUD_BASEURL=http://localhost

```

# Usage:
### create user:
```php
// reqeust to API
$data = NextcloudApi::createUser($username, $password);

// do something with it
if ($data['success']) {

    // do something ...

} else {

    // do something else ...

    echo $data['message'];

}
```

### user list:
```php
// reqeust to API
$data =  NextcloudApi::getUserList();

// do something with it
if ($data['success']) {

    foreach ($data['users'] as $userid) {
        // do something with $userid
    }

} else {
    
    // do something else ...

}

```

### edit user param:
```php
// reqeust to API
$data = NextcloudApi::editUser('rabbit','quota', '200 MB');

if ($data['success']) {

    // do something ...

} else {

    // do something else ...

}
```


### enable/disable user:
```php
// reqeust to API
$data = NextcloudApi::enableUser('bird');
//$data = NextcloudApi::disableUser('turtle');

if ($data['success']) {

    // do something ...

} else {

    // do something else ...

}
```

# exceptions

```php

use MasterZero\Nextcloud\Exceptions\XMLParseException;
use MasterZero\Nextcloud\Exceptions\CurlException;

// ... 

try {
    // reqeust to API
    NextcloudApi::editUser('rabbit','quota', '200 MB');
} catch (XMLParseException $e) {
    // bad nextcloud answer
} catch (CurlException $e) {
    // bad connection
} catch (\Exception $e) {
    // bad something else
}

```


# multi-server usage

```php

use MasterZero\Nextcloud\Api;

// ... 

$api = new Api([
    'baseUrl' => 'http://develop.localhost:3500',
    'login' => 'admin',
    'password' => '12345678',
    'sslVerify' => false,


    // use default value
    // 'apiPath' => 'custom/path/to/api.php', 
    // 'userPath' => '',
    // 'enablePath' => '',
    // 'disablePath' => '',
]);


$api->createUser( 'dummy', 'qwerty');

```