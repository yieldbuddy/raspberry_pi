create database yieldbuddy;
grant all on yieldbuddy.* to root@localhost;
use yieldbuddy;
source /var/www/yieldbuddy/install/yieldbuddy-0.sql;
