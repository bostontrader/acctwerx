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

--with-gd

sudo apt-get install libpng-dev Needed for gd

gd is needed to tdraw our pretty graphs.



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

##On Categories

There are several places where we want to "categorize" the various accounts. Not only
do we want to categorize them, but we also want to display them in a particular order.

Some examples:

1. Each account is one of [Assets, Liabilities, Equity, Revenue, Expenses]

2. Asset categorization might be further subdivided into Current, Equipment, and Building with them
appearing on the Balance Sheet in this order.

3. Any other random grouping of accounts, statistics of which might appear on a graph.

We have two basic choices in dealing with this:

1. Create additional fields as necessary, in order to handle the various categorizations and orderings that are desired by the user.

2. Create a small number of fields, possibly only one or two, and create some encoding system whereby the categorizations and orderings may be described.

3. Create a single "categories" table that contains an entry for any type of category we might want.  Then associate an account with any number of these tags.

Choice 1 has the benefit of better fitting with db normalization.  We don't want to lump lots of things into a single field,
so therefore we might need several fields.  It has the drawback of proliferating fields, with all the associated UI, testing, and general user confusion that comes with that.

Choice 2 Violates normalization by lumping things together, but we would need fewer fields.  This is not such an advantage as it seems.  Althought actual quantity of controls in any UI would be slightly reduced, testing would probably be more difficult because of the need to consider the wide range of possible inputs.  User confusion would probably actually increase as well.  If the user can't figure out what three different categorization fields are, he'll have an even more difficult time figuring out some encoding scheme.

After reflection, it becomes obvious that choices 1 and 2 are dogs (no slur against dogs intended) and choice 3 is the best choice.
