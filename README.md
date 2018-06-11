# Laravel Nextcloud User Management
manage your nextcloud users via laravel

# Setup:
1. Use following command in your terminal to install this library. (Currently the library is in development mode):

    `composer require mansa/simplepay dev-master`

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
];

```

5. Add these params to `.env` (optional):

```sh
NEXTCLOUD_LOGIN=admin
NEXTCLOUD_PASSWORD=12345678

```

#Usage:

```php
    NextcloudApi::createUser($username, $password);
```