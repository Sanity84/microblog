<?php 

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__);
define('ROUTE_DIR', ROOT.DS.'albus'.DS.'Routes'.DS.'*.php'); // may need to be tweaked

require ROOT.DS.'albus'.DS.'Core'.DS.'autoloader.php';

foreach (glob(ROUTE_DIR) as $userRoute) {
    require $userRoute;
}
