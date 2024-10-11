<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
define('RAIZ_CORE', $_SERVER["DOCUMENT_ROOT"]); //nos estamos devolviendo un nivel en la ruta padre para ingresar a otra aplicacion que es msm


$absolute_path_core = "";

if (DIRECTORY_SEPARATOR == '/') {
  $absolute_path_core = RAIZ_CORE . '/';
} else {
  $absolute_path_core = str_replace('\\', '/', RAIZ_CORE);
}


define('RUTAS_CORE', $absolute_path_core);
//echo RUTAS_CORE;


/* El controlador que se ejecutara por defecto junto con su metodo por defecto */
define('CONTROLADOR_DEFECTO', 'general');
define('ACCION_DEFECTO', 'index');

/*
  todos los proyectos apuntaran a la raiz de la carpeta que contiene el MVC junto con todas las funciones y librerias que se usaran
  y de esa forma reutilizar codigo y ahorrar espacio en disco.
 */
define('RUTA_IMG', 'image_request_app/');
//define('RUTA_MVC', $_SERVER['DOCUMENT_ROOT'] . '/application/');
define('RUTA_MVC', RUTAS_CORE . '/application/');
define('RUTA_RECURSOS', '/application/');
define("INICIAR_SIMULADOR", false);


/* Se deben usar las constantes del archivo que posee las credenciales ODBC Y FTP 'connection2.php' para la conexion a la base de datos.
  Si necesita usar mas de una conexion diferente puede agregar mas constantes */
/*define("ODBC", 'ifxibgdir'); //ifxibgdir ifx10064
define("ODBC_USER", 'informix');
define("ODBC_PASS", 'poseidon');*/

define("ODBC", 'ifx_ibg'); //ifxibgdir
define("ODBC_USER", USER_IBG_SERVER);
define("ODBC_PASS", PASS_IBG_SERVER);


define("ODBCMYSQL", 'intranet'); //ifxibgdir
define('ODBC_USERMYSQL', USER_INTRANET);
define('ODBC_PASSMYSQL', PASS_INTRANET);
define("IP_FTP", "192.168.100.10");
define("PORT_FTP", "3306");
