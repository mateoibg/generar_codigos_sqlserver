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
class sqlgenerarcodigos_model extends Odbc
{

    function __construct()
    { }


    /**
     * inserta seriales en la bd 
     */
    function insertarSeriales($maorpro, $codpro,$codpro_padre, $serial, $cantidad, $cant_imp, $can_fal,$num_copias,  $codbar_iniabs, $codbar_finabs, $user_intra)
    {
        $mensaje = "error desconocido";
        $resultado = 1;

        $cantidad_series = $this->comprobarCantidadLineas($maorpro);
        $existe_serial = $this->comprobarSerialExiste($maorpro,  $serial);

        if ($cantidad_series >= $cantidad) {
            $mensaje = "No puede generar mas seriales, ya estan completos";
            $resultado = 1;
        } else {

            if ($existe_serial > 0) {
                $mensaje = "El serial ya existe para esta orden de produccion";
                $resultado = 1;
            } else {

              //  echo  $serial." >= ". $codbar_iniabs. " &&" .$serial." <=".$codbar_finabs;

                if ($serial >= $codbar_iniabs && $serial <= $codbar_finabs) {
                    $query = "INSERT into spserial_codbar
                        (
                            maorpro,codpro,codpadre,num_serie,cant_imp,cant_fal,cant_reimp,num_serie_ini,
                            num_serie_fin,usrcrea,feccrea,horacre
                        )
                        values
                        (
                            '$maorpro','$codpro','$codpro_padre','$serial','$cant_imp','$can_fal','$num_copias','$codbar_iniabs',
                            '$codbar_finabs','$user_intra',
                            CONVERT (date, SYSDATETIME()),GETDATE()
                        )
                    ";

                    $resultado = $this->consultar($query, __FUNCTION__);
                    $datosodbc = $this->getDatosOdbc();

                    if ($resultado == 0) {
                        $mensaje = 'Los seriales se han guardado correctamente';
                    } else {
                        $mensaje = 'Error en la insercion de los seriales';
                        $resultado = 1;
                    }
                } else {
                    $mensaje = 'Los seriales estan por fuera del rango de la orden';
                    $resultado = 1;
                }
            }
        }

        $arrayresultadoguardado['mensaje'] = trim($mensaje);
        $arrayresultadoguardado['resultado'] = trim($resultado);

        return $arrayresultadoguardado;
    }


    /**
     * actulizar reimpresion de seriales en la bd 
     */
    function actualizarSeriales($maorpro, $codpro, $codpro_padre,$serial, $cantidad, $num_copias, $codbar_iniabs, $codbar_finabs, $user_intra)
    {       
        $mensaje = "error desconocido";
        $resultado = 1;

        $existe_serial = $this->comprobarSerialExiste($maorpro,  $serial);

        if ($existe_serial < 0) {
            $mensaje = "El serial no existe para esta orden de produccion";
            $resultado = 1;
        } else {

            if ($serial >= $codbar_iniabs && $serial <= $codbar_finabs) {
                $query = "UPDATE 
                        spserial_codbar 
                        set 
                        codpro='$codpro',
                        codpadre='$codpro_padre',
                        cant_reimp= (cant_reimp+$num_copias),
                        usrmodi='$user_intra',
                        fecmodi=CONVERT (date, SYSDATETIME()),
                        horamod=GETDATE() 
                        where  
                        maorpro='$maorpro'
                        and num_serie='$serial'
                    ";

                  //  echo "<pre>". $query."</pre>",

                $resultado = $this->consultar($query, __FUNCTION__);
                $datosodbc = $this->getDatosOdbc();

                if ($resultado == 0) {
                    $mensaje = 'Las reimpresiones se han actualizado correctamente';
                } else {
                    $mensaje = 'Error en la reimpresion de los seriales';
                    $resultado = 1;
                }
            } else {
                $mensaje = 'Los seriales estan por fuera del rango de la orden';
                $resultado = 1;
            }
        }


        $arrayresultadoguardado['mensaje'] = trim($mensaje);
        $arrayresultadoguardado['resultado'] = trim($resultado);

        return $arrayresultadoguardado;
    }


    /**
     * funcion que permite comprobar la cantidad seriales insertados
     */
    function comprobarCantidadLineas($maorpro)
    {
        $query = "SELECT
                    count(*) cant  
                    from
                    spserial_codbar  codbar    
                    where
                    codbar.maorpro='$maorpro'                                           
                ";

        $this->consultar($query, __FUNCTION__);
        $datosodbc = $this->getDatosOdbc();

        $reg = $datosodbc->getRegistro();

        $cant = trim($reg['cant']);

        return $cant;
    }

    /**
     * metodo que permite comprobar si un un serial ya existe
     */
    function comprobarSerialExiste($maorpro,  $serial)
    {
        $query = "SELECT
                    count(*) cant  
                    from
                    spserial_codbar  codbar    
                    where
                    codbar.maorpro='$maorpro'             
                    and codbar.num_serie='$serial'                                                         
                ";

        $this->consultar($query, __FUNCTION__);
        $datosodbc = $this->getDatosOdbc();

        $reg = $datosodbc->getRegistro();

        $cant = trim($reg['cant']);

        return $cant;
    }
}
