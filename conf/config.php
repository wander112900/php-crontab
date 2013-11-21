<?php
define ( 'ROOT_PATH', dirname ( __FILE__ ) . '/../' );
define ( 'DEBUG_MODE', true );
define ( 'PHP_EXEC', '/usr/local/php/bin/php' );
//主库
define('MASTER_DB_HOST', '192.168.1.230');
define('MASTER_DB_PORT', '3306');
define('MASTER_DB_DBNAME', 'test');
define('MASTER_DB_USERNAME', 'root');
define('MASTER_DB_PWD', '123456');
//从库
define('SLAVE_DB_HOST', '192.168.1.230');
define('SLAVE_DB_PORT', '3306');
define('SLAVE_DB_DBNAME', 'online');
define('SLAVE_DB_USERNAME', 'root');
define('SLAVE_DB_PWD', '123456');
?>
