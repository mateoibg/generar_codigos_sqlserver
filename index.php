<?php


/* apli. hijo de venmader */
$IDMENUACTUAL[8] = true;

/* Configuración global */
require_once 'config/global.php';
require_once 'config/structure.php';
/* Funciones para el controlador frontal */
require_once RUTA_MVC . 'core2/ControladorBase_SQL.php';
require_once RUTA_MVC . 'core2/VistaBase.php';

define('RAIZ_APLICACION', dirname(__FILE__)); // nos ubicamos en la raiz detalle_venta

$absolute_path_applocal = "";

if (DIRECTORY_SEPARATOR == '/') {
    $absolute_path_applocal = RAIZ_APLICACION . '/';
} else {
    $absolute_path_applocal = str_replace('\\', '/', RAIZ_APLICACION) . '/';
}

define('RUTA_IMPRESION', $absolute_path_applocal . "impresion" . "/");
define('RUTAS_SMS', RUTA_IMPRESION );


date_default_timezone_set('America/Bogota');

/* Cargamos controladores y acciones */
if (isset($_REQUEST["controlador"])) {
    $controllerObj = cargarControlador($_REQUEST["controlador"]);
    lanzarAccion($controllerObj);
} else {
    $controllerObj = cargarControlador(CONTROLADOR_DEFECTO);
    lanzarAccion($controllerObj);
}
?>
