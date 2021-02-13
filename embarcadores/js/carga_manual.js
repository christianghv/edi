$( document ).ready(function() {
	$('#bodyDescargarOC [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
      style: 'cream' // Give it some style
   });
    
	$("#boton_subir_archivo").click(function(){
		$("#respuesta").html("");
		validarIngresoArchivo();
    });
	
	$("#btn_volver").click(function(){
		var url="../../embarcadores.php";
		$(location).attr('href',url);
    });
	$("#boton_limpiar_carga_manual").click(function(){
		LimpiarCampos();
    });
});
////// FUNCIONES PAGINA PRINCIPAL //////////////////////////////////////
function validarIngresoArchivo()
{
	if($("#chk_confirmacion").prop('checked'))
	{
		$("#validacion_confirmacion").val("ok");
	}
	else
	{
		$("#validacion_confirmacion").val("Nok");
	}
	
	$("#sociedad").attr("required", "required");
	$("#cboTipo").attr("required", "required");
	$("#btn_FormCargaMasiva").trigger( "click" );
	if(formSubirExcel.checkValidity())
	{
		formSubirExcel.submit();
		cargandoDatos();
	}
}
function cargandoDatos()
{
	$("#cboProvedor").attr("disabled", true );
	$("#boton_subir_archivo").css("display","none");
	$("#boton_limpiar_carga_manual").css("display","none");
	$("#ImagenCargando").css("display","inline");
	$("#archivo").attr("disabled", true );
}
function DetenerCarga()
{
	$("#boton_subir_archivo").css("display","inline");
	$("#boton_limpiar_carga_manual").css("display","inline");
	$("#ImagenCargando").css("display","none");
	$("#archivo").attr("disabled", false );
}

function cargando(){
    $("#respuesta").html('Cargando');
}
            
function IngresarLog(tipo,respuesta)
{
	switch(tipo) {
		case 'N':
			$("#respuesta").append(respuesta+'<br />');
			break;
		case 'E':
			$("#respuesta").append('<span class="label label-danger">'+respuesta+'</span>'+'<br />');
			break;
		case 'P':
			$("#respuesta").append('<span class="label label-primary">'+respuesta+'</span>'+'<br />');
			break;
		case 'I':
			$("#respuesta").append('<span class="label label-info">'+respuesta+'</span>'+'<br />');
			break;
		case 'W':
			$("#respuesta").append('<span class="label label-warning">'+respuesta+'</span>'+'<br />');
			break;
		case 'S':
			$("#respuesta").append('<span class="label label-success">'+respuesta+'</span>'+'<br />');
			break;
	}
}
            
function resultadoErroneo(Error){
	$("#respuesta").append('<span class="label label-danger">'+Error+'</span>'+'<br />');
}
function FinCargaManual(NombreArchivoSubido)
{
	IngresarLog('N','<br />====================== Proceso terminado carga EDI Terminado del archivo ('+NombreArchivoSubido+') ====================== <br /><br />');
	DetenerCarga();
}
function MensajeAlerta(Mensaje)
{
	alert(''+Mensaje);
}
function LimpiarCampos()
{
	$("#archivo").val("");
	$("#cboProvedor").val("");
	$("#SeleccionProveedor").html("");
	$("#respuesta").html("");
	$("#cboProvedor").attr("disabled", false);
	
	$("#chk_confirmacion").prop('checked',false);
	$("#validacion_confirmacion").val("Nok");
}
/////////////////FIN FUNCIONES PAGINA DETALLE //////////////////////////