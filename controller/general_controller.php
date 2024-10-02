
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of datos_primarios
 *
 * @author Usuario
 */
class general_controller extends ControladorBase
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        @session_name('intranet');
        @session_start();

        // $_SESSION['vistaingreso'] = 'prodproveedor';

        $usuariointranet = trim($_SESSION["session_intranet_es"]);
        $loginintranet = trim($_SESSION["session_intranet_login"]);
        $cedulaintranet = trim($_SESSION["session_intranet_cedula"]);
        $conservar = '0-9'; // juego de caracteres a conservar
        $regex = sprintf('~[^%s]++~i', $conservar); // case insensitive
        $cedulaintranet = preg_replace($regex, '', $cedulaintranet);
        $areaintranet = trim($_SESSION["session_intranet_area_dir"]);
        $codcargointranet = trim($_SESSION["session_intranet_cod_cargo_dir"]);
        $direccionip = trim($_SESSION["session_intranet_direccion_ip"]);
        $nivel_permiso = $_SESSION["session_intranet_nivel"];
        $sucursalintranet = $_SESSION['session_intranet_suc_dir'];

        $tipomenu = "";

        if (isset($_REQUEST['tipo'])) {
            $tipomenu = $_REQUEST['tipo'];
        }

        $tipomenuselect = [];

        $tipomenuselect['vistaactual'] = $tipomenu;

        if (isset($tipomenu) || !isset($tipomenu)) { //variable que viene desde menu o si no existe cuando se habre por el navegador por primera vez
            @$tipomenu = trim($_REQUEST['tipo']);
            $menu['menuselec']['general'] = 'active';
        }


        $fecha_hoy = $this->ObtenerFechaHoy();

        $_SESSION['datos_session']['login'] = $loginintranet;
        $_SESSION['datos_session']['fecha_hoy'] = $fecha_hoy;
        $_SESSION['datos_session']['nombre_usuario'] = $usuariointranet;

        $vista = cargarView("general");
        $vista->cargarTemplate("head");
        $vista->asignarVariable($menu);
        $vista->asignarVariable($tipomenuselect);
        $vista->cargarTemplate("menu");
        $vista->dibujar();
        $vista->cargarTemplate("foot");
    }

    /**
     * permite capturar la fecha actual
     */
    function ObtenerFechaHoy()
    {
        $fechahoy = date('m/d/Y');

        return  $fechahoy;
    }

    /**
     * permite obtener un listado de productos por codigo o nombre
     */
    function  ObtenerProductos()
    {
        $codigopr =  $_POST['codigopr'];
        if (isset($codigopr) && is_numeric($codigopr)) {

            $modelo = cargarModel('sqlgeneral');
            $modelo->conectar(ODBC, ODBC_USER, ODBC_PASS, '', '');

            $productos = $modelo->ObtenerProductos($codigopr);

            echo json_encode($productos);
        } else {
            echo null;
        }
    }

    /**
     * obtiene el producto maestro de un producto detalle
     */
    function  ObtenerProducto()
    {
        $modelo = cargarModel('sqlgeneral');
        $modelo->conectar(ODBC, ODBC_USER, ODBC_PASS, '', '');

        $codsubpro = "";

        if (isset($_REQUEST['codsubpro'])) {
            $codsubpro = $_REQUEST['codsubpro'];
        }

        $productos_padre = $modelo->ObtenerProducto($codsubpro);

        echo json_encode($productos_padre);
    }



    /**
     * traer la lista de ordenes de produccion  pendientes
     */

    function consultarOrdenesProduccion()
    {
        $modelo = cargarModel('sqlgeneral');
        $modelo->conectar(ODBC, ODBC_USER, ODBC_PASS, '', '');

        $anio = "";
        $mes = "";

        if (isset($_REQUEST['anio'])) {
            $anio = $_REQUEST['anio'];
        }

        if (isset($_REQUEST['mes'])) {
            $mes = $_REQUEST['mes'];
        }

        $ordenes_produccion = $modelo->getOrdenesProduccion($anio, $mes);

        echo json_encode($ordenes_produccion);
    }

    /**
     * obtiene las series pertenecientes a una orden en autocompletar
     */
    function  consultarSeriales()
    {
        $modelo = cargarModel('sqlgeneral');
        $modelo->conectar(ODBC, ODBC_USER, ODBC_PASS, '', '');

        $consec = "";

        if (isset($_REQUEST['consec'])) {
            $consec = $_REQUEST['consec'];
        }

        $series = $modelo->ObtenerSeriales($consec);

        echo json_encode($series);
    }


    /**
     * mostrar lista de seriales que pertenecen a una orden de produccion
     */
    function  consultarListaSeriales()
    {
        $modelo = cargarModel('sqlgeneral');
        $modelo->conectar(ODBC, ODBC_USER, ODBC_PASS, '', '');

        $consec = "";

        if (isset($_REQUEST['consec'])) {
            $consec = $_REQUEST['consec'];
        }

        $series = $modelo->ObtenerListaSeriales($consec);

        echo json_encode($series);
    }
}
