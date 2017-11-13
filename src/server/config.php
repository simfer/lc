<?php
// ** MySQL settings ** //
define('DB_DSN','mysql:host=localhost;dbname=simmaco');
//define('DB_HOST','localhost');
define('DB_USER', 'root');     // Your MySQL username
define('DB_PASSWORD', 'Admin123'); // ...and password
//define('DB_NAME','lcdb');

// Automatically make db connection inside lib
define("AUTOCONNECT",0);

define('ABSPATH', dirname(__FILE__).'/');
define('LOGPATH',ABSPATH . 'logs/');


?>
