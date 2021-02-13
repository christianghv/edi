$( document ).ready(function() {
    $('#bodyDescargarOC [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
      style: 'cream' // Give it some style
	});
    cargaSociedades("(*) Sociedad: ", "sociedad");
	//cargaProveedores();
	
	getProveedores();
	/*
	var data = getProveedores();

     $("#txtDescargarOC_850").autocomplete({
                 source: data,
                 minLength: 2,
                 select: function(event, ui) {
                         // prevent autocomplete from updating the textbox
                         event.preventDefault();
                        // manually update the textbox and hidden field
                         $(this).val(ui.item.label);
                         $("#txtDescargarOC_850").val(ui.item.label)
                         $("#txtIdProveedor").val(ui.item.id)
						 $("#SeleccionProveedor").html(ui.item.label)						 
                 }
    });
	*/
	
	$('#bodyDescargarOC [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
      style: 'cream' // Give it some style
   });
   
    $('#tabla_oc').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[15,30, 50, 75, -1], [15,30, 50, 75, "All"]],
		"iDisplayLength": 15
	});
	
	$("#boton_descargar_archivo").click(function(){
		//$("#respuesta").html("");
		validarDescargaArchivo();
    });
	
	$("#btn_volver").click(function(){
		var url="../embarcadores.php";
		$(location).attr('href',url);
    });
	
	//

	
   });
////// FUNCIONES PAGINA PRINCIPAL //////////////////////////////////////
function validarDescargaArchivo()
{
	$("#sociedad").attr("required", "required");
	$("#btnValidarFormulario").trigger("click");
	if(formDescargarArchivo.checkValidity())
	{
		if($("#SeleccionProveedor").html()=="")
		{
			$("#txtDescargarOC_850").val("");
			$("#btnValidarFormulario").trigger("click");
		}
		else
		{
		DejarLimpiarTabla('tabla_oc');
		buscarHeader850();
		}
	}
}

function cargando(){
    $("#respuesta").html('Cargando');
}
            
function resultadoActual(respuesta){
	var htmlRes=$("#respuesta").html();
	if($.trim($("#respuesta").html())=="")
	{
    $("#respuesta").html(respuesta);
	}
	else{
	$("#respuesta").html(htmlRes+'<br />'+respuesta);
	}
}
            
function resultadoErroneo(Error){
	var htmlRes=$("#respuesta").html();
	if($.trim($("#respuesta").html())=="")
	{
    $("#respuesta").html(Error);
	}
	else{
	$("#respuesta").html(htmlRes+'<br />'+Error);
	}
}

function CambiarAlClicChk(chek)
{
	//$(chek).parents('tr').toggleClass('selected');
	if($(chek).is(':checked')) {  
            $(chek).parents('tr').removeAttr('class');
			$(chek).parents('tr').attr('class', 'selected'); 
        } else {  
            $(chek).parents('tr').removeAttr('class');  
        }  
		
	//$(chek).parents('tr').removeAttr('class');
	//$(chek).parents('tr').attr('class', 'selected');
	//alert("echo");	
}
/*
function getProveedores()
{
     var data_ws = [];
     $.post("../../EDI/ajax/ws_buscarProvedores.php", {}, function(response) {})
     .done(function(response){
     var json = jQuery.parseJSON(response);
     $(function () {
                   $.each(json, function(i, d) {
                          data_ws.push({value:d.nAME1, label:d.nAME1+" ("+d.lIFNR+")", id:d.lIFNR});
                          });
					});
     })
	 $('#proveedores').html("");
	 
	 var cajaTexto='<label>Proveedor: </label><span id="SeleccionProveedor"></span><br /> ';
		 cajaTexto+='<input type="text" class="form-control" placeholder="Proveedor" name="txtDescargarOC_850" id="txtDescargarOC_850" '
		 cajaTexto+='value="" size="25" required="required" title="Ingrese Proveedor">';
	 
	 $('#proveedores').html(cajaTexto);
	 $("#txtDescargarOC_850").autocomplete({
                 source: data_ws,
                 minLength: 2,
                 select: function(event, ui) {
                         // prevent autocomplete from updating the textbox
                         event.preventDefault();
                        // manually update the textbox and hidden field
                         $(this).val(ui.item.label);
                         $("#txtDescargarOC_850").val(ui.item.label)
                         $("#txtIdProveedor").val(ui.item.id)
						 $("#SeleccionProveedor").html(ui.item.label)						 
                 }
    });
     //return data_ws;
}

*/

function getProveedores()
{
	var data_ws = [];
	$.ajax({
            data:  {},	
            url:   '../../EDI/ajax/ws_buscarProvedores.php',
            type:  'post',
            beforeSend: function () {
               
            },
            success:  function (response) {
				   var json = jQuery.parseJSON(response);
                   $.each(json, function(i, d) {
                          data_ws.push({value:d.NAME1, label:d.NAME1+" ("+d.LIFNR+")", id:d.LIFNR});
                          });
					
					$('#proveedores').html("");
	 
					 var cajaTexto='<label class="control-label">Proveedor: </label><span class="control-label" id="SeleccionProveedor"></span><br /> ';
						 cajaTexto+='<input type="text" class="form-control" placeholder="Proveedor" name="txtDescargarOC_850" id="txtDescargarOC_850" '
						 cajaTexto+='value="" size="25" required="required" title="Ingrese Proveedor">';
					 
					$('#proveedores').html(cajaTexto);
					
				},
				complete: function(){
					
				$("#txtDescargarOC_850").autocomplete({
							 source: data_ws,
							 minLength: 2,
							 select: function(event, ui) {
									 // prevent autocomplete from updating the textbox
									 event.preventDefault();
									// manually update the textbox and hidden field
									 $(this).val(ui.item.label);
									 $("#txtDescargarOC_850").val(ui.item.label)
									 $("#txtIdProveedor").val(ui.item.id)
									 $("#SeleccionProveedor").html(ui.item.label)						 
							 }
				});
				}
	});
}
function cargaProveedores()
{
	$.ajax({
            data:  {},	
            url:   '../../EDI/ajax/ws_buscarProvedores.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				$('#proveedores').html("");
				var json = jQuery.parseJSON(response);
					var content='<label>Proveedor: </label><select class="form-control" id="cboProvedor" name="cboProvedor" required="required">';
					content+="<option value=''>-Seleccione-</option>";
					$.each(json, function(i, d) {	
						content += '<option value='+d.lIFNR+'>'+d.nAME1+'</option>';
					});
					content +='</select>';

				
				$('#proveedores').html(content);                       
                },
				complete: function(){
				}
			});
}
function descargarOC(Fila)
{
	//
	$("#boton_descargar_archivo").attr("disabled", true);
	//Variables
	var NumeroDeOC=$(Fila).parent('td').parent('tr').find('td:eq(0) label').html();
	if (NumeroDeOC=="") NumeroDeOC = "          ";
	var FechaDeOC=$(Fila).parent('td').parent('tr').find('td:eq(1)').html();
	var MontoDeOC=$(Fila).parent('td').parent('tr').find('td:eq(2)').html();
	var respuesta;
	
	$.ajax({
            data:  {
					accion:"obtenerDetalleOC850",
					NumeroOC: NumeroDeOC,
					FechaOC: FechaDeOC,
					MontoOC: MontoDeOC,
					Sociedad: $("#sociedad").val(),
					Proveedor: $("#cboProvedor").val()			
					},	
            url:   '../proveedores/ajax/descargarOC.php',
            type:  'post',
            beforeSend: function () {
                $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				respuesta=response;
				CerrarThickBox();
				$("#secretIFrame").attr("src","../proveedores/ajax/descargarArchivoForzado.php?ruta="+String(response));
				
            },
			complete: function(){
			 $("#boton_descargar_archivo").attr("disabled", false );			 
			}
	});
}
function descargarOCEDI(Fila)
{
	//
	$("#boton_descargar_archivo").attr("disabled", true);
	//Variables
	var NumeroDeOC=$(Fila).parent('td').parent('tr').find('td:eq(0) label').html();
	var FechaDeOC=$(Fila).parent('td').parent('tr').find('td:eq(1)').html();
	var MontoDeOC=$(Fila).parent('td').parent('tr').find('td:eq(2)').html();
	var respuesta;
	$.ajax({
            data:  {
					accion:"obtenerDetalleOC850EDI",
					NumeroOC: NumeroDeOC,
					FechaOC: FechaDeOC,
					MontoOC: MontoDeOC,
					Sociedad: $("#sociedad").val(),
					Proveedor: $("#cboProvedor").val()			
					},	
            url:   'ajax/descargarOC.php',
            type:  'post',
            beforeSend: function () {				
                $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);
				CerrarThickBox();
				$("#secretIFrame").attr("src","ajax/descargarArchivoForzado.php?ruta="+String(response));
				
            },
			complete: function(){
			 $("#boton_descargar_archivo").attr("disabled", false );
			}
	});
}
function buscarHeader850()
{
	var NumeroDeOC =  $("#nro_factura").val()
	if (NumeroDeOC=="") NumeroDeOC = "          ";
	$.ajax({
            data:  {
					sociedad: $("#sociedad").val(),
					proveedor: $("#txtIdProveedor").val(),
					fecha_inicio: $("#fecha_inicio").val(),
					fecha_termino: $("#fecha_termino").val(),
					NroOC: NumeroDeOC
					},
            url:   'ajax/../../proveedores/ajax/ws_cabeceraoc850.php',
            type:  'post',
            beforeSend: function () {
                $("#AbrirCargando").trigger("click");
				$("#boton_descargar_archivo").attr("disabled", true );
            },
            success:  function (response) {
							
				//alert("Respuesta: ("+response+")")
				//Variables error
				var Context="";
				var Code="";
				var TextoError="";
				var Stado="";
				
				if($.trim(String(response))!="")
				{
					var json = jQuery.parseJSON(response);
						
						var t = $('#tabla_oc').DataTable();
						
						$.each(json, function(i, d) {
							
						if(d.Resultado=="S")
						{	
							t.row.add( [
							'<label id="N_OC">'+d.NroOC+'</label>',
							d.Fecha,
							d.Monto,
							'<img src="../../images/txt_32.png" id="imgDescargarOC" style="cursor:pointer" onclick="javascript:descargarOC(this);" />'+
							'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
							'<img src="../../images/edi850_32.png" id="imgDescargarOCEDI" style="cursor:pointer" onclick="javascript:descargarOCEDI(this);" />'
							] ).draw();
						}
						else
						{
							Stado="E";
							Context=d.Context;
							Code=d.Code;
							TextoError=d.Texto;
						}
							
						});
				}
				else
				alert("-Error WS: "+Context+", "+Code+",Texto-> "+TextoError+"-");
            },
			complete: function(){
			$("#boton_descargar_archivo").attr("disabled", false );
			CerrarThickBox();
			}
	});
}
function DejarLimpiarTabla(idTabla)
{
	var oSettings = $('#'+idTabla+'').dataTable().fnSettings();
	var iTotalRecords = oSettings.fnRecordsTotal();
	for (i=0;i<=iTotalRecords;i++) {
	$('#'+idTabla+'').dataTable().fnDeleteRow(0,null,true);
	}
}
/////////////////FIN FUNCIONES PAGINA DETALLE //////////////////////////




