<?php


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sql_model
 *
 * @author Usuario
 */
class sqlgeneral_model extends Odbc
{

    function __construct()
    {
    }


    /**
     * permite obtener un listado de productos por codigo o nombre
     */
    function ObtenerProductos($codigopr)
    {
        $query = "SELECT s.sicopr,
        s.sicod_ean,     
        s.sidepr
        FROM spmaematr mm, spdetmatr dm ,siprdcto s
        WHERE mm.consec = dm.conmas
        AND dm.codmatp = '$codigopr'
        AND sicopr[1,13]=codprod[1,13]
        AND sicodmat=0
        AND sigrupo in(4,5)    
        AND sioblser='S'   --si el producto obliga  aque tenga serie      
        AND length(sicopr)=16
        AND  sicoci='A'
        ";

        /*  $query = "
             select
             sicopr,
             sicod_ean,     
             sidepr
             from 
             siprdcto
             where           
             sigrupo in(4,5)    
             and sioblser='S'   --si el producto obliga  aque tenga serie      
             and length(sicopr)=16
             and  sicoci='A'                   
         "; */

        // echo "<pre>".$query."</pre>";

        $this->consultar($query, __FUNCTION__);
        $datosodbc = $this->getDatosOdbc();

        $productos = array();

        while ($reg = $datosodbc->getRegistro()) {

            $cod_prod = trim($reg['sicopr']);
            $cod_ean = trim($reg['sicod_ean']);
            $nom_prod = utf8_encode(trim($reg['sidepr']));
            $datos_prod = $nom_prod . " - (" . $cod_prod . ")";

            if ($cod_ean == null || $cod_ean == '') {
                $cod_ean = " ";
            }

            array_push($productos, array('cod_prod' => $cod_prod, 'cod_ean' => $cod_ean, 'datos_prod' => $datos_prod));
        }

        //print(print_r($productos,true));

        return $productos;
    }

    /**
     * obtiene el producto maestro de un producto detalle
     */
    function ObtenerProducto($codsubpro)
    {
        $query = "
                    select 
                    maes.consec,
                    maes.cod_combo as codpadre,
                    sipr.sidepr as nomprod,
                    sipr.sicod_ean
                    from 
                    s2detprod  det
                    join jst.s2masprod maes                   
                    on det.consec_mascombo=maes.consec
                    join siprdcto sipr 
                    on maes.cod_combo=sipr.sicopr
                    where                     
                    det.codpro='$codsubpro'
                    and det.estado='A'  
                ";

        //echo "<pre>".$query."</pre>";exit();

        $this->consultar($query, __FUNCTION__);
        $datosodbc = $this->getDatosOdbc();

        $productos_padre = array();

        while ($reg = $datosodbc->getRegistro()) {

            $consec = trim($reg['consec']);
            $codpadre = utf8_encode(trim($reg['codpadre']));
            $cod_ean = utf8_encode(trim($reg['sicod_ean']));
            $nom_prod = utf8_encode(trim($reg['nomprod']));
            $datos_prod = $nom_prod . " - (" . $codpadre . ")";


            array_push($productos_padre, array('consec' => $consec, 'cod_ean' => $cod_ean, 'codpadre' => $codpadre, 'datos_prod' => $datos_prod));
        }

        return $productos_padre;
    }


    /**
     * traer la lista de ordenes de produccion  pendientes
     */
    function  getOrdenesProduccion($anio, $mes)
    {
        $buscar_porfecha = true;

        $condicion_fecha = "";

        if ($buscar_porfecha == true) {

            $fechaini_busqueda = $mes . "/" . '01' . '/' . $anio;
            $numero_dias_mes = date('t', strtotime($fechaini_busqueda)); //traemos el nuero de dias del mes
            $fechafin_busqueda = $mes . "/" . $numero_dias_mes . '/' . $anio;

            $condicion_fecha = "and orp.fecprodu>= '$fechaini_busqueda'  and orp.fecprodu<= '$fechafin_busqueda'";
        }

        $query =
            "
            select 
            orp.consec,
            to_char(orp.fecprodu) as fecprodu,
            orp.numdocum,
            orp.descripc,
            orp.codigopr,
            orp.cantidad,
            (
                select 
                max(codbar1.num_serie)
                from 
                spserial_codbar codbar1
                where 
                codbar1.maorpro=orp.consec
            ) as serial_act,
            codbar.codpro,
            codbar.codpadre as codpadreselec,
            sipr.sidepr as nomproinv,
            sipr.sicod_ean,
            codbar.cant_imp,             
            codbar.cant_fal           
            from 
            spmaorpro  orp  
            LEFT OUTER JOIN 
            spserial_codbar codbar  
            on  codbar.maorpro=orp.consec             
            LEFT OUTER JOIN  
            siprdcto as sipr
            on sipr.sicopr=codbar.codpro
            where
            (
                codbar.num_serie=
                            ( 
                                select
                                max(codbar2.num_serie)    
                                from 
                                spserial_codbar codbar2
                                where 
                                codbar2.maorpro=orp.consec    
                            )
                or codbar.num_serie is null
            ) 
            $condicion_fecha           
            order by consec 
        ";


         //echo "<pre>".$query."</pre>";exit();

        $this->consultar($query, __FUNCTION__);
        $datosodbc = $this->getDatosOdbc();
        $ordenes_produccion = [];

        while ($reg = $datosodbc->getRegistro()) {

            $consec = trim($reg['consec']);
            $fecprodu = trim($reg['fecprodu']);
            $numdocum = trim($reg['numdocum']);
            $anio_orden = substr($numdocum, 0, 2);
            $mes_orden = substr($numdocum, 2, 2);
            $consec_generacion = substr($numdocum, 4, 3);

            $descripc = trim($reg['descripc']);

            if ($descripc == null || $descripc == '') {
                $descripc = '';
            }

            $codigopr = trim($reg['codigopr']);
            $codpro = trim($reg['codpro']);
            $codpadreselec = trim($reg['codpadreselec']);
            $nomproinv = trim($reg['nomproinv']);
            $cod_ean = trim($reg['sicod_ean']);
            $cantidad = trim($reg['cantidad']);

            $serial_act = trim($reg['serial_act']);
            if ($serial_act == null) {
                $serial_act = "";
            }

            $cant_imp = trim($reg['cant_imp']);
            $cant_fal = trim($reg['cant_fal']);



            array_push($ordenes_produccion, array(
                'consec' => $consec, 'fecprodu' => $fecprodu, "anio_ord" => $anio_orden, 'numdocum' => $numdocum,
                'mes_ord' => $mes_orden, 'consec_generacion' => $consec_generacion, 'descripc' => $descripc,
                'codpro' => $codpro, 'codpadreselec' => $codpadreselec, 'cod_ean' => $cod_ean, 'nomproinv' => $nomproinv, 'codigopr' => $codigopr,
                'cantidad' => $cantidad, 'serial_act' => $serial_act, 'cant_imp' => $cant_imp, 'cant_fal' => $cant_fal
            ));
        }

        return $ordenes_produccion;
    }

    /**
     * obtiene las series pertenecientes a una orden en autocompletar
     */
    function ObtenerSeriales($consec)
    {

        $query = "
                select 
                consec, 
                num_serie                   
                from 
                spserial_codbar
                where                  
                maorpro='$consec'  
                order by num_serie                               
        ";

        //  print("<pre>".$query."</pre>");

        $this->consultar($query, __FUNCTION__);
        $datosodbc = $this->getDatosOdbc();

        $series = array();

        while ($reg = $datosodbc->getRegistro()) {

            $consec = trim($reg['consec']);
            $num_serie = trim($reg['num_serie']);

            array_push($series, array(
                'consec' => $consec, 'num_serie' => $num_serie
            ));
        }

        return  $series;
    }



    /**
     * mostrar lista de seriales que pertenecen a una orden de produccion
     */
    function ObtenerListaSeriales($consec)
    {
        $query = "
                select 
                maorpro,                 
                codpro,
                cant_reimp,
                num_serie,
                usrcrea, 
                usrmodi,                  
                to_char(feccrea) feccrea,
                horacre,
                to_char(fecmodi) fecmodi,
                horamod
                from 
                spserial_codbar
                where                  
                maorpro='$consec'                 
                order by num_serie                               
            ";

        // print("<pre>".$query."</pre>");

        $this->consultar($query, __FUNCTION__);
        $datosodbc = $this->getDatosOdbc();

        $series = array();

        while ($reg = $datosodbc->getRegistro()) {

            $maorpro = trim($reg['maorpro']);
            $codpro = trim($reg['codpro']);
            $cant_reimp = trim($reg['cant_reimp']);
            $num_serie = trim($reg['num_serie']);
            $usrcrea = trim($reg['usrcrea']);
            $usrmodi = trim($reg['usrmodi']);
            $feccrea = trim($reg['feccrea']);
            $horacre = trim($reg['horacre']);
            $fecmodi = trim($reg['fecmodi']);
            $horamod = trim($reg['horamod']);

            array_push($series, array(
                'maorpro' => $maorpro, 'codpro' => $codpro, 'cant_reimp' => $cant_reimp, 'num_serie' => $num_serie,
                'usrcrea' => $usrcrea, 'usrmodi' => $usrmodi, 'feccrea' => $feccrea, 'horacre' => $horacre,
                'fecmodi' => $fecmodi, 'horamod' => $horamod
            ));
        }

        return  $series;
    }
}
