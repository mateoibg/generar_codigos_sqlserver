var vistaactual = $('#inputvistaactual').val();


if (vistaactual == 'generarcodigos') {

  var listaProdPadres = new Array();
  var lista_seriales = new Array();


  /**
   * traer la lista de productos
   * @param {*} input 
   * @param {*} origen 
   */
  function cargarListaProductos(input, origen, codigopr) {

    $.ajax({
      url: 'index.php',
      type: 'post',
      dataType: 'json',
      async: false,
      data: {
        controlador: 'general',
        metodo: 'ObtenerProductos',
        codigopr: codigopr
      },
      success: function (data) {
        if (data.length > 0) {
          generarListaProductos(data, input, origen);
        } else {
          alertify.warning('No se encontraron productos con obligación de serie', 5, null);
        }
      },
      error: function (data) {
        alertify.error('No se puedo cargar la lista de productos', 2, null);
      }
    });
  }


  /**
   * generar lista de productos para el input
   * @param {*} data 
   * @param {*} input 
   * @param {*} origen 
   */
  function generarListaProductos(data, input, origen) {

    // listaProd = new Array();
    var listaProd = new Array();
    var i = 0;

    for (var valor in data) {
      var cod_prod = data[valor].cod_prod;
      var cod_ean = data[valor].cod_ean;
      var nom_prod = data[valor].datos_prod.trim();

      if (cod_ean == null || cod_ean == '') {
        cod_ean = '';
      }

      //listaProd[cod_prod] = nom_prod;

      listaProd[i] = {
        value: cod_prod,
        label: nom_prod,
        label2: cod_ean
      };

      i++;
    }

    AutoCompletarProductosModal(input, origen, listaProd);
  }

  /**
   * autocompletar el input
   * @param {*} input 
   * @param {*} origen 
   */
  function AutoCompletarProductosModal(input, origen, listaProd) {


    $(input).autocomplete({
      minLength: 2,
      source: function (request, response) {
        var results = $.ui.autocomplete.filter(listaProd, request.term);
        response(results.slice(0, 10));
      },
      focus: function (event, ui) {
        event.preventDefault();
        $(this).val(ui.item.label);
      },
      select: function (event, ui) {
        // prevent autocomplete from updating the textbox
        event.preventDefault();
        // manually update the textbox and hidden field
        $(this).val(ui.item.label);

        $(this).attr("value", ui.item.value);

        $(this).attr("data-codean", ui.item.label2);

        console.log(this);


        let id_input_ean = ""; //es el input que contendra el codigo ean en cada modal
        let id_input_codpro = ""; // es el del input qu contiene el codigo del sub producto o producto hijo
        let select = ""; //es el select (la lista) que contendra los padres de cada subproducto
        let modal = ""; //sera la ventana modal de origen

        if (origen == '#modalimprimircodigos') { //modal generar e imprimir seriales
          id_input_ean = document.getElementById('input_ean_prod_modal');
          id_input_codpro = document.getElementById('input_cod_prod_edit');
          select = 'select_cod_prodpadre_edit';
          modal = '#modalimprimircodigos';
        }

        if (origen == '#modalreimprimircodigos') { //modal generar e imprimir seriales
          id_input_ean = document.getElementById('input_ean_prod_modal2');
          id_input_codpro = document.getElementById('input_cod_prod_modal2');
          select = 'select_cod_prodpadre_modal2';
          modal = '#modalimprimircodigos';
        }
        id_input_ean.textContent = ''; // se limpia el contenido del input codigo EAN

        var codsubpro = ui.item.value; // es el codigo del subproducto actual seleccionado
        var label_actual = ui.item.label; //obtenemos el nombre del option actual seleccionado
        //var cod_ean_subprod = ui.item.label2; //es el codigo ean del subproducto(seleccionado)     

        cargarListaProductosPadre(select, codsubpro, id_input_codpro, id_input_ean, origen);


      },
      appendTo: origen
    });

    input

  }



  /**
   * traer la lista de productos
   * @param {*} input 
   * @param {*} origen 
   */
  function cargarListaProductosPadre(select, codsubpro, id_input_codpro, id_input_ean, origen, codpadreselec = '') {

    var select_padres = document.getElementById(select);
    select_padres.options.length = 0; //limpiamos el select
    let cod_ean_subprod = id_input_codpro.getAttribute('data-codean');

    $.ajax({
      url: 'index.php',
      type: 'post',
      dataType: 'json',
      async: false,
      data: {
        controlador: 'general',
        metodo: 'ObtenerProducto',
        codsubpro: codsubpro
      },
      success: function (data) {

        // id_input_ean.value = '';

        /* if (data.length > 0) //si tiene al menos tiene un hijo
         {*/
        for (let index = 0; index < data.length; index++) { //llenamos el select de padres
          let cod_padre = data[index].codpadre;
          let dato_padre = data[index].datos_prod;
          var cod_ean = data[index].cod_ean;
          let id = "padre_" + index;

          if (cod_ean == '' || cod_ean == null) {
            cod_ean = '';
          }


          var option = document.createElement("option");
          option.text = dato_padre;
          option.value = cod_padre;
          option.setAttribute('data-codean', cod_ean);
          option.setAttribute('id', id);

          if (origen == '#modalreimprimircodigos' && codpadreselec.trim() == cod_padre.trim()) { //entra cuando se busca para poder seleccionar el producto padre con que se imprimio el serial
            option.setAttribute("selected", "true"); //seleccionamos el padre dueño del ean             
            //id_input_ean.value = cod_ean;                        
          }

          select_padres.add(option)

          let opcion_actual = document.getElementById(id);

          opcion_actual.addEventListener('click', (event) => { //agregamos el evento al option damos click en cualquiera de ellos
            let cod_ean_actual = opcion_actual.dataset.codean;
            // id_input_ean.value = cod_ean_actual;             
          });
          id_input_ean.value = cod_ean_subprod;

        }
        /* } else {
           let cod_ean_subprod = id_input_codpro.getAttribute('data-codean');
           id_input_ean.value = cod_ean_subprod;
         }*/
      },
      error: function (data) {
        alertify.error('No se puedo cargar la lista de productos', 2, null);
      }
    });
  }



  /**
   * obtener la lista de seriales
   * @param {*} input 
   * @param {*} origen 
   * @param {*} consec 
   * @param {*} tipo_imp 
   */
  function cargarListaSeriales(input, origen, consec, tipo_imp) {

    $.ajax({
      url: 'index.php',
      type: 'post',
      dataType: 'json',
      async: false,
      data: {
        controlador: 'general',
        metodo: 'consultarSeriales',
        consec: consec
      },
      success: function (data) {
        generarListaSeriales(data, input, origen, consec, tipo_imp);
      },
      error: function (data) {
        alertify.error('No se puedo cargar la lista de seriales', 2, null);
      }
    });
  }


  /**
   * generar la lista de seriales para llenar el input
   * @param {*} data 
   * @param {*} input 
   * @param {*} origen 
   */
  function generarListaSeriales(data, input, origen) {

    listaProd = new Array();
    lista_seriales = new Array();
    var i = 0;

    for (var valor in data) {
      var consec = data[valor].consec;
      var num_serie = data[valor].num_serie;

      listaProd[consec] = num_serie;

      lista_seriales[i] = {
        value: consec,
        label: num_serie
      };

      i++;
    }
    AutoCompletarSerialesModal(input, origen);
  }


  /**
   * autocompleta el input de los seriales 
   * @param {*} input 
   * @param {*} origen 
   */
  function AutoCompletarSerialesModal(input, origen) {

    $(input).autocomplete({
      minLength: 2,
      source: function (request, response) {
        var results = $.ui.autocomplete.filter(lista_seriales, request.term);
        response(results.slice(0, 10));
      },
      focus: function (event, ui) {
        event.preventDefault();
        $(this).val(ui.item.label);
      },
      select: function (event, ui) {

        // prevent autocomplete from updating the textbox
        event.preventDefault();
        // manually update the textbox and hidden field
        $(this).val(ui.item.label);

        $(this).attr("value", ui.item.value);
        //$("#tcliente" + nro).blur();

        seleccionarSerial(event, ui, input);
      },
      // change: showLabel,
      appendTo: origen
    });
  }


  /**
   * funcion que permite imprimir codigos  desde una modal
   * @param {*} registro 
   */
  function ModalImprimirCodigos(registro) {

    $('#btn_generarcodigo').show();

    $('#modalimprimircodigos').modal({ //evitar que se cierre la modal cuando se da click en la parte gris
      backdrop: 'static',
      keyboard: false
    });

    $("#modalimprimircodigos").modal(); //accion para abrir la modal

    $("#modalimprimircodigos").on('shown.bs.modal', function () { //lo que se hara despues de que la modal sea visible al usuario

      var input = '#input_cod_prod_edit'; //modal generar e imprrmir seriales
      var modal = '#modalimprimircodigos';

      var codigopr = registro.codigopr;
      cargarListaProductos(input, modal, codigopr);

      var fecha_hoy = $('#input_fecha_hoy').val();
      //var loginintranet = $('#input_loginintranet').val();
      document.getElementById("btn_generarcodigo").disabled = true;


      var consec = registro.consec;
      var fecprodu = registro.fecprodu;
      var numdocum = registro.numdocum;
      var descripc = registro.descripc;
      var cantidad = registro.cantidad;
      var numiniconsec = registro.numiniconsec;
      var numfinconsec = registro.numfinconsec;
      var cant_imp = registro.cant_imp;
      var cant_fal = registro.cant_fal;
      var cant_reimp = registro.cant_reimp;
      var serial_iniabs = registro.serial_inicio_absoluto;
      var serial_finabs = registro.serial_fin_absoluto;

      var modal = $(this);

      modal.find('.modal-body #input_desc_prod_modal').val(descripc);
      modal.find('.modal-body #input_codpr_modal').val(codigopr);
      modal.find('.modal-body #input_numdocum_mod').val(numdocum);
      modal.find('.modal-body #input_totalcod_modal').val(cantidad); //
      modal.find('.modal-body #input_impcod_modal').val(cant_imp);
      modal.find('.modal-body #input_falcod_modal').val(cant_fal);
      modal.find('.modal-body #input_reimpcod_modal').val(cant_reimp);
      modal.find('.modal-body #input_codbarini_modal').val(numiniconsec);
      modal.find('.modal-body #input_consecordprod_modal').val(consec);
      //modal.find('.modal-footer #input_userintra').val(loginintranet);
      modal.find('.modal-body #input_serial_iniabs').val(serial_iniabs);
      modal.find('.modal-body #input_serial_finabs').val(serial_finabs);

    });


    $('#modalimprimircodigos').on('hidden.bs.modal', function () { //lo que se hara cuando la modal se cierre

      $(this).find("input,textarea,select").val('').end();
      $(".input_clear").css('background', '#ffffff'); //para quitar el valor rojo de input que pusimos con jquery validate
      $('.select_clear').val('').trigger("change");
      $('#selec_numcopias_modal').val('0').end();
      $('#input_cantcod_modal').val('0').end();

      $('#btn_generarcodigo').off('click'); // detenemos la propagacion del evento para que no muestro varios alertify cada vez que se habre la ventana


      location.reload();


    });
  }


  /**
   * mostrar lista de seriales que pertenecen a una orden de produccion
   * @param {*} consec 
   */
  function consultarListaSeriales(consec) {

    var inputbuscarTablaOrdProduc = '#inputDetSerTablaOrdProduc';
    var tablaProdProveedor = '#tablaDetSerOrdProd';

    $.ajax({
      url: 'index.php',
      type: 'post',
      //  dataType: 'json',
      data: {
        controlador: 'general',
        metodo: 'consultarListaSeriales',
        consec: consec,
      },
      success: function (data) {

        var dataarray = JSON.parse(data);

        if (Object.keys(dataarray).length !== 0) {

          var html_tabla = '<input class="form-control border border-success" id="inputDetSerTablaOrdProduc" type="text" placeholder="Filtrar datos Tabla..">';
          html_tabla += '<br>';

          html_tabla += '<table id="tablaDetSerOrdProd" class="table table-sm table-bordered js-sort-table">';

          html_tabla += '<thead>';
          html_tabla += '<tr>';
          html_tabla += '<th class="js-sort-number">consec</th>'; //sortTable(0, 'str')"
          html_tabla += '<th class="js-sort-number" >maorpro</th>';
          html_tabla += '<th class="js-sort-number">codpro</th>';
          html_tabla += '<th>serie</th>';
          html_tabla += '<th>impresos</th>';
          html_tabla += '</th>';
          html_tabla += '</tr>';
          html_tabla += '</thead>';
          html_tabla += '<tbody>';

          //[{"consec":"15","codpro":"9019001","num_serie":"1909073001","usrcrea":"lagiraldo","usrmodi":"lagiraldo","feccrea":"2019-09-11","horacre":"14:55:14","fecmodi":"2019-09-11","horamod":"14:56:10"},

          for (var i = 0; i < dataarray.length; i++) {

            var consec = i + 1;
            var maorpro = dataarray[i].maorpro;
            var codpro = dataarray[i].codpro;
            var num_serie = dataarray[i].num_serie;
            var cant_reimp = dataarray[i].cant_reimp;
            var usrmodi = dataarray[i].usrmodi;
            var fecmodi = dataarray[i].fecmodi;

            var digitos_serie = num_serie.length;

            var primeros_sietedigitos = num_serie.slice(0, 7);
            var ultimos_tresdigitos = num_serie.slice(7, 10);

            html_tabla += '<tr>';
            html_tabla += '<td>' + consec + '</td>';
            html_tabla += '<td>' + maorpro + '</td>';
            html_tabla += '<td>' + codpro + '</td>';
            html_tabla += '<td>' + primeros_sietedigitos + '<font color="red">' + ultimos_tresdigitos + '</font></td>';
            html_tabla += '<td>' + cant_reimp + '</td>';

            html_tabla += '</tr>';
          }
          html_tabla += '</tbody>';

          html_tabla += '</tfoot>';
          html_tabla += '</table>';

          $('#cardTablaDetSerOrdProd').show();
          $('#divTablaDetSerOrdProd').html(html_tabla);

          sortTable.init(); //para inicializar el script de sort-table

          BuscarEnTabla(inputbuscarTablaOrdProduc, tablaProdProveedor);

          $.LoadingOverlay("hide");

        } else {

          $.LoadingOverlay("hide");

          alertify.warning('No se encontraron resultados', 2, null);
        }
      },
      error: function (data) {

        alertify.error('Error message', 2, null);
        $.LoadingOverlay("hide");
      }

    });
  }

  /**
   * funcion que permite imprimir codigos  desde una modal
   * @param {*} registro 
   */
  function ModalReimprimirCodigos(registro) {

    $('#btn_reimprimircodigo_modal2').show();

    //ValidarFormEditarProdProv(); // se tiene cargado todo para la validacion del formulario de la modal

    $('#modalreimprimircodigos').modal({ //evitar que se cierre la modal cuando se da click en la parte gris
      backdrop: 'static',
      keyboard: false
    });

    $("#modalreimprimircodigos").modal(); //accion para abrir la modal

    $("#modalreimprimircodigos").on('shown.bs.modal', function () { //lo que se hara despues de que la modal sea visible al usuario

      var fecha_hoy = $('#input_fecha_hoy').val();
      //var loginintranet = $('#input_loginintranet').val();
      var tipo_imp = $('#select_tipoimp_modal2').val();
      document.getElementById("btn_reimprimircodigo_modal2").disabled = true;

      var consec = registro.consec;
      var fecprodu = registro.fecprodu;
      var numdocum = registro.numdocum;
      var codigopr = registro.codigopr;
      var codpro = registro.codpro;
      var codpadreselec = registro.codpadreselec;
      var cod_ean = registro.cod_ean;
      var nomproinv = registro.nomproinv;
      var producto_inventario = nomproinv + " - (" + codpro + ")";

      var descripc = registro.descripc;
      var cantidad = registro.cantidad;
      var numiniconsec = registro.numiniconsec;
      var numfinconsec = registro.numfinconsec;
      var cant_imp = registro.cant_imp;
      var cant_fal = registro.cant_fal;
      var cant_reimp = registro.cant_reimp;
      var serial_iniabs = registro.serial_inicio_absoluto;
      var serial_finabs = registro.serial_fin_absoluto;
      var nomprod = nomproinv + " - (" + codpro + ")";


      var input = '#input_cod_prod_modal2'; //modal generar e imprrmir seriales
      var modal = '#modalreimprimircodigos';

      cargarListaProductos(input, modal,codigopr);

      var select_codbarini_modal = '#input_codbarini_modal2'; //modal reimpirmir seriales
      var select_codbarfin_modal = '#input_codbarfin_modal2'; // modal reimprimir seriales

      cargarListaSeriales(select_codbarini_modal, '#modalreimprimircodigos', consec, tipo_imp);
      cargarListaSeriales(select_codbarfin_modal, '#modalreimprimircodigos', consec, tipo_imp);

      var modal = $(this);

      modal.find('.modal-body #input_desc_prod_modal').val(descripc);
      modal.find('.modal-body #input_codpr_modal').val(codigopr);
      modal.find('.modal-body #input_numdocum_mod').val(numdocum);
      modal.find('.modal-body #input_totalcod_modal2').val(cantidad); //
      modal.find('.modal-body #input_impcod_modal2').val(cant_imp);
      modal.find('.modal-body #input_falcod_modal2').val(cant_fal);
      modal.find('.modal-body #input_reimpcod_modal').val(cant_reimp);
      modal.find('.modal-body #input_codbarini_modal').val(numiniconsec);
      modal.find('.modal-body #input_consecordprod_modal2').val(consec);
      // modal.find('.modal-footer #input_userintra2').val(loginintranet);
      modal.find('.modal-body #input_serial_iniabs_modal2').val(serial_iniabs);
      modal.find('.modal-body #input_serial_finabs_modal2').val(serial_finabs);

      let id_input_codpro = document.getElementById('input_cod_prod_modal2');
      /*id_input_codpro.setAttribute('value',codprod);
      id_input_codpro.val=nompro;
      id_input_codpro.setAttribute('data-codean', cod_ean);*/

      $(id_input_codpro).val(nomprod);
      $(id_input_codpro).attr("value", codpro);
      $(id_input_codpro).attr("data-codean", cod_ean);

      let select = 'select_cod_prodpadre_modal2';
      let id_input_ean = document.getElementById('input_ean_prod_modal2');


      cargarListaProductosPadre(select, codpro, id_input_codpro, id_input_ean, '#modalreimprimircodigos', codpadreselec);

      /*let select_padres=document.getElementById('select_cod_prodpadre_modal2');
      select_padres.value=codpro;*/

    });

    $('#modalreimprimircodigos').on('hidden.bs.modal', function () { //lo que se hara cuando la modal se cierre

      $(this).find("input,textarea").val('').end();
      $(this).find("#select_tipoimp_modal2").val('Unico').end();
      $(this).find("#selec_numcopias_modal2").val('0').end();
      $(".input_clear").css('background', '#ffffff'); //para quitar el valor rojo de input que pusimos con jquery validate
      $('.select_clear').val('').trigger("change");
      $('#selec_numcopias_modal2').val('0').end();

      $('#btn_generarcodigo').off('click'); // detenemos la propagacion del evento para que no muestro varios alertify cada vez que se habre la ventana

      location.reload();
    });
  }

  /**
   * carga la modal para visualizar los detalles de los seriales
   * @param {*} consec 
   */
  function ModalDetalleSerieOP(consec) {

    $.LoadingOverlay("show", {
      fade: [-1000, 2000] //aparece en 0 y desaparece  en 5000          
    });

    $("#modaldetalleseries").modal(); //accion para abrir la modal

    $("#modaldetalleseries").on('shown.bs.modal', function () { //lo que se hara despues de que la modal sea visible al usuario
      consultarListaSeriales(consec)
    });


    $('#modaldetalleseries').on('hidden.bs.modal', function () { //lo que se hara cuando la modal se cierre

      //  $('#cardTablaDetSerOrdProd').hide();
      // $('#divTablaDetSerOrdProd').html('');
      // location.reload();
    });
  }

  /**
   * guarda los datos en la base de datos en la tabla spserial_codbar
   */
  function GuardarDatosSerial() {

    $.LoadingOverlay("show", {
      fade: [-1000, 2000] //aparece en 0 y desaparece  en 5000          
    });

    /*******************  variables de la pagina principal   ************************** */
    var ip = $('#inputip').val();
    var puerto = $('#inputpuerto').val();

    var tipo_codigo = $("#select_tipo_codigo").val();

    /*******************  variables de la modal   ******************************* */
    var codpro = $('#input_cod_prod_edit').attr('value');
    var nombre_producto = $('#input_cod_prod_edit').val();

    var codpro_padre = document.getElementById('select_cod_prodpadre_edit').value;


    if (codpro_padre == null || codpro_padre == '') {
      codpro_padre = '';
    }

    var cod_ean = document.getElementById('input_ean_prod_modal').value;
    console.log(document.getElementById('input_ean_prod_modal'));

    var nombre_prod = nombre_producto;
    var regExp = /-\s\([A-Za-z0-9]{1,20}\)/; //expresion regular creada para separar el nombre del codigo
    var resultado = regExp.test(nombre_producto);
    if (resultado == true) {
      nombre_prod = nombre_producto.replace(regExp, "");
    }


    var tamano_nompro = nombre_producto.length;
    if (tamano_nompro > 50) {
      nombre_prod = nombre_prod.slice(0, 50);
    }

    var maorpro = parseInt($('#input_consecordprod_modal').val()); //tabla spmaespro maestro produccion
    var codbar_iniabs = parseInt($('#input_serial_iniabs').val()); //es el primer serial de todos siempre terminara en 001
    var codbar_finabs = parseInt($('#input_serial_finabs').val()); //es el ultimo serial de todos siempre sera 001+ cantidad-1
    var codbarini = parseInt($('#input_codbarini_modal').val());
    var codbarfin = parseInt($('#input_codbarfin_modal').val());
    var cantidad = parseInt($('#input_totalcod_modal').val());
    var cant_imp = parseInt($('#input_impcod_modal').val());
    var can_falt = parseInt($('#input_falcod_modal').val());

    var num_copias = parseInt($('#selec_numcopias_modal').val());

    var user_intra = $('#input_loginintranet').val();

    var seriales = [];

    if (codpro == undefined || codpro == null || codpro == "") {
      $.LoadingOverlay("hide");
      alertify.warning('Seleccione el producto', 3, null);
      return false;
    }

    if (codbarfin == undefined || codbarfin == null || codbarfin == "" || codbarfin == 0) {
      $.LoadingOverlay("hide");
      alertify.warning('Revise el codigo final', 3, null);
      return false;
    }

    if (cod_ean == undefined || cod_ean == null || cod_ean == "") {
      $.LoadingOverlay("hide");
      alertify.warning('No existe codigo EAN para el producto seleccionado', 3, null);
      //return false; --DESCOMENTAR PARA PRODUCCION
    }

    if ((codbarini.toString().length != 10 || codbarfin.toString().length != 10)) {
      $.LoadingOverlay("hide");
      alertify.warning('el serial debe tener exactamente 10 digitos', 3, null);
      return false;
    }


    for (codbarini; codbarini <= codbarfin; codbarini++) {
      cant_imp++
      can_falt--

      var seriales_obj = {
        ip: ip,
        puerto: puerto,
        nombre_prod: nombre_prod,
        tipo_codigo: tipo_codigo,
        maorpro: maorpro,
        codpro: codpro,
        codpro_padre: codpro_padre,
        cod_ean: cod_ean,
        serial: codbarini,
        cantidad: cantidad,
        cant_imp: cant_imp,
        can_falt: can_falt,
        codbar_iniabs: codbar_iniabs,
        codbar_finabs: codbar_finabs,
        num_copias: num_copias,
        user_intra: user_intra
      };

      seriales.push(seriales_obj);
    }

    $.ajax({
      url: 'index.php',
      type: 'post',
      dataType: 'json',
      data: {
        controlador: 'generarcodigos',
        metodo: 'guardarSeriales',
        seriales: seriales,
      },
      success: function (data) {

        $.LoadingOverlay("hide");

        var mensaje = data.mensaje;
        var resultado = parseInt(data.resultado)

        if (resultado === 0) {

          document.getElementById("btn_generarcodigo").disabled = true;

          alertify.success(mensaje, 3, null);

        } else {
          alertify.error(mensaje, 3, null);
        }

      },
      error: function (data) {
        $.LoadingOverlay("hide");
        alertify.error('Ha ocurrido un error comuniquese con tics', 2, null);
      }

    });


  }

  /**
   * guarda los datos en la base de datos en la tabla spserial_codbar
   */
  function ReimprimirDatosSerial() {

    $.LoadingOverlay("show", {
      fade: [-1000, 2000] //aparece en 0 y desaparece  en 5000          
    });

    /*******************  variables de la pagina principal   ************************** */
    var ip = $('#inputip').val();
    var puerto = $('#inputpuerto').val();
    var tipo_codigo = $("#select_tipo_codigo").val();

    /*******************  variables de la modal   ******************************* */

    var codpro = $('#input_cod_prod_modal2').attr('value');
    var nombre_producto = $('#input_cod_prod_modal2').val();

    var codpro_padre = document.getElementById('select_cod_prodpadre_modal2').value;

    var cod_ean = document.getElementById('input_ean_prod_modal2').value; //input_ean_prod_modal


    var nombre_prod = nombre_producto;
    var regExp = /-\s\([A-Za-z0-9]{1,20}\)/; //expresion regular creada para separar el nombre del codigo
    var resultado = regExp.test(nombre_producto);
    if (resultado == true) {
      nombre_prod = nombre_producto.replace(regExp, "");
    }

    var tamano_nompro = nombre_producto.length;
    if (tamano_nompro > 50) {
      nombre_prod = nombre_prod.slice(0, 50);
    }

    var maorpro = parseInt($('#input_consecordprod_modal2').val()); //tabla spmaespro maestro produccion
    var codbar_iniabs = parseInt($('#input_serial_iniabs_modal2').val()); //es el primer serial de todos siempre terminara en 001
    var codbar_finabs = parseInt($('#input_serial_finabs_modal2').val()); //es el ultimo serial de todos siempre sera 001+ cantidad-1
    var codbarini = parseInt($('#input_codbarini_modal2').val());
    var codbarfin = parseInt($('#input_codbarfin_modal2').val());
    var cantidad = parseInt($('#input_totalcod_modal2').val());
    var num_copias = parseInt($('#selec_numcopias_modal2').val());
    var user_intra = $('#input_loginintranet').val();
    var seriales = [];


    if (codpro == undefined || codpro == null || codpro == "") {
      $.LoadingOverlay("hide");
      alertify.warning('Seleccione el producto', 3, null);
      return false;
    }

    if ((codbarfin == undefined || codbarfin == null || codbarfin == "" || codbarfin == 0)) {
      $.LoadingOverlay("hide");
      alertify.warning('Revise el serial final', 3, null);
      return false;
    }


    /*if (cod_ean == undefined || cod_ean == null || cod_ean == "") {
      $.LoadingOverlay("hide");
      alertify.warning('No existe codigo EAN para el producto seleccionado', 3, null);
      return false;
    }*/


    if ((codbarini.toString().length != 10 || codbarfin.toString().length != 10)) {
      $.LoadingOverlay("hide");
      alertify.warning('el serial debe tener exactamente 10 digitos', 3, null);
      return false;
    }


    for (codbarini; codbarini <= codbarfin; codbarini++) {
      var seriales_obj = {
        ip: ip,
        puerto: puerto,
        nombre_prod: nombre_prod,
        tipo_codigo: tipo_codigo,
        maorpro: maorpro,
        codpro: codpro,
        codpro_padre: codpro_padre,
        cod_ean: cod_ean,
        serial: codbarini,
        cantidad: cantidad,
        codbar_iniabs: codbar_iniabs,
        codbar_finabs: codbar_finabs,
        num_copias: num_copias,
        user_intra: user_intra
      };

      seriales.push(seriales_obj);
    }


    $.LoadingOverlay("hide");

    $.ajax({
      url: 'index.php',
      type: 'post',
      dataType: 'json',
      data: {
        controlador: 'generarcodigos',
        metodo: 'reimprimirSeriales',
        seriales: seriales,
      },
      success: function (data) {

        $.LoadingOverlay("hide");

        var mensaje = data.mensaje;
        var resultado = parseInt(data.resultado)

        if (resultado === 0) {

          document.getElementById("btn_generarcodigo").disabled = true;

          alertify.success(mensaje, 3, null);

        } else {
          alertify.error(mensaje, 3, null);
        }

      },
      error: function (data) {
        $.LoadingOverlay("hide");
        alertify.error('Ha ocurrido un error comuniquese con tics', 2, null);
      }

    });

  }

  /**
   * evento que permite verificar lso codigos de barra que se van a imprimir
   */
  $("#input_cantcod_modal").blur(function () {

    document.getElementById("btn_generarcodigo").disabled = false;

    var cant_actual = parseInt(this.value);

    var cant_faltantes = parseInt($('#input_falcod_modal').val());

    if (!cant_actual > 0) {

      $('#input_codbarfin_modal').val("");
      document.getElementById("btn_generarcodigo").disabled = true;
      alertify.error('No ha ingresado ninguna cantidad para imprimir', 3, null);
      return false;
    }

    if (cant_actual > cant_faltantes) {
      $('#input_codbarfin_modal').val("");
      document.getElementById("btn_generarcodigo").disabled = true;
      alertify.error('No puede imprimir mas codigos de los faltantes', 3, null);
      return false;
    }

    var codbarini = parseInt($('#input_codbarini_modal').val());

    var codbarfin = codbarini + (cant_actual - 1);

    $('#input_codbarfin_modal').val(codbarfin);

  });


  $("#select_tipoimp_modal2").change(function () {
    $('#input_codbarini_modal2').val('');
    $('#input_codbarini_modal2').attr('value', '');
    $('#input_codbarfin_modal2').val('');
    $('#input_codbarfin_modal2').attr('value', '');

    document.getElementById("btn_reimprimircodigo_modal2").disabled = true;
  });



  function cambiosSelect(elemento) {
    console.log(elemento);
  }



  /**
   * evento para seleccionar el serial inicial para reimpresion
   * @param {*} event 
   * @param {*} ui 
   */
  function seleccionarSerial(event, ui, input) {

    var value_actual = ui.item.value;
    var label_actual = ui.item.label; //obtenemos el valor del option actual seleccionado

    var tipo_imp = $('#select_tipoimp_modal2').val();

    if (input == '#input_codbarini_modal2') {

      if (tipo_imp == "Unico") {
        $('#input_codbarfin_modal2').val(label_actual);
        $('#input_codbarfin_modal2').attr('value', value_actual);

        document.getElementById("btn_reimprimircodigo_modal2").disabled = false;
      }


      if (tipo_imp == "Todos") {
        for (let index = 0; index < lista_seriales.length; index++) {

          let value = lista_seriales[index].value;
          let label = lista_seriales[index].label;

          if (index == 0) { //ini        
            $('#input_codbarini_modal2').val(label);
            $('#input_codbarini_modal2').attr('value', value);
          }

          if (index == (lista_seriales.length) - 1) { //final 
            $('#input_codbarfin_modal2').val(label);
            $('#input_codbarfin_modal2').attr('value', value);
          }
        }
        document.getElementById("btn_reimprimircodigo_modal2").disabled = false;
      }
    }

    if (input == '#input_codbarfin_modal2') {
      if (tipo_imp == "Rango") {
        var label_ini = parseInt($("#input_codbarini_modal2").val());
        if (label_ini > parseInt(label_actual)) {
          document.getElementById("btn_reimprimircodigo_modal2").disabled = true;
          alertify.error('El serial inicial no puede ser mayor que el final', 3, null);

        } else {
          document.getElementById("btn_reimprimircodigo_modal2").disabled = false;
        }
      }
    }
  }

}