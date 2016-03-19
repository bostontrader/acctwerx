An accounting engine that supports multiple currencies.

## Installation

This app was developed using CakePHP 3.2.5 served by LEMP on Ubuntu.

The first step in installation would be to replicate our server installation.  That's fairly easily done by 
examining http://github.com/bostontrader/lemp. Although that serves as the foundation, there is some minor 
customization required, in order to accommodate this app.  More specifically:

When we build PHP we need to add the following configuration options:

--with-pdo-mysql=$STACK_ROOT/mysql
--enable-intl

These options are required by CakePHP.

--with-openssl

Needed by composer. Which means you'll have to have OpenSSL installed on your server.

--enable-mbstring

Needed by CakePHP

Recall that STACK_ROOT is defined by the LEMP stack installation.

cd $STACK_ROOT/html

git clone https://github.com/bostontrader/acctwerx.git

Setup the proper nginx config file.

###Install composer.

RTFM at https://getcomposer.org/download/

Or do this...
$STACK_ROOT/php/bin/php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
$STACK_ROOT/php/bin/php composer-setup.php
php -r "unlink('composer-setup.php');"

The composer installation instructions include the above steps, but also another step to compute a hash
of the installer and compare to a constant.  We don't want to record that step, because it's always changine.
RTFM to get the most up-to-date hash/comparison or live dangerously and do without.

$STACK_ROOT/php/bin/php composer.phar install

###Configure config/app.php
The existing edition is set for a very low-security testing
environment.  You'll need to tighten screws and adapt for production use.
  
###Turn on the Server
sudo rm $STACK_ROOT/mysql.err
sudo $STACK_ROOT/mysql/bin/mysqld_safe --user=batman --log-error=$STACK_ROOT/mysql.err --datadir=$STACK_ROOT/mysql/data --port=3307 &
$STACK_ROOT/php/sbin/php-fpm
$STACK_ROOT/nginx/sbin/nginx &
