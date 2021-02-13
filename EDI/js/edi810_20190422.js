$( document ).ready(function() {
	$("#Gee_txtEta").datepicker({dateFormat:"dd/mm/yy"}).datepicker();
	$('[class=close]').css("display","none");
	lineasIngresoFactura("",5);
	cargaSociedades("(*) Sociedad: ", "sociedad");
	/////////////////////////////////////////////////	
	$('#bodyEdi810 [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
      style: 'cream' // Give it some style
	});
	$("#btn_tabla_generar_entrega").click(function(e) {      
		RecorrerCheckGEE();  
    });
	
	$("#boton_subir_archivo_actualizar_ee").click(function(e) {
		e.preventDefault();
		$("#btn_FormCargaActualizaEE").trigger("click");
		
		if(formCargaActualizaEE.checkValidity())
		{
			$("#ImagenCargandoCargaActualizaEE").show();
			$("#archivo_Actualizar_EE").hide();
			$("#boton_subir_archivo_actualizar_ee").hide();
			
			$("#alertSuccessAEE").hide();
			$("#alertSuccessAEE").html('');
			$("#alertDangerAEE").hide();
			$("#alertDangerAEE").html('');
			$("#DivOpcionesActualizarEE").hide();
			
			formCargaActualizaEE.submit();
		}
	});
	
	$("#btn_tabla_ActualizarEE").click(function(e) {
		$("#alertSuccessAEE").css("display","none");
		$("#alertDangerAEE").css("display","none");
		$("#archivo_Actualizar_EE").val("");
		$("#btn_ActualizarEE").show();
		RecorrerCheckActualizarEE();  
    });
	
	$("#btn_generararEE").click(function(e) {
		$("#btnValidarGee").trigger("click");
		var CampoSearch= $('#tabla_EE_filter').find('label input');
		
		$(CampoSearch).val("");
		
		ev = $.Event('keyup');
		ev.keyCode= 13; // enter
		$(CampoSearch).trigger(ev);
		
		
		if(frm_gee.checkValidity()){
			GenerarGEE();
		}
	});
	
	$("#btn_ActualizarEE").click(function(e) {
		
		var ingresados=0;
		var errores=0;
		var mensajeError="";
		var JSON_ArrayDeAEE="";
		var ArrayDeAEE = new Array();
		
		$("input[name=aee_chk]").each(function(){
		
			var fila=$(this).parent("td").parent("tr");
			var display=""+$(fila).css("display");
			
			if(($(this).val())!="" && display!="none")
			{
				$(this).attr('required', 'required');
				
				var item = {
						"InvoiceNumber": $(fila).find('td:eq(0)').html(),
						"PONumber": $(fila).find('td:eq(1)').html(),
						"EE": $(this).val()
				};	
                
				ArrayDeAEE.push(item);
				ingresados++;
			}
			else
			{
				$(this).removeAttr('required');
			}
		});
		if(ingresados>0)
		{
			if(frm_actualizarEE.checkValidity())
			{
				JSON_ArrayDeAEE = JSON.stringify({ArrayDeAEE: ArrayDeAEE});
				ActualizarEE(JSON_ArrayDeAEE);
			}
		}
		else
		{
			errores++;
			$("#btnValidarActuEE").trigger("click");
			mensajeError+="&nbsp Debe verificar los datos ingresados"+"<br/>";
		}
		
		if(errores>0)
		{
			$("#alertDangerAEE").css("display","block");
			var htmlMensaje="<strong>¡Error!</strong>"+mensajeError;
			$("#alertDangerAEE").html(htmlMensaje);
		}
		else
		{
			$("#alertDangerAEE").css("display","none");
		}
	});	

	$("#btn_CancelarEE").click(function(e) {      
		limpiarGee();
    });
	
	//cbo_Formato	
     var data = getRutasWS();

     $("#Gee_txtRuta").autocomplete({
                 source: data,
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
                         $("#Gee_txtRuta").val(ui.item.value)
                         $("#txtIdRuta").val(ui.item.id)
						 $("#SeleccionRuta").html(ui.item.label)						 
                 }
    });

	$("#btn_buscar").click(function(e) {  
		$("#sociedad").attr('required', 'required');
		$("#submit_filtros").trigger("click");		
		if(frm_filtros.checkValidity()){
			buscarFacturas();  
		}
    });
	
	$("#btn_EESelect").click(function(e) {
		 seleccionarTodasEE();
    });
	
	$("#btn_EEDeselc").click(function(e) {
		$("input[name=gee_chk]").each(function(){
			$(this).prop('checked', false);
			$(this).parents('tr').removeAttr('class');
		});
    });
	
	$('#nro_factura').dblclick(function() {
		$("#ingreso_factura").trigger("click");
	});
	$("#btn_ingreso_factura").click(function(e) {      
		//IngresarFactura810();
		IngresarFactura810_2();
    });
	
	$("#btn_deselec").click(function(e) {      
		unselect_all();    
    });
	
	$("#btn_tabla_ingreso").click(function(e) {      
		tablaIngresarFactura();    
    });
	
	$("#btn_agrega_lineas").click(function(e) {  
		var cantidad = $("#lineas").val();    
		if(cantidad != "")
			lineasIngresoFacturaAgregar(cantidad);
		else $("#lineas").focus();
    });
	
	$("#btn_limpia_tabla_ingreso_factura").click(function(e) {      
		lineasIngresoFactura("",5);   
    });

	$("#btn_NumCerrarFacturas").click(function(e) {    
		$("#txtNumeroFacturas").html("");
	 	lineasIngresoFactura("",5);
		$("#lineas").val("5");
		$("#btn_guardar_nro_facturas").trigger("click");
		
    });
	
	$("#btn_selec").click(function () {
		select_all();
	})
	
	$(".check_ee").change(function () {
		if (this.attr('checked')) {
			$(this).parents('tr').removeAttr('class');
			$(this).parents('tr').attr('class', 'selected');
		} 
		else
		{
			$(this).parents('tr').removeAttr('class');
		}	
	});
	
	$("#btn_guardar_nro_facturas").click(function(){
		$("#nro_factura").val("");
    });
	
	$("#btn_inicio").click(function(){
		var url="../../internos.php";
		$(location).attr('href',url);
    });
	
	$("#Gee_txtRuta").keypress(function() {
		$("#HcboRuta").val($("#Gee_txtRuta").val());		
		if($("#HcboRuta").val()!="")
		{
			$("#SeleccionRuta").html($("select[name='HcboRuta'] option:selected").text());
			$("#txtIdRuta").val($("#HcboRuta").val());
		}
	});

	$("#Gee_txtRuta").change(function() {
		$("#HcboRuta").val($("#Gee_txtRuta").val());		
		if($("#HcboRuta").val()!="")
		{
			$("#SeleccionRuta").html($("select[name='HcboRuta'] option:selected").text());
			$("#txtIdRuta").val($("#HcboRuta").val());
		}
	});
	
	$('#btnBuscarMaterial').click(function(e) {
		e.preventDefault();
		$('#submit_GeeBuscarMaterial').trigger('click');
		
		if(frm_GeeBuscarMaterial.checkValidity())
		{
			bloquearOpcionesEE();
			$('#SpanBuscarMaterial').attr('class','fa fa-cog fa-spin fa-fw');
			
			$.post('ajax/edi810.php',{
				accion			: 'BuscarMaterialEnFacturas', 
				Material		: $('#txtBuscarMaterialGee').val(), 
				JSON_ArrayDeGEE	: $('#JSON_ArrayDeGEE').html()
				},
			function(response) {
				var json 	= jQuery.parseJSON(response);
				$.each(json, function(i, d) {
					try {
						$('#'+d.InvoiceNumber+'_'+d.PONumber).parents('tr').removeClass('selected').addClass('selectedDestacado');
					}catch(err) {}
				});
			}).done(function(response) {
				desbloquearOpcionesEE();
				$('#SpanBuscarMaterial').attr('class','glyphicon glyphicon-search');
			});
		}
	});
	$("#rdb_BuscarFactura").change(function(e) {
		e.preventDefault();
		if($('#rdb_BuscarFactura').prop('checked'))
		{
			$('#nro_factura').prop('placeholder','Ingrese factura..');
			$('#TituloModalFactura').html('Ingresar Facturas');
			$('#TituloPanelIngresoFacturas').html('INGRESO DE MULTIPLES FACTURAS');
			$('#TipoDocumentoBusqueda').val('Factura');
			$('#txtNumeroFacturas').val('');
		}
    });
	
	$("#rdb_BuscarMawbBL").change(function(e) {
		e.preventDefault();
		if($('#rdb_BuscarMawbBL').prop('checked'))
		{
			$('#nro_factura').prop('placeholder','Ingrese Documento Embarque..');
			$('#TituloModalFactura').html('Ingresar Documento Embarque');
			$('#TituloPanelIngresoFacturas').html('INGRESO DE MULTIPLES DOCUMENTOS DE EMBARQUES');
			$('#TipoDocumentoBusqueda').val('MawbBL');
			$('#txtNumeroFacturas').val('');
		}
    });
	
	
	$("#btn_selec").attr("disabled",true);
	$("#btn_deselec").attr("disabled",true);
	//$("#btn_ingreso_factura").attr("disabled",true);
	$("#btn_tabla_generar_entrega").attr("disabled",true);
	$("#btn_tabla_ActualizarEE").attr("disabled",true);
});
function DetenerCargaActualizarEE()
{
	$("#archivo_Actualizar_EE").css("display","inline");
	$("#boton_subir_archivo_actualizar_ee").css("display","inline");	
	$("#ImagenCargandoCargaActualizaEE").css("display","none");
	$("#archivo_Actualizar_EE").attr("disabled", false );
	
	$("#DivOpcionesActualizarEE").show();
	
	$("#alertSuccessAEE").delay(2000).hide("fade");
	$("#alertDangerAEE").delay(2000).hide("fade");
	
	//$("#btn_CancelarCsv").trigger("click");	
}
function resultadoErroneoActualizarEE(Error)
{
	$('#alertDangerAEE').show('fade');
	
	var htmlRes=$("#alertDangerAEE").html();
	if($.trim($("#alertDangerAEE").html())=="")
	{
		$("#alertDangerAEE").html(Error);
	}
	else{
		$("#alertDangerAEE").html(htmlRes+'<br />'+Error);
	}
}
function LogRegistroActualizarEE(Tipo,Mensaje)
{
	alert(Mensaje);
}
function resultadoCorrectoActualizarEE(Mensaje)
{
	$('#alertSuccessAEE').show('fade');
	
	$("#alertSuccessAEE").html(Mensaje);
	
	$("#btn_ActualizarEE").hide();
	
	cargarFacturas2(false);
	
}
function seleccionarTodasEE()
{
	$("input[name=gee_chk]").each(function(){
		$(this).prop('checked', true);
		$(this).parents('tr').removeAttr('class');
		$(this).parents('tr').attr('class', 'selected'); 
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

function cargarEditarFactura(invoiceNumber, invoiceDate)
{
		$.ajax({
            data:  {invoice: invoiceNumber},	
            url:   'editar_edi810.php',
            type:  'post',
            beforeSend: function () {
                $("#AbrirCargando").trigger("click");
				$("#btn_inicio").css("display","none");
            },
             success:  function (response) {
			 $("#div_detalle").load("editar_edi810.php",{
															invoice: invoiceNumber, 
															invoiceDate: invoiceDate
														}, function(response, status, xhr) {
                          if (status == "error") {
                            var msg = "Error!, algo ha sucedido: ";
                            $("#div_detalle").html(msg + xhr.status + " " + xhr.statusText);
                          }
		
                        });
			$("#div_detalle").show();
			$("#wrapper").hide();
			
             },
			 complete: function(){
			 }
	});
		$("#wrapper").hide();
}
function IngresarFactura810()
{
		$.ajax({
            data:  {invoice: ""},	
            url:   'editar_edi810.php',
            type:  'post',
            beforeSend: function () {
                $("#AbrirCargando").trigger("click");
            },
             success:  function (response) {
			 $("#div_detalle").load("editar_edi810.php",{invoice:""}, function(response, status, xhr) {
                          if (status == "error") {
                            var msg = "Error!, algo ha sucedido: ";
                            $("#div_detalle").html(msg + xhr.status + " " + xhr.statusText);
                          }
		
                        });
			$("#div_detalle").show();
			$("#wrapper").hide();
			
             },
			 complete: function(){
				//CerrarThickBox();
			 }
		});
		$("#wrapper").hide();
		
	
}

function ActualizarEE(JsonAEE)
{
	$.ajax({
            data:  {
					accion: "ActualizarEE",
					JsonAEE:JsonAEE
					},	
            url:   'ajax/edi810.php',
            type:  'post',
            beforeSend: function () {
				$("#ImagenCargandoAEE").css("display","inline");
				$("#btn_CancelarAEE").css("display","none");
				$("#btn_ActualizarEE").css("display","none");
				$("#alertSuccessAEE").css("display","none");
            },
             success:  function (response) {
				var json 	= jQuery.parseJSON(response);
				var mensajeCorrecto="";
				
				$.each(json, function(i, d) {
					$("#tr_"+d.InvoiceNumber+"").css("display","none");
					mensajeCorrecto="EDI 810 ("+d.InvoiceNumber+") actualizado correctamente<br/>";
				});
				
				if(mensajeCorrecto!="")
				{
					$("#alertSuccessAEE").css("display","block");				
					$("#alertSuccessAEE").html("");
					var htmlMensaje="<strong>¡Éxito!</strong>&nbsp  "+mensajeCorrecto;
					$("#alertSuccessAEE").html(htmlMensaje);
				}
             },
			 complete: function(){
				$("#ImagenCargandoAEE").css("display","none");
				$("#btn_CancelarAEE").css("display","inline");
				$("#btn_ActualizarEE").css("display","inline");
				cargarFacturas2(false);
			 }
	});
}

function IngresarFactura810_2()
{		
	$("#AbrirCargando").trigger("click");
	$("#div_detalle").load("editar_edi810.php",{invoice: ''	}, 
	function(response, status, xhr) {
				if (status == "error") {
					var msg = "Error!, algo ha sucedido: ";
					$("#div_detalle").html(msg + xhr.status + " " + xhr.statusText);
				}
	});
	$("#div_detalle").show();
	$("#wrapper").hide();
}

function verDetalle(invoice) {
		var d = document.frm_cabecera
		d.invoice.value = invoice
		d.submit();
}

function select_all() 
{	
	$("#AbrirCargando").trigger("click");
	$("[class=check_ee]").each(function(){
			$(this).prop('checked', true);
			$(this).parents('tr').removeAttr('class');
			$(this).parents('tr').attr('class', 'selected'); 
	});
	CerrarThickBox();
}

function unselect_all() 
{
	$("#AbrirCargando").trigger("click");
	$("[class=check_ee]").each(function(){
			$(this).prop('checked', false);
			$(this).parents('tr').removeAttr('class');
	});
	CerrarThickBox();
}

function buscarFacturas() 
{
		$("#btn_buscar").attr("disabled", true );
		$("#Cargando").dialog("open");
		//cargaFacturas(true,false);
		cargarFacturas2(true);
}

function cargarFacturas2(cargando)
{
	$('#div_tabla_cabecera').html("");
	$("#btn_selec").attr("disabled",true);
	$("#btn_deselec").attr("disabled",true);
	$("#btn_tabla_generar_entrega").attr("disabled",true);
	if(cargando==true)
	{
		$("#AbrirCargando").trigger("click");
	}
	////////////////////////////////////////////////////////////////////
	var accion 			= 'cargarFacturas2';
	var sociedad 		= $("#sociedad").val();
	var estatus_ee 		= $("#estatus_ee").val();
	var fechaInicio 	= $("#fecha1_cabecera").val();
	var fechaTermino 	= $("#fecha2_cabecera").val();
	var factura			= $("#nro_factura").val();
	var TipoDocumentoBusqueda = $("#TipoDocumentoBusqueda").val();
	var facturas 		= '';
	/////////// NROS DE FACTURAS ///////////////////////////////////////

	var textArea=$('#txtNumeroFacturas').val();
	
	var value = textArea.replace(/\r?\n/g, "|");
	facturas=value;
	
	if(factura != "") {
		facturas += factura;
	}
		
	//////////////////////////////////////////////////////////////////
	$.post('ajax/edi810.php',{
								accion		: accion, 
								sociedad	: sociedad, 
								estatus_ee	: estatus_ee, 
								fechaInicio	: fechaInicio, 
								fechaTermino: fechaTermino,
								facturas	: facturas,
								TipoDocumentoBusqueda	: TipoDocumentoBusqueda 
							 }, function(response) {
	}).done(function(response) {
		var json 	= jQuery.parseJSON(response);
		var count 	= 0;
		var content = '';
		content += '<table id="tabla_facturas" class="display table-condensed" cellspacing="0" width="100%">';
		content += '<thead><tr>';
		content += '	<th>Invoice Number</th><th>Invoice Date</th><th>Invoice Currency</th><th>Invoice Net Value</th>';
		content += '	<th>Invoice Gross Value</th><th>Invoice Gastos</th><th>Invoice Vendor</th><th>Sociedad</th>';
		content += '	<th>Status EE</th><th>EE</th><th>856</th>';
		content += '</tr></thead><tbody>';
					
		$.each(json, function(i, d) {	
			content += '<tr id="'+d.InvoiceNumber+'" style="cursor:pointer">';
			content += '<td onclick="cargarEditarFactura(\''+ d.InvoiceNumber +'\', \''+ d.InvoiceDate +'\')">'+d.InvoiceNumber+'</td>';
			
			var invoicedate='';
			var FechaNumerica=0;
			try {
				var date 		= d.InvoiceDate.split('-');
				var year_time 	= date[2].split(' ');
				var year 		= year_time[0];
				var mes 		= date[1];
				var dia 		= date[0];
				invoicedate 	= dia+'-'+mes+'-'+year;
				var FechaObj 	= new Date(year+"/"+mes+"/"+dia+" 00:00:00");
				FechaNumerica 	= FechaObj.getTime() / 1000;
			}
			catch(err) {
				
			}
			
			var NetValue   = parseFloat(d.InvoiceNetValue);
				NetValue   = NetValue + 0.00;
				
			var GrossValue = parseFloat(d.InvoiceGrossValue);
				GrossValue = GrossValue + 0.00;
				
			var GastosVal  = parseFloat(d.InvoiceGastos);
				GastosVal  = GastosVal+ 0.00;
			
			content += '<td align="center" data-order="'+FechaNumerica+'" >'+invoicedate+'</td>';
			content += '<td align="center" >'+d.InvoiceCurrency+'</td>';
			content += '<td align="center" >'+NetValue.toFixed(2)+'</td>';
			content += '<td align="center" >'+GrossValue.toFixed(2)+'</td>';
			content += '<td align="center" >'+GastosVal.toFixed(2)+'</td>';
			content += '<td align="center" >'+d.InvoiceVendor+'</td>';
			content += '<td align="center" >'+d.Sociedad+'</td>';
			
			var EntregaEntrante = 0;
			try
			{EntregaEntrante=parseInt(d.EntregaEntrante);
			}catch(err){}
			
			switch(EntregaEntrante) {
				case 1:
					content += '<td align="center"><img src="../../images/tick_16.png"></td>';	
					content += '<td >&nbsp</td>';
					break;
				case 2:
					content += '<td align="center"><img src="../../images/warning_16.png" width="13" height="13"></td>';
					content += '<td align="center"><input type="checkbox" name="EE" id="EE" class="check_ee" value="'+d.InvoiceNumber+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
					break;
				case 2:
					content += '<td align="center"><img src="../../images/warning_16.png" width="13" height="13"></td>';
					content += '<td align="center"><input type="checkbox" name="EE" id="EE" class="check_ee" value="'+d.InvoiceNumber+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
					break;
				case 3:
					content += '<td align="center"><img src="../../images/delete_16.png" width="13" height="13"></td>';	
					content += '<td align="center"><input type="checkbox" name="EE" id="EE" class="check_ee" value="'+d.InvoiceNumber+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
					break;
				default:
					content += '<td align="center"><img src="../../images/delete_16.png" width="13" height="13"></td>';	
					content += '<td align="center"><input type="checkbox" name="EE" id="EE" class="check_ee" value="'+d.InvoiceNumber+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
			}
			
			if (d.Estado856 == "Nok") {
				content += '<td align="center" title="'+d.Estado856+'" data-order="0">';
				content += '<img src="../../images/delete_16.png" width="13" height="13">';
				content += '</td>';	
			}else {
				content += '<td align="center" title="'+d.Estado856+'" data-order="1">';
				content += '<img src="../../images/tick_16.png" width="13" height="13">';
				content += '</td>';
			}
			content += '</tr>';
			count++;
			
		});
		content += '</tbody><tfoot><tr>';
		content += '<th>Invoice Number</th><th>Invoice Date</th><th>Invoice Currency</th><th>Invoice Net Value</th>';
		content += '<th>Invoice Gross Value</th><th>Invoice Gastos</th><th>Invoice Vendor</th><th>Sociedad</th>';
		content += '<th>Status EE</th><th>EE</th><th>856</th>';
		content += '</tr></tfoot></table>';
				
		$('#div_tabla_cabecera').html(content);
		
		$('#tabla_facturas').dataTable({
		  "scrollY": "200px",
		  "scrollCollapse": true,
		  "paging": false,
		  "language": LAN_ESPANOL
		});
		var tHeader= $('#tabla_facturas').find('thead');
		$(tHeader).css("display","none");
		
		$("#btn_buscar").attr("disabled", false );   
		
		
		if(count > 0) {
			$("#btn_selec").attr("disabled",false);
			$("#btn_deselec").attr("disabled",false);
			$("#btn_tabla_generar_entrega").attr("disabled",false);
			$("#btn_tabla_ActualizarEE").attr("disabled",false);			
		}
		if(cargando==true)
		{
			CerrarThickBox();
		}
		dejarFacturasSelecionadasAlGEE();
	});
}

function lineasIngresoFactura(accion, cantidad) 
{	
	$('#tabla_nro_facturas tbody').html("");
	var content = "";
	var count = 1;
	if(accion == "add")	var count = 1;
	
	for ( var i = count+1; i <= (count+cantidad); i++ ) {
		content += '<tr  style="cursor:pointer">';
		content += '	<td ><input class="form-control" id="linea_'+count+'" name="linea_'+count+'"></td>';
		content += '</tr>';
	}
	$('#tabla_nro_facturas tbody').html(content);
}

function lineasIngresoFacturaAgregar(cantidad) {		
	var content = "";			
	for ( var i = 0; i < (cantidad); i++ ) {
		content += '<tr  style="cursor:pointer">';
		content += '	<td ><input class="form-control" id="linea_'+cantidad+'" name="linea_1"></td>';
		content += '</tr>';
	}
	$('#tabla_nro_facturas tbody').append(content);
}
function GenerarAgrupacionSugerida(json)
{
	var RegistrosAgrupacion = new Array();
	var LineaAgrupacion = new Array();
	var MtzLineas = new Array();
	var MaximoSociedad=999;
	
	var RegistroAgrupacionBulto=0;
	
	$.each(json, function(i, d) {
		try {MaximoSociedad=parseInt(d.Sociedad_lineas);}catch(err) {}
		//alert(MaximoSociedad);
		var Etiqueta=''+d.Centro+'_'+d.PONumber.substring(10,12)+'_'+d.Incoterms;
		
		if(String(d.ID_Contenedor)!='')
		{
			Etiqueta+='_'+d.ID_Contenedor;
		}
		
		//Variables para validar el peso
		var PesoBulto=0;
		var PesoSociedad=0;
		
		//Variables para validar el largo ancho o alto
		var LargoBulto=0;
		var AnchoBulto=0;
		var AltoBulto=0;
		var LargoSociedad=0;
		var AnchoSociedad=0;
		var AltoSociedad=0;
		var Lineas=0;
		
		try {Lineas=parseInt(d.Lineas);}catch(err) {}
		//Convertir a float peso
		try {
			PesoBulto=parseFloat(d.peso);
			PesoSociedad=parseFloat(d.sociedad_peso);
		}
		catch(err) {}
		
		//Validar si tiene registrada la sociedad
		if(d.sociedad_urgencia == null)
		{
			if (RegistrosAgrupacion[Etiqueta+'_'+d.PONumber.substring(10,12)] == null)
			{
				RegistroAgrupacionBulto++;
				RegistrosAgrupacion[Etiqueta+'_'+d.PONumber.substring(10,12)]=numberToColumnName(RegistroAgrupacionBulto);
				MtzLineas[Etiqueta+'_'+d.PONumber.substring(10,12)]=Lineas;
				LineaAgrupacion[i]=numberToColumnName(RegistroAgrupacionBulto);
			}
			else
			{
				//Validar si excede el maximo de lineas
				if((MtzLineas[Etiqueta+'_'+d.PONumber.substring(10,12)]+Lineas)>MaximoSociedad)
				{
					//Se separa el packing por excer lo permitido
					RegistroAgrupacionBulto++;
					RegistrosAgrupacion[Etiqueta+'_'+d.PONumber.substring(10,12)]=numberToColumnName(RegistroAgrupacionBulto);
					MtzLineas[Etiqueta+'_'+d.PONumber.substring(10,12)]=Lineas;
					LineaAgrupacion[i]=numberToColumnName(RegistroAgrupacionBulto);
				}
				else
				{
					//Se mantiene
					MtzLineas[Etiqueta+'_'+d.PONumber.substring(10,12)]+=Lineas;
					LineaAgrupacion[i]=RegistrosAgrupacion[Etiqueta+'_'+d.PONumber.substring(10,12)];
				}
			}
			return true;
		}
		
		//Validar peso
		if(PesoBulto>=PesoSociedad)
		{
			//Se excedio el maximo debe ir en otro grupo
			if (RegistrosAgrupacion[Etiqueta+'_'+d.idenBulto] == null)
			{
				RegistroAgrupacionBulto++;
				RegistrosAgrupacion[Etiqueta+'_'+d.idenBulto]=numberToColumnName(RegistroAgrupacionBulto);
				MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]=Lineas;
				LineaAgrupacion[i]=numberToColumnName(RegistroAgrupacionBulto);
			}
			else
			{
				//Validar si excede el maximo de lineas
				if((MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]+Lineas)>MaximoSociedad)
				{
					//Se separa el packing por excer lo permitido y ya fue aumentado en el ciclo anterior
					RegistroAgrupacionBulto++;
					RegistrosAgrupacion[Etiqueta+'_'+d.idenBulto]=numberToColumnName(RegistroAgrupacionBulto);
					MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]=Lineas;
					LineaAgrupacion[i]=numberToColumnName(RegistroAgrupacionBulto);
				}
				else
				{
					//Se mantiene
					MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]+=Lineas;
					LineaAgrupacion[i]=RegistrosAgrupacion[Etiqueta+'_'+d.idenBulto];
				}
			}
			return true;
		}
		
		//Convertir a float dimensiones
		try {
			LargoBulto=parseFloat(d.longitud);
			AnchoBulto=parseFloat(d.ancho);
			AltoBulto=parseFloat(d.alto);
			
			LargoSociedad=parseFloat(d.sociedad_longitud);
			AnchoSociedad=parseFloat(d.sociedad_ancho);
			AltoSociedad=parseFloat(d.sociedad_alto);
		}
		catch(err) {}
		
		//Validar dimensiones
		if(LargoBulto>=LargoSociedad || AnchoBulto>=AnchoSociedad || AltoBulto>=AltoSociedad || isNaN(LargoSociedad) || isNaN(AnchoSociedad) || isNaN(AltoSociedad))
		{
			//Se excedio el maximo debe ir en otro grupo
			if (RegistrosAgrupacion[Etiqueta+'_'+d.idenBulto] == null)
			{
				RegistroAgrupacionBulto++;
				RegistrosAgrupacion[Etiqueta+'_'+d.idenBulto]=numberToColumnName(RegistroAgrupacionBulto);
				MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]=Lineas;
				LineaAgrupacion[i]=numberToColumnName(RegistroAgrupacionBulto);
			}
			else
			{
				//Validar si excede el maximo de lineas
				if((MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]+Lineas)>MaximoSociedad)
				{
					//Se separa el packing por excer lo permitido
					RegistroAgrupacionBulto++;
					RegistrosAgrupacion[Etiqueta+'_'+d.idenBulto]=numberToColumnName(RegistroAgrupacionBulto);
					MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]=Lineas;
					LineaAgrupacion[i]=numberToColumnName(RegistroAgrupacionBulto);
				}
				else
				{
					//Se mantiene
					MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]+=Lineas;
					LineaAgrupacion[i]=RegistrosAgrupacion[Etiqueta+'_'+d.idenBulto];
				}
			}
			return true;
		}
		
		
		//Normal
		if (RegistrosAgrupacion[Etiqueta+'_ok'] == null)
		{
			RegistroAgrupacionBulto++;
			RegistrosAgrupacion[Etiqueta+'_ok']=numberToColumnName(RegistroAgrupacionBulto);
			MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]=Lineas;
			LineaAgrupacion[i]=numberToColumnName(RegistroAgrupacionBulto);
		}
		else
		{
			//Validar si excede el maximo de lineas
			if((MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]+Lineas)>MaximoSociedad)
			{
				//Se separa el packing por excer lo permitido
				RegistroAgrupacionBulto++;
				RegistrosAgrupacion[Etiqueta+'_'+d.idenBulto]=numberToColumnName(RegistroAgrupacionBulto);
				MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]=Lineas;
				LineaAgrupacion[i]=numberToColumnName(RegistroAgrupacionBulto);
			}
			else
			{
				//Se mantiene
				MtzLineas[numberToColumnName(RegistroAgrupacionBulto)]+=Lineas;
				LineaAgrupacion[i]=numberToColumnName(RegistroAgrupacionBulto);
			}
		}
	});
	return LineaAgrupacion;
}
function RecorrerCheckGEE()
{
	var ArrayDeGEE = new Array();
	var JSON_ArrayDeGEE = "";
	var tbody="";
	
	var chekeados=0;
	
	//MostrarTodasLasFacturasDeLaTabla();
	
	$('[class=check_ee]').each(function(){
        if($(this).is(':checked')){
					var item = {
						"InvoiceNumber": $(this).val()			
					};	
                    ArrayDeGEE.push(item);
					chekeados++;
                }
    });
	
	//DejarTablaFacturasDininamicaNormal();
		
	JSON_ArrayDeGEE = JSON.stringify({ArrayDeGEE: ArrayDeGEE});
	$("#JSON_ArrayDeGEE").html(JSON_ArrayDeGEE);
	
	var JSONPONumber_InvoiceNUmberResponse;
	
	var AbrirModal=false;
	
	if(chekeados>0)
	{
		limpiarGee();
		
		$.ajax({
				data:  {
						accion: 'cargarTablaGEE',
						JSON_ArrayDeGEE: JSON_ArrayDeGEE,
						sociedad: $("#sociedad").val()
				},	
				url:   'ajax/edi810.php',
				type:  'post',
				beforeSend: function () {
					$("#AbrirCargando").trigger("click");
				},
				 success:  function (response) {
					//alert(response);
					if(response=="proveedor no unico")
					{
						alert("Las facturas seleccionadas tiene diferentes Proveedores");
						CerrarThickBox();
						return;
					}
					
					if(response=="transporte no registrado")
					{
						alert("No se encontraron registros de transporte de las facturas seleccionadas");
						CerrarThickBox();
						return;
					}
					
					if(response=="transporte no unico")
					{
						alert("Las facturas seleccionadas tienen diferente tipo de transporte");
						CerrarThickBox();
						return;
					}
					
					$('#tabla_EE').dataTable().fnDestroy();
					$('#tdBodyEE').html('');
					 
					json=jQuery.parseJSON(response);
					
					AbrirModal=true;
					
					var AgrupacionSugerida=GenerarAgrupacionSugerida(json);
					var HabilitarMaritimo=false;
					
					$.each(json, function(i, d) {
						
						var adicional=false;
						
						var Etiqueta=''+d.Centro+'_'+d.PONumber.substring(10,12)+'_'+d.Incoterms;
						if(String(d.ID_Contenedor)!='')
						{
							Etiqueta+='_'+d.ID_Contenedor;
						}
						tbody+='<tr>';
						tbody+='<td style="width:8%">'+d.InvoiceNumber+'</td>';
						tbody+='<td style="width:8%">'+d.PONumber+'</td>';
						tbody+='<td style="width:8%">'+d.Centro+'</td>';
						tbody+='<td style="width:8%">'+d.Incoterms+'</td>';
						tbody+='<td style="width:5%">'+d.PONumber.substring(10, 12)+'</td>';
						tbody+='<td style="width:8%" data-order="'+d.idenBulto+'"><label style="cursor: pointer" onClick="CargaDetalleCompletoBulto(event,this)">'+d.idenBulto+'</label></td>';
						tbody+='<td style="width:8%">'+d.peso+'</td>';
						tbody+='<td style="width:8%">'+d.longitud+'</td>';
						tbody+='<td style="width:8%">'+d.ancho+'</td>';
						tbody+='<td style="width:8%">'+d.alto+'</td>';
						tbody+='<td style="width:8%">'+d.Lineas+'</td>';
						if(d.ship_transport=="M")
						{
							tbody+='<td style="width:8%">'+d.ID_Contenedor+'</td>';
							HabilitarMaritimo=true;
						}
						else
						{
							tbody+='<td style="width:8%;display:none">0</td>';
						}
						
						var Agrupacion=AgrupacionSugerida[i];
						
						tbody+='<td style="width:8%" data-order="'+Agrupacion+'"><input type="text" style="width:50%;text-align: center;text-transform: uppercase" name="Agrup" id="'+d.InvoiceNumber+'_'+d.PONumber+'" idbulto="'+d.idenBulto+'" value="'+Agrupacion+'" required="required"/></td>';
						tbody+='<td style="width:5%"><input type="checkbox" id="gee_'+d.InvoiceNumber+'" volumenbulto="'+d.volumen+'" name="gee_chk" value="'+d.PONumber.substring(10, 12)+'" onchange="javascript:CambiarAlClicChk(this);" /></td>';
						tbody+='</tr>';
						
						$('#Gee_IdProveedor').val(d.InvoiceVendor);
					});
					
					if(HabilitarMaritimo)
					{
						$('#ThIdContenedor').show();
						$('#H_GEE_Maritimo').val('1');
					}
					else
					{
						$('#ThIdContenedor').hide();
						$('#H_GEE_Maritimo').val('0');
					}
					 
					$('#tdBodyEE').html(tbody);
					CerrarThickBox();
						
				 },
				 complete: function(){
					if(AbrirModal)
					{
						$("#btn_GEE").trigger("click");
						setTimeout(function(){
							$('#tabla_EE').dataTable({
								"scrollY":        "200px",
								"scrollCollapse": true,
								"paging":         false,
								"language": LAN_ESPANOL
							});
							setTimeout(function(){
								$("#DivGee").css("visibility", "");
							}, 250);
						}, 750);
						seleccionarTodasEE();
					}
				}
		});
	}else{
	alert("No se ha seleccionado ninguna factura para generar Entrega Entrante ");
	}

}
function RecorrerCheckActualizarEE()
{
	var ArrayDeGEE = new Array();
	var JSON_ArrayDeGEE = "";
	
	var chekeados=0;
	
	$('[class=check_ee]').each(function(){
        if($(this).is(':checked')){
					var item = {
						"InvoiceNumber": $(this).val()			
					};	
                    ArrayDeGEE.push(item);
					chekeados++;
                }
    });
		
	JSON_ArrayDeGEE = JSON.stringify({ArrayDeGEE: ArrayDeGEE});
	var JSONPONumber_InvoiceNUmberResponse;
	
	if(chekeados>0)
	{
		limpiarGee();
		var tbody="";
		
		$.ajax({
				data:  { 
						accion: 'cargarTablaGEE',
						JSON_ArrayDeGEE: JSON_ArrayDeGEE,
						sociedad: $("#sociedad").val()
						},	
				url:   'ajax/edi810.php',
				type:  'post',
				beforeSend: function () {
					$("#AbrirCargando").trigger("click");
				},
				 success:  function (response) {
					//alert(response);
					
					$('#tabla_AEE').dataTable().fnDestroy();
					$('#tdBodyAEE').html('');
					 
					json=jQuery.parseJSON(response);
					
					var RegistrosAgrupacion = new Array();
					
					 $.each(json, function(i, d) {
						
						var Etiqueta=d.InvoiceNumber+'_'+d.PONumber+'_'+d.Centro;
						
						if (RegistrosAgrupacion[Etiqueta] == null)
						{
							tbody+='<tr id="tr_'+d.InvoiceNumber+'">';
							tbody+='<td align="center" width="10">'+d.InvoiceNumber+'</td>';
							tbody+='<td align="center" width="20">'+d.PONumber+'</td>';
							tbody+='<td align="center" width="10">'+d.Centro+'</td>';
							tbody+='<td align="center" width="60"><input type="number" id="aee_'+d.InvoiceNumber+'" name="aee_chk" value=""/></td>';
							tbody+='</tr>';
							
							//Registrando Etiqueta
							RegistrosAgrupacion[Etiqueta]=Etiqueta;
						}
					 });
					 
					$('#tdBodyAEE').html(tbody);
						
					setTimeout(function(){
							$('#tabla_AEE').dataTable({
								"scrollY":        "200px",
								"scrollCollapse": true,
								"paging":         false,
								"language": LAN_ESPANOL
							});
						}, 250);
					
					CerrarThickBox();
				 },
				 complete: function(){
					
					$("#btn_AEE").trigger("click");
				}
		});
	}else{
		alert("No se ha seleccionado ninguna factura para generar Entrega Entrante ");
	}
}
  function getRutasWS()
  {
     var data_ws = [];
	 var comboBox='<select id="HcboRuta" name="HcboRuta">';
	 comboBox+='<option value=""></option>';
	 
     $.post("ajax/ws_buscarRuta.php", {}, function(response) {})
     .done(function(response){
     var json = jQuery.parseJSON(response);
	 
						
     $(function () {
                   $.each(json, function(i, d) {
                          data_ws.push({value:d.ROUTE, label:d.BEZEI+'(' + d.ROUTE +')', id:'selRuta_'+d.ROUTE});
						  comboBox+='<option value="'+d.ROUTE+'">'+d.BEZEI+'(' + d.ROUTE +')'+'</option>';
                          });
						  comboBox+='</select>';
						 $("#DivRutasH").html(comboBox);
					});
					
     })
     return data_ws;
  }
  function GenerarGEE()
  {	  
		var ArrayDeGEE = {};
		var JSON_ArrayDeGEE = "";
		var correcta=false;
		
		var JsonRespuesta;
		
		var chekeados=false;		
		
		$('[name=gee_chk]').each(function(){
			if($(this).is(':checked')){
				
				//Etiqueta es el grupo
				var Etiqueta='';
				
				//Si es que es maritimo
				if($('#H_GEE_Maritimo').val()=='1')
				{
					Etiqueta=$(this).parent('td').parent('tr').find('td:eq(13) input').val();
					
				}
				else
				{
					Etiqueta=$(this).parent('td').parent('tr').find('td:eq(12) input').val();
				}
				
				var item = {
					"InvoiceNumber": $(this).parent('td').parent('tr').find('td:eq(0)').html(),
					"PONumber": $(this).parent('td').parent('tr').find('td:eq(1)').html(),
					"Centro": $(this).parent('td').parent('tr').find('td:eq(2)').html(),
					"Bulto": $(this).parent('td').parent('tr').find('td:eq(5) label').html(),
					"Peso": $(this).parent('td').parent('tr').find('td:eq(6)').html(),
					"Volumen": $(this).attr('volumenbulto'),
					"Alto": $(this).parent('td').parent('tr').find('td:eq(9)').html(),
					"Largo": $(this).parent('td').parent('tr').find('td:eq(7)').html(),
					"Ancho": $(this).parent('td').parent('tr').find('td:eq(8)').html(),
					"InvoicePosition": ""
				};
				
				
				if(ArrayDeGEE[Etiqueta] == null)
				{
					var Contenedor = new Array();
					Contenedor.push(item);
					ArrayDeGEE[Etiqueta]=Contenedor;
				}
				else
				{
					//alert(ArrayDeGEE[Etiqueta].length);
					ArrayDeGEE[Etiqueta].push(item);
				}
				chekeados=true;
			}
		});
		
		
		if(chekeados)
		{
				var errorRuta=0;
				
				if($("#txtIdRuta").val()=="")
				{
					$("#SeleccionRuta").html("");
					$("#Gee_txtRuta").val("");
					errorRuta++;
				}
				
				if(errorRuta>0)
				{
					$("#btnValidarGee").trigger("click");
					return;
				}
				else
				{
					
					bloquearOpcionesEE();
					
					$("#ImagenCargando").css("display","inline");
					$("#btn_CancelarEE").css("display","none");
					$("#btn_generararEE").css("display","none");
					
					$("#alertDanger").css("display","none");										
					$("#alertDanger").html("");
					
					$("#alertWarning").css("display","none");										
					$("#alertWarning").html("");

					$("#alertSuccess").css("display","none");										
					$("#tBodySucessEE").html("");
					
					$("#alertSuccessMensaje").css("display","none");										
					$("#alertSuccessMensaje").html("");
					
					setTimeout(function () {
					$.each(Object.keys(ArrayDeGEE), function(index, key) {
						//alert( index + ": " + value );
						
						JSON_ArrayDeGEE = JSON.stringify({ArrayDeGEE: ArrayDeGEE[key]});
						
						//Generando EE
						$.ajax({
								data:  {JSON_ArrayDeGEE: JSON_ArrayDeGEE,
										CantidadBulto: $("#Gee_txtCantBulto").val(),
										CartaPorte: $("#Gee_txtCartaPorte").val(),
										ETA: $("#Gee_txtEta").val(),
										Ruta: $("#Gee_txtRuta").val(),
										Sociedad: $("#sociedad").val()
										},
								url:   'ajax/ws_CrearEE.php',
								type:  'post',
								async: false,
								beforeSend: function () {
									
								},
								success:  function (response) {
									//alert("WS :"+response);									
									JsonRespuesta = jQuery.parseJSON(response);
									
									$.each(JsonRespuesta, function(i, d) {
										if(d.TYPE=="E")
										{
											correcta=false;
											$("#alertDanger").css("display","block");										
											var htmldiv=$("#alertDanger").html();
											var htmlMensaje="<strong>¡Error!</strong>&nbsp "+d.MESSAGE;
											$("#alertDanger").html(htmldiv+htmlMensaje+"<br/>");
										}
										
										if(d.TYPE=="W")
										{
											correcta=false;
											//alert("alerta encontrado: ");
											$("#alertWarning").css("display","block");										
											var htmldiv=$("#alertWarning").html();
											var htmlMensaje="<strong>¡Advertencia!</strong>&nbsp  "+d.MESSAGE;
											$("#alertWarning").html(htmldiv+htmlMensaje+"<br/>");
										}
									
										if(d.TYPE=="S" && d.LOG_NO=="MSG")
										{
											correcta=false;
											//alert("alerta encontrado: ");
											$("#alertSuccessMensaje").css("display","block");										
											var htmldiv=$("#alertSuccessMensaje").html();
											var htmlMensaje=""+d.MESSAGE;
											$("#alertSuccessMensaje").html(htmldiv+htmlMensaje+"<br/>");
										}
										
										if(d.TYPE=="S" && d.LOG_NO=="EE")
										{
											correcta=true;
											//alert("alerta encontrado: ");
											$("#alertSuccess").css("display","block");										
											//var htmldBody=$("#alertSuccess").html();
											//var htmlMensaje="<strong>¡Éxito!</strong>&nbsp  "+d.MESSAGE;
											var filaHtml='<tr>';
												filaHtml+='<td>'+$("#Gee_txtCartaPorte").val()+'</td>';
												filaHtml+='<td>'+d.NumeroFactura+'</td>';
												filaHtml+='<td>'+d.PONumber+'</td>';
												filaHtml+='<td>'+d.Centro+'</td>';
												filaHtml+='<td>'+d.EE+'</td>';
												filaHtml+='<td>'+d.FECHA+'</td>';
												filaHtml+='</tr>';												
											
											$('#tBodySucessEE').append(filaHtml);
											
										}
									});
												
									//Elimnando los checkCompletos
									$.each(JsonRespuesta, function(i, d) {
										
										if(d.TYPE=="S" && d.LOG_NO=="EE")
										{
											$('[name=gee_chk]').each(function(){
												if($(this).is(':checked'))
												{	
													var valorEnCelda=String($(this).parent('td').parent('tr').find('td:eq(0)').html());
													var valorPONumber=String($(this).parent('td').parent('tr').find('td:eq(1)').html());
													
													var numeroFacturaRetorno=String(d.NumeroFactura);
													var PONumberRetorno=String(d.PONumber);
													
													var Agrupacion='';
													
													if($('#H_GEE_Maritimo').val()=='1')
													{
														Agrupacion=$(this).parent('td').parent('tr').find('td:eq(13) input').val();
														
													}
													else
													{
														Agrupacion=$(this).parent('td').parent('tr').find('td:eq(12) input').val();
													}
													
													if(valorEnCelda==numeroFacturaRetorno && valorPONumber==PONumberRetorno && Agrupacion==key)
													{
														$(this).parent('td').parent('tr').hide();
														//$("#gee_"+numeroFacturaRetorno).prop("checked", "");
														$(this).prop("checked", "");
														$(this).parents('tr').removeAttr('class');
													}
												}
											});
										}
									});
								},
								complete: function(){
									
								},
								timeout:0
						});//Ajac crear EE
						
					});//$.each(Object.keys(ArrayDeGEE)
					
					desbloquearOpcionesEE();
					$("#ImagenCargando").css("display","none");
					$("#btn_CancelarEE").css("display","inline");
					$("#btn_generararEE").css("display","inline");
					cargarFacturas2(false);
					
					}, 750);
				}
		}else
		{
			alert("Para generar EE tiene que seleccionar al menos un registro");
		}
  }
  function bloquearOpcionesEE()
  {
	$("#btn_EESelect").attr("disabled",true);
	$("#btn_EEDeselc").attr("disabled",true);
	$("#btnBuscarMaterial").attr("disabled",true);
	$("#btn_CancelarEE").attr("disabled",true);
	$("#btn_generararEE").attr("disabled",true);
	
	$("#Gee_txtCantBulto").attr("disabled",true);
	$("#Gee_txtCartaPorte").attr("disabled",true);
	$("#Gee_txtEta").attr("disabled",true);
	$("#Gee_txtRuta").attr("disabled",true);
	$("#txtBuscarMaterialGee").attr("disabled",true);
	
	var CampoSearch= $('#tabla_EE_filter').find('label input');
	$(CampoSearch).attr("disabled",true);
				
	$('[name=gee_chk]').each(function(){
		$(this).attr("disabled",true);
	});
	
	$('[name=Agrup]').each(function(){
		$(this).attr("disabled",true);
	});
  }
  
  function desbloquearOpcionesEE()
  {
	$("#btn_EESelect").attr("disabled",false);
	$("#btn_EEDeselc").attr("disabled",false);
	$("#btnBuscarMaterial").attr("disabled",false);
	$("#btn_CancelarEE").attr("disabled",false);
	$("#btn_generararEE").attr("disabled",false);
	
	$("#Gee_txtCantBulto").attr("disabled",false);
	$("#Gee_txtCartaPorte").attr("disabled",false);
	$("#Gee_txtEta").attr("disabled",false);
	$("#Gee_txtRuta").attr("disabled",false);
	$("#txtBuscarMaterialGee").attr("disabled",false);
	
	var CampoSearch= $('#tabla_EE_filter').find('label input');
	$(CampoSearch).attr("disabled",false);
				
	$('[name=gee_chk]').each(function(){
		$(this).attr("disabled",false);
	});
	
	$('[name=Agrup]').each(function(){
		$(this).attr("disabled",false);
	});
  }
  function limpiarGee()
  {
	$("#ImagenCargando").css("display","none");
	$("#btn_CancelarEE").css("display","inline");
	$("#btn_generararEE").css("display","inline");
	
	$("#Gee_txtCantBulto").val("");
	$("#Gee_txtCartaPorte").val("");
	$("#Gee_txtEta").val("");
	$("#Gee_txtRuta").val("");
	$("#txtBuscarMaterialGee").val("");
	
	$("#SeleccionRuta").html("");	
	
	$("#alertDanger").html("");
	$("#alertDanger").css("display","none");
	$("#alertWarning").html("");
	$("#alertWarning").css("display","none");
	$("#alertInfo").html("");
	$("#alertInfo").css("display","none");
	$("#tBodySucessEE").html("");
	$("#alertSuccess").css("display","none");
	
	$("#alertSuccessMensaje").html("");
	$("#alertSuccessMensaje").css("display","none");
	
	$('#tabla_EE').dataTable().fnDestroy();
	$('#tdBodyEE').html("");
	
	$("#DivGee").css("visibility", "hidden");
  }
  function VerificarFacturasConEECompletas()
  {
		var ArrayDeGEE = new Array();
		var JSON_ArrayDeGEE = "";
		
		var chekeados=0;
				
		$('[name=gee_chk]').each(function(){
			if($(this).is(':checked')){
						var item = {
							"InvoiceNumber": $(this).parent('td').parent('tr').find('td:eq(0)').html(),
							"PONumber": $(this).parent('td').parent('tr').find('td:eq(1)').html(),
							"Centro": $(this).parent('td').parent('tr').find('td:eq(2)').html(),
							"PONumberCorto": $(this).val()
						};	
						//alert($(this).parent('td').parent('tr').find('td:eq(1)').html());
						ArrayDeGEE.push(item);
						chekeados++;
					}
		});
					
		JSON_ArrayDeGEE = JSON.stringify({ArrayDeGEE: ArrayDeGEE});
		
		if(chekeados>0)
		{
			$.ajax({
					data:  {
							accion: 'VerificarEECompleta',
							JSON_ArrayDeGEE: JSON_ArrayDeGEE							
							},	
					url:   'ajax/edi810.php',
					type:  'post',
					beforeSend: function () {
						//
					},
					success:  function (response) {
								//alert("WS :"+response);
								var json = jQuery.parseJSON(response);
										$.each(json, function(i, d) {
											//Recorreremos los que ya existen
											$('[name=gee_chk]').each(function(){
											if($(this).is(':checked')){
														var valorEnCelda=String($(this).parent('td').parent('tr').find('td:eq(0)').html());
														var valorPONumber=String($(this).parent('td').parent('tr').find('td:eq(1)').html());
														
														var numeroFacturaRetorno=String(d.InvoiceNumber);
														var PONumberRetorno=String(d.PONumber);
														
														
														if(valorEnCelda==numeroFacturaRetorno && valorPONumber==PONumberRetorno)
														{
															$(this).parent('td').parent('tr').hide();
															$(this).prop("checked", "");
															$(this).parents('tr').removeAttr('class');
														}
													}
											});
										});
								},
					complete: function(){
								//$("#btn_GEE").trigger("click");
								$("#ImagenCargando").css("display","none");
								$("#btn_CancelarEE").css("display","inline");
								$("#btn_generararEE").css("display","inline");
								desbloquearOpcionesEE();
								cargarFacturas2(false);
								}
			});
		}
  }
function dejarFacturasSelecionadasAlGEE()
{
	//Recorremos los que quedaron checkeados en GEE
	$('[name=gee_chk]').each(function(){
			if($(this).is(':checked')){
				//Obtenemos el invoice number
				var invoiceNumberCheckeado=$(this).parent('td').parent('tr').find('td:eq(0)').html();
				//alert("Number checkeado: "+invoiceNumberCheckeado);
				//Recorremos ahora los checkbox de la tabla
				$('[name=EE]').each(function(){
					//alert(invoiceNumberCheckeado+", valor en tabla: "+$(this).val());
					if($(this).val()==invoiceNumberCheckeado)
					{
						//alert("encontradooo");
						$(this).prop('checked', true);
						$(this).parents('tr').removeAttr('class');
						$(this).parents('tr').attr('class', 'selected');
					}
				});
			}
	});
}
function CargaDetalleCompletoBulto(e,Celda) 
{
	e.preventDefault();
	var IdBulto = $(Celda).html();
	var InvoiceNumber = $(Celda).parent('td').parent('tr').find('td:eq(0)').html();
	
	$('#btnAbrirModalBulto').trigger('click');
	
	$.post('ajax/edi810.php',{ accion: 'CargaDetalleCompletoBulto',InvoiceNumber:InvoiceNumber, IdBulto:IdBulto }, function(response) {
	}).done(function(response) {

		$('#lblModalTitulo').html('- Bulto '+IdBulto);
	
		var json = jQuery.parseJSON(response);
		var tbody = "";
		$.each(json['Cabecera'], function(i, d){
			$('#txtTipoBulto').val(d.tipoBulto);
			$('#txtPesoBulto').val(d.peso);
			$('#txtVolumenBulto').val(d.volumen);
			$('#txtLargoBulto').val(d.longitud);
			$('#txtAnchoBulto').val(d.ancho);
			$('#txtAltoBulto').val(d.alto);
			$('#txtFechaDespachoBulto').val(d.fechaDespacho);
		});
		
		$.each(json['Detalle'], function(i, d){
			tbody+='<tr>';
			tbody+='<td style="width:14%">'+d.segm_Id+'</td>';
			tbody+='<td style="width:14%">'+d.it_prodid+'</td>';
			tbody+='<td style="width:14%">'+d.it_po+'</td>';
			tbody+='<td style="width:14%">'+d.it_poPosition+'</td>';
			tbody+='<td style="width:14%">'+d.it_unitmeasurement+'</td>';
			tbody+='<td style="width:14%">'+d.it_refnumber+'</td>';
			tbody+='<td style="width:14%">'+d.it_unitshiped+'</td>';
			tbody+='</tr>';
		});
		
		$('#tabla_bulto_modal').dataTable().fnClearTable();
		$('#tabla_bulto_modal').dataTable().fnDestroy();
		$('#tabla_bulto_modal tbody').html(tbody);
		
		$('#tabla_bulto_modal').dataTable( {
			"scrollY": "200px",
			"scrollCollapse": true,
			"paging": false,
			"language": LAN_ESPANOL,
			"fnInitComplete": function () {
				this.fnAdjustColumnSizing();
				this.fnDraw();
			}
		});
		
		CerrarThickBox();
	});
}