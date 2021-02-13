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
		var url="../../proveedores.php";
		$(location).attr('href',url);
    });
	$("#boton_limpiar_carga_manual").click(function(){
		LimpiarCampos();
    });
	cargaProveedores();
});
function cargaProveedores()
{
	var data_ws = [];
	$.ajax({
            data:  {},	
            url:   'ajax/ws_buscarProvedores.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
		   var json = jQuery.parseJSON(response);
                   $.each(json, function(i, d) {	
                          data_ws.push({value:d.NAME1, label:d.NAME1+" ("+d.LIFNR+")", id:d.LIFNR});
                    });   
					
					$('#div_proveedores').html("");
	 
					 var cajaTexto='<label class="control-label">Proveedor: </label><span class="control-label" id="SeleccionProveedor"></span><br /> ';
						 cajaTexto+='<input type="text" class="form-control" placeholder="Proveedor" required="required" name="cboProvedor" id="cboProvedor" '
						 cajaTexto+='value="" size="25" required="required" title="Ingrese Proveedor">';
					 
					$('#div_proveedores').html(cajaTexto);
					
			},
			complete: function(){
					
				$("#cboProvedor").autocomplete({
							 source: data_ws,
							 minLength: 2,
							 open: function(event, ui) {
								$(this).autocomplete("widget").css({
									"width": $(this).width()
								});
							},
							 select: function(event, ui) {
									 // prevent autocomplete from updating the textbox
									 event.preventDefault();
									// manually update the textbox and hidden field
									 $(this).val(ui.item.label);
									 $("#cboProvedor").val(ui.item.label)
									 $("#txtIdProveedor").val(ui.item.id)
									 $("#SeleccionProveedor").html(ui.item.label)						 
							 }
				});
			}
	});
}
////// FUNCIONES PAGINA PRINCIPAL //////////////////////////////////////
function validarIngresoArchivo()
{
	if($.trim($("#txtIdProveedor").val())=="")
	{
		$("#cboProvedor").val("");
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
}
/////////////////FIN FUNCIONES PAGINA DETALLE //////////////////////////




