<?php

ini_set('date.timezone', 'UTC');

define('YAAMP_LOGS', '/var/log');
define('YAAMP_HTDOCS', '/var/web');
define('YAAMP_BIN', '/var/bin');

define('YAAMP_DBHOST', 'localhost');
define('YAAMP_DBNAME', 'yaamp');
define('YAAMP_DBUSER', 'root');
define('YAAMP_DBPASSWORD', 'password');

define('YAAMP_PRODUCTION', true);
define('YAAMP_RENTAL', true);
define('YAAMP_LIMIT_ESTIMATE', false);

define('YAAMP_FEES_SOLO', 0.8);

define('YAAMP_FEES_MINING', 0.8);
define('YAAMP_FEES_EXCHANGE', 2);
define('YAAMP_FEES_RENTING', 2);
define('YAAMP_TXFEE_RENTING_WD', 0.002);
define('YAAMP_PAYMENTS_FREQ', 0.1*60*60);
define('YAAMP_PAYMENTS_MINI', 0.001);

define('YAAMP_ALLOW_EXCHANGE', false);
define('YIIMP_PUBLIC_EXPLORER', true);
define('YIIMP_PUBLIC_BENCHMARK', false);
define('YIIMP_FIAT_ALTERNATIVE', 'EUR'); // USD is main

define('YAAMP_USE_NICEHASH_API', false);

define('YAAMP_BTRMADDRESS', 'BTRM POOL ADDRESS');
define('YAAMP_SITE_URL', 'yiimp.ccminer.org');
define('YAAMP_STRATUM_URL', YAAMP_SITE_URL); // change if your stratum server is on a different host
define('YAAMP_SITE_NAME', 'YiiMP - Bitoreum');
define('YAAMP_ADMIN_EMAIL', 'yiimp@spam.la');
define('YAAMP_ADMIN_IP', ''); // samples: "80.236.118.26,90.234.221.11" or "10.0.0.1/8"
define('YAAMP_ADMIN_WEBCONSOLE', true);
define('YAAMP_CREATE_NEW_COINS', true);
define('YAAMP_NOTIFY_NEW_COINS', false);
define('YAAMP_DEFAULT_ALGO', 'gr');

define('YAAMP_USE_NGINX', false);

// Exchange public keys (private keys are in a separate config file)
define('EXCH_EXBITRON_KEY', '');


// Automatic withdraw to Yiimp BTRM pool wallet if BTRN exchange balance > 3000
define('EXCH_AUTO_WITHDRAW', 3000);

// nicehash keys deposit account & amount to deposit at a time
define('NICEHASH_API_KEY','xxx');
define('NICEHASH_API_ID','xxx');
define('NICEHASH_DEPOSIT','xxx');
define('NICEHASH_DEPOSIT_AMOUNT','0.01');


$cold_wallet_table = array(
	'BonFdNamEzB5q5Po3iHgMbMjKmmQbCDMd8' => 0.10,
);

// Sample fixed pool fees
$configFixedPoolFees = array(
        'zr5' => 2.0,
        'scrypt' => 20.0,
        'sha256' => 5.0,
);

// Sample fixed pool fees solo
$configFixedPoolFeesSolo = array(
		'zr5' => 2.0,
        'scrypt' => 2.0,
        'sha256' => 5.0,
);

// Sample custom stratum ports
$configCustomPorts = array(
//	'x11' => 7000,
);

// mBTRM Coefs per algo (default is 1.0)
$configAlgoNormCoef = array(
//	'x11' => 5.0,
);

