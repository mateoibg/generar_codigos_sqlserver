<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of datosprimarios_view
 *
 * @author Usuario
 */
class generarcodigos_view extends VistaBase
{

    public function dibujar()
    { //echo"entra3";
        ?>


        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 ">
                        <form class="mb-0" id="form_imp_codbar" onsubmit="return false;">
                            <div class="card mb-0">
                                <div class="card-body ">

                                    <div class=" border p-3 mb-2">

                                        <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-1">

                                            <div class="col-md-3 d-flex flex-wrap pt-3">
                                                <div class="mr-2">
                                                    <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Año</label>
                                                </div>
                                                <div>
                                                    <select id="select_anios" name="select_anios" class='mr-2' style="width: 100%" onchange="calcularAniosMeses('select')">

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 d-flex flex-wrap pt-3">
                                                <div class="mr-2">
                                                    <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mes</label>
                                                </div>
                                                <div>
                                                    <select id="select_meses" name="select_meses" class='mr-2' style="width: 100%">

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="p-2 bd-highlight">
                                                <button id="btn_buscar_ordprod" class="btn btn-success" onclick="ObtenerOrdenesProduccion()">Buscar Orden</button>
                                            </div>
                                        </div>
                                    </div>



                                    <div class=" border p-3">

                                        <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-1">

                                            <div class="col-md-4 d-flex flex-wrap mt-2">
                                                <div class="mr-2">
                                                    <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tipo Etiqueta</label>
                                                </div>
                                                <div>
                                                    <select id="select_tipo_codigo" name="select_tipo_codigo" class='mr-2' style="width: 100%">
                                                        <option value="tipo_1">papel (8.5cms x 2.3cms)</option>
                                                        <option value="tipo_2">papel (8cms x 4.3cms) </option>
                                                        <option value="tipo_3">nylon(3cms ancho)</option> <!--selected-->
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="p-2 bd-highlight">
                                                <input type="text" name="inputip" id="inputip" value="192.168.150.10" disabled>
                                            </div>
                                            <div class="p-2 bd-highlight">
                                                <input type="text" name="inputpuerto" id="inputpuerto" value="9100" disabled>
                                            </div>

                                            <div class="p-2 bd-highlight">
                                                <label class="text-danger"><input type="checkbox" id="check_ip" name="check_ip" value="" onclick="editarDireccionIp()">&nbsp;&nbspModificar Ip o Puerto</label>
                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </div>

                        </form>

                        <div id="cardTablaOrdProd" class="card table-responsive" style="display: none;">
                            <div class="card-body">
                                <div id="divTablaOrdProd"> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--</div>-->


            <!--  inicia ventana modal ver factura-->

            <div class="modal fade  " id="modalvisualizarPDF" tabindex="-1" role="dialog" aria-labelledby="VerManualPDF" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">

                        <!-- Modal body -->
                        <div class="modal-body pt-1 pb-0">
                            <form id="formvisualizarPDF" onsubmit="return false;">
                                <div id="divvisualizarPDF">

                                </div>
                            </form>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer pt-1 pb-1">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- modal para  generar e imprimir codigos nuevos modal identificada sin numero final en los campos -->
            <div class="modal fade  " id="modalimprimircodigos" tabindex="-1" role="dialog" aria-labelledby="modalimprimircodigosprod" aria-hidden="true">
                <div class="modal-dialog modal-lg ">
                    <!--modal-xl-->
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header pt-1 pb-1">
                            <h4 class="modal-title">Imprimir Codigos de Barra</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body pt-1 pb-0">
                            <form id="form_editarprodprovee" onsubmit="return false;">

                                <div class="card">
                                    <div class="card-body pb-0">

                                        <div class=" border p-3 mb-2">
                                            <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-auto p-1 ">
                                                <div class="d-flex flex-grow-1 mr-2">
                                                    <input id="input_cod_prod_edit" name="input_cod_prod_edit" class='flex-grow-1 ' />
                                                    <!--select_clear-->
                                                </div>
                                            </div>

                                            <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-auto p-1 ">
                                                <div class="d-flex flex-grow-1 mr-2">
                                                    <select multiple id="select_cod_prodpadre_edit" name="select_cod_prodpadre_edit" class="form-control"  disabled >

                                                    </select>
                                                    <!--select_clear-->
                                                </div>
                                            </div>
                                        </div>

                                        <div class=" border pr-3 pl-3 pt-3 pb-0">
                                            <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-auto p-1 ">
                                                <div class="d-flex mb-auto p-1">
                                                    <div>
                                                        <label for=" input_desc_prod_modal" class=" control-label mr-2">Producto</label>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="input_desc_prod_modal" value="" id="input_desc_prod_modal" class="form-control form-control-sm mr-2 " size="30" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex mb-auto p-1">
                                                    <div>
                                                        <label for=" input_codpr_modal" class=" control-label mr-2">Codigo</label>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="input_codpr_modal" value="" id="input_codpr_modal" class="form-control form-control-sm mr-2 " size="12" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex mb-auto p-1">
                                                    <div>
                                                        <label for="input_ean_prod_modal" class="mr-2">Ean</label>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="input_ean_prod_modal" value="" id="input_ean_prod_modal" size="12" class=" form-control form-control-sm mr-2" disabled />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-auto p-1">

                                                <div class="d-flex  flex-column mb-auto p-2">
                                                    <div>
                                                        <label class="control-label mr-2">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                                    </div>
                                                    <div>
                                                        <label class="control-label mb-3">Cantidad</label>
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_totalcod_modal" value="" id="input_totalcod_modal" size="10" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;impreso</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_impcod_modal" value="" id="input_impcod_modal" size="10" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;pendiente</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_falcod_modal" value="" id="input_falcod_modal" size="10" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Imprimir</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_cantcod_modal" value="0" id="input_cantcod_modal" size="10" class="form-control form-control-sm mb-2" />
                                                    </div>
                                                </div>


                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#copias</label>
                                                    </div>
                                                    <div>
                                                        <select name="selec_numcopias_modal" id="selec_numcopias_modal" class="form-control form-control-sm mb-2">
                                                            <option value="0">0</option>
                                                            <option value="1">1</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div> <!-- end row -->

                                            <div class="form-group row d-flex justify-content-between align-content-between  mb-auto p-1">


                                                <div class="d-flex  flex-column mb-auto p-2">
                                                    <div>
                                                        <label class="control-label mr-2">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                                    </div>
                                                    <div>
                                                        <label class="control-label mb-3">Orden Prod</label>
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Numero</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_numdocum_mod" value="" id="input_numdocum_mod" size="12" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Serial Inicial</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_codbarini_modal" value="" id="input_codbarini_modal" size="12" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Serial Final</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_codbarfin_modal" value="" id="input_codbarfin_modal" size="12" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>
                                            </div> <!-- end row -->
                                        </div>

                                        <div class="mt-2 mb-2">
                                            <button id="btn_generarcodigo" class="btn btn-success btn-block mr-2" title="Imprimir codigo de barras" onclick="GuardarDatosSerial();"><i class='fa fa-print'></i></button>
                                        </div>

                                    </div>
                                </div>

                                <input type="hidden" name="input_consecordprod_modal" id="input_consecordprod_modal" disabled>
                                <input type="hidden" name="input_serial_iniabs" id="input_serial_iniabs" disabled>
                                <input type="hidden" name="input_serial_finabs" id="input_serial_finabs" disabled>

                            </form>

                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer pt-1 pb-1">
                            <!-- <input type="text" name="input_userintra" id="input_userintra" size="12" class=" form-control form-control-sm mb-2" disabled />-->
                            <button type="button" class="btn btn-primary " data-dismiss="modal">Cerrar</button>
                        </div>

                    </div>

                </div>
            </div>

            <!--   fin ventana para generar e imprimir codigos nuevos-->



            <!-- modal para  reimprimir codigos   modal identificada con numero2 al final de los campos -->
            <div class="modal fade  " id="modalreimprimircodigos" tabindex="-1" role="dialog" aria-labelledby="modalimprimircodigosprod" aria-hidden="true">
                <div class="modal-dialog modal-lg ">
                    <!--modal-xl-->
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header pt-1 pb-1">
                            <h4 class="modal-title">Reimprimir Codigos de Barra</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body pt-1 pb-0">
                            <form id="form_reimprimircodigos" onsubmit="return false;">

                                <div class="card">
                                    <div class="card-body pb-0">

                                        <div class=" border p-3 mb-2">
                                            <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-auto p-1 ">
                                                <div class="d-flex flex-grow-1 mr-2">
                                                    <input id="input_cod_prod_modal2" name="input_cod_prod_modal2" class='flex-grow-1' />
                                                </div>
                                            </div>

                                            <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-auto p-1 ">
                                                <div class="d-flex flex-grow-1 mr-2">
                                                    <select multiple id="select_cod_prodpadre_modal2" name="select_cod_prodpadre_modal2" class="form-control" disabled>

                                                    </select>
                                                    <!--select_clear-->
                                                </div>
                                            </div>
                                        </div>


                                        <div class=" border pr-3 pl-3 pt-3 pb-0">
                                            <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-auto p-1 ">
                                                <div class="d-flex mb-auto p-1">
                                                    <div>
                                                        <label for=" input_desc_prod_modal" class=" control-label mr-2">Producto</label>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="input_desc_prod_modal" value="" id="input_desc_prod_modal" class="form-control form-control-sm mr-2 " size="30" disabled />
                                                    </div>
                                                </div>
                                                <div class="d-flex mb-auto p-1">
                                                    <div>
                                                        <label for="input_codpr_modal" class="mr-2">Codigo pr</label>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="input_codpr_modal" value="" id="input_codpr_modal" size="12" class=" form-control form-control-sm mr-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex mb-auto p-1">
                                                    <div>
                                                        <label for="input_ean_prod_modal" class="mr-2">Ean</label>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="input_ean_prod_modal2"  id="input_ean_prod_modal2" size="12" class=" form-control form-control-sm " value="" disabled />
                                                    </div>
                                                </div>

                                            </div>


                                            <div class="form-group row d-flex justify-content-between align-content-between flex-wrap mb-auto p-1">

                                                <div class="d-flex  flex-column mb-auto p-2">
                                                    <div>
                                                        <label class="control-label mr-2">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                                    </div>
                                                    <div>
                                                        <label class="control-label mb-3">Cantidad</label>
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_totalcod_modal2" value="" id="input_totalcod_modal2" size="10" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;impreso</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_impcod_modal2" value="" id="input_impcod_modal2" size="10" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;pendiente</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_falcod_modal2" value="" id="input_falcod_modal2" size="10" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Imprimir</label>
                                                    </div>
                                                    <div>
                                                        <select name="select_tipoimp_modal2" id="select_tipoimp_modal2" class="form-control form-control-sm mb-2">
                                                            <option value="Rango">Rango</option>
                                                            <option value="Unico">Unico</option>
                                                            <option value="Todos">Todos</option>
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#copias</label>
                                                    </div>
                                                    <div>
                                                        <select name="selec_numcopias_modal2" id="selec_numcopias_modal2" class="form-control form-control-sm mb-2">
                                                            <option value="0">0</option>
                                                            <option value="1">1</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> <!-- end row -->


                                            <div class="form-group row d-flex justify-content-between align-content-between  mb-auto p-1">


                                                <div class="d-flex  flex-column mb-auto p-2">
                                                    <div>
                                                        <label class="control-label mr-2">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                                    </div>
                                                    <div>
                                                        <label class="control-label mb-3">Orden Prod</label>
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Numero</label>
                                                    </div>
                                                    <div>
                                                        <input name="input_numdocum_mod" value="" id="input_numdocum_mod" size="12" class=" form-control form-control-sm mb-2" disabled />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Serial Inicial</label>
                                                    </div>
                                                    <div>
                                                        <input id="input_codbarini_modal2" name="input_codbarini_modal2" class='flex-grow-1 select_clear' onKeyPress="return soloNumeros(event)" />
                                                    </div>
                                                </div>

                                                <div class="d-flex  flex-column  mb-auto p-2">
                                                    <div>
                                                        <label class="control-label flex-wrapmb-2 text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Serial Final</label>
                                                    </div>
                                                    <div>
                                                        <input id="input_codbarfin_modal2" name="input_codbarfin_modal2" class='flex-grow-1 select_clear' onKeyPress="return soloNumeros(event)" />
                                                    </div>
                                                </div>
                                            </div> <!-- end row -->
                                        </div>

                                        <div class="mt-2 mb-2">
                                            <button id="btn_reimprimircodigo_modal2" class="btn btn-success btn-block mr-2" title="Imprimir codigo de barras" onclick="ReimprimirDatosSerial();"><i class='fa fa-print'></i></button>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="input_consecordprod_modal2" id="input_consecordprod_modal2" disabled>
                                <input type="hidden" name="input_serial_iniabs_modal2" id="input_serial_iniabs_modal2" disabled>
                                <input type="hidden" name="input_serial_finabs_modal2" id="input_serial_finabs_modal2" disabled>
                            </form>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer pt-1 pb-1">
                            <!-- <input type="text" name="input_userintra2" id="input_userintra2" size="12" class=" form-control form-control-sm mb-2" disabled />-->
                            <button type="button" class="btn btn-primary " data-dismiss="modal">Cerrar</button>
                        </div>


                    </div>
                </div>
            </div>


            <!-- modal para mostrar el detallado de seriales para una orden de produccion  modal identificada con numero3 al final de los campos -->
            <div class="modal fade  " id="modaldetalleseries" tabindex="-1" role="dialog" aria-labelledby="modalimprimircodigosprod" aria-hidden="true">
                <div class="modal-dialog modal-lg ">
                    <!--modal-xl-->
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header pt-1 pb-1">
                            <h4 class="modal-title">Detallle Series Orden Produccion</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body pt-1 pb-0">
                            <form id="form_detalleseriales" onsubmit="return false;">
                                <div id="cardTablaDetSerOrdProd" class="card" style="display: none;">
                                    <div class="card-body">
                                        <div id="divTablaDetSerOrdProd">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer pt-1 pb-1">
                            <!-- <input type="text" name="input_userintra3" id="input_userintra3" size="12" class=" form-control form-control-sm mb-2" disabled />-->
                            <button type="button" class="btn btn-primary " data-dismiss="modal">Cerrar</button>
                        </div>


                    </div>
                </div>
            </div>

    <?php
        }
    }
    ?>