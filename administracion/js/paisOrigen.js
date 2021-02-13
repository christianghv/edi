$( document ).ready(function() {
	$('#tabla_pais_origen').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
		"iDisplayLength": 10
	});
	cargaProveedores();
	traerPaisesComboBox('cboPaisOrigen','DivCboPaisWS');
    
	$("#btn_agregarPaisOrigen").click(function(e) {
		e.preventDefault(); 
		if($("#H_IdProveedor").val()!="")
		{
			limpiarModal();
			$(".modal-content").css("display","block");
			$("#PaisOrigen_proveedor").attr("disabled", false );
			$('#modalPaisOrigenLabel').html("Ingresar Pais Origen");
			$("#btn_AbrirModalPaisOrigen").trigger("click");
		}
		else
		{
			alert("Debe seleccionar un proveedor para poder agregar un pais de origen");
		}
	});
	
	$("#btn_volver").click(function(e) {
		e.preventDefault();
		var url="../../internos.php";
		$(location).attr('href',url);
    });
	
	$("#btn_grabarModalPaisOrigen").click(function(e) {
		e.preventDefault();
		//Solo guardara la cabecera
		if(FormIngresarPaisOrigen.checkValidity())
		{
			var valID=ValidarIngresoDeCodigo();
				
			if(valID==true)
			{
				grabarPaisOrigen(1);
			}
			else
			{
				alert("El Codigo ingresado, ya se encuentra en uso, por favor ingrese otro");
				$("#PaisOrigen_proveedor").focus();
			}
					
		}
		else
		{
			$("#btn_ValidarFormIngresarSociedad").trigger("click")
			//--Fin Tab 856 activa --//
		};
	});	
	
	$("#btn_limpiar").click(function(e) {
		e.preventDefault();
		$("#SeleccionProveedor").html("");
		$("#cboProvedor").val("");
		$("#H_IdProveedor").val("");
		var content = '<tbody></tbody>';
		$('#tabla_pais_origen').dataTable().fnClearTable();
		$('#tabla_pais_origen').dataTable().fnDestroy();
		$('#tabla_pais_origen tbody').replaceWith(content);
		$('#tabla_pais_origen').dataTable({
			"language": LAN_ESPANOL,
			"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
			"iDisplayLength": 10
		});
	});	
	
	$("#btn_EliminarPaisOrigen").click(function(e) {
		e.preventDefault();
		if($("#H_IdProveedor").val()!="")
		{
			if (confirm('¿Está seguro de eliminar él o los codigos seleccionados?')) {
				EliminarPaisOrigen();
			} else {
				// Do nothing!
			}
		}
		else
		{
			alert("Debe seleccionar un proveedor para poder agregar un pais de origen");
		}
	});	
});
function EliminarPaisOrigen()
{
	var ArrayCodigo = new Array();
	
	$('[class="selected"]').each(function(){
	var item = {
			"codigo": $(this).find("td:eq(0) label").html()
		};
		//ImprimirObjeto(item);
		
		ArrayCodigo.push(item);
	});
	
	var JSON_ArrayCodigo = JSON.stringify({ArrayCodigo: ArrayCodigo});
	
		$.ajax({
				data:  {accion: "EliminarPaisOrigen",
						id_proveedor:$('#H_IdProveedor').val(),
						JSON_ArrayCodigo:  JSON_ArrayCodigo
						},	
				url:   'ajax/paisOrigen.php',
				type:  'post',
				beforeSend: function () {
					$("#AbrirCargando").trigger("click");
				},
				success:  function (response) {
						//alert(response);
						afectadasProveedores=parseFloat(response);
					},
				complete: function(){
							//CerrarThickBox();
							//alert("Seleccionados eliminados");								
							TraerPaisOrigen(0);							
				}
		});
}
function ValidarIngresoDeCodigo()
{
	var validacion=true;
	
	MostrarTodosLosRegistros();
	if($('#H_OldCodigo').val()=="")
	{
		$("[id=codigo]").each(function(){
			if($(this).html()==$('#txt_codigo').val())
			{
				validacion=false;
			}		
		});
	}	
	
	DejarDataTableDinamicaNormal();
	return 	validacion;
}

function limpiarModal()
{
	$("#txt_codigo").val("");
	$("#txt_paisOrigen").val("");
	$("#H_PaisOrigen").val("");
	$("#H_OldCodigo").val("");
	$("#SeleccionPaisOrigen").html("");
}

function grabarPaisOrigen(TipoFuncion)
{
	$('.modal-content').fadeOut(500);
	$('.modal-backdrop').fadeOut(500);
	$('#btn_CancelarSociedad').trigger("click");
	//modal-backdrop fade in	
	$("#AbrirCargando").trigger("click");	
	$.ajax({
            data:  {
					accion:"grabarPaisOrigen",
					OldCodigo    : $("#H_OldCodigo").val(),
					Id_proveedor : $("#H_IdProveedor").val(),
					Codigo 	 	: $("#txt_codigo").val(),
					PaisOrigen  : $("#H_PaisOrigen").val()
					},	
            url:   'ajax/paisOrigen.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);		                    
            },
			complete: function(){
				$('#H_OldCodigo').val("");
				if(TipoFuncion==1)
				{
					$("#btn_CancelarPaisOrigen").trigger("click");
					TraerPaisOrigen(0);
				}
			}
	});
}

function CargarDetailOnClick(label)
{	
	limpiarModal();
	$(".modal-content").css("display","block");
	
	$("#AbrirCargando").trigger("click");
	$('#modalPaisOrigenLabel').html("Editar Pais Origen");
	$('#H_OldCodigo').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#txt_codigo').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#H_PaisOrigen').val($(label).parent('td').parent('tr').find('td:eq(0) label').attr('pais_origen'));
	
	$('#cboPaisOrigen').val($('#H_PaisOrigen').val());
	
	$('#txt_paisOrigen').val($("#cboPaisOrigen :selected").text());
	$('#SeleccionPaisOrigen').html($("#cboPaisOrigen :selected").text());

	CerrarThickBox();
	$("#btn_AbrirModalPaisOrigen").trigger("click");
}
function TraerPaisOrigen(AbrirCargando)
{
	$.ajax({
            data:{
					accion:"buscarPaisOrigen",
					id_proveedor:$('#H_IdProveedor').val()
			},	
            url:   'ajax/paisOrigen.php',
            type:  'post',
            beforeSend: function () {
				if(AbrirCargando==1)
				{
					$("#AbrirCargando").trigger("click");
				}
            },
            success:  function (response) {
				var content="";
				content += '<tbody>';
				var json = jQuery.parseJSON(response);
					$.each(json, function(i, d) {	
						$('#cboPaisOrigen').val(d.pais_origen);
						
						content += '<tr id="'+d.codigo+'" class="editar">';
						content += '<td><label onclick="javascript:CargarDetailOnClick(this);" id="codigo" codigo="'+d.codigo+'" pais_origen="'+d.pais_origen+'" style="cursor:pointer">'+d.codigo+'</label></td>';
						content += '<td>'+$("#cboPaisOrigen :selected").text()+'</td>';
						content += '<td><input type="checkbox" class="marca_chk" name="chk_PaisOrigen" id="chk_PaisOrigen" value="'+d.codigo+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
						content += '</tr>';
					});
					content += '</tbody>';
					$('#tabla_pais_origen').dataTable().fnClearTable();
					$('#tabla_pais_origen').dataTable().fnDestroy();
					$('#tabla_pais_origen tbody').replaceWith(content);
					$('#tabla_pais_origen').dataTable({
						"language": LAN_ESPANOL,
						"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
						"iDisplayLength": 10
					});
				                    
                },
				complete: function(){
						CerrarThickBox();					
				}
	});
}

function CambiarAlClicChk(chek)
{
	if($(chek).is(':checked')) {
            $(chek).parents('tr').removeAttr('class');
			$(chek).parents('tr').attr('class', 'selected'); 
        } else {  
            $(chek).parents('tr').removeAttr('class');  
        }
}
function MostrarTodosLosRegistros()
{
	$('#tabla_pais_origen').dataTable().fnDestroy();
	$('#tabla_pais_origen').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
		"iDisplayLength": -1
	});
}
function DejarDataTableDinamicaNormal()
{
	$('#tabla_pais_origen').dataTable().fnDestroy();
	$('#tabla_pais_origen').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
		"iDisplayLength": 10
	});
}
function ActivarQtip()
{
	$('#bodySocieadades [title]').qtip({
			content: {
				text: false // Use each elements title attribute
					  },
			hide: {
				event: 'blur',
				leave: false
				},
			show: {
				event: 'focus',
				leave: false
					},
			style: 'cream' // Give it some style					  
				   });
}
function cargaProveedores()
{
	var data_ws = [];
	$.ajax({
            data:  {},	
            url:   '../EDI/ajax/ws_buscarProvedores.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				var json = jQuery.parseJSON(response);
				$.each(json, function(i, d) {
					data_ws.push({value:d.NAME1, label:d.NAME1+" ("+d.LIFNR+")", id:d.LIFNR});
                });
					
				var cajaTexto='<label class="control-label">Proveedor: </label><span class="control-label" id="SeleccionProveedor"></span><br /> ';
						 cajaTexto+='<input type="text" class="form-control" placeholder="Proveedor" name="cboProvedor" id="cboProvedor" '
						 cajaTexto+='value="" size="25" required="required" title="Ingrese Proveedor">';
					 
				$('#div_proveedores').html(cajaTexto);
			},
			complete: function(){
					$("#cboProvedor").autocomplete({
						source: data_ws,
						minLength: 1,
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
							$("#H_IdProveedor").val(ui.item.id);
							$("#SeleccionProveedor").html(ui.item.label);
							TraerPaisOrigen(1);
						}
					});
				}
	});
}
function traerPaisesComboBox(id_select,divUbicacionCbo)
{
	var data_ws = [];
	
	$.ajax({
            data:  {},	
            url:   '../EDI/ajax/ws_buscarPaises.php',
            type:  'post',
            beforeSend: function () {
				
            },
            success:  function (response) {				
				var json = jQuery.parseJSON(response);
				//alert(response);
					var content='<select class="form-control" id="'+id_select+'" name="'+id_select+'">';
					content+="<option value=''>-Seleccione-</option>";
					$.each(json, function(i, d) {	
						content += '<option value='+d.Land1+'>'+d.Land1+' - '+d.Landx50+'</option>';
						data_ws.push({value:d.Land1+' - '+d.Landx50, label:d.Land1+' - '+d.Landx50, id:d.Land1});
					});
					content +='</select>';
				$('#'+divUbicacionCbo+'').html(content);
				
				var cajaTexto='<label class="control-label">Pais Origen: </label><span class="control-label" id="SeleccionPaisOrigen"></span><br /> ';
						 cajaTexto+='<input type="text" class="form-control" placeholder="Pais Origen" name="txt_paisOrigen" id="txt_paisOrigen" '
						 cajaTexto+='value="" size="25" required="required" title="Ingrese Pais Origen" >';
					 
				$('#div_PaisOrigen').html(cajaTexto);
				
				$("#txt_paisOrigen").autocomplete({
						source: data_ws,
						minLength: 0,
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
							$("#H_PaisOrigen").val(ui.item.id);
							$("#SeleccionPaisOrigen").html(ui.item.label);
						}
				});
				
                },
				complete: function(){						
					
				}
	});
}