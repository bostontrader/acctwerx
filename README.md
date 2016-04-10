An accounting engine that supports multiple currencies.

## Non Discrimination Policy

I consider that the "politically correct" attitude that has been infecting society for some time now to be deeply problematic and hereby reject it. In this document I will use "he", "his", or "him" in cases of indefinite gender. If you think gender is a spectrum not a binary choice, that's fine by me but I'll not modify any software to accommodate you. In fact, I'll not modify this software to accommodate anybody, except via polite request, persuasion, and negotiation.  If you're blind, illiterate, or sub-normal you have my sympathy, but you still can't read this.  If you're a person of whatever color, gender, sexual orientation, potatoness, nationality, ethnicity, religion, or whatever, I don't care and I won't bother you.  But don't nag me about any perceived slight.  People are different, sometimes we like to point fingers and laugh at each other, and sometimes this hurts feelings.  Deal with it. Grow a pair and don't be a pussy about this.

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

##On Categories and Ordering

There are several places where we want to "categorize" the various accounts. Not only
do we want to categorize them, but we also want to deal with them in a particular order.

Some examples:

1. Each account is one of [Assets, Liabilities, Equity, Revenue, Expenses] and these broad categories have a customary position in financial statements.

2. Asset categorization might be further subdivided into Current, Equipment, and Building with them
appearing on the Balance Sheet in this order.

3. Any other random grouping of accounts, statistics of which might appear on a graph.

We have three basic choices in dealing with this:

1. Create additional fields as necessary, in order to handle the various categorizations and orderings that are desired by the user.

2. Create a small number of fields, possibly only one or two, and create some encoding system whereby the categorizations and orderings may be described therein.

3.

 A. Create a single "categories" table that contains an entry for any type of category we might want.  Then associate an account with any number of these tags.
 B. Don't include any specific ordering.  Each view or graph will determine ordering using methods unique to it and information already available.

Choice 1 has the benefit of better fitting with db normalization.  We don't want to lump lots of things into a single field,
so therefore we might need several fields.  It has the drawback of proliferating fields, with all the associated UI, testing, and general user confusion that comes with that.

Choice 2 Violates normalization by lumping things together, but we would need fewer fields.  This is not such an advantage as it seems.  Althought actual quantity of controls in any UI would be slightly reduced, testing would probably be more difficult because of the need to consider the wide range of possible inputs.  User confusion would certainly actually increase as well.  If the user can't figure out what three different categorization fields are, he'll have an even more difficult time figuring out some encoding scheme.

After reflection, it becomes obvious that choices 1 and 2 are dogs (no slur against dogs intended) and choice 3 is the best choice.

More specifically, let's examine specific usages:

1. Create categories for "Assets","Liabilities","Equity","Revenue", and "Expenses."  Every account gets tagged with exactly one of these.  The Balance Sheet and Income Statements verify this and display their information in a hardwired customary order.  The report decidees how to order individual accounts.

2. Create categories for "Current" and "Long-term" (for example.)  Assets deemed "current" or "long-term" get tagged thus.  The financial reports will display current assets before long-term because it;s hardwired to look for these categories and display them in said order.  We can use the same same tags for liabilities. The financial reports should include an implicit category for "uncategorized" to include accounts that are not otherwise categorized.

3. Create categories for "bank" and "short-term note".  Tag certain accounts as one or the other of these.  A graph can operate only on these specific accounts and seperately display each category.  No individual accounts need apply.

When individual accounts are listed, pick some method of ordering them using already existing info.  Perhaps alpha by account title or by descending account balance.

In each case it is the reponsibility of the report or graph to understand the available categories, present them in the proper order, and to ensure that the categorization is plausible. For example, nothing should be tagged as an asset _and_ a liability.