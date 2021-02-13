$( document ).ready(function() {
	//tabla detalle
	$('#tabla_datos_oc_detalle').dataTable({
		"scrollY":        "200px",
		"scrollCollapse": true,
		"paging":         false,
		"language": LAN_ESPANOL
	});
	
	//tabla modal
	$('#tabla_nro_oc_modal').dataTable( {
		"scrollY":        "200px",
		"scrollCollapse": true,
		"paging":         false,
		"language": LAN_ESPANOL
	});

    $("#selectall").click(function() { 
		
		$('#tabla_nro_oc_modal').dataTable();
		$('#tabla_nro_oc_modal').dataTable().fnDestroy();
		$('#tabla_nro_oc_modal input:checkbox').prop('checked', true); 
		$('#tabla_nro_oc_modal input:checkbox').parents('tr').removeAttr('class');
		$('#tabla_nro_oc_modal input:checkbox').parents('tr').attr('class', 'selected'); 
		$('#tabla_nro_oc_modal').dataTable({
			"scrollY":        "200px",
			"scrollCollapse": true,
			"paging":         false,
			"language": LAN_ESPANOL
		}); 
    });
    
    $("#deselect_all_modal").click(function() {   
		$('#tabla_nro_oc_modal').dataTable();
		$('#tabla_nro_oc_modal').dataTable().fnDestroy();
		$('#tabla_nro_oc_modal input:checkbox').prop('checked', false); 
		$('#tabla_nro_oc_modal input:checkbox').parents('tr').removeAttr('class');
		$('#tabla_nro_oc_modal').dataTable({
			"scrollY":        "200px",
			"scrollCollapse": true,
			"paging":         false,
			"language": LAN_ESPANOL
		});
    });
    
    $("#btn_modificar_modal").click(function() { 
		//$("#AbrirCargando").trigger("click");        
		guardarModificacionDetalle();    
    });
    $("#modificar_detalle").click(function(e) {      
		modificarOC();    
    });
    $("#volver_detalle").click(function(e) { 
		//$("#Divbtn_volverHome").css("margin-left","25px");
		$("#Divbtn_volverHome").css("display","block");
		$("#Divbtn_volverHome").css("margin-left","25px");
		
		volver_principal();    
    });

    cargaDatosOC(true);
});

function volver_principal() 
{
	$("#div_detalle").html("");
	$("#div_detalle").hide();
	$("#wrapper").show();
}

function modificarOC() {

	$("#select_all_modal").attr("disabled",true);
	$("#deselect_all_modal").attr("disabled",true);
	$("#btn_modificar_modal").attr("disabled",true);
	
	$("#alertDanger").html("");
	$("#alertDanger").css("display","none");
	$("#alertWarning").html("");
	$("#alertWarning").css("display","none");
	$("#alertInfo").html("");
	$("#alertInfo").css("display","none");
	$("#alertSuccess").html("");
	$("#alertSuccess").css("display","none");

	var nro_oc = $("#nro_oc_detalle").val();
	$("#oc_modal").text("-Nro. oc: "+nro_oc);
	////////////////////////////////////////////////////////////////////
	
	$.ajaxSetup({
	type: 'POST',
	timeout: 0,
	error: function(xhr) {
		alert("Se ha superado el tiempo de espera");
						 }
    })

	$('#tabla_nro_oc_modal tbody').html("");
	$.post('ajax/edi855.php',{ accion: 'cargaDetalleOCModal',Sociedad:$('#sociedad').val(), nro_oc:nro_oc }, function(response) { 
	}).done(function(response) {
		//alert(response);
		var json = jQuery.parseJSON(response);
		var content = "";
		$.each(json, function(i, d) {
			////////////////////////////////////////////////////////////
			var cprecio   = "igual";
			var ccantidad = "igual";
			var cmaterial = "igual";
			var tolerancia = parseFloat(d.tolerancia);
			
			if(d.cantidad != d.cantidad_ack) ccantidad 		 = "distinto";
			
			if(d.precio != d.precio_ack)
			{
				var diferencia = parseFloat(d.precio_ack)-parseFloat(d.precio);
				
				//alert(d.precio_ack+" VS "+d.precio+" Diferencia->"+Math.abs(diferencia)+" ,Tolerancia->"+tolerancia);
				
				if(Math.abs(diferencia)>tolerancia)
				{
					cprecio 	 = "excede";
				}
				else
				{
					cprecio 	 = "distinto";
				}
			}
			
			if(d.nro_parte != d.nro_parte_ack) cmaterial = "distinto";
			////////////////////////////////////////////////////////////
			if(cprecio == "distinto")
			{
				content += '<tr class="selected">';
				content += '<td >'+d.pos_oc+'</td>';
				content += '<td >PRECIO</td>';
				content += '<td >'+d.precio+'</td>';
				content += '<td >'+d.precio_ack+'</td>';
				content += '<td align="center"><input type="checkbox" value="" checked="checked" onchange="javascript:CambiarAlClicChk(this);"></td>';
				content += '</tr>';				
			}
			else
			{
				if(cprecio == "excede")
				{
					content += '<tr>';
					content += '<td >'+d.pos_oc+'</td>';
					content += '<td >PRECIO</td>';
					content += '<td >'+d.precio+'</td>';
					content += '<td >'+d.precio_ack+'</td>';
					content += '<td align="center"><input type="checkbox" title="Diferencia ('+Math.abs(diferencia)+') excede tolerancia '+tolerancia+'" value="" onchange="javascript:CambiarAlClicChk(this);"></td>';
					content += '</tr>';
				}
			}
			if(ccantidad == "distinto")
			{
				content += '<tr>';
				content += '<td >'+d.pos_oc+'</td>';
				content += '<td >CANTIDAD</td>';
				content += '<td >'+d.cantidad+'</td>';
				content += '<td >'+d.cantidad_ack+'</td>';
				content += '<td align="center"><input type="checkbox" value="" onchange="javascript:CambiarAlClicChk(this);"></td>';
				content += '</tr>';
			}
			if(cmaterial == "distinto")
			{
				content += '<tr>';
				content += '<td >'+d.pos_oc+'</td>';
				content += '<td >MATERIAL</td>';
				content += '<td >'+d.nro_parte+'</td>';
				content += '<td >'+d.nro_parte_ack+'</td>';
				content += '<td align="center"><input type="checkbox" value="" onchange="javascript:CambiarAlClicChk(this);"></td>';
				content += '</tr>';
			}
		});
		$('#tabla_nro_oc_modal').dataTable().fnClearTable();
		$('#tabla_nro_oc_modal').dataTable().fnDestroy();
		$('#tabla_nro_oc_modal tbody').html(content);
		$('#tabla_nro_oc_modal').dataTable( {
			"scrollY": "200px",
			"scrollCollapse": true,
			"paging": false,
			"language": LAN_ESPANOL,
		});
		$('#tabla_nro_oc_modal [title]').qtip({
		  content: {
			 text: false // Use each elements title attribute
		  },
		  style: 'cream' // Give it some style
		});
		
		$("#select_all_modal").attr("disabled",false);
		$("#deselect_all_modal").attr("disabled",false);
		$("#btn_modificar_modal").attr("disabled",false);
	});
}

function cargaDatosOC(CerrarCargando) 
{
	var modificada = $("#modificada").val();

	if(modificada == '1') 
	
		$("#div_img").html('<img src="../images/tick_16.png" height="17" width="17" id="semaforo_detalle" title="Orden Compra sin diferencias">');
	else if(modificada == '2')
		$("#div_img").html('<img src="../images/warning_16.png" height="17" width="17" id="semaforo_detalle" title="Orden Compra con alguna diferencia">');
	else if (modificada == '3')
		$("#div_img").html('<img src="../images/delete_16.png" height="17" width="17" id="semaforo_detalle" title="Orden Compra con diferencias">');
	else
		$("#div_img").html('');
	
	
	
	$('#tabla_datos_oc_detalle tbody').html("");
	var nro_oc = $("#nro_oc_detalle").val();

	$.post('ajax/edi855.php',{ accion: 'cargaDatosOC', nro_oc:nro_oc }, function(response) {
	}).done(function(response) {	

		var json = jQuery.parseJSON(response);
		var content = "";
		$.each(json, function(i, d) {
			content += '<tr id="oc_'+d.pos_oc+'" onClick="tooltip(this, ';
			content += '\''+d.tipo_ack+'\',\''+d.dif_precio+'\',\''+d.dif_cantidad+'\',\''+d.dif_nro_parte+'\',';
			content += '\''+d.precio+'\',\''+d.precio_ack+'\',\''+d.cantidad+'\',\''+d.cantidad_ack+'\');" >';
			content += '<td align="center">'+d.pos_oc+'</td>';
			content += '<td align="center">'+d.nro_parte+'</td>';
			content += '<td align="center">'+d.descripcion+'</td>';
			content += '<td align="center">'+d.cantidad+'</td>';
			content += '<td align="center">'+d.unidad+'</td>';
			content += '<td align="center">'+d.precio+'</td>';
			content += '<td align="center">'+d.moneda+'</td>';
			content += '<td align="center">'+d.nro_parte_ack+'</td>';
			content += '<td align="center">'+d.cantidad_ack+'</td>';
			content += '<td align="center">'+d.unidad_ack+'</td>';
			content += '<td align="center">'+d.precio_ackNumber+'</td>';
			content += '<td align="center">'+d.fecha_promesa+'</td>';
			
			if(d.modificada == 1) {
				content += '<td align="center"><img src="../images/tick_16.png" height="13" width="13" title="Posición sin direfencias"></td>';
			}else if(d.modificada == 2) {
				content += '<td align="center"><img src="../images/warning_16.png" height="13" width="13" title="Posición con alguna diferencia"></td>';
			}else if(d.modificada == 3) {
				content += '<td align="center"><img src="../images/delete_16.png" height="13" width="13" title="Posición con diferencias"></td>';
			}else {
				content += '<td alig="center"></td>';
			}
					from1 = d.fecha_promesa.split("-");
			fecha1 = new Date(from1[2], from1[1] - 1, from1[0]);
			content += '<td >'+fecha1+'</td>';
			
			content += '</tr>';
		});
		
		$('#tabla_datos_oc_detalle').dataTable().fnClearTable();
		$('#tabla_datos_oc_detalle').dataTable().fnDestroy();
		$('#tabla_datos_oc_detalle tbody').html(content);
		
		$('#tabla_datos_oc_detalle').dataTable( {
			"scrollY": "200px",
			"scrollCollapse": true,
			"paging": false,
			"language": LAN_ESPANOL,
			"aoColumnDefs": [
								{ "bVisible": false, "aTargets": [ 13 ] },
								{ "iDataSort": 13, "aTargets": [ 11 ] },
							]
		});
		//if(CerrarCargando==true)
		//{
			CerrarThickBox();
		//}
	});
}

function guardarModificacionDetalle()
{
	//Limpiar mensajes
	$("#alertDanger").html("");
	$("#alertDanger").css("display","none");
	$("#alertWarning").html("");
	$("#alertWarning").css("display","none");
	$("#alertInfo").html("");
	$("#alertInfo").css("display","none");
	$("#alertSuccess").html("");
	$("#alertSuccess").css("display","none");
	
	
	var data = "";
	var nro_oc = $("#nro_oc_detalle").val();
	var count = 0;

		$("#tabla_nro_oc_modal tbody tr").each(function (index) {
			var check = $(this).children("td").eq(4).children("input").is(':checked');
			if(check == true) 
			{
				$(this).children("td").each(function (index2) {
					
					switch (index2) {
						case 0:
							data += $(this).text()+';';
							break;
						case 1:
							data += $(this).text()+';';
							break;
						case 3:
							data += $(this).text()+';';
							break;
						case 4:
							var check = $(this).children("input").is(':checked');
							
							if(check == true) { data += '1#'; }
							else { data += '0#'; }
							break;
					}
				}); //fin ciclo fila..
			}//fin verifica checked de la fila...
		});//FIN RECORRIDO TABLA....
		
	if(data != "" ) 
	{
		$('#deselect_all_modal').attr('disabled',true);
		$('#selectall').attr('disabled',true);
		//$("#div_tabla_modal").hide();
		//$("#div_modificar").hide();
		//$("#div_modificar2").show();
				
		$.ajax({
					data:  {
							nro_oc:nro_oc,
							data:data						
							},	
					url:   'ajax/ws_modificarOC.php',
					type:  'post',
					beforeSend: function () {
						//
						$('#ImagenCargando').css('display','inline');
						$('#cerrar').css('display','none');
						$('#btn_modificar_modal').css('display','none');
					},
					success:  function (response) {
					var json = jQuery.parseJSON(response);
						$.each(json, function(j, d) {
							if(parseInt(d.ERRORWS)==0)
							{
								if(d.TYPE == 'S') {
									$('#alertSuccess').css('display','block');										
									var htmldiv=$('#alertSuccess').html();
									var htmlMensaje='<strong>¡Éxito!</strong>&nbsp  '+d.MESSAGE;
									$('#alertSuccess').html(htmldiv+htmlMensaje+'<br/>');
									sacaFilasModificadas();
									cargaDatosOC(false);
									//$('#div_modificar2').html("Modificado!");						
								}
								
								if(d.TYPE == 'W') {
									$('#alertWarning').css('display','block');										
									var htmldiv=$('#alertWarning').html();
									var htmlMensaje='<strong>¡Advertencia!</strong>&nbsp  '+d.MESSAGE;
									$('#alertWarning').html(htmldiv+htmlMensaje+'<br/>');
									//sacaFilasModificadas();
									//$('#div_modificar2').html("Modificado!");						
								}
								
								if(d.TYPE == 'E') {
									$('#alertDanger').css('display','block');										
									var htmldiv=$('#alertDanger').html();
									var htmlMensaje='<strong>¡Error!</strong>&nbsp  '+d.MESSAGE;
									$('#alertDanger').html(htmldiv+htmlMensaje+'<br/>');
									//sacaFilasModificadas();
									//$('#div_modificar2').html("Modificado!");								
								}
							}
							else
							{
								$('#alertDanger').css('display','block');										
								var htmldiv=$('#alertDanger').html();
								var htmlMensaje='<strong>¡Error WS!</strong>';
								htmlMensaje+='<br />Faultcode : '+d.Faultcode;
								htmlMensaje+='<br />Faultstring : '+d.Faultstring;
								htmlMensaje+='<br />Context : '+d.Context;
								htmlMensaje+='<br />Code : '+d.Code;
								htmlMensaje+='<br />TextWS : ';
								htmlMensaje+='<br /><textarea rows="6" cols="75">'+d.TextWS+'</textarea>';
								$('#alertDanger').html(htmldiv+htmlMensaje+'<br/>');
							}
							$('#deselect_all_modal').attr('disabled',false);
							$('#selectall').attr('disabled',false);
						});
					},
					complete: function(){
								//$("#btn_GEE").trigger("click");
								$('#ImagenCargando').css('display','none');
								$('#cerrar').css('display','inline');
								$('#btn_modificar_modal').css('display','inline');	
								}
			});
	}//fin if data...
	else {
		alert('No hay información que modificar.');
	}
}

function sacaFilasModificadas()
{
	$("#tabla_nro_oc_modal tbody tr").each(function (index) {
		
		$(this).children("td").each(function (index2) {
			switch (index2) {
			case 4:
				var check = $(this).children("input").is(':checked');
				if(check == true) { 
					$(this).parent('tr').hide(); 
					$(this).children("input").prop('checked', false); 
					$(this).parents('tr').removeAttr('class');
				}
				break;
			}
		});
	});
}

function CambiarAlClicChk(chek)
{
	if($(chek).is(':checked')) 
	{  
		$(chek).parents('tr').removeAttr('class');
		$(chek).parents('tr').attr('class', 'selected'); 
	} else {  
		$(chek).parents('tr').removeAttr('class');  
	}  
}

function tooltip(item, tipo, difprecio, difcantidad, difnparte, precio, precioack, cantidad, cantidadack )
{
	if(tipo == "" || tipo == null)
		tipo = 0;
		
	var content = '<div align="center"><table class="table_tooltip">';
	content += '<tr><td class="row1">Tipo ACK</td><td class="row2">'+tipo+'</td></tr>';
	
	if(precio != precioack) 
		content += '<tr><td class="row1">Dif Precio</td><td class="row2">'+difprecio+'</td></tr>';
	if(cantidad != cantidadack) 
		content += '<tr><td class="row1">Dif Cantidad</td><td class="row2">'+difcantidad+'</td></tr>';
	if(difnparte == true) {
		img = '<img src="../images/tick_16.png" height="15" width="15">';
		content += '<tr><td class="row1">Dif Nro Parte</td><td class="row2">'+img+'</td></tr></table></div>';
	}
	
	$(item).qtip({
		content: {
			title: "<div align='center'><b>INFORMACIÓN ADICIONAL</b></div>",
			text: content,
		},
		show: {
			event:'click'
		},
		hide: { 
			event: 'unfocus'
		},
		position: {
			my: 'top center',
			at: 'bottom center'
		}
	});
		
	$(item).qtip('api').show();
}




