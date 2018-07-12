# server {
#   listen  80;
#   server_name www.myapp.com;
#   rewrite ^(.*) http://myapp.com$1 permanent;
# }

server {
    listen 80;
    server_name myapp.com;

    client_max_body_size 108M;

    # access_log /var/www/myapp/logs/access.log;
    # error_log /var/www/myapp/logs/error.log;

    root /var/www/myapp/webroot/;
    index index.php;

#    if (!-e $request_filename) {
#        rewrite ^.*$ /index.php last;
#    }

    location / {
      try_files $uri /index.php?$args;
    }

    # location ~ \.php$ {
    #     fastcgi_pass siep-php-fpm:9000;
    #     fastcgi_index index.php;
    #     fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    #     # fastcgi_param PHP_VALUE "error_log=/var/www/myapp/logs/php_errors.log";
    #     fastcgi_buffers 16 16k;
    #     fastcgi_buffer_size 32k;
    #     include fastcgi_params;
    # }
    location ~ \.php$ {
        set $memcached_key '$request_uri';
        memcached_pass ${MEMCACHED};
        default_type       text/html;
        error_page 404 405 502 = @no_cache;
    }    location @no_cache {
        fastcgi_pass ${UPSTREAM};
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        # fastcgi_param PHP_VALUE "error_log=/var/www/myapp/logs/php_errors.log";
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        include fastcgi_params;
    }

}
