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
class general_view extends VistaBase
{

    public function dibujar()
    { //echo"entra3";
        ?>

        <section class="content">
            <div class="container-fluid">

                <div class="row">

                    <div class="col-md-12 ">
                        <form id="form_prodprovee" onsubmit="return false;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mt-2">

                                        <div class="alert alert-info" role="alert">
                                            <h4 class="alert-heading">Seccion</h4>
                                            <p id="p_nombre_usuario"></p>
                                            <hr>
                                            <p class="mb-0">Bienvenido(da) a la aplicacion para generar seriales e impresion de codigos de barras</p>

                                            <a href="resources/manual/index.htm" target="_blank">Ver Manual</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            </div>






    <?php
        }
    }
    ?>