var options_series = [];

$(document).ready(function () {
  datosGeneralesInicioSession();
});


/**
 * funcion que permite cargar datos informativos para la pagina de inicio
 */
function datosGeneralesInicioSession() {
  var nombre_usuario = $('#input_nombre_usuario').val();
  $('#p_nombre_usuario').text(nombre_usuario);
}


/**
 * funcion que permite buscar en una tabla dado el input de busqueda y la tabla destino
 * @param {*} input 
 * @param {*} tabla 
 */
function BuscarEnTabla(input, tabla) {
  $(document).ready(function () {
    $(input).on("keyup", function () {
      var value = $(this).val().toLowerCase();
      $(tabla + " tbody tr").filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
}


/**
 * funcion que permite obtener la ruta absoluta por ejemplo http://127.0.0.1/detalle_venta/
 */
function getAbsolutePath() {

  var loc = window.location;

  var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/'));

  return loc.href.trim().substring(0, loc.href.trim().length - ((loc.pathname.trim() + loc.search.trim() + loc.hash.trim()).length - pathName.trim().length));
}


/**
 * funcion que permite cargar un archivo guardado en disco y visualizarlo en una ventana modal
 * @returns {undefined}
 */
function VisualizarPDFGuardado() {

  var rutaabs = getAbsolutePath();

  var rutacompleta = rutaabs + "/" + 'impresion' + '/' + 'codigos.pdf';

  var html2 = '<object id="idPdf"' +
    'type="application/pdf" width="100%" height="650"' +
    // 'data="http://192.168.100.245:8080//informix/v4/cartera/detalle_venta/impresion/file2.pdf">' +
    'data="' + rutacompleta + '">' +
    '<p> Este navegador no soporta vista previa de PDF. Por favor descarguelo para visualizarlo: <a href="' + rutacompleta + '">Download PDF</a>.</p>' +
    '</object>';

  $('#divvisualizarPDF').html(html2);

  $('#modalvisualizarPDF').modal();
}


/**
 * validar que se ingresen solo numeros
 * @param {*} e 
 */
function soloNumeros(e)
{
	var key = window.Event ? e.which : e.keyCode
	return ((key >= 48 && key <= 57) || (key==8))
}
