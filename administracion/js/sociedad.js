$( document ).ready(function() {
	TraerSociedades(1);
	var BANDERA_ID_RECEIVER=false;
	var BANDERA_PARAMETROS_DISTRIBUCION=false;
    
	$("#btn_AgregarSociedad").click(function() {  
		limpiarModal();
		$(".modal-content").css("display","block");
		$("#Sociedad_id_sociedad").attr("disabled", false );
		$('#modalAgregarSociedadLabel').html("Ingresar Sociedad");
		$("#btn_AbrirModalSociedad").trigger("click");
	});
	
	$("#btn_volver").click(function(){
		var url="../../internos.php";
		$(location).attr('href',url);
    });
	
	$("#btn_grabarModalSociedad").click(function() {
		//Solo guardara la cabecera
		if(FormIngresarSociedad.checkValidity())
		{
			var valID=ValidarIngresoDeIdSociedad();
				
			if(valID==true)
			{
				grabarSociedad(1);
			}
			else
			{
				alert("El ID de Sociedad ingresado, ya se encuentra en uso, por favor ingrese otro");
				$("#Sociedad_id_sociedad").focus();
			}
					
		}
		else
		{
			$("#btn_ValidarCabecera").trigger("click")
			//--Fin Tab 856 activa --//
		};
	});

	$("#btn_CodigoAsociados").click(function(e) {
		e.preventDefault();
		if(String($("#estadoCodigoAsociados").val())=="0")
		{
			$("#divCodigoAsociados").fadeIn("slow");
			$("#divParametrosDistribucion").hide();
			$("#estadoCodigoAsociados").val("1");
			$("#estadoParametrosDistribucion").val("0");
			
			//Crear tabla
			if(!window.BANDERA_ID_RECEIVER)
			{
				CrearDataTableReceiverAsociados();
			}
		}
		else
		{
			$("#divCodigoAsociados").fadeOut( "slow" );
			$("#estadoCodigoAsociados").val("0");
		}
	});
	
	$("#btn_ParametrosDistribucion").click(function(e) {
		e.preventDefault();
		if(String($("#estadoParametrosDistribucion").val())=="0")
		{
			$("#divCodigoAsociados").hide();
			$("#divParametrosDistribucion").fadeIn("slow");
			$("#estadoCodigoAsociados").val("0");
			$("#estadoParametrosDistribucion").val("1");
			//Crear tabla
			if(!window.BANDERA_PARAMETROS_DISTRIBUCION)
			{
				CrearDataTableParametrosDistribucion();
			}
		}
		else
		{
			$("#divParametrosDistribucion").fadeOut( "slow" );
			$("#estadoParametrosDistribucion").val("0");
		}
	});
	
	$("#btn_EliminarSociedad").click(function() {
		if (confirm('¿Está seguro de eliminar él o las sociedades seleccionados?')) {
				EliminarSociedades();
		}else{
			// Do nothing!
		}
    });
	$("#btn_AgregarIdReceiver").click(function(e) {
		e.preventDefault();
		if(FormIngresarIdReceiver.checkValidity())
		{
			var valID=ValidarIngresoDeIdReceiver();
				
			if(valID==true)
			{
				grabarIdReceiver();
			}
			else
			{
				alert("El ID Receiver ingresado, ya se encuentra en uso, por favor ingrese otro");
				$("#txt_AddIdReceiver").focus();
			}
		}
		else
		{
			$('#btn_ValidarFormIngresarIdReceiver').trigger("click");
		};
	});
	
	$("#btn_CancelarSociedad").click(function(e) {
		limpiarModal();
	});
	
	$("#btn_AgregarParametroDistribucion").click(function() {
		if(FormIngresarParametroDistribucion.checkValidity())
		{
			var valID=ValidarIngresoDeIdUrgencia();
				
			if(valID==true)
			{
				grabarParametroDistribucion();
			}
			else
			{
				alert("El ID Receiver ingresado, ya se encuentra en uso, por favor ingrese otro");
				$("#txtUrgencia").focus();
			}
		}
		else
		{
			$('#btn_ValidarFormIngresarParametro').trigger("click");
		};
	});
});
function validarTodasLasRutas()
{
	validacion=false;
	var errores=0;
	//RutaLocal
	var respuesta=validarRuta($("#Embarcadores_RutaLocal").val());
	if(respuesta==true)
	{
		//Correcto
	}
	else
	{
		errores++;
		$("#Embarcadores_RutaLocal").css("border-color","#a94442");
	}
		
	
	if(errores==0)
	{
		validacion=true;
		$("#Embarcadores_RutaLocal").css("border","1px solid #ccc");
	}
	
	return validacion;
}
function EliminarSociedades()
{
	var ArrayId_sociedad = new Array();
	
	$('[class="selected"]').each(function(){
	var item = {
			"id_sociedad": $(this).find("td:eq(0) label").html()
		};
		//ImprimirObjeto(item);
		
		ArrayId_sociedad.push(item);
	});
	
	var JSON_ArrayId_sociedad = JSON.stringify({ArrayId_sociedad: ArrayId_sociedad});
	
		$.ajax({
					data:  {accion: "EliminarSociedad",
							JSON_ArrayId_sociedad:  JSON_ArrayId_sociedad
							},	
					url:   'ajax/sociedades.php',
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
								TraerSociedades(0);							
						}
		});
}
function ValidarIngresoDeIdSociedad()
{
	var validacion=true;
	
	MostrarTodosLosRegistrosTabla('tabla_sociedades');
	
	if($('#H_OldIdSociedad').val()=="")
	{
		$("[id=Id_sociedad]").each(function(){
			if($(this).html()==$('#Sociedad_id_sociedad').val())
			{
				validacion=false;
			}		
		});
	}	
	
	DejarDataTableDinamicaNormal('tabla_sociedades');
	return 	validacion;
}
function ValidarIngresoDeIdReceiver()
{
	var validacion=true;
	
	MostrarTodosLosRegistrosTabla('tbl_IdReceiverAsociados');
	
	if($('#H_OldIdReceiver').val()=="")
	{
		$("[id=IdReceiverAsociado]").each(function(){
			if($(this).html()==$('#txt_AddIdReceiver').val())
			{
				validacion=false;
			}		
		});
	}	
	
	DejarDataTableDinamicaNormal('tbl_IdReceiverAsociados');
	return 	validacion;
}
function ValidarIngresoDeIdUrgencia()
{
	var validacion=true;
	
	MostrarTodosLosRegistrosTabla('tbl_ParametrosDistribucion');
	
	if($('#H_OldUrgencia').val()=="")
	{
		$("[id=UrgenciaParametrosDistribucion]").each(function(){
			if($(this).html()==$('#txtUrgencia').val())
			{
				validacion=false;
			}		
		});
	}	
	
	DejarDataTableDinamicaNormal('tbl_ParametrosDistribucion');
	return 	validacion;
}
function limpiarModal()
{
	$("#Sociedad_id_sociedad").val("");
	$("#Sociedad_nombre").val("");
	$("#Sociedad_tolerancia").val("");
	$("#btn_CodigoAsociados").hide("fade");
	$("#btn_ParametrosDistribucion").hide("fade");
	$("#divCodigoAsociados").hide();
	$("#estadoCodigoAsociados").val("0");
	$("#divParametrosDistribucion").hide();
	$("#estadoParametrosDistribucion").val("0");
	$("#txt_AddIdReceiver").val("");
	$("#txtUrgencia").val("");
	$("#txtPeso").val("");
	$("#txtLargo").val("");
	$("#txtAncho").val("");
	$("#txtAlto").val("");
	window.BANDERA_ID_RECEIVER=false;
	window.BANDERA_PARAMETROS_DISTRIBUCION=false;
}

function grabarSociedad(TipoFuncion)
{
	$('.modal-content').fadeOut(500);
	$('.modal-backdrop').fadeOut(500);
	//modal-backdrop fade in	
	$("#AbrirCargando").trigger("click");	
	$.ajax({
            data:  {
					accion:"grabarSociedad",
					Sociedad_id_sociedad : $("#Sociedad_id_sociedad").val(),
					Sociedad_nombre 	 : $("#Sociedad_nombre").val(),
					Sociedad_tolerancia  : $("#Sociedad_tolerancia").val()
					},	
            url:   'ajax/sociedades.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);		    
				$('#btn_CancelarSociedad').trigger("click");
            },
			complete: function(){
				$('#H_OldIdSociedad').val("");
				if(TipoFuncion==1)
				{
					$("#btn_CancelarEmbarcador").trigger("click");
					TraerSociedades(0);
				}
			}
	});
}

function grabarIdReceiver()
{
	$("#DivMenuIdReceiver").hide();
	$("#ImagenCargandoIdReceiver").show("fade");
	
	$.ajax({
            data:  {
					accion:"grabarIdReceiver",
					IdReceiver			 : $("#txt_AddIdReceiver").val(),
					H_OldIdReceiver 	 : $("#H_OldIdReceiver").val(),
					IdSociedad 	 	     : $('#Sociedad_id_sociedad').val()
					},	
            url:   'ajax/sociedades.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);
				BuscarIdReceiverAsociados($('#Sociedad_id_sociedad').val(),true);
            },
			complete: function(){
				$('#H_OldIdReceiver').val("");
				$('#txt_AddIdReceiver').val("");
				
				
				$("#ImagenCargandoIdReceiver").hide();
				$("#DivMenuIdReceiver").show("fade");
			}
	});
}

function grabarParametroDistribucion()
{
	$("#DivMenuIngresarParametro").hide();
	$("#DivImagenCargandoIngresarParametro").show("fade");
	
	$.ajax({
            data:  {
					accion:"grabarParametroDistribucion",
					Urgencia		 : $("#txtUrgencia").val(),
					H_OldUrgencia 	 : $("#H_OldUrgencia").val(),
					Peso		 	 : $("#txtPeso").val(),
					Largo		 	 : $("#txtLargo").val(),
					Ancho		 	 : $("#txtAncho").val(),
					Alto		 	 : $("#txtAlto").val(),
					IdSociedad 	     : $('#Sociedad_id_sociedad').val()
					},	
            url:   'ajax/sociedades.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);
				BuscarParametrosDistribucion($('#Sociedad_id_sociedad').val(),true);
            },
			complete: function(){
				
				$('#txtUrgencia').val("");
				$('#H_OldUrgencia').val("");
				$('#txtPeso').val("");
				$('#txtLargo').val("");
				$('#txtAncho').val("");
				$('#txtAlto').val("");
				
				
				$("#DivImagenCargandoIngresarParametro").hide();
				$("#DivMenuIngresarParametro").show("fade");
			}
	});
}

function CargarDetailOnClick(label)
{
	limpiarModal();
	$(".modal-content").css("display","block");
	
	$("#AbrirCargando").trigger("click");
	$("#Sociedad_id_sociedad").attr("disabled", true );
	$('#modalAgregarSociedadLabel').html("Editar Sociedad");
	$('#Sociedad_id_sociedad').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#H_OldIdSociedad').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#Sociedad_nombre').val($(label).parent('td').parent('tr').find('td:eq(1)').html());
	$('#Sociedad_tolerancia').val($(label).parent('td').parent('tr').find('td:eq(2)').html());
	
	BuscarIdReceiverAsociados($('#Sociedad_id_sociedad').val(),false);
	BuscarParametrosDistribucion($('#Sociedad_id_sociedad').val(),false);
	CerrarThickBox();
	$("#btn_AbrirModalSociedad").trigger("click");
}
function BuscarParametrosDistribucion(id_sociedad,CrearTabla)
{
	$.ajax({
            data:  {
					accion:"BuscarParametrosDistribucion",
					id_sociedad:id_sociedad
					},	
            url:   'ajax/sociedades.php',
            type:  'post',
            beforeSend: function () {
				
            },
            success:  function (response) {
				var content="";
				var json = jQuery.parseJSON(response);
				
				$.each(json, function(i, d) {	
					content += '<tr>';
					content += '<td style="width:20%"><label onclick="javascript:CargarParametroDistribucionOnClick(this);" id="UrgenciaParametrosDistribucion" style="cursor:pointer">'+d.urgencia+'</label></td>';
					content += '<td style="width:20%">'+d.peso+'</td>';
					content += '<td style="width:20%">'+d.longitud+'</td>';
					content += '<td style="width:20%">'+d.ancho+'</td>';
					content += '<td style="width:20%">'+d.alto+'</td>';
					content += '<td style="text-align: center;"><span class="glyphicon glyphicon-remove" aria-hidden="true" style="cursor:pointer" onclick="javascript:EliminarParametroDistribucion(event,this);" ></span></td>';
					content += '</tr>';
				});
				$('#tbl_ParametrosDistribucion').dataTable().fnDestroy();
				$('#tBodyParametroDistribucion').html(content);
				if(CrearTabla)
				{
					CrearDataTableParametrosDistribucion();
				}
            },
			complete: function(){
				$("#btn_ParametrosDistribucion").show("fade");
			}
	});
}
function BuscarIdReceiverAsociados(id_sociedad,CrearTabla)
{
	$.ajax({
            data:  {
					accion:"buscarIdReceiverAsociados",
					id_sociedad:id_sociedad
					},	
            url:   'ajax/sociedades.php',
            type:  'post',
            beforeSend: function () {
				
            },
            success:  function (response) {
				var content="";
				var json = jQuery.parseJSON(response);
				$.each(json, function(i, d) {	
					content += '<tr>';
					content += '<td style="width:100%"><label onclick="javascript:CargarIdReceiverOnClick(this);" id="IdReceiverAsociado" style="cursor:pointer">'+d.id_receiver+'</label></td>';
					content += '<td style="text-align: center;"><span class="glyphicon glyphicon-remove" aria-hidden="true" style="cursor:pointer" onclick="javascript:EliminarIdReceiver(event,this);" ></span></td>';
					content += '</tr>';
				});
				$('#tbl_IdReceiverAsociados').dataTable().fnDestroy();
				$('#tBodyIdReceiverAsociados').html(content);
				if(CrearTabla)
				{
					CrearDataTableReceiverAsociados();
				}
            },
			complete: function(){
				$("#btn_CodigoAsociados").show("fade");
			}
	});
}
function EliminarIdReceiver(e,span)
{
	e.preventDefault();
	var IdReceiver=$(span).parent('td').parent('tr').find('td:eq(0) label').html();
	
	$(span).parent('td').parent('tr').find('td:eq(1)').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i><span class="sr-only">Loading...</span>');
	
	$.ajax({
            data:  {
					accion:"EliminarIdReceiverAsociado",
					id_sociedad:$('#Sociedad_id_sociedad').val(),
					IdReceiver:IdReceiver
					},	
            url:   'ajax/sociedades.php',
            type:  'post',
            beforeSend: function () {
				
            },
            success:  function (response) {
				
            },
			complete: function(){
				BuscarIdReceiverAsociados($('#Sociedad_id_sociedad').val(),true);
			}
	});
}
function EliminarParametroDistribucion(e,span)
{
	e.preventDefault();
	var Urgencia=$(span).parent('td').parent('tr').find('td:eq(0) label').html();
	
	$(span).parent('td').parent('tr').find('td:eq(5)').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i><span class="sr-only">Loading...</span>');
	
	$.ajax({
            data:  {
					accion:"EliminarParametroDistribucion",
					id_sociedad:$('#Sociedad_id_sociedad').val(),
					Urgencia:Urgencia
					},	
            url:   'ajax/sociedades.php',
            type:  'post',
            beforeSend: function () {
				
            },
            success:  function (response) {
				BuscarParametrosDistribucion($('#Sociedad_id_sociedad').val(),true);
            },
			complete: function(){
				
			}
	});
}
function CargarIdReceiverOnClick(label)
{
	$('#txt_AddIdReceiver').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#H_OldIdReceiver').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
}
function CargarParametroDistribucionOnClick(label)
{
	$('#txtUrgencia').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#txtPeso').val($(label).parent('td').parent('tr').find('td:eq(1)').html());
	$('#txtLargo').val($(label).parent('td').parent('tr').find('td:eq(2)').html());
	$('#txtAncho').val($(label).parent('td').parent('tr').find('td:eq(3)').html());
	$('#txtAlto').val($(label).parent('td').parent('tr').find('td:eq(4)').html());
	$('#H_OldUrgencia').val($(label).parent('td').parent('tr').find('td:eq(0)').html());
}
function TraerSociedades(AbrirCargando)
{
	$.ajax({
            data:  {
					accion:"buscarSociedadesMantenedor"
					},	
            url:   'ajax/sociedades.php',
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
						content += '<tr id="'+d.id_sociedad+'" class="editar">';
						content += '<td><label onclick="javascript:CargarDetailOnClick(this);" id="Id_sociedad" style="cursor:pointer">'+d.id_sociedad+'</label></td>';
						content += '<td>'+d.desc_sociedad+'</td>';
						content += '<td>'+d.tolerancia+'</td>';
						content += '<td><input type="checkbox" class="marca_chk" name="chk_sociedad" id="chk_sociedad" value="'+d.id_sociedad+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
						content += '</tr>';
					});
					content += '</tbody>';
					$('#tabla_sociedades').dataTable().fnClearTable();
					$('#tabla_sociedades').dataTable().fnDestroy();
					$('#tabla_sociedades tbody').replaceWith(content);
					$('#tabla_sociedades').dataTable({
						"scrollY": "200px",
						"scrollCollapse": true,
						"paging": false,
						"language": LAN_ESPANOL
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
function MostrarTodosLosRegistrosTabla(IdTabla)
{
	$('#'+IdTabla+'').dataTable().fnDestroy();
}
function DejarDataTableDinamicaNormal(IdTabla)
{
	$('#'+IdTabla+'').dataTable({
		"scrollY": "200px",
		"scrollCollapse": true,
		"paging": false,
		"language": LAN_ESPANOL
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
function CrearDataTableParametrosDistribucion()
{
	$('#tbl_ParametrosDistribucion').dataTable({
		"scrollY": "200px",
		"scrollCollapse": true,
		"paging": false,
		"language": LAN_ESPANOL
	});
	
	window.BANDERA_PARAMETROS_DISTRIBUCION=true;
}
function CrearDataTableReceiverAsociados()
{
	$('#tbl_IdReceiverAsociados').dataTable({
		"scrollY": "200px",
		"scrollCollapse": true,
		"paging": false,
		"language": LAN_ESPANOL
	});
	window.BANDERA_ID_RECEIVER=true;
}