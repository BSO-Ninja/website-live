<?php
/**
 * Optional config override so you don't need to edit anything between win/lin
 * a php file which returns an array ex.
 * <?php //config_override.php
 * return [
 *    'database' => [
 *    'host' => 'localhost',
 *    'username' => 'test123',
 *    'password' => 'MyLittlePassW0rd'
 *    ]
 * ];
 */

$config_override = [];
if(file_exists("config_override.php")){
	$config_override = include 'config_override.php';
}

# ERROR REPORTING
$showErrors = isset($config_override['debug']) ? $config_override['debug'] : false;
//$showErrors = true;

if(isset($_GET['debug']) || $showErrors){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL & ~E_NOTICE);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

# RECAPTCHA
define("RECAPTCHA_SECRET", isset($config_override['recaptcha']['secret']) ? $config_override['recaptcha']['secret'] : '6LdVmsEUAAAAAOGRhdDzgpdEpqKaKxNvVsunonaN');

# CRON ACCESS
define("CRON_ACCESS", isset($config_override['cron_access']['password']) ? $config_override['cron_access']['password'] : 'run');

# ADMIN
define("DEVELOPMENT", false);

# ADMIN
define("ADMIN_TIME_BEFORE_NEW_IDENTICAL_LOG", ( 60 * 1 )); // 5 minutes

# LANGUAGE
define("LANGUAGE_DEFAULT_ISO_CODE", "EN");

# GUESTBOOK
define("GUESTBOOK_COMMENT_MAX_CHARACTERS", 1000);

# MAINTENANCE
define("MAINTENANCE", false);
define("MAINTENANCE_PASSWORD", "letmein");

# TEMPLATE
define('MASTERPAGE', 'default');

# DATABASE
define('DB_HOST', isset($config_override['database']['host']) ? $config_override['database']['host'] : 'localhost');
define('DB_USER', isset($config_override['database']['user']) ? $config_override['database']['user'] : 'root');
define('DB_PASSWORD', isset($config_override['database']['password']) ? $config_override['database']['password'] : 'Andan2387');
define('DB_DATABASE', isset($config_override['database']['database']) ? $config_override['database']['database'] : 'tibiamate_local');

# EMAIL
define('EMAIL_HOST', isset($config_override['email']['host']) ? $config_override['email']['host'] : '');
define('EMAIL_USERNAME', isset($config_override['email']['username']) ? $config_override['email']['username'] : '');
define('EMAIL_PASSWORD', isset($config_override['email']['password']) ? $config_override['email']['password'] : '');
define('EMAIL_PORT', isset($config_override['email']['port']) ? $config_override['email']['port'] : '');

# USER
define('USER_IP', "{$_SERVER['REMOTE_ADDR']}");

# ACCOUNT
define('LOGIN_EXPIRATION', ( 60 * 15 )) ; // 15 minutes
define('LOGIN_EXPIRATION_ALWAYS', ( 60 * 60 * 24 * 365) ); // 1 year
define('ONLINE_MINUTES', ( 60 * 5 )); // 5 minutes

# PATHS
define('ROOT_PATH', str_replace("/http", "", $_SERVER['DOCUMENT_ROOT']) . "/");
define('INTERNAL_PATH', __DIR__);
define('EXTERNAL_PATH', $_SERVER['DOCUMENT_ROOT']);
define('SERVER_NAME', isset($config_override['server_name']) ? $config_override['server_name'] : 'https://' . $_SERVER['SERVER_NAME']);

# FACEBOOK API
define("FACEBOOK_APP_ID", 0);
define("FACEBOOK_APP_SECRET", 'secret');


# MASTER TEMPLATE
if(isset($_COOKIE['is_app'])) {
  define("MASTER_PATH", INTERNAL_PATH. '/pages/master/master_default_app.php');
}
else {
  define("MASTER_PATH", INTERNAL_PATH. '/pages/master/master_default.php');
}

    require __DIR__ .'/classes/core/DB.php';

\core\DB::init();
