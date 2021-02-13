$( document ).ready(function() {
	$('#body_edi1666 [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
      style: 'cream' // Give it some style
	});
	
	cargaSociedades("(*) Sociedad: ", "sociedad");
	
	cargaProveedores('div_proveedoresHidden','div_proveedores','txtIdProveedor',false);
	
	$( "#fecha2_principal" ).datepicker({dateFormat:"dd/mm/yy"}).datepicker("setDate",new Date());
	$( "#fecha_ingreso_sap_termino" ).datepicker({dateFormat:"dd/mm/yy"}).datepicker("setDate",new Date());
	
	var date1 = new Date();
	date1.setDate(date1.getDate() - 30);
	$( "#fecha1_principal" ).datepicker({dateFormat:"dd/mm/yy"}).datepicker("setDate",date1);	
	$( "#fecha_ingreso_sap_inicio" ).datepicker({dateFormat:"dd/mm/yy"}).datepicker("setDate",date1);
	
	//Limpiando campos
	$("#fecha1_principal").val("");
	$("#fecha2_principal").val("");
	$("#fecha_ingreso_sap_inicio").val("");
	$("#fecha_ingreso_sap_termino").val("");
	
	
	
	//////////////////////////////////////////////////
	$("#btn_buscar_principal").click(function(e) {  
		e.preventDefault();
		$("#sociedad").attr('required', 'required');
		
		//Si viene proveedor
		if($("#txtIdProveedor").val()!="")
		{
			$("#fecha1_principal").attr('required', 'required');
			$("#fecha2_principal").attr('required', 'required');
			BuscarDatos();
			return;
		}
		else
		{
			$("#fecha1_principal").removeAttr('required');
			$("#fecha2_principal").removeAttr('required');
		}
		
		//Si ingresa fecha de inicio o termino
		if($("#fecha1_principal").val()!="" || $("#fecha2_principal").val()!="")
		{
			$("#fecha1_principal").attr('required', 'required');
			$("#fecha2_principal").attr('required', 'required');
			BuscarDatos();
			return;
		}
		else
		{
			$("#fecha1_principal").removeAttr('required');
			$("#fecha2_principal").removeAttr('required');
		}
		
		//Si ingresa fecha de inicio sap o termino sap
		if($("#fecha_ingreso_sap_inicio").val()!="" || $("#fecha_ingreso_sap_termino").val()!="")
		{
			$("#fecha_ingreso_sap_inicio").attr('required', 'required');
			$("#fecha_ingreso_sap_termino").attr('required', 'required');
			BuscarDatos();
			return;
		}
		else
		{
			$("#fecha_ingreso_sap_inicio").removeAttr('required');
			$("#fecha_ingreso_sap_termino").removeAttr('required');
		}
		
		BuscarDatos();
    });
	
	$("#boton_limpiar_edi1666").click(function(e) {  
		e.preventDefault();
		LimpiarFiltros();
	});
	
	$("#btn_inicio").click(function(e){
		e.preventDefault();
		var url="../../internos.php";
		$(location).attr('href',url);
		$("#Divbtn_volverHome").css("margin-left","25px");
    });
	
	$("#btn_descargar_todos").click(function(e) {
		e.preventDefault();
		
		var sociedad = $("#sociedad").val();
		var nro_oc   = $("#n_oc_principal").val();
		var factura  = $("#nro_factura").val();
		var documento_embarque  = $("#n_documento_embarque").val();
		var proveedor   = $("#txtIdProveedor").val();
		var entrega_entrante   = $("#txt_ee").val();
		var fecha_inicio  = $("#fecha1_principal").val();
		var fecha_termino  = $("#fecha2_principal").val();
		var fecha_inicio_sap   = $("#fecha_ingreso_sap_inicio").val();
		var fecha_termino_sap  = $("#fecha_ingreso_sap_termino").val();
		var ArrayInvoiceNumber = new Array();
		var ControlInvoiceNumber = new Array();
		
		var facturas 		= '';
		var textArea=$('#txtNumeroFacturas').val();
		
		var value = textArea.replace(/\r?\n/g, "|");
		facturas=value;
		
		if(factura != "") {
			facturas += factura;
		}
		
		var nro_ocs 		= '';
		var textArea=$('#txtNumeroOC').val();
		
		var value = textArea.replace(/\r?\n/g, "|");
		nro_ocs=value;
		
		if(nro_oc != "") {
			nro_ocs += nro_oc;
		}
		
		var documentos_embarque = '';
		var textArea=$('#txtNumeroDocumentoEmbarque').val();
		
		var value = textArea.replace(/\r?\n/g, "|");
		documentos_embarque=value;
		
		if(documento_embarque != "") {
			documentos_embarque += documento_embarque;
		}
		
		var entregas_entrante = '';
		var textArea=$('#txtNumeroMasivoEntregaEntrante').val();
		
		var value = textArea.replace(/\r?\n/g, "|");
		entregas_entrante=value;
		
		if(entrega_entrante != "") {
			entregas_entrante += entrega_entrante;
		}
		
		//Validar si solo viene la Sociedad
		if(sociedad!="" && nro_ocs=="" && facturas=="" && documentos_embarque=="" 
			&& entregas_entrante=="" && proveedor=="" && fecha_inicio=="" && fecha_termino=="" 
			&& fecha_inicio_sap=="" && fecha_termino_sap=="")
		{
			alert("Error se deben ingresar mas filtros de busqueda");
			return;
		}

		$("#AbrirCargando").trigger("click"); 
		$.post('ajax/edi1666_detalle_excel.php',
				{ 
					sociedad:sociedad, 
					nro_ocs:nro_ocs, 
					facturas:facturas,
					documentos_embarque:documentos_embarque,
					entregas_entrante:entregas_entrante,
					proveedor:proveedor,
					fecha_inicio :fecha_inicio, 
					fecha_termino:fecha_termino,
					fecha_inicio_sap :fecha_inicio_sap,
					fecha_termino_sap:fecha_termino_sap
				}, 
		function(response) {
				
				if(response!="Nok")
				{
					$("#contenedor_generarexcel").attr("src","ajax/archivos/edi1666/"+response);
				}
				else
				{
					alert("Error al crear documento, mejore los filtros de busqueda");
				}
		}).done(function(response) {
			CerrarThickBox();
		});
    });
	
	$('#nro_factura').dblclick(function() {
		$("#ingreso_factura").trigger("click");
	});
	
	$('#n_oc_principal').dblclick(function() {
		$("#ingreso_oc").trigger("click");
	});
	
	$('#n_documento_embarque').dblclick(function() {
		$("#ingreso_docuemnto_embarque").trigger("click");
	});
	
	$('#txt_ee').dblclick(function() {
		$("#ingreso_multiple_ee").trigger("click");
	});
	
});
function LimpiarFiltros()
{
	$("#sociedad").val("");
	$("#nro_factura").val("");
	$("#txtNumeroFacturas").val("");
	$("#n_oc_principal").val("");
	$("#txtNumeroOC").val("");
	$("#cboProvedor").val("");
	$("#SeleccionProveedor").html("");
	$("#txtIdProveedor").val("");
	$("#n_documento_embarque").val("");
	$("#txtNumeroDocumentoEmbarque").val("");
	$("#txt_ee").val("");
	$("#txtNumeroMasivoEntregaEntrante").val("");
	$("#fecha1_principal").val("");
	$("#fecha2_principal").val("");
	$("#fecha_ingreso_sap_inicio").val("");
	$("#fecha_ingreso_sap_termino").val("");
	
	
}
function BuscarDatos()
{
	$("#submit_filtros").trigger("click");		
	if(frm_filtros.checkValidity()){
		cargaCabecera();
	}
}
function generarEDI1666(e,celda) 
{
	e.preventDefault();
	$("#AbrirCargando").trigger("click"); 
	
	$.post('ajax/edi1666_detalle_excel.php',
		{ 
			facturas: ""+$(celda).parent('td').parent('tr').find('td:eq(0)').html(), 
			sociedad:$("#sociedad").val()
		}, 
	function(response) {	
		//alert(response);
		if(response!="Nok")
		{
			$("#contenedor_generarexcel").attr("src","ajax/archivos/edi1666/"+response);
		}
		else
		{
			alert("Error al crear documento, mejore los filtros de busqueda");
		}
	}).done(function(response) {
		CerrarThickBox();
	});
	
}
////// FUNCIONES PAGINA PRINCIPAL //////////////////////////////////////
function cargaCabecera() 
{
	$("#btn_descargar_todos").attr("disabled",true);
	
	var sociedad = $("#sociedad").val();
	var nro_oc   = $("#n_oc_principal").val();
	var factura  = $("#nro_factura").val();
	var documento_embarque  = $("#n_documento_embarque").val();
	var proveedor   = $("#txtIdProveedor").val();
	var entrega_entrante   = $("#txt_ee").val();
	var fecha_inicio  = $("#fecha1_principal").val();
	var fecha_termino  = $("#fecha2_principal").val();
	var fecha_inicio_sap   = $("#fecha_ingreso_sap_inicio").val();
	var fecha_termino_sap  = $("#fecha_ingreso_sap_termino").val();
	var ArrayInvoiceNumber = new Array();
	var ControlInvoiceNumber = new Array();
	
	var facturas 		= '';
	var textArea=$('#txtNumeroFacturas').val();
	
	var value = textArea.replace(/\r?\n/g, "|");
	facturas=value;
	
	if(factura != "") {
		facturas += factura;
	}
	
	var nro_ocs 		= '';
	var textArea=$('#txtNumeroOC').val();
	
	var value = textArea.replace(/\r?\n/g, "|");
	nro_ocs=value;
	
	if(nro_oc != "") {
		nro_ocs += nro_oc;
	}
	
	var documentos_embarque = '';
	var textArea=$('#txtNumeroDocumentoEmbarque').val();
	
	var value = textArea.replace(/\r?\n/g, "|");
	documentos_embarque=value;
	
	if(documento_embarque != "") {
		documentos_embarque += documento_embarque;
	}
	
	var entregas_entrante = '';
	var textArea=$('#txtNumeroMasivoEntregaEntrante').val();
	
	var value = textArea.replace(/\r?\n/g, "|");
	entregas_entrante=value;
	
	if(entrega_entrante != "") {
		entregas_entrante += entrega_entrante;
	}
	
	//Validar si solo viene la Sociedad
	if(sociedad!="" && nro_ocs=="" && facturas=="" && documentos_embarque=="" 
			&& entregas_entrante=="" && proveedor=="" && fecha_inicio=="" && fecha_termino=="" 
			&& fecha_inicio_sap=="" && fecha_termino_sap=="")
	{
		alert("Error se deben ingresar mas filtros de busqueda");
		return;
	}
	
	$("#AbrirCargando").trigger("click");
	
	$.post('ajax/edi1666.php',{ 
		accion: 'cargaCabecera', 
		sociedad:sociedad, 
		nro_ocs:nro_ocs, 
		facturas:facturas,
		documentos_embarque:documentos_embarque,
		entregas_entrante:entregas_entrante,
		proveedor:proveedor,
		fecha_inicio :fecha_inicio, 
		fecha_termino:fecha_termino,
		fecha_inicio_sap :fecha_inicio_sap,
		fecha_termino_sap:fecha_termino_sap
	}, function(response) {
	}).done(function(response) {
		
		$('#div_table_data').html("");
		var json = jQuery.parseJSON(response);
			var content = "";
			var count = 0;
			
			content += '<table class="display" cellspacing="0" width="100%" id="tabla_cabecera_principal">';
			content += '<thead>';
			content +=' <tr>';
			content += '<th class="ThFabrica" colspan="5">Fabrica</th>';
            content += '<th class="ThEmbarcador" colspan="4">Embarcador</th>';
			content += '<th class="ThCompras" colspan="2">Compras</th>';
			content += '<th class="ThComex" colspan="3">Comex</th>';
			content += '<th class="ThTransportes">Transportes</th>';
			content += '<th class="ThCD">CD</th>';
			content += '<th>&nbsp;</th>';
            content += '</tr>';
			content +=' <tr>';
			content += '<th class="ThFabrica">N° Factura</th><th class="ThFabrica thMaterial">Material</th><th class="ThFabrica">Cantidad Facturada</th><th class="ThFabrica">Código Bulto</th><th class="ThFabrica thFecha">Fecha Factura</th>';
            content += '<th class="ThEmbarcador">Recepción de Embarcador</th><th class="ThEmbarcador">N° documento embarque</th><th class="ThEmbarcador thFecha">Fecha envio Embarcador</th><th class="ThEmbarcador thFecha">Fecha estimada arribo Aeropuerto/Puerto</th><th class="ThCompras">Entrega Entrante</th>';
			content += '<th class="ThCompras">Status Importación</th><th class="ThComex thFecha">Fecha pago derechos</th><th class="ThComex thFecha">Fecha creación Guia de Despacho Aduana</th><th class="ThComex">Número Guia de Despacho Aduana</th>';
			content += '<th class="ThTransportes">N° Seguimiento Transporte Nacional</th><th class="ThCD thFecha">Fecha de Ingreso de Materiales a destino</th><th>EDI 1666</th>';
            content += '</tr>';
			content += '</thead><tbody>';
            
			$.each(json, function(i, d) {
				content += '<tr>';
				content += '<td align="center">'+d.InvoiceNumber+'</td>';
				content += '<td align="center">'+d.ProductID+'</td>';
				content += '<td align="center">'+d.ProductQuantity+'</td>';
				content += '<td align="center">'+d.idenBulto+'</td>';
				content += '<td align="center" data-order="'+ObtenerFechaNumerica(d.InvoiceDate)+'">'+d.InvoiceDate+'</td>';
				content += '<td align="center" data-order="'+ObtenerFechaNumerica(d.DateReceived)+'">'+d.DateReceived+'</td>';
				content += '<td align="center">'+d.MawbBL+'</td>';
				content += '<td align="center" data-order="'+ObtenerFechaNumerica(d.ETD)+'">'+d.ETD+'</td>';
				content += '<td align="center" data-order="'+ObtenerFechaNumerica(d.ETA)+'">'+d.ETA+'</td>';
				content += '<td align="center">'+d.EntregaEntrante+'</td>';
				content += '<td align="center" style="font-size: 7px;">'+d.StatusAWB_BL_ParaEE+'</td>';
				content += '<td align="center" data-order="'+ObtenerFechaNumerica(d.FechaPagoDerechos)+'">'+d.FechaPagoDerechos+'</td>';
				content += '<td align="center" data-order="'+ObtenerFechaNumerica(d.FechaG_D)+'">'+d.FechaG_D+'</td>';
				content += '<td align="center">'+d.G_DAduana+'</td>';
				content += '<td align="center">'+d.BoletoTransportes+'</td>';
				content += '<td align="center">'+d.FechaIngresoSap+'</td>';
				content += '<td align="center"><img src="../images/edi_16.png" style="cursor:pointer" onclick="generarEDI1666(event,this)"></td>';
				content += '</tr>';
				count++;
				if (ControlInvoiceNumber[d.InvoiceNumber] == null)
				{
					ControlInvoiceNumber[d.InvoiceNumber]=d.InvoiceNumber;
					ArrayInvoiceNumber.push(d.InvoiceNumber);
				}
			});
			
			content += '</tbody>';
			/**
			content += '<tfoot><tr>';
			content += '<th class="ThFabrica">Invoice</th><th class="ThFabrica">Part N°</th><th class="ThFabrica">Qty Despachado</th><th class="ThFabrica">Packing slip N° Bulto</th><th class="ThFabrica">Fecha despacho Fábrica</th>';
            content += '<th class="ThEmbarcador">Date Received</th><th class="ThEmbarcador">Fecha MAWB/ B/L#</th><th class="ThEmbarcador">ETA</th><th class="ThCompras">E.E.</th>';
			content += '<th class="ThCompras">Status AWB/BL para EE</th><th class="ThComex">Fecha pago derechos</th><th class="ThComex">Fecha G/D</th><th class="ThComex">G/D Aduana</th>';
			content += '<th class="ThTransportes">Boleto Transportes</th><th class="ThCD">Fecha Ingreso SAP</th><th>EDI 1666</th>';
            content += '</tr></tfoot>'
			*/
			content += '</table>';
			
			$('#div_table_data').html(content);
			
			$('#tabla_cabecera_principal').dataTable({
				"scrollY": "200px",
				"paging": false,
				"language": LAN_ESPANOL
			});
			
			$('#Json_InvoiceNumber').html(JSON.stringify(ArrayInvoiceNumber));
			
			CerrarThickBox();
			if(count > 0) {
				$("#btn_descargar_todos").attr("disabled",false);
			}
	});
}