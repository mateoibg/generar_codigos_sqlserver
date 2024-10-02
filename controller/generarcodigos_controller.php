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
class generarcodigos_controller extends ControladorBase
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        @session_name('intranet');
        @session_start();

        $tipomenu = "";

        if (isset($_REQUEST['tipo'])) {
            $tipomenu = trim($_REQUEST['tipo']);
        }

        $tipomenuselect['vistaactual'] = $tipomenu;

        if (isset($tipomenu) || !isset($tipomenu)) { //variable que viene desde menu o si no existe cuando se habre por el navegador por primera vez
            $tipomenu = trim($_REQUEST['tipo']);
            $menu['menuselec']['generarcodigos'] = 'active';
        }

        $vista = cargarView("generarcodigos");
        $vista->cargarTemplate("head");
        $vista->asignarVariable($menu);
        $vista->asignarVariable($tipomenuselect);
        $vista->cargarTemplate("menu");
        $vista->dibujar();
        $vista->cargarTemplate("foot");
    }

    /**
     * inserta seriales en la bd 
     */
    function guardarSeriales()
    {
        //error corregido No tuples available at this result index in C:\application\core\Odbc.php on line 106

        $modelo = cargarModel('sqlgenerarcodigos');
        $modelo->conectar(ODBC, ODBC_USER, ODBC_PASS, '', '');

        $seriales = [];

        if (isset($_REQUEST['seriales'])) {
            $seriales = $_REQUEST['seriales'];
        }

        $flag = 0;

        $modelo->autocommit();

        foreach ($seriales as $key => $value) {
            $maorpro =    $value['maorpro'];
            $codpro =  $value['codpro'];
            $codpro_padre = $value['codpro_padre'];
            $serial  =  $value['serial'];
            $cantidad = $value['cantidad'];
            $cant_imp  =  $value['cant_imp'];
            $can_fal  =  $value['can_falt'];
            $codbar_iniabs  =  $value['codbar_iniabs'];
            $codbar_finabs  =  $value['codbar_finabs'];
            $num_copias =  $value['num_copias'];
            $user_intra  =  $value['user_intra'];

            $respuesta__insert_series = $modelo->insertarSeriales(
                $maorpro,
                $codpro,
                $codpro_padre,
                $serial,
                $cantidad,
                $cant_imp,
                $can_fal,
                $num_copias + 1,
                $codbar_iniabs,
                $codbar_finabs,
                $user_intra
            );

            $mensaje = $respuesta__insert_series['mensaje'];
            $resultado = $respuesta__insert_series['resultado'];

            if ($resultado != 0) {
                $flag += 1;
                break;
            } else {
                $flag += $resultado;
            }
        }

        $resultado = 0;

        if ($flag == 0) {

            $modelo->commit();

            $respuesta_generarcod = $this->GenerarCodigos($seriales);

            $mensaje = $respuesta_generarcod['mensaje'];
            $resultado += $respuesta_generarcod['resultado'];
        } else {
            $modelo->rollback();
        }

        $arrayresultadoguardado['mensaje'] = trim($mensaje);
        $arrayresultadoguardado['resultado'] = trim($resultado);
        echo json_encode($arrayresultadoguardado);

        $modelo->close();
    }


    /**
     * inserta seriales en la bd 
     */
    function reimprimirSeriales()
    {
        //error corregido No tuples available at this result index in C:\application\core\Odbc.php on line 106

        $modelo = cargarModel('sqlgenerarcodigos');
        $modelo->conectar(ODBC, ODBC_USER, ODBC_PASS, '', '');

        $seriales = [];

        if (isset($_REQUEST['seriales'])) {
            $seriales = $_REQUEST['seriales'];
        }



        $flag = 0;

        $modelo->autocommit();

        foreach ($seriales as $key => $value) {
            $maorpro =    $value['maorpro'];
            $codpro =  $value['codpro'];
            $codpro_padre = $value['codpro_padre'];
            $serial  =  $value['serial'];
            $cantidad = $value['cantidad'];
            $codbar_iniabs  =  $value['codbar_iniabs'];
            $codbar_finabs  =  $value['codbar_finabs'];
            $num_copias =  $value['num_copias'];
            $user_intra  =  $value['user_intra'];

            $respuesta__insert_series = $modelo->actualizarSeriales(
                $maorpro,
                $codpro,
                $codpro_padre,
                $serial,
                $cantidad,
                $num_copias + 1,
                $codbar_iniabs,
                $codbar_finabs,
                $user_intra
            );


            $mensaje = $respuesta__insert_series['mensaje'];
            $resultado = $respuesta__insert_series['resultado'];

            if ($resultado != 0) {
                $flag += 1;
                break;
            } else {
                $flag += $resultado;
            }
        }

        $resultado = 0;

        if ($flag == 0) {
            $modelo->commit();
            $respuesta_generarcod = $this->GenerarCodigos($seriales);
            $mensaje = $respuesta_generarcod['mensaje'];
            $resultado = $respuesta_generarcod['resultado'];
        } else {
            $modelo->rollback();
        }

        $arrayresultadoguardado['mensaje'] = trim($mensaje);
        $arrayresultadoguardado['resultado'] = trim($resultado);
        echo json_encode($arrayresultadoguardado);

        $modelo->close();
    }


    /**
     *  genera el codigo de barras
     */
    function GenerarCodigos($seriales)
    {

        //  print(print_r($seriales,true));

        $respuesta_impcodigos = [];
        $tipo_mensaje = 0;

        foreach ($seriales as $key => $value) {
            $ip =  $value['ip'];
            $puerto = $value['puerto'];
            $nombre_prod = $value['nombre_prod'];
            $tipo_codigo = $value['tipo_codigo'];
            $codpro =  $value['codpro'];
            $cod_ean = $value['cod_ean'];
            $serial  =  $value['serial'];
            $num_copias =  $value['num_copias'];
            $codigo_barras = "";

            for ($i = 0; $i <= $num_copias; $i++) {

                switch ($tipo_codigo) {

                    case "tipo_1":
                        $codigo_barras  = "^XA"

                        ."^PR7"   //para colocar la velocidad de la impresora   
                        ."~SD10"   //~ SD  ^MD10  para colocar el contraste de impresion , se hace por medio de calor 

                            //logo tamaño echo con pain 3d y conertido en http://labelary.com/viewer.html
                            . "^FO75,5^GFA,1786,1786,38,J01FF,I01F03E,I07I01C,001C7FFC6,0033JF18,0047JFCC,019KFE6,033LFB,067LFD8,04MFEC,09NF4,1BNF2,120E007801B,360E003I0D01F7E7E7F87C780FFC07F8IF7FC7FC07F800FF01FC1FC7F3FEFFC03FC00FF,2E0E003I0D81F7E7E7F87C780FFE0FFCIF7FCFFE0FFC01FF83FE1FCFE3FEFFC07FE00FF,6E0E003I0C81F7E7E7F87C780IF1FFCIF3FCIF1FFC03FFC7FF1FCFF3FEFFC0FFE00FF,4E0E183060E81F3E7E7FC7E780FDF1F3EIF7FCFDF1F7E03E7C7DF1FCFF3FEFFC0FBE00FF,5E0E183060EC1F3E7E7FC7E780FDF1F3E1F83E0FCF1F3E03E7CFDF9FCFF3E00FC0FBE00FF,5E0E183060E41F3E7C7FC7E780FDF1F3E0F83E0FCF1F3E03E7CFDF9FEFF3E00F80FBE01FF8,DF0E183060F41F3E7CFFC7F780F9F1F3E0F87E0FCF1F3E03E7CFDF9FEFF3E01F80F8001FF8,9E0E183061F41F3E7CFBC7F780FDF1F3E0F83E0FDF1F3E03E7CFDF9FEFF3E01F80FC001F78,9E0E00307FF41F3F7CFBC7FF80IF1F3E0F83FC7FF1F3E03E00FDF9JF3FC1F00FF001F78,BF0E00707FF41F1F7CFBC7FF80FFC1F3E0F87FCFFE1F3E03E00FDF9JF3FE1F007F801E78,BF0E007040F41F1F7CFBE7FF80FFE1F3E0F87FCFFE1F3E03EFCFDF9FFDF3FE3F003FC01E78,BF0E003040F41F1FFCFBE7FF80IF1F3E0F87FCFDF1F3E03EFCFDF9FFDF3FE3E001FE01E7C,9F0E083040F41F1FF8F3E7FF80FDF9F3E0F83E0FCF1F3E03EFCFDF9FFDF3E03EI07E03E7C,9F0E1C3040F41F1FF8FBE7FF80FDF9F3E0F83E0FCF1F3E03E7CFDF9F7DF3E07EI03F03F7C,DF0E183060F41F1FF9FFE7DF80FDF9F3E0F87E0FCF1F3E03E7CFDF9F7DF3E07C00F9F03FFC,5F0E183060E41F1FF9FFE7DF80FDF9F3E0F87E07CF1F3E03E7CFDF9F7DF3E07C00F9F03FFC,5F0E0C3060EC1F0FF9FFE7DF80F9F9F3E0F83E0FCF1F3E03E7CFDF9E7DF3E0FC00F9F03FFC,4F0E183060E81F0FF9F3F7CF80IF9F3E0F83FEFCF1F3E03E7C7DF1E79F3FEFFC0F9F7BE7CF,6F0E001I0C81F0FF9F1F7CF80IF9FFC0F83FEFCF1FFC03FFC7FF1F79F3FEFFC0FFE7BE7EF,2F06003I0D81F0FF1F1F7CF80IF0FFC0F87FE7CF0FFC01FFC3FF1E79E3FEFFC07FE7BE7EF,370E003I0901F0FF1F1F7CF80FFE07F80F87FEFCF07F800F9C1FC1F39F3FEFFC03FC7FE3EF,130E003801B,1BNF2,09MFE4,04MFEC,067LFD8,033LFB,019KFE6,00C7JFCC,0033JF38,001C7FF86,I07I038,J0F03E,J01FE,^FS"

                            //nombre de la empresa
                            // . "^CF0,25" // letra por defecto, tamaño
                            // . "^FO140,22^FDIVAN BOTERO GOMEZ S.A^FS"

                            //nombre del producto con caracter internacional utf8
                            . "^CI28"  //caracter utf8
                            . "^CF0,15" // letra por defecto, tamaño
                            . "^FO70,57" //posicion
                            . "^FB300,2,,C"  //para colocar texto en un bloque (label), el dos es el numero maximo de lineas
                            . "^FD" . $nombre_prod . "^FS"

                            //tamaño que tendra la letra de aqui en adelante
                            . "^CF0,8"

                            //titulos de cada codigo de barras
                            . "^FO490,10^FDEAN^FS"
                            . "^FO190,90^FDSKU^FS"
                            . "^FO480,90^FDSERIE^FS"

                            //pintamos el cuadro
                            // . "^FO,40^GB440,0,3^FS" //pinta el linea horizonta2 intermedia, empieza  en la posicion 40 (Y) y la baja 0 (alto para que sea una linea horizontal), el ancho es 440 
                            //. "^FO263,90^GB0,90,3^FS" //pinta la linea vertical intermedia , empieza  en la posicion 90 (Y) y la baja 92 (alto), el ancho es 0 (para que sea una linea vertical)
                            // . "^FO,90^GB440,90,3^FS"  //pinta el cuadro de abajo completo , empieza  en la posicion 90 (Y) y baja 90 (alto), con 440 de ancho


                            //codigo barras bar CODE EAN 13  para el codigo internacional del producto                          

                            . "^BY2,2.0,121^FS"
                            . "^FO420,23^BEN,40,Y,N^FD" . $cod_ean . "^FS" // EAN-13 770=colombia 7086=productor ibg, 72336 producto (CAJ IBG HELICONIA ESPEJ DE PIE CHOC) 8 dv calculado

                            //codigo barras bar code 128 para el producto (SKU) 
                            . "^BY2,2.0,121^FS"
                            . "^FO70,105^BCN,40,Y,N,N^FD>;" . $codpro . "^FS"

                            //codigo barras bar code 128 para serial de productos de madera
                            . "^BY2,2.0,121^FS"
                            . "^FO420,105^BCN,40,Y,N,N^FD>;" . $serial . "^FS" //tipo c o tipo a solo numeros, debe ser un numero par de digitos
                            . "^XZ";
                        break;

                    case "tipo_2":
                        $codigo_barras = "^XA"

                        ."^PR7"   //para colocar la velocidad de la impresora   
                        ."~SD10"   //~ SD  ^MD10  para colocar el contraste de impresion , se hace por medio de calor               
                   

                            . "^FO160,20^GFA,1786,1786,38,J01FF,I01F03E,I07I01C,001C7FFC6,0033JF18,0047JFCC,019KFE6,033LFB,067LFD8,04MFEC,09NF4,1BNF2,120E007801B,360E003I0D01F7E7E7F87C780FFC07F8IF7FC7FC07F800FF01FC1FC7F3FEFFC03FC00FF,2E0E003I0D81F7E7E7F87C780FFE0FFCIF7FCFFE0FFC01FF83FE1FCFE3FEFFC07FE00FF,6E0E003I0C81F7E7E7F87C780IF1FFCIF3FCIF1FFC03FFC7FF1FCFF3FEFFC0FFE00FF,4E0E183060E81F3E7E7FC7E780FDF1F3EIF7FCFDF1F7E03E7C7DF1FCFF3FEFFC0FBE00FF,5E0E183060EC1F3E7E7FC7E780FDF1F3E1F83E0FCF1F3E03E7CFDF9FCFF3E00FC0FBE00FF,5E0E183060E41F3E7C7FC7E780FDF1F3E0F83E0FCF1F3E03E7CFDF9FEFF3E00F80FBE01FF8,DF0E183060F41F3E7CFFC7F780F9F1F3E0F87E0FCF1F3E03E7CFDF9FEFF3E01F80F8001FF8,9E0E183061F41F3E7CFBC7F780FDF1F3E0F83E0FDF1F3E03E7CFDF9FEFF3E01F80FC001F78,9E0E00307FF41F3F7CFBC7FF80IF1F3E0F83FC7FF1F3E03E00FDF9JF3FC1F00FF001F78,BF0E00707FF41F1F7CFBC7FF80FFC1F3E0F87FCFFE1F3E03E00FDF9JF3FE1F007F801E78,BF0E007040F41F1F7CFBE7FF80FFE1F3E0F87FCFFE1F3E03EFCFDF9FFDF3FE3F003FC01E78,BF0E003040F41F1FFCFBE7FF80IF1F3E0F87FCFDF1F3E03EFCFDF9FFDF3FE3E001FE01E7C,9F0E083040F41F1FF8F3E7FF80FDF9F3E0F83E0FCF1F3E03EFCFDF9FFDF3E03EI07E03E7C,9F0E1C3040F41F1FF8FBE7FF80FDF9F3E0F83E0FCF1F3E03E7CFDF9F7DF3E07EI03F03F7C,DF0E183060F41F1FF9FFE7DF80FDF9F3E0F87E0FCF1F3E03E7CFDF9F7DF3E07C00F9F03FFC,5F0E183060E41F1FF9FFE7DF80FDF9F3E0F87E07CF1F3E03E7CFDF9F7DF3E07C00F9F03FFC,5F0E0C3060EC1F0FF9FFE7DF80F9F9F3E0F83E0FCF1F3E03E7CFDF9E7DF3E0FC00F9F03FFC,4F0E183060E81F0FF9F3F7CF80IF9F3E0F83FEFCF1F3E03E7C7DF1E79F3FEFFC0F9F7BE7CF,6F0E001I0C81F0FF9F1F7CF80IF9FFC0F83FEFCF1FFC03FFC7FF1F79F3FEFFC0FFE7BE7EF,2F06003I0D81F0FF1F1F7CF80IF0FFC0F87FE7CF0FFC01FFC3FF1E79E3FEFFC07FE7BE7EF,370E003I0901F0FF1F1F7CF80FFE07F80F87FEFCF07F800F9C1FC1F39F3FEFFC03FC7FE3EF,130E003801B,1BNF2,09MFE4,04MFEC,067LFD8,033LFB,019KFE6,00C7JFCC,0033JF38,001C7FF86,I07I038,J0F03E,J01FE,^FS"


                            //  . "^CF0,30" //CF stablece la fuente predeterminada utilizada en su impresora, EL 30 ES EL TAMAÑO
                            //  . "^FO200,25^FDIVAN BOTERO GOMEZ S.A^FS"


                            //nombre del producto con caracter internacional utf8
                            . "^CI28"  //caracter utf8
                            . "^CF0,18" // letra por defecto, tamaño
                            . "^FO140,75" //posicion
                            . "^FB350,2,,C"  //para colocar texto en un bloque (label), el dos es el numero maximo de lineas
                            . "^FD" . $nombre_prod . "^FS"


                            //tamaño que tendra la letra de aqui en adelante
                            . "^CF0,8"


                            . "^FO170,150^FDSKU^FS"
                            . "^FO190,220^FDEAN^FS"
                            . "^FO430,220^FDSERIE^FS"


                            //pintamos los cuadros
                            // . "^FO,150^GB435,0,3^FS" //pinta el linea horizonta intermedia, empieza  en la posicion 150 (Y) y la baja 0 (alto para que sea una linea horizontal), el ancho es 435 
                            // . "^FO220,150^GB0,130,3^FS" //pinta la linea vertical intermedia , empieza  en la posicion 150 (Y) y la baja 130 (alto), el ancho es 0 (para que sea una linea vertical)
                            // . "^FO,40^GB435,240,3^FS"  //pinta el cuadro de abajo completo , empieza  en la posicion 40 (Y) y baja 240 (alto), con 435 de ancho

                            //codigo barras bar code 128 para el producto (SKU) 
                            . "^BY2,2.0,126^FS"
                            . "^FO200,120^BCN,60,Y,N,N^FD>;" . $codpro . "^FS"

                            //codigo barras bar CODE EAN 13  para el codigo internacional del producto                          

                            . "^BY2,2.0,126^FS"
                            . "^FO110,240^BEN,60,Y,N^FD" . $cod_ean . "^FS" // EAN-13 770=colombia 7086=productor ibg, 72336 producto (CAJ IBG HELICONIA ESPEJ DE PIE CHOC) 8 dv calculado

                            //codigo barras bar code 128 para serial de productos de madera
                            . "^BY2,2.0,126^FS"
                            . "^FO360,240^BCN,60,Y,N,N^FD>;" . $serial . "^FS" //tipo c o tipo a solo numeros, debe ser un numero par de digitos
                            . "^XZ";
                        break;

                    case "tipo_3": //solo para papel continuo o nylon
                        $codigo_barras = "^XA"

                            ."^LL700"  
                            ."^PR5"   //para colocar la velocidad de la impresora   
                            ."~SD20"   //~ SD  ^MD10  para colocar el contraste de impresion , se hace por medio de calor               
                       
                            //logo tamaño echo con pain 3d y covertido en http://labelary.com/viewer.
                            
                            //. "^FO0,95^GFA,1800,1800,6,M03C,:::,M01C,L0FFC,J03IFC,I07JFC,:I07JF8,I07F01C,I07FC1C,I07JFC,::J03IFC,L07FC,N04,M03C,:::,L03E,I03E0FF8,I03E1FFC,I07E3FFC,I07E7FFC,I07C7E1C,I070FC1C,I07BF9FC,I07FF9FC,I07FF1FC,I03FE1F8,I03FC1F,,:::::I07C003C,I07F803C,I07FF03C,I07IF3C,I07JFC,:I079IFC,I0781FFC,I07803FC,I078007C,,I078383C,I078783C,::I07JFC,::::,:I07JFC,::::I07FE,I07IFC,I03JFC,K07FFC,J03IFC,I07JF8,I07FFC,I07IF0C,I07JFC,:::,:J07FFC,I01JF8,I03JF8,I07JFC,::I07I01C,I07JFC,::I03JF8,I01JF,J07FFC,,:I01F9FFC,I03F9FFC,I07F9FFC,I07F9FF8,:I0701C1C,I07I01C,I07JFC,::I03JF8,I01JF,,::::::J0IFE,I03JF8,I07JFC,::I078001C,I07I01C,I07JFC,::I03JF8,I01JF,,:J010228,I01FCFFC,I03JFC,I07JFC,:I07CF8,I0707,I07JFC,::::I076BF74,,M03C,I078783C,::I07JFC,::::I02D342C,,I078,::I07JFC,::::I07C,I078,::,J0IFE,I03JF8,I07JFC,::I07I01C,:I07JFC,::I03JF8,I01JF,,:L07F,I01FDFF8,I03FDFFC,I07JFC,::I078783C,I07B7FBC,I07JFC,::::,::::::I07JFC,:::K07FFC,J01FFC,J0FFE,I07JFC,::::,M03C,K01FFC,J0JFC,I07JFC,:I07IFE,I07F01C,I07FFBC,I07JFC,::J01IFC,L01FC,I078,I07FF8,I07JF,I07JFC,::L0FFC,K07FFC,I07JFC,::I07IF8,I07FC,I07,,I07JFC,::::,::::J07FFC,I03C0078,I0E1FF0E,0018IFE3,0067JF8C,00CKFE6,01B80E0033,037006001D8,06F006001EC,0DF006001F6,09F006001F2,13F0FE1E1FB,17F0IFE1FD,27FK01FC82FFK01FE84FFK01FEC5FFK01FE45FF8J03FF45PF4DLFEIF69FF803001FF29FFK01FF2::9FFJ041FF29FF0F87E1FF6DFF0F87A1FF45FFK01FF4:4FFK01FE46FFK01FEC27NFE837NFD813LF7F9,1BFK01FB,09FK01F2,04FK01E4,067K01C8,03300JF98,019LF3,0047JFC6,0033JF98,001C7FFC7,I070381C,I01F01F,J01FF,^FS"

                            //."^LS10"
                            ."^FO0,95^GFA,1800,1800,6,M03C,:::,M01C,L0FFC,J03IFC,I07JFC,:I07JF8,I07F01C,I07FC1C,I07JFC,::J03IFC,L07FC,N04,M03C,:::,L03E,I03E0FF8,I03E1FFC,I07E3FFC,I07E7FFC,I07C7E1C,I070FC1C,I07BF9FC,I07FF9FC,I07FF1FC,I03FE1F8,I03FC1F,,:::::I07C003C,I07F803C,I07FF03C,I07IF3C,I07JFC,:I079IFC,I0781FFC,I07803FC,I078007C,,I078383C,I078783C,::I07JFC,::::,:I07JFC,::::I07FE,I07IFC,I03JFC,K07FFC,J03IFC,I07JF8,I07FFC,I07IF0C,I07JFC,:::,:J07FFC,I01JF8,I03JF8,I07JFC,::I07I01C,I07JFC,::I03JF8,I01JF,J07FFC,,:I01F9FFC,I03F9FFC,I07F9FFC,I07F9FF8,:I0701C1C,I07I01C,I07JFC,::I03JF8,I01JF,,::::::J0IFE,I03JF8,I07JFC,::I078001C,I07I01C,I07JFC,::I03JF8,I01JF,,:J010228,I01FCFFC,I03JFC,I07JFC,:I07CF8,I0707,I07JFC,::::I076BF74,,M03C,I078783C,::I07JFC,::::I02D342C,,I078,::I07JFC,::::I07C,I078,::,J0IFE,I03JF8,I07JFC,::I07I01C,:I07JFC,::I03JF8,I01JF,,:L07F,I01FDFF8,I03FDFFC,I07JFC,::I078783C,I07B7FBC,I07JFC,::::,::::::I07JFC,:::K07FFC,J01FFC,J0FFE,I07JFC,::::,M03C,K01FFC,J0JFC,I07JFC,:I07IFE,I07F01C,I07FFBC,I07JFC,::J01IFC,L01FC,I078,I07FF8,I07JF,I07JFC,::L0FFC,K07FFC,I07JFC,::I07IF8,I07FC,I07,,I07JFC,::::,::::J07FFC,I03C0078,I0E1FF0E,0018IFE3,0067JF8C,00CKFE6,01B80E0033,037006001D8,06F006001EC,0DF006001F6,09F006001F2,13F0FE1E1FB,17F0IFE1FD,27FK01FC82FFK01FE84FFK01FEC5FFK01FE45FF8J03FF45PF4DLFEIF69FF803001FF29FFK01FF2::9FFJ041FF29FF0F87E1FF6DFF0F87A1FF45FFK01FF4:4FFK01FE46FFK01FEC27NFE837NFD813LF7F9,1BFK01FB,09FK01F2,04FK01E4,067K01C8,03300JF98,019LF3,0047JFC6,0033JF98,001C7FFC7,I070381C,I01F01F,J01FF,^FS"

                            ."^FWB" //rota la impresion en 270 grados de  todos los campos que no tiene una rotacion propia en sus propiedades

                            //nombre de la empresa
                            // . "^CF0,25" // letra por defecto, tamaño
                            // . "^FO140,22^FDIVAN BOTERO GOMEZ S.A^FS"

                            //nombre del producto con caracter internacional utf8
                            . "^CI28"  //caracter utf8
                            . "^CF0,15" // letra por defecto, tamaño
                            . "^FO60,280" //posicion
                            . "^FB170,3,,C"  //para colocar texto en un bloque (label), el dos es el numero maximo de lineas
                            . "^FD" .$nombre_prod . "^FS"  //

                            //tamaño que tendra la letra de aqui en adelante
                            . "^CF0,8"

                            //titulos de cada codigo de barras  //
                            . "^FO45,120^FDEAN^FS"
                            . "^FO125,360^FDSKU^FS"
                            . "^FO125,120^FDSERIE^FS"

                            //pintamos el cuadro
                            // . "^FO,40^GB440,0,3^FS" //pinta el linea horizonta2 intermedia, empieza  en la posicion 40 (Y) y la baja 0 (alto para que sea una linea horizontal), el ancho es 440 
                            //. "^FO263,90^GB0,90,3^FS" //pinta la linea vertical intermedia , empieza  en la posicion 90 (Y) y la baja 92 (alto), el ancho es 0 (para que sea una linea vertical)
                            // . "^FO,90^GB440,90,3^FS"  //pinta el cuadro de abajo completo , empieza  en la posicion 90 (Y) y baja 90 (alto), con 440 de ancho

                            . "^FO0,620^GB180,0,3^FS"

                            //codigo barras bar CODE EAN 13  para el codigo internacional del producto                          

                            . "^BY2,2.0,121^FS"
                            . "^FO55,25^BEB,40,Y,N^FD" . $cod_ean . "^FS" // EAN-13 770=colombia 7086=productor ibg, 72336 producto (CAJ IBG HELICONIA ESPEJ DE PIE CHOC) 8 dv calculado
                           

                            //codigo barras bar code 128 para el producto (SKU) 
                            . "^BY2,2.0,121^FS"
                            . "^FO135,260^BCB,40,Y,N,N^FD>;" . $codpro . "^FS"


                            //codigo barras bar code 128 para serial de productos de madera
                            . "^BY2,2.0,121^FS"
                            . "^FO135,25^BCB,40,Y,N,N^FD>;" . $serial . "^FS" //tipo c o tipo a solo numeros, debe ser un numero par de digitos
                           
                            . "^XZ";
                        break;
                    default:
                        $codigo_barras = "^XA"

                            //logo tamaño echo con pain 3d y convertido en http://labelary.com/viewer.html
                            . "^FO75,5^GFA,1786,1786,38,J01FF,I01F03E,I07I01C,001C7FFC6,0033JF18,0047JFCC,019KFE6,033LFB,067LFD8,04MFEC,09NF4,1BNF2,120E007801B,360E003I0D01F7E7E7F87C780FFC07F8IF7FC7FC07F800FF01FC1FC7F3FEFFC03FC00FF,2E0E003I0D81F7E7E7F87C780FFE0FFCIF7FCFFE0FFC01FF83FE1FCFE3FEFFC07FE00FF,6E0E003I0C81F7E7E7F87C780IF1FFCIF3FCIF1FFC03FFC7FF1FCFF3FEFFC0FFE00FF,4E0E183060E81F3E7E7FC7E780FDF1F3EIF7FCFDF1F7E03E7C7DF1FCFF3FEFFC0FBE00FF,5E0E183060EC1F3E7E7FC7E780FDF1F3E1F83E0FCF1F3E03E7CFDF9FCFF3E00FC0FBE00FF,5E0E183060E41F3E7C7FC7E780FDF1F3E0F83E0FCF1F3E03E7CFDF9FEFF3E00F80FBE01FF8,DF0E183060F41F3E7CFFC7F780F9F1F3E0F87E0FCF1F3E03E7CFDF9FEFF3E01F80F8001FF8,9E0E183061F41F3E7CFBC7F780FDF1F3E0F83E0FDF1F3E03E7CFDF9FEFF3E01F80FC001F78,9E0E00307FF41F3F7CFBC7FF80IF1F3E0F83FC7FF1F3E03E00FDF9JF3FC1F00FF001F78,BF0E00707FF41F1F7CFBC7FF80FFC1F3E0F87FCFFE1F3E03E00FDF9JF3FE1F007F801E78,BF0E007040F41F1F7CFBE7FF80FFE1F3E0F87FCFFE1F3E03EFCFDF9FFDF3FE3F003FC01E78,BF0E003040F41F1FFCFBE7FF80IF1F3E0F87FCFDF1F3E03EFCFDF9FFDF3FE3E001FE01E7C,9F0E083040F41F1FF8F3E7FF80FDF9F3E0F83E0FCF1F3E03EFCFDF9FFDF3E03EI07E03E7C,9F0E1C3040F41F1FF8FBE7FF80FDF9F3E0F83E0FCF1F3E03E7CFDF9F7DF3E07EI03F03F7C,DF0E183060F41F1FF9FFE7DF80FDF9F3E0F87E0FCF1F3E03E7CFDF9F7DF3E07C00F9F03FFC,5F0E183060E41F1FF9FFE7DF80FDF9F3E0F87E07CF1F3E03E7CFDF9F7DF3E07C00F9F03FFC,5F0E0C3060EC1F0FF9FFE7DF80F9F9F3E0F83E0FCF1F3E03E7CFDF9E7DF3E0FC00F9F03FFC,4F0E183060E81F0FF9F3F7CF80IF9F3E0F83FEFCF1F3E03E7C7DF1E79F3FEFFC0F9F7BE7CF,6F0E001I0C81F0FF9F1F7CF80IF9FFC0F83FEFCF1FFC03FFC7FF1F79F3FEFFC0FFE7BE7EF,2F06003I0D81F0FF1F1F7CF80IF0FFC0F87FE7CF0FFC01FFC3FF1E79E3FEFFC07FE7BE7EF,370E003I0901F0FF1F1F7CF80FFE07F80F87FEFCF07F800F9C1FC1F39F3FEFFC03FC7FE3EF,130E003801B,1BNF2,09MFE4,04MFEC,067LFD8,033LFB,019KFE6,00C7JFCC,0033JF38,001C7FF86,I07I038,J0F03E,J01FE,^FS"

                            //nombre de la empresa
                            // . "^CF0,25" // letra por defecto, tamaño
                            // . "^FO140,22^FDIVAN BOTERO GOMEZ S.A^FS"

                            //nombre del producto con caracter internacional utf8
                            . "^CI28"  //caracter utf8
                            . "^CF0,15" // letra por defecto, tamaño
                            . "^FO70,57" //posicion
                            . "^FB300,2,,C"  //para colocar texto en un bloque (label), el dos es el numero maximo de lineas
                            . "^FD" . $nombre_prod . "^FS"

                            //tamaño que tendra la letra de aqui en adelante
                            . "^CF0,8"

                            //titulos de cada codigo de barras
                            . "^FO490,10^FDEAN^FS"
                            . "^FO190,90^FDSKU^FS"
                            . "^FO480,90^FDSERIE^FS"

                            //pintamos el cuadro
                            // . "^FO,40^GB440,0,3^FS" //pinta el linea horizonta2 intermedia, empieza  en la posicion 40 (Y) y la baja 0 (alto para que sea una linea horizontal), el ancho es 440 
                            //. "^FO263,90^GB0,90,3^FS" //pinta la linea vertical intermedia , empieza  en la posicion 90 (Y) y la baja 92 (alto), el ancho es 0 (para que sea una linea vertical)
                            // . "^FO,90^GB440,90,3^FS"  //pinta el cuadro de abajo completo , empieza  en la posicion 90 (Y) y baja 90 (alto), con 440 de ancho


                            //codigo barras bar CODE EAN 13  para el codigo internacional del producto                          

                            . "^BY2,2.0,121^FS"
                            . "^FO420,23^BEN,40,Y,N^FD" . $cod_ean . "^FS" // EAN-13 770=colombia 7086=productor ibg, 72336 producto (CAJ IBG HELICONIA ESPEJ DE PIE CHOC) 8 dv calculado

                            //codigo barras bar code 128 para el producto (SKU) 
                            . "^BY2,2.0,121^FS"
                            . "^FO70,105^BCN,40,Y,N,N^FD>;" . $codpro . "^FS"

                            //codigo barras bar code 128 para serial de productos de madera
                            . "^BY2,2.0,121^FS"
                            . "^FO420,105^BCN,40,Y,N,N^FD>;" . $serial . "^FS" //tipo c o tipo a solo numeros, debe ser un numero par de digitos
                            . "^XZ";
                        break;
                }


                $respuesta_impcodigos = $this->imprimirCodigos($codigo_barras, $ip, $puerto);

                $tipo_mensaje = $respuesta_impcodigos['resultado'];

                if ($tipo_mensaje != 0) {
                    break;
                }
            }
            if ($tipo_mensaje != 0) {
                break;
            }
        }

        return  $respuesta_impcodigos;
    }


    /**
     * imprime codigo zpl en impresora ip zebra 
     */
    function imprimirCodigos($codigo_barras, $ip, $puerto)
    {

        $resultado = array();
        $mensaje = "";
        $resultado = 1;

        if ($ip != null && $ip != "" && $puerto != null && $puerto != "") {

            if ($codigo_barras == null ||  $codigo_barras == "") {
                $mensaje = "No hay datos para imprimir";
                $resultado = 1;
            } else {

                $conexion = @pfsockopen($ip, $puerto, $errno, $errstr, 20);

                if (!$conexion) {

                    $mensaje = "Error en la conexion o la impresora esta apagada";
                    $resultado = 1;
                } else {
                    fputs($conexion, $codigo_barras);
                    fclose($conexion);

                    $mensaje = "impresion correcta";
                    $resultado = 0;
                }
            }
        } else {
            $mensaje = "Error en la ip o el puerto";
            $resultado = 1;
        }

        $array_resultado['mensaje'] = $mensaje;
        $array_resultado['resultado'] = $resultado;

        return $array_resultado;
    }
}
