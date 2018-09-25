# Build development environment for TecPoster
#project/tecposter

## hosts
/etc/hosts
```
127.0.0.1       localhost
127.0.0.1       php redis db

# The following lines are desirable for IPv6 capable hosts
::1     localhost ip6-localhost ip6-loopback
ff02::1 ip6-allnodes
ff02::2 ip6-allrouters

# hostname loopback address
127.0.1.1       tecposter
```

## PHP
Install
```
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install php-fpm php-cli
or
sudo apt-get install php7.2-fpm php7.2-cli
```

modify `/etc/php/7.2/fpm/pool.d/www.conf`
```
;listen = /run/php/php7.2-fpm.sock
listen = [::]:9000
```

some php extensions
```
sudo apt install php-xml php-mbstring
```


### php composer
[Composer](https://getcomposer.org/download/)


## Redis
Install Redis server using apt
```
sudo add-apt-repository ppa:chris-lea/redis-server
sudo apt update
sudo apt install redis-server
```

Remove old redis service
```
systemctl stop redis_6379.service
systemctl disable redis_6379.service
rm /etc/systemd/system/redis_6379.service
rm /etc/systemd/system/redis_6379.service symlinks that might be related
systemctl daemon-reload
systemctl reset-failed
```

Install php-redis
```
sudo apt install php-redis
```



## Nginx
Install v1.14.x
```
sudo apt install nginx
```

config
```
user www-data;
worker_processes auto;
pid /var/run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

events {
    worker_connections 768;
}

http {

    ##
    # Basic Settings
    ##

    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    # server_tokens off;

    server_names_hash_bucket_size 64;
    # server_name_in_redirect off;

    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    ##
    # SSL Settings
    ##

    ssl_protocols TLSv1 TLSv1.1 TLSv1.2; # Dropping SSLv3, ref: POODLE
    ssl_prefer_server_ciphers on;

    ##
    # Logging Settings
    ##

    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;

    ##
    # Gzip Settings
    ##

    gzip on;

    ##
    # Virtual Host Configs
    ##

    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*.conf;
```

/etc/nginx/sites-available/tecposter.cn.conf

```
server {
    listen    80;
    server_name    tecposter.cn;
    return 301 $scheme://www.tecposter.cn$request_uri;
}

server {
    listen  80;
    server_name user.tecposter.cn;

    index   index.html index.php;
    root    /var/space/tec-user/site/public;

    access_log  /var/space/tec-user/log/access.log.gz combined gzip;
    error_log /var/space/tec-user/log/error.log;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php(/|$) {
        try_files $uri = 404;

        fastcgi_split_path_info ^(.+\.php)(/.*)$;

        include fastcgi.conf;

        fastcgi_connect_timeout 60;
        fastcgi_send_timeout 180;
        fastcgi_read_timeout 180;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;

        fastcgi_index   index.php;
        fastcgi_pass    php:9000;
    }

    location ~ /\.ht {
        deny all;
    }
}
```


## Let's Encrypt
* https://letsencrypt.org/getting-started/
* [Certbot](https://certbot.eff.org/)

```
sudo apt-get update
sudo apt-get install software-properties-common
sudo add-apt-repository ppa:certbot/certbot
sudo apt-get update
sudo apt-get install python-certbot-nginx 
```

```
sudo certbot --nginx certonly -w /var/space/tec-user/site/public -d user.tecposter.cn

-w --webroot-path
-d domains
```


Generate dhparam, may take a little long time
```
sudo openssl dhparam -out /etc/nginx/ssl/dhparam.pem 2048
```

add *ssl_ciphers* in */etc/nginx/nginx.conf*
```
ssl_prefer_server_ciphers on;
# add ssl_ciphers after ssl_prefer_server_ciphers
ssl_ciphers EECDH+CHACHA20:EECDH+AES128:RSA+AES128:EECDH+AES256:RSA+AES256:EECDH+3DES:RSA+3DES:!MD5;
```

## Build Gap Project
prepare
```
sudo apt install php-xml php-mbstring
```

```
composer create-project gap/project tec-user
```

## Start coding
Go to 'tec-user' project dir

### config
Add setting/setting.local.php
```php
<?php
$collection = new \Gap\Config\ConfigCollection();

$collection
    ->set('debug', true)
    ->set('baseDir', realpath(__DIR__ . '/../'))
    ->set('baseHost', 'tecposter.cn')
    ->set('front', [
        'port' => 8787
    ])
    ->set('local', [
        'db' => [
            'host' => 'db',
            'database' => 'tec',
            'username' => 'tec',
            'password' => '123456789'
        ],
        'cache' => [
            'host' => 'redis'
        ],
        'session' => [
            'save_handler' => 'redis',
            'save_path' => 'tcp://redis:6379?database=10',
            'subdomain' => 'user'
        ]
    ]);

return $collection;
```

Edit setting/system.site.php
```php
<?php
$collection = new \Gap\Config\ConfigCollection();

$collection
    ->set('site', [
        'default' => [
            'host' => 'user.%baseHost%',
        ],
        'api' => [
            'host' => 'user-api%baseHost%',
        ],
        'static' => [
            'host' => 'user-static.%baseHost%',
            'dir' => '%baseDir%/site/static',
        ],
    ]);

return $collection;
```

Build App
```
composer gap buildApp 'Tec\User'
```

Build Module
```
composer gap buildModule 'Tec\User\Landing'
```

Edit Router
```php
// app/tec/user/setting/router/landing.php
<?php
$collection = new \Gap\Routing\RouteCollection();

$collection
    ->site('default')
    ->access('public')

    ->get('/', 'home', 'Tec\User\Landing\Ui\HomeUi@front');

return $collection;

```

Build Entity
```
composer gap buildEntity 'Tec\User\Landing\Ui\HomeUi' 
```

Edit UI entity file
```php
<?php
namespace Tec\User\Landing\Ui;

use Gap\Http\Response;

class HomeUi extends UiBase
{
    public function front(): Response
    {
        return new Response('home');
    }
}
```
