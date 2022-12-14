<?php

// default values if local server config keys are not set (also used to add defines in git)
// do not change them here... set them in your serverconfig.php

if (!defined('YAAMP_PRODUCTION')) define('YAAMP_PRODUCTION', false);
if (!defined('YAAMP_USE_NGINX')) define('YAAMP_USE_NGINX', false);

if (!defined('YAAMP_DBHOST')) define('YAAMP_DBHOST', 'localhost');
if (!defined('YAAMP_DBNAME')) define('YAAMP_DBNAME', 'yaamp');
if (!defined('YAAMP_DBUSER')) define('YAAMP_DBUSER', 'root');
if (!defined('YAAMP_DBPASSWORD')) define('YAAMP_DBPASSWORD', '');

if (!defined('YIIMP_PUBLIC_EXPLORER')) define('YIIMP_PUBLIC_EXPLORER', true);
if (!defined('YIIMP_PUBLIC_BENCHMARK')) define('YIIMP_PUBLIC_BENCHMARK', false);
if (!defined('YIIMP_FIAT_ALTERNATIVE')) define('YIIMP_FIAT_ALTERNATIVE', 'EUR');
if (!defined('YIIMP_KWH_USD_PRICE')) define('YIIMP_KWH_USD_PRICE', 0.25);

if (!defined('YAAMP_FEES_MINING')) define('YAAMP_FEES_MINING', 0.8);
if (!defined('YAAMP_FEES_EXCHANGE')) define('YAAMP_FEES_EXCHANGE', 2);
if (!defined('YAAMP_FEES_RENTING')) define('YAAMP_FEES_RENTING', 2);
if (!defined('YAAMP_TXFEE_RENTING_WD')) define('YAAMP_TXFEE_RENTING_WD', 0.002);
if (!defined('YAAMP_PAYMENTS_FREQ')) define('YAAMP_PAYMENTS_FREQ', 0.1*60*60);
if (!defined('YAAMP_PAYMENTS_MINI')) define('YAAMP_PAYMENTS_MINI', 0.001);

if (!defined('YAAMP_ALLOW_EXCHANGE')) define('YAAMP_ALLOW_EXCHANGE', false);
if (!defined('EXCH_AUTO_WITHDRAW')) define('EXCH_AUTO_WITHDRAW', 9999.9999);

if (!defined('EXCH_EXBITRON_KEY')) define('EXCH_EXBITRON_KEY', '');

if (!defined('YAAMP_BTRMADDRESS')) define('YAAMP_BTRMADDRESS', '');
if (!defined('YAAMP_SITE_URL')) define('YAAMP_SITE_URL', 'localhost');
if (!defined('YAAMP_API_URL')) define('YAAMP_API_URL', YAAMP_SITE_URL);
if (!defined('YAAMP_API_PAYOUTS')) define('YAAMP_API_PAYOUTS', false);
if (!defined('YAAMP_API_PAYOUTS_PERIOD')) define('YAAMP_API_PAYOUTS_PERIOD', 0.1 * 60 * 60);
if (!defined('YAAMP_STRATUM_URL')) define('YAAMP_STRATUM_URL', YAAMP_SITE_URL);
if (!defined('YAAMP_SITE_NAME')) define('YAAMP_SITE_NAME', 'YiiMP - Bitoreum');
if (!defined('YAAMP_DEFAULT_ALGO')) define('YAAMP_DEFAULT_ALGO', 'gr');
if (!defined('YAAMP_ADMIN_EMAIL')) define('YAAMP_ADMIN_EMAIL', 'yiimp@spam.la');
if (!defined('YAAMP_ADMIN_IP')) define('YAAMP_ADMIN_IP', '127.0.0.1');
if (!defined('YAAMP_ADMIN_WEBCONSOLE')) define('YAAMP_ADMIN_WEBCONSOLE', true);

if (!defined('YAAMP_CREATE_NEW_COINS')) define('YAAMP_CREATE_NEW_COINS', true);
if (!defined('YAAMP_NOTIFY_NEW_COINS')) define('YAAMP_NOTIFY_NEW_COINS', false);

if (!defined('YAAMP_LIMIT_ESTIMATE')) define('YAAMP_LIMIT_ESTIMATE', false);
if (!defined('YAAMP_RENTAL')) define('YAAMP_RENTAL', false);
if (!defined('YAAMP_USE_NICEHASH_API')) define('YAAMP_USE_NICEHASH_API', false);

if (!defined('NICEHASH_API_KEY')) define('NICEHASH_API_KEY','');
if (!defined('NICEHASH_API_ID')) define('NICEHASH_API_ID','0000');
if (!defined('NICEHASH_DEPOSIT')) define('NICEHASH_DEPOSIT','');
if (!defined('NICEHASH_DEPOSIT_AMOUNT')) define('NICEHASH_DEPOSIT_AMOUNT','0.01');

// cli stuff
if (!defined('YIIMP_CLI_ALLOW_TXS')) define('YIIMP_CLI_ALLOW_TXS', false);

