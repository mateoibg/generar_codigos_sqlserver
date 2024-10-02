var vistaactual = $('#inputvistaactual').val();


if (vistaactual == 'generarcodigos') {

  $(document).ready(function () {
    calcularAniosMeses('inicial');
    ObtenerOrdenesProduccion();
  
    try {
      var tipo_etiqueta = sessionStorage.getItem("tipo_etiqueta");

      //alert(tipo_etiqueta);

      if(tipo_etiqueta==null)
      {
        $("#select_tipo_codigo").val("tipo_1"); 
      }else{
        $("#select_tipo_codigo").val(tipo_etiqueta);
      }         

    } catch (error) {
      $("#select_tipo_codigo").val("tipo_1");
    }  

  });


  /**
   * permite llenar los campos para busqueda por año y les
   */
  function calcularAniosMeses(origen) {
    //variables pra obtener la fecha actual
    var fecha = new Date();
    var anio_actual = fecha.getFullYear();
    var mes_actual = fecha.getMonth() + 1;
    var anio_inicio = 2018; //fecha de inicio de funcionamiento de la aplicacion
    var array_anios = [];
    var array_meses = {
      1: "Enero",
      2: "Febrero",
      3: "Marzo",
      4: "Abril",
      5: "Mayo",
      6: "Junio",
      7: "Julio",
      8: "Agosto",
      9: "Septiembre",
      10: "Octubre",
      11: "Noviembre",
      12: "Diciembre"
    };
    const datos_mes = Object.entries(array_meses);

    if (origen == 'inicial') {

      if (anio_actual > anio_inicio) {
        let anio_anterior = anio_actual - 1;
        array_anios.push(anio_anterior);
      }

      array_anios.push(anio_actual);

      for (let index = 0; index < array_anios.length; index++) {
        const anio = array_anios[index];
        var select_anios = document.getElementById("select_anios"); //llenamos el select de años
        var option = document.createElement("option");
        option.text = anio;
        option.value = anio;
        select_anios.add(option);
      }

      $('#select_anios').val(anio_actual);
    }

    var anio_seleccionado = document.getElementById("select_anios").value;

    var clear_meses = document.getElementById("select_meses");
    clear_meses.options.length = 0;

    for (const [value, text] of datos_mes) { // extraemos los datos de los meses
      var codmes = value;
      var mes = text;

      if (anio_seleccionado == anio_actual) {
        if (codmes <= mes_actual) {
          var select_meses = document.getElementById("select_meses"); //llenamos el select de meses
          var option = document.createElement("option");
          option.text = mes;
          option.value = codmes;
          select_meses.add(option);
        }
      } else {
        var select_meses = document.getElementById("select_meses"); //llenamos el select de meses
        var option = document.createElement("option");
        option.text = mes;
        option.value = codmes;
        select_meses.add(option);
      }
    }

    if (origen == 'inicial') {
      $('#select_meses').val(mes_actual);
    }
  }


  /**
   *  funcion que trae los productos vendidos por un proveedor
   */
  function ObtenerOrdenesProduccion() {

    var anio = $('#select_anios').val();
    var mes = $('#select_meses').val();

    $.LoadingOverlay("show", {
      fade: [-1000, 2000] //aparece en 0 y desaparece  en 5000          
    });

    var inputbuscarTablaOrdProduc = '#inputbuscarTablaOrdProduc';
    var tablaProdProveedor = '#tablaOrdProd';

    var condicion = '';

    $.ajax({
      url: 'index.php',
      type: 'post',
      //  dataType: 'json',
      data: {
        controlador: 'general',
        metodo: 'consultarOrdenesProduccion',
        anio: anio,
        mes: mes
      },
      success: function (data) {

        if (data !== "[]") {
          console.log(data);
          var dataarray = JSON.parse(data);

          var html_tabla = '<input class="form-control border border-success" id="inputbuscarTablaOrdProduc" type="text" placeholder="Filtrar datos Tabla..">';


          html_tabla += '<br>';

          html_tabla += '<table id="tablaOrdProd" class="table table-sm table-bordered js-sort-table">';

          html_tabla += '<thead>';
          html_tabla += '<tr>';
          html_tabla += '<th class="js-sort-number">consec</th>'; //sortTable(0, 'str')"
          html_tabla += '<th class="js-sort-date" >fecprodu</th>';
          html_tabla += '<th class="js-sort-number">numdocum</th>';
          html_tabla += '<th>codigopr</th>';
          html_tabla += '<th>descripc</th>';
          html_tabla += '<th>cantidad</th>';
          html_tabla += '<th>impresa</th>';
          html_tabla += '<th>faltante</th>';
          html_tabla += '<th>ultimo serial</th>';
          html_tabla += '<th>Generar</th>';
          html_tabla += '<th>Reimprimir</th>';
          html_tabla += '<th>Detalle</th>';
          html_tabla += '</th>';
          html_tabla += '</tr>';
          html_tabla += '</thead>';
          html_tabla += '<tbody>';

          for (var i = 0; i < dataarray.length; i++) {

            var consec = dataarray[i].consec;
            var fecprodu = dataarray[i].fecprodu;
            var numdocum = parseInt(dataarray[i].numdocum);
            var descripc = dataarray[i].descripc;
            var codigopr = dataarray[i].codigopr;
            var codpro = dataarray[i].codpro;
            var codpadreselec = dataarray[i].codpadreselec;
            var cod_ean = dataarray[i].cod_ean;

            var nomproinv = dataarray[i].nomproinv;
            var cantidad = parseInt(dataarray[i].cantidad);
            var serial_act = dataarray[i].serial_act;
            var cant_imp = dataarray[i].cant_imp;
            var cant_fal = dataarray[i].cant_fal;
            var estado = 'A';
            var color = "";
            var serial_inicio_absoluto = (numdocum * 1000) + 1; //es el primer serial de todos siempre terminara en 001
            var serial_fin_absoluto = serial_inicio_absoluto + cantidad - 1; //es el ultimo serial de todos siempre sera 001+ cantidad-1

            var serial_actual_temp = serial_act; //se crea esta temporal simplemente para mostrar mostrar vacio en la tabla ppal y no mostrar el serial terminado en 000

            if (serial_act === "" || serial_act === null) {
              serial_actual_temp = numdocum * 1000;
            } else {
              serial_actual_temp = parseInt(serial_actual_temp);
            }

            if (cant_imp === null || cant_imp === "") {
              cant_imp = 0;
            }

            if (cant_fal === null || cant_fal === "") {
              cant_fal = cantidad;
            }

            var datosminimax = calcularMinimoMaximo(serial_actual_temp, cantidad);

            var numiniconsec = datosminimax.minimo; //serial el valor minimo de la generacion actual
            var numfinconsec = datosminimax.maximo; // serial el valor maximo de la generacion actual

            var registroEditar = new RegistroEditar(
              consec,
              fecprodu,
              numdocum,
              codigopr,
              codpro,
              codpadreselec,
              cod_ean,
              nomproinv,
              descripc,
              cantidad,
              serial_actual_temp,
              numiniconsec,
              numfinconsec,
              cant_imp,
              cant_fal,
              estado,
              serial_inicio_absoluto,
              serial_fin_absoluto

            );

            var registro = JSON.stringify(registroEditar);

            html_tabla += '<tr>';
            html_tabla += '<td>' + consec + '</td>';
            html_tabla += '<td>' + fecprodu + '</td>';
            html_tabla += '<td>' + numdocum + '</td>';
            html_tabla += '<td>' + codigopr + '</td>';
            html_tabla += '<td>' + descripc + '</td>';
            html_tabla += '<td>' + cantidad + '</td>';
            html_tabla += '<td>' + cant_imp + '</td>';
            html_tabla += '<td>' + cant_fal + '</td>';


            if (cantidad === parseInt(cant_imp)) {
              html_tabla += '<td class="td_color_success" title="Todos los seriales ya fueron impresos" >' + serial_act + '</td>';
            } else {
              html_tabla += '<td>' + serial_act + '</td>';
            }


            if (serial_act != serial_fin_absoluto) {
              html_tabla += "<td>" + "<button type='button' class='btn btn-sm  btn-outline-primary' id='btneditarprodprov' title='generar nuevas series'  onclick='ModalImprimirCodigos(" + registro + ");' ><i  class='fa fa-plus'></i></button>" + "</td>";
            } else {
              html_tabla += "<td>" + "</td>";
            }


            if (serial_act === "" || serial_act === null) {
              html_tabla += "<td>" + "</td>";
              html_tabla += "<td>" + "</td>";
            } else {
              html_tabla += "<td>" + "<button type='button' class='btn btn-sm  btn-outline-warning' id='btneditarprodprov' title='reimprimir series'  onclick='ModalReimprimirCodigos(" + registro + ");'><i  class='fa fa-print'></i></button>" + "</td>";
              html_tabla += "<td>" + "<button type='button' class='btn btn-sm  btn-outline-secondary' id='btn_detalle_seriales' title='visualizar las series generadas para esta OP'  onclick='ModalDetalleSerieOP(" + consec + ");'><i  class='fa fa-eye ' aria-hidden='true'></i></button>" + "</td>";
            }

            html_tabla += '</tr>';
          }
          html_tabla += '</tbody>';

          html_tabla += '</tfoot>';
          html_tabla += '</table>';

          $('#cardTablaOrdProd').show();
          $('#divTablaOrdProd').html(html_tabla);

          sortTable.init(); //para inicializar el script de sort-table

          BuscarEnTabla(inputbuscarTablaOrdProduc, tablaProdProveedor);


          $.LoadingOverlay("hide");

        } else {
          $('#cardTablaOrdProd').hide();
          $('#divTablaOrdProd').html('');

          $.LoadingOverlay("hide");
          alertify.warning('No se encontraron resultados', 2, null);
        }
      },
      error: function (data) {
        $.LoadingOverlay("hide");
        alertify.error('Error message', 2, null);
      }

    });
  }


  /**
   * permite crear un objeto para enviar datos a las modales
   * @param {*} consec 
   * @param {*} fecprodu 
   * @param {*} numdocum 
   * @param {*} codigopr 
   * @param {*} codpro 
   * @param {*} nomproinv 
   * @param {*} descripc 
   * @param {*} cantidad 
   * @param {*} serial_act 
   * @param {*} numiniconsec 
   * @param {*} numfinconsec 
   * @param {*} cant_imp 
   * @param {*} cant_fal 
   * @param {*} estado 
   * @param {*} serial_inicio_absoluto 
   * @param {*} serial_fin_absoluto 
   */
  function RegistroEditar(
    consec, fecprodu, numdocum, codigopr, codpro, codpadreselec, cod_ean, nomproinv, descripc,
    cantidad, serial_act, numiniconsec, numfinconsec, cant_imp, cant_fal, estado,
    serial_inicio_absoluto, serial_fin_absoluto) {
    this.consec = consec;
    this.fecprodu = fecprodu;
    this.numdocum = numdocum;
    this.codigopr = codigopr;
    this.codpro = codpro;
    this.codpadreselec = codpadreselec;
    this.cod_ean = cod_ean;
    this.nomproinv = nomproinv;
    this.descripc = descripc;
    this.cantidad = cantidad;
    this.serial_act = serial_act;
    this.numiniconsec = numiniconsec,
      this.numfinconsec = numfinconsec,
      this.cant_imp = cant_imp,
      this.cant_fal = cant_fal,
      this.estado = estado;
    this.serial_inicio_absoluto = serial_inicio_absoluto;
    this.serial_fin_absoluto = serial_fin_absoluto;
  }


  /**
   * calcula el maximo y minimo que se generara para imprimir codigos de barras
   * @param {*} serial_actual 
   * @param {*} cantidad 
   */
  function calcularMinimoMaximo(serial_actual, cantidad) {

    var numiniconsec = "";
    var numfinconsec = "";

    for (var i = 1; i <= cantidad; i++) {
      if (i == 1) {
        numiniconsec = serial_actual + 1;
      }
      if (i == cantidad) {
        numfinconsec = serial_actual + cantidad;
      }
    }

    var datos = {
      minimo: numiniconsec,
      maximo: numfinconsec
    }

    return datos;
  }


  /**
   * funcion que permite habilitar la edicion del puerto o la ip
   */
  function editarDireccionIp() {
    var checkBox = document.getElementById("check_ip");
    var campo_ip = document.getElementById("inputip");
    var campo_puerto = document.getElementById("inputpuerto");
    if (checkBox.checked == true) {
      campo_ip.disabled = false;
      campo_puerto.disabled = false;
    } else {
      campo_ip.disabled = true;
      campo_puerto.disabled = true;
    }
  }

  $("#select_tipo_codigo").change(function () {
    var seleccionado = $(this).val();
    sessionStorage.setItem("tipo_etiqueta", seleccionado); 
  });

}