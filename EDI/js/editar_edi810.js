$(document).ready(function(){
	var PAIS_ORIGEN=new Array();
	$.ajax({
            data:  {},	
            url:   'ajax/ws_buscarPaises.php',
            type:  'post',
            beforeSend: function () {
																					
            },
            success:  function (response) {
					var json = jQuery.parseJSON(response);
							
					var content='<label>Pais Origen: </label><select class="form-control" id="cboPaisOrigen" name="cboPaisOrigen">';
					content+="<option value=''>-Seleccione-</option>";
					PAIS_ORIGEN['-Seleccione-']='';
					
					$.each(json, function(i, d) {
						content += '<option value='+d.Land1+'>'+d.Land1+' - '+d.Landx50+'</option>';
						PAIS_ORIGEN[d.Land1+' - '+d.Landx50]=d.Land1;
					});
					
					content +='</select>';
					$('#divComboBoxPaises').html(content);
                },
				complete: function(){
					
				}
	});
	
	cargaSociedadesEditar810();
	cargaProveedores('div_proveedoresHidden','div_proveedores','txtIdProveedor',true);
	
	function ContadorCargaProveedores(callback){
		var thisFunc = arguments.callee;
		
		//Validador para salir de la funcion
		if($('#div_proveedoresHidden').html() != "" && $('#divComboBoxPaises').html() != "")
		{
			return callback();//Funcion que se ejecuta al terminar
		}

		setTimeout(function(){ // se vuelve a invocar la misma funcion;
			thisFunc(callback); // se invoca la funcion counter pasando el callback;
		}, 1000);
	}

	// el callback;
	function ValTraerCabecera(func){
		if($("#txt_invoiceNumber").val()!="")
		{
			BuscarHeaderFactura();				
		}
		else
		{
			$("#txt_invoiceNumber").attr("disabled",false);
			$("#H_accionPagina").val("Ingreso");
			CerrarThickBox();						
		}
	}

	ContadorCargaProveedores(ValTraerCabecera);
	
	
	/**
	if($("#txt_invoiceNumber").val()!="")
	{
		BuscarHeaderFactura();				
	}
	else
	{
		$("#txt_invoiceNumber").attr("disabled",false);
		$("#H_accionPagina").val("Ingreso");
		CerrarThickBox();						
	}
	*/
	$('#bodyEdi810 [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
      style: 'cream' // Give it some style
   });
   
   	$('#tabla_OC').dataTable({
					"scrollY":        "200px",
					"scrollCollapse": true,
					"paging":         false,
					"language": LAN_ESPANOL
	});
	
	$("#tabla_OC_filter").find('label input').keypress(function() {
		eliminarTheadOC();
	});
	
	$("#tabla_OC_filter").find('label input').keyup(function() {
		eliminarTheadOC();
	});
	
	
	eliminarTheadOC();
	$("#tabla_OC_filter").find('label input').keypress(function() {
		eliminarTheadOC();
	});
	
	$("#tabla_OC_filter").find('label input').keyup(function() {
		eliminarTheadOC();
	});
	
	if($("#txt_invoiceNumber").val()=="")
	{
		$("#btn_eleminarFactura").attr("disabled", true );
	}
	else
	{
		$("#btn_eleminarFactura").attr("disabled", false );
	}	
	
	$('#tabla_detalle').dataTable({
					"scrollY":        "250px",
					"scrollCollapse": true,
					"paging":         false,
					"language": LAN_ESPANOL
	});
	eliminarTheadDetalle();
	$("#tabla_detalle_filter").find('label input').keypress(function() {
		eliminarTheadDetalle();
	});
	
	$("#tabla_detalle_filter").find('label input').keyup(function() {
		eliminarTheadDetalle();
	});
	
	
	$("#btn_eleminarFactura").click(function(e) { 
		limpiarSearchDetalle();
		if (confirm('¿Esta seguro de eliminar la factura '+$('#txt_invoiceNumber').val()+' y todo su contenido?')) {
			EliminarFactura();
		} else {
			// Do nothing!
		}
			
	});
	
	$("#btn_load_detalle").click(function(e) { 
		limpiarSearchDetalle();
	});
	
	$("#btnPruebas").click(function(e) { 
		CargarTotalEnBaseDeTabla();
	});
	
	
		
	$("#tabla_OC_filter").find('label input').keypress(function() {
		eliminarTheadOC();
	});
	
	$("#tabla_OC_filter").find('label input').keyup(function() {
		eliminarTheadOC();
	});
	
	
	$("#btn_load_detalleExcel").click(function(e) { 
		limpiarSearchDetalle();
		$("#respuestaXLS").html("");
	});
			
	$("#btn_descargarDetalle").click(function(e) {
		limpiarSearchDetalle();
		//formDescDet810
		//Crearemos Json para el servidor php
		var ArrayDetalle = new Array();
		
		var encontrado=0;
		
		//Validar para que tome toda la factura en su detalle
		if($("[class='sorting_1']").find('label').size()==0)
		{
			$('[name=bas]').each(function(){
				$(this).prop('checked', false);
				$(this).parents('tr').removeAttr('class');
				$(this).parent('td').parent('tr').find('td:eq(1)').attr('class','sorting_1');
			});
		}
		
		$("[class='sorting_1']").find('label').each(function(){
		
				var paisOrigenHTML=$(this).parent('td').parent('tr').find('td:eq(8) label').html();
				
				var item = {
				"InvoicePosition": $(this).parent('td').parent('tr').find('td:eq(0) label').html(),
				"PONumber": $(this).parent('td').parent('tr').find('td:eq(1) label').html(),
				"POPosition": $(this).parent('td').parent('tr').find('td:eq(2) label').html(),
				"ProductID": $(this).parent('td').parent('tr').find('td:eq(3) label').html(),
				"ProductDesciption": $(this).parent('td').parent('tr').find('td:eq(4) label').html(),
				"ProductMeasure": $(this).parent('td').parent('tr').find('td:eq(5) label').html(),
				"ProductQuantity": $(this).parent('td').parent('tr').find('td:eq(6) label').html(),
				"PorductPrice": $(this).parent('td').parent('tr').find('td:eq(7) label').html(),
				"PaisOrigen": PAIS_ORIGEN[paisOrigenHTML],
				"EntregaEntrante": $(this).parent('td').parent('tr').find('td:eq(10) label').html()			
			};
			//ImprimirObjeto(item);
			encontrado++;
			ArrayDetalle.push(item);
		});
		
		if(encontrado==0)
		{
			alert("-No se puede descargar archivo Excel de una tabla vacía-");
		}
		else
		{
			JSON_ArrayDetail = JSON.stringify({ArrayDetalle: ArrayDetalle});
			
			$("#Desc_jsonDetalle").val(JSON_ArrayDetail);
			$("#Desc_invoiceNumber").val($("#txt_invoiceNumber").val());
			formDescDet810.submit();
		}
	});	
	
	$("#btn_agregarDetalleOC").click(function(e) {
		var CampoSearch= $('#tabla_OC_filter').find('label input');
		
		$(CampoSearch).val("");
		
		ev = $.Event('keyup');
		ev.keyCode= 13; // enter
		$(CampoSearch).trigger(ev);
		
		var vali=validarTablaDetalle();
		if(vali)
		{
			AgregarOCAFactura();
		}
	});
	
	$("#btn_ingresarOC").click(function(e) {		
		limpiarSearchDetalle();
		
		var divHead=$('#tabla_OC_wrapper').find('div div div');
		$(divHead).css('width','');
		
		var tableDeHead=$(divHead).find('table');
		$(tableDeHead).css('width','');
	});
	
	
	$("#boton_subir_archivo").click(function(e) {		
		$("#btnValidarFormulario").trigger("click");
		if(formSubirFactura.checkValidity())
		{
			$("#ImagenCargando").css("display","inline");
			$("#btn_CancelarCsv").css("display","none");
			$("#boton_subir_archivo").css("display","none");
			formSubirFactura.submit();
		}
	});	
	
	$("#boton_subir_archivoXLS").click(function(e) {		
		$("#btnValidarFormularioXLS").trigger("click");
		if(formSubirXLS.checkValidity())
		{
			$("#ImagenCargandoXLS").css("display","inline");
			$("#btn_CancelarXLS").css("display","none");
			$("#boton_subir_archivoXLS").css("display","none");
			formSubirXLS.submit();
		}
	});	
	
	$("#ADDOC_btnBuscarOC").click(function(e) { 
		var valorOC=$("#ADDOC_txtNumOC").val();
		valorOC=$.trim(valorOC);
		$("#ADDOC_txtNumOC").val(""+valorOC);
		//alert($("#ADDOC_txtNumOC").val());
		if(FormOC.checkValidity())
		{
			BuscarOC();
		}
	});	
	
	$("#btn_grabarFactura").click(function(e) {
		limpiarSearchDetalle();
		var verif=validarFactura();	
		//alert(verif);
		if(verif==true)
		{				
			//Validar que no exista el Invoice
			var registrosEncontrados=0;
			
			$.post('ajax/editar810.php',{
				accion		: 'VerificarInvoiceNumber810',
				InvoiceNumber: $('#txt_invoiceNumber').val()
			}, 
			function(response) {
				registrosEncontrados=parseFloat(response);
			})
			.done(function(response) {
			$("#btn_verificar").attr("disabled", false);
			$("#btn_grabarFactura").attr("disabled", false);
			$("#btn_eleminarFactura").attr("disabled", false);
			
			if(registrosEncontrados>0 && $("#H_accionPagina").val()=="Ingreso")
			{
				alert('El Invoice Number ya esta registrado');
				$("#txt_invoiceNumber").focus();
				return;
			}
			$("#currency").attr('required', 'required');	
			$("#cboSociedadEditar810").attr('required', 'required');
			$("#cboProvedor").attr('required', 'required');	
						
				$("#btn_validarFormularioH810").trigger("click");
				
				var ValPro=true;
				
				if($("#cboProvedor").val()!= $("#txtTextoProveedor_old").val())
				{
					if($("#txtIdProveedor").val()==$("#txtIdProveedor_old").val()) 
					{
						$("#div_proveedores").attr("class","form-group has-error");
						alert("Para cambiar el proveedor debe seleccionarlo de la lista (Actual: "+$("#SeleccionProveedor").html()+")");
						ValPro=false;
					}
					else
					{
						$("#div_proveedores").attr("class","form-group");
					}
				}
				else
				{
					$("#div_proveedores").attr("class","form-group");
				}
				
				if(ValPro==true)
				{
					if(frm_editar_cabecera.checkValidity()){
						if(frm_input.checkValidity())
						{
							$("#btn_grabarFactura").attr("disabled", true );
							GrabarFactura(PAIS_ORIGEN);
						}
						else
						{
							$("#btn_input").trigger("click");
						}
					}
				}
				else
				{
					$("#cboProvedor").val($("#SeleccionProveedor").html());
				}
			});
		}
		else
		{
			$("#btn_eleminarFactura").attr("disabled", false );
			$("#btn_verificar").attr("disabled", false );
			$("#btn_grabarFactura").attr("disabled", false );
		}
	});
	//
	$("#btn_verificar").click(function(e) {
		
		limpiarSearchDetalle();
		
		$("#btn_validarFormularioH810").trigger("click");
		if(frm_editar_cabecera.checkValidity()){
		
			var verif=validarFactura();
						
			if(verif==true)
			{
				//Validar que no exista el Invoice
				var registrosEncontrados=0;
				
				$.post('ajax/editar810.php',{
											accion		: 'VerificarInvoiceNumber810',
											InvoiceNumber: $('#txt_invoiceNumber').val()
										 }, 
				function(response) {
					registrosEncontrados=parseFloat(response);
				})
				.done(function(response) {
				
				$("#btn_verificar").attr("disabled", false);
				$("#btn_grabarFactura").attr("disabled", false);
				
				if($("#H_accionPagina").val()!="Ingreso")
				{
					$("#btn_eleminarFactura").attr("disabled", false);
				}
				
				if(registrosEncontrados>0 && $("#H_accionPagina").val()=="Ingreso")
				{
					alert('El Invoice Number ya esta registrado');
					$("#txt_invoiceNumber").focus();
					return;
				}
				else
				{
					if(frm_input.checkValidity())
					{						
						$("[cambio='cambio']").each(function(){
							$(this).blur();
						});
						
						if(frm_input.checkValidity())
						{
							alert('Factura verificada correctamente');
						}
						else
						{
							$("#btn_input").trigger("click");
						}
					}
					else
					{
						$("#btn_input").trigger("click");
					}
				}
				});
			}
		}
		$("#btn_verificar").attr("disabled", false);
		$("#btn_grabarFactura").attr("disabled", false);
		
		if($("#H_accionPagina").val()!="Ingreso")
		{
			$("#btn_eleminarFactura").attr("disabled", false);
		}
		
	});
	
	$("#btn_eleminar").click(function(e) {
		limpiarSearchDetalle();
		
		$('#tabla_detalle').DataTable().rows('.selected').remove().draw( false );
		
		CalcularTotalFactura();
	});
	$("#btn_volver").click(function(e) {
		dejarTablabusquedaNormal();
		VolverADivWrapper();	
    });	
	
	$("#btn_Cancelar").click(function(e) {      
		//buscarFacturas();
		$("#btn_insertarDetalle").hide();
		$("#btn_grabarDetalle").css("display","inline");
    });
		
	$("#btn_agregarDetalle").click(function(e) {
		limpiarSearchDetalle();
		var vali=validarTablaDetalle();
		
		if(vali)
		{
			agregarLineaDetalle();
		}
		/**
		$('#myModalLabel').html("Ingresar Detail");
		limpiarCuadroDetail();
		calcularSiguienteEinvoicePosition();
		$("#Detailtxt_invoiceNumber").val($("#txt_invoiceNumber").val());
		$("#myModalLabel").html("Ingresar Detail");
		$("#btn_insertarDetalle").css("display","inline");
		$("#btn_grabarDetalle").css("display","none");
		$("#modificarDetail").trigger("click");
		*/
    });	
	
	$("#btn_insertarDetalle").click(function(e) {  		
		if(FormEditarDetalle.checkValidity()){
		var validarInvoicePosition=ValidarIngresoInvoicePosition();
			if(validarInvoicePosition==true)
			{				
				ingresarNuevoDetail();
				limpiarCuadroDetail();
				calcularSiguienteEinvoicePosition();
				CalcularTotalFactura();
				var ThclassName = $('#thInvoicePosition').attr('class');				
				if(ThclassName=="sorting_asc")
				{
					$("#thInvoicePosition").trigger("click");
				}
				alert('Invoice detail ingresado correctamente');				
			}
			else
			{
				alert('El invoice position ingresado ya está en uso, por favor ingrese otro');
				$("#Detailtxt_InvoicePosition").focus();
			}
		}
		});	
		
	$("#btn_grabarDetalle").click(function(e) {  
		
		if(FormEditarDetalle.checkValidity()){
		
			$("[id=InvPos]").each(function(){
				if($('#H_OldPosition').val()==($(this).html()))
				{
					var EE=0;
					try {EE=parseFloat($("#H_OldEE").val())+0;}catch(err) {}
					if(isNaN(EE)){EE=0}
					
					var invoiceNuevo=FormatearInvoicePosition($("#Detailtxt_InvoicePosition").val());
					$(this).parent('td').parent('tr').find('td:eq(0) label').html(invoiceNuevo);
					$(this).parent('td').parent('tr').attr("id",invoiceNuevo);
					$(this).parent('td').parent('tr').find('td:eq(1)').html($("#Detailtxt_PONumber").val());					
					$(this).parent('td').parent('tr').find('td:eq(2)').html($("#Detailtxt_POPosition").val());
					$(this).parent('td').parent('tr').find('td:eq(3)').html($("#Detailtxt_Product_id").val());
					$(this).parent('td').parent('tr').find('td:eq(4)').html($("#Detailtxt_ProductDesciption").val());
					$(this).parent('td').parent('tr').find('td:eq(5)').html($("#Detailtxt_ProductMeasure").val());
					$(this).parent('td').parent('tr').find('td:eq(6)').html($("#Detailtxt_ProductQuantity").val());
					$(this).parent('td').parent('tr').find('td:eq(7)').html($("#Detailtxt_ProductPrice").val());
					$(this).parent('td').parent('tr').find('td:eq(8)').html($("#cboPaisOrigen :selected").text());
					$(this).parent('td').parent('tr').find('td:eq(10)').html(EE);
					
					//Total

					var cantida=parseFloat($("#Detailtxt_ProductQuantity").val());
					var precio=parseFloat($("#Detailtxt_ProductPrice").val());
					var total=cantida*precio;
					
					$(this).parent('td').parent('tr').find('td:eq(11)').html('<label name="det_tot">'+total.toFixed(2)+'</label>');
					CalcularTotalFactura();
					var ThclassName = $('#thInvoicePosition').attr('class');				
					if(ThclassName=="sorting_asc")
					{
						$("#thInvoicePosition").trigger("click");
					}
					$("#btn_Cancelar").trigger("click");				
				}
					
			});
		
		}
		
	});
	
	/////////////// CARGA DETALLE //////////////////////////////////////    
    $('#csv_detalle').change(function()
    {
        var file = $("#csv_detalle")[0].files[0];
        validaArchivo(file);
    });
    /////////////// FIN CARGA DETALLE //////////////////////////////////
});

function procesaCSV()
{
	$("#AbrirCargando").trigger("click");
	var file = $('#csv_detalle').val();
	
	if(file != "")
	{
		archivo = $("#csv_detalle")[0].files[0];
		var fileName = archivo.name;
		cargaDetalleCSV(archivo, fileName);
	}else { 
		CerrarThickBox();
	}
}

function cargaDetalleCSV(archivo, fileName)
{
	dataT = new FormData();
	dataT.append("archivo",archivo);
	$.ajax({
		url:"ajax/import_file.php",
		type:'POST',
		contentType:false,	
		dataType:'json',			
		data:dataT,				
		processData:false,				
		cache:false,
		complete: function() {			
			$.post("ajax/editar810.php", {accion:"cargarDetalleFromCSV", archivo: filename})
			.done(function(response) {
				//alert(response);
				limpiaSelect();
			});
		}
	});
}

function limpiaSelect()
{
	fileupload = $('#csv_detalle');  
	fileupload.replaceWith(fileupload.clone(true)); 
	$('#csv_detalle').focus();
}
function cargaSociedadesEditar810()
{
	$.post('ajax/sociedades.php',{ }, 
	function(response) {
		$('#sociedades').html("");
		var json = jQuery.parseJSON(response);

		var content = "";
			content += "<label for='sociedad'>(*) Sociedad: </label>";
			content += '<select class="form-control" id="cboSociedadEditar810" name="cboSociedadEditar810" onchange="javascript:CargarToleranciaPorSociedad();">';
			content += "<option value=''>-Seleccione-</option>";
			$.each(json, function(i, d) {
				content += "<option value='"+d.id_sociedad+"'>"+d.desc_sociedad+"</option>";
			});
		content += "</select>";
		$('#sociedades').html(content);
	}).done(function(response) {
	}).error(function() {
		$('#sociedades').html("Error al intentar cargar Zonas");
	});	
}

function isCSV(extension)
{	
	var result = true;
	if(extension == 'csv'){
		result = false;
	} else {
		result = true;
	}
	return result;
}

function validaArchivo(file)
{
	var fileName = file.name;
	fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
	if(isCSV(fileExtension) )
	{
		alert("Archivo no compatible, debe ser CSV.", 2000, true);
		limpiaSelect();
	}
}

function ingresarNuevoDetail()
{
	var cantida=parseFloat($("#Detailtxt_ProductQuantity").val());
	var precio=parseFloat($("#Detailtxt_ProductPrice").val());
	var total=cantida*precio;
	var t = $('#tabla_detalle').DataTable();
	
	var NumeroInvoicePosition=""+$("#Detailtxt_InvoicePosition").val();
	
	NumeroInvoicePosition=FormatearInvoicePosition(NumeroInvoicePosition);
	
	var EE=0;
	try {EE=parseFloat($("#H_OldEE").val())+0;}catch(err) {}
	if(isNaN(EE)){EE=0}
	
	var NuevaFila='<tr>';
	NuevaFila+='<td><label id="InvPos" style="cursor:pointer">'+NumeroInvoicePosition+'</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+$("#Detailtxt_PONumber").val()+'</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+$("#Detailtxt_POPosition").val()+'</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+$("#Detailtxt_Product_id").val()+'</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+$("#Detailtxt_ProductDesciption").val()+'</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+$("#Detailtxt_ProductMeasure").val()+'</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="cantida" min="1" style="cursor:pointer">'+$("#Detailtxt_ProductQuantity").val()+'</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="ProPrice" min="" style="cursor:pointer">'+$("#Detailtxt_ProductPrice").val()+'</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Cbo" itipo="Cbo" step="" valAdd="" min="" style="cursor:pointer" codPais="'+$("#cboPaisOrigen").val()+'">'+$("#cboPaisOrigen :selected").text()+'</label></td>';
	NuevaFila+='<td><input type="checkbox" class="marca_chk" name="bas" id="bas" value="'+NumeroInvoicePosition+'" onchange="javascript:CambiarAlClicChk(this);"></td>';
	NuevaFila+='<td>'+EE+'</td>';
	NuevaFila+='<td><label name="det_tot">'+parseFloat(total).toFixed(2)+'</label></td>';
	NuevaFila+='<td align="center" title="Nok" data-order="0"><img src="../../images/delete_16.png" width="13" height="13"></td>';
	NuevaFila+="</tr>";
	
	t.row.add($(NuevaFila)).draw();
		
	eliminarTheadDetalle();
}
function calcularSiguienteEinvoicePosition()
{
		//Verificar si esta ordenado en sorting_desc		
		var ThclassName = $('#thInvoicePosition').attr('class');
				
		if(ThclassName=="sorting_asc")
		{
			$("#thInvoicePosition").trigger("click");
		}
		
		var ArraytxtNumero = new Array();
		var NumeroMayor=0;
		//alert($("[name='tdInvoicePosition']").html());
		$("[class='sorting_1']").find('label').each(function(){
				ArraytxtNumero.push($(this).html());
				//alert($(this).html());
		});
		
		for (i in ArraytxtNumero){
			var InvoiceFloat=parseFloat(ArraytxtNumero[i]);
			//alert(InvoiceFloat);
			if(InvoiceFloat>NumeroMayor)
			{
				NumeroMayor=InvoiceFloat;
			}				
		}
		NumeroMayor=NumeroMayor+1
		var NumeroMayorString=""+NumeroMayor;
		
		NumeroMayorString=FormatearInvoicePosition(NumeroMayorString);
		$("#Detailtxt_InvoicePosition").val(NumeroMayorString);
		//alert("Proximo numero es : "+(NumeroMayor+1));
}
function VolverADivWrapper()
{
	$("#div_detalle")
	$("#btn_inicio").css("display","inline");
	$("#div_detalle").html("");
	$("#div_detalle").hide();
	$("#wrapper").show();
}

function BuscarHeaderFactura()
{
	var NumeroFactura=$("#txt_invoiceNumber").val();
	$.ajax({
                data:  { accion: 'buscarHeaderFactura',invoiceNumber: NumeroFactura},	
                url:   'ajax/editar810.php',
                type:  'post',
                beforeSend: function () {
                      //  $("#AbrirCargando").trigger("click");
                },
                success:  function (response) {
				//$('#divTablaDinamica').html("");
				    var json = jQuery.parseJSON(response);
					$.each(json, function(i, d) {	
						$("#invoice_date").val(d.InvoiceDate);
						$("#H_Tolerancia").val(parseFloat(d.tolerancia)+0);
						
						var NetValue=parseFloat(d.InvoiceNetValue);
						NetValue+=0.00;						
						$("#txt_netValue").val(NetValue.toFixed(2));
						
						var GrossValue=parseFloat(d.InvoiceGrossValue);
						GrossValue+=0.00;	
						$("#txt_grossvalue").val(GrossValue.toFixed(2));
						
						var GastosValue=parseFloat(d.InvoiceGastos);
						GastosValue+=0.00;	
						$("#txt_invoiceGastos").val(GastosValue.toFixed(2));
						
						
						$("#proveedoresHidden").val(d.InvoiceVendor);
						$("#cboProvedor").val($("#proveedoresHidden option:selected").text());
						$("#txtTextoProveedor_old").val($("#proveedoresHidden option:selected").text());
						
						$("#SeleccionProveedor").html($("#proveedoresHidden option:selected").text());
						
						
						
						$("#txtIdProveedor").val(d.InvoiceVendor);
						$("#txtIdProveedor_old").val(d.InvoiceVendor);
						
						$("#cboSociedadEditar810").val(d.Sociedad);	
						$("#currency").val(d.InvoiceCurrency);							
					});                    
                },
				complete: function(){
					//CerrarThickBox();
					cargaDetalleInvoiceDetail();
				}
	});
}

function lineasIngresoFactura(accion, cantidad) {	
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
function cargaDetalleInvoiceDetail() 
{
	var NumeroFactura = $("#txt_invoiceNumber").val();
	
	$.ajax({
                data:  { accion: 'cargarInvoiceDetail',invoiceNumber: NumeroFactura},
	
                url:   'ajax/edi810.php',
                type:  'post',
                beforeSend: function () {
                       // $("#AbrirCargando").trigger("click");
                },
                success:  function (response) {
				//$('#divTablaDinamica').html("");
				var content = "";
				var json = jQuery.parseJSON(response);
				//$('tabla_detalle').DataTable();
					content += '<tbody>';
					$.each(json, function(i, d) {
						var cantida=parseFloat(d.ProductQuantity);
						var precio=parseFloat(d.PorductPrice);
						var total=cantida*precio;
						
						cantida+=0.00;
						//alert(cantida.toFixed(2));
						content += '<tr id="'+d.InvoicePosition+'" class="editar">';
						content += '<td><label id="InvPos" style="cursor:pointer">'+d.InvoicePosition+'</label></td>';
						content += '<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+d.PONumber+'</label></td>';
						content += '<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+d.POPosition+'</label></td>';
						content += '<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+d.ProductID+'</label></td>';
						content += '<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+d.ProductDesciption+'</label></td>';
						content += '<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+d.ProductMeasure+'</label></td>';
						content += '<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="cantida" min="1" style="cursor:pointer">'+cantida.toFixed(2)+'</label></td>';	
						var ProPrice=parseFloat(d.PorductPrice);
						ProPrice+=0.00;
						content += '<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="ProPrice" min="" style="cursor:pointer">'+ProPrice.toFixed(2)+'</label></td>';
						
						if($.trim(d.PaisOrigen)!="")
						{
							content += '<td><label onclick="javascript:ModificarCelda(this);" id="Cbo" itipo="Cbo" step="" valAdd="" min="" style="cursor:pointer" codPais="'+d.PaisOrigen+'">'+d.PaisOrigen+'</label></td>';
						}
						else
						{
							content += '<td><label onclick="javascript:ModificarCelda(this);" id="Cbo" itipo="Cbo" step="" valAdd="" min="" style="cursor:pointer" codPais=""></label></td>';
						}
						content += '<td><input type="checkbox" class="marca_chk" name="bas" id="bas" value="'+d.InvoicePosition+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
						content += '<td>'+d.EntregaEntrante+'</td>';						
						var cantida=parseFloat(d.ProductQuantity);
						var precio=parseFloat(d.PorductPrice);
						var total=cantida*precio;						
						content += '<td><label name="det_tot">'+ parseFloat(total).toFixed(2)+'</label></td>';
						
						if (d.EDI_856 == "Nok") {
							
							//Validar si fue reparado y tiene EE
							if(!VieneEE(d.EntregaEntrante))
							{
								content += '<td align="center" title="'+d.EDI_856+'" data-order="0">';
								content += '<img src="../../images/delete_16.png" width="13" height="13">';
								content += '</td>';	
							}
							else
							{
								content += '<td align="center" title="'+d.EDI_856+'" data-order="1">';
								content += '<img src="../../images/tick_16.png" width="13" height="13">';
								content += '</td>';
							}
						}else {
							content += '<td align="center" title="'+d.EDI_856+'" data-order="1">';
							content += '<img src="../../images/tick_16.png" width="13" height="13">';
							content += '</td>';
						}
						content += '</tr>';
						
					});
					content += '</tbody>';
					$('#tabla_detalle').dataTable().fnClearTable();
					$('#tabla_detalle').dataTable().fnDestroy();
					$('#tabla_detalle tbody').replaceWith(content);
					CalcularTotalFactura();
					$("#btn_eleminarFactura").attr("disabled", false );						
				$("#btn_buscar").attr("disabled", false );                        
                },
				complete: function(){
					renombrarPaisOrigen();
					$('#tabla_detalle').dataTable({
									"scrollY":        "250px",
									"scrollCollapse": true,
									"paging":         false,
									"language": LAN_ESPANOL
					});
					eliminarTheadDetalle();
					$("#tabla_detalle_filter").find('label input').keypress(function() {
						eliminarTheadDetalle();
					});
					
					$("#tabla_detalle_filter").find('label input').keyup(function() {
						eliminarTheadDetalle();
					});
					CalcularTotalFactura();					
					CerrarThickBox();
				}
	});
}
function CargarDetailParaEditar()
{
	$('#Detailtxt_invoiceNumber').val($('#txt_invoiceNumber').val());
	$('#Detailtxt_InvoicePosition').val($('#tabla_detalle tbody tr td:eq(0)').html());
	$('#H_OldPosition').val($('#tabla_detalle tbody tr td:eq(0)').html());
	$('#Detailtxt_PONumber').val($('#tabla_detalle tbody tr td:eq(1)').html());
	$('#Detailtxt_POPosition').val($('#tabla_detalle tbody tr td:eq(2)').html());
	$('#Detailtxt_Product_id').val($('#tabla_detalle tbody tr td:eq(3)').html());
	$('#Detailtxt_ProductDesciption').val($('#tabla_detalle tbody tr td:eq(4)').html());
	$('#Detailtxt_ProductMeasure').val($('#tabla_detalle tbody tr td:eq(5)').html());
	$('#Detailtxt_ProductQuantity').val($('#tabla_detalle tbody tr td:eq(6)').html());
	$('#Detailtxt_ProductPrice').val($('#tabla_detalle tbody tr td:eq(7)').html());
	$('#cboPaisOrigen').val($('#tabla_detalle tbody tr td:eq(8)').html());
	$('#H_OldEE').val($('#tabla_detalle tbody tr td:eq(10)').html());
	
	$("#modificarDetail").trigger("click");
	//alert ($('#tabla_detalle tbody tr td:eq(0)').html());
}

function limpiarCuadroDetail()
{
	$("#Detailtxt_InvoicePosition").val("");
	$("#Detailtxt_PONumber").val("");
	$("#Detailtxt_POPosition").val("");
	$("#Detailtxt_Product_id").val("");
	$("#Detailtxt_ProductDesciption").val("");
	$("#Detailtxt_ProductMeasure").val("");
	$("#Detailtxt_ProductQuantity").val("");
	$("#Detailtxt_ProductPrice").val("");
	$("#cboPaisOrigen").val("");
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
function FormatearInvoicePosition(NumeroInvoicePosition)
{
	/**
	while(NumeroInvoicePosition.length<4)
	{
		NumeroInvoicePosition="0"+NumeroInvoicePosition;
	}
	*/
	return NumeroInvoicePosition;
}
function ValidarIngresoInvoicePosition()
{
	var respuesta=true;
	var invoicePositionAIngresar=$("#Detailtxt_InvoicePosition").val();
	invoicePositionAIngresar=FormatearInvoicePosition(invoicePositionAIngresar);
	
	$('.sorting_1').find('label').each(function(){	
		if($(this).html()==invoicePositionAIngresar)
		{
			respuesta=false;
		}
	 });
	 return respuesta;
	 //$("#tabla_detalle").dataTable();
}
function CalcularTotalFactura()
{	
	var total=0;
	$("[name=det_tot]").each(function(){		
		total=total+parseFloat($(this).html());
	 });
	 total+="";
	 total=parseFloat(total).toFixed(2);
	 $('#H_total').val(total);
	 //alert("Listo: "+total);	 
	 
	 CargarTotalEnBaseDeTabla();
}
function CargarTotalEnBaseDeTabla()
{
	var tFoot=$('#divTabla').find('.dataTables_scrollFoot');
	$(tFoot).find('th:eq(11)').html('<label class="label label-danger" style="font-size:100%;float:right">'+$('#H_total').val()+'</label>');
	//alert("Aplicado");
}
function ModificarCelda(celda)
{
	if(frm_input.checkValidity())
	{
		var valorActual=$(celda).html();
		var Contenedor=$(celda).parent('td');
		
		if( $(celda).attr("id")=="Input" )
		{
			$(Contenedor).html("");
			
			var inputHTML ='<input type="'+$(celda).attr("itipo")+'" min="'+$(celda).attr("min")+'" valAdd="'+$(celda).attr("valAdd")+'"';
			inputHTML+=' step="'+$(celda).attr("step")+'" required="" style="width:170px"size="25" ';
			inputHTML+=' value="'+valorActual+'" id="input_'+$(celda).attr('id')+'" class="form-control"';
			inputHTML+=' onblur="javascript: DejarCeldaNormal(this);"';
			inputHTML+=' tipo="input" cambio="cambio" />';		
			inputHTML+=' <input type="submit" id="btn_input" style="display:none"/>';
			
			$(Contenedor).html(inputHTML);
			
			$(Contenedor).find('input').focus();
		}
		
		if( $(celda).attr("id")=="Cbo" )
		{
			$(Contenedor).html("");
						
			var inputHTML=$('#divComboBoxPaises').html();			
			$(Contenedor).html(inputHTML);
			$(Contenedor).find('label').remove();
			
			var cboPais=$(Contenedor).find('select');
			$(cboPais).val($(celda).attr("codPais"));
			$(cboPais).attr("id","cboPais");
			$(cboPais).attr("tipo","Cbo");
			$(cboPais).attr("required","required");
			$(cboPais).attr("cambio","cambio");
			$(cboPais).attr("onblur","javascript: DejarCeldaNormal(this);");
			
			$(cboPais).focus();
		}
	}
	else
	{
		$("#btn_input").trigger("click");
	}
}
function DejarCeldaNormal(Input)
{	
	if(frm_input.checkValidity())
	{
		if($(Input).attr('tipo')=='input')
		{	
			//ProPrice
			if($(Input).attr('valAdd')=='ProPrice')
			{
				var ProPrice=parseFloat($(Input).val());
				
				var Trmaestro=$(Input).parent('td').parent('tr');
				
				var cantida=parseFloat($(Trmaestro).find('td:eq(6) label').html());
				
				var total=cantida*ProPrice;
						cantida+=0.00;
						total+=0.00;
				
				$(Trmaestro).find('td:eq(11) label').html(total.toFixed(2));
				CalcularTotalFactura();
				
				ProPrice+=0.00;
				
				//Grabar info en tabla
				var tdContenedor=$(Input).parent('td');
				$(tdContenedor).html("");
				
				var htmlNormal='<label onclick="javascript:ModificarCelda(this);" id="Input" itipo="'+$(Input).attr("type")+'" min="" step="any" valAdd="ProPrice"';
				htmlNormal+=' style="cursor:pointer">'+ProPrice.toFixed(2)+'</label>';
				
				$(tdContenedor).html(htmlNormal);
				CalcularTotalFactura();
			}
			
			//Cantida
			if($(Input).attr('valAdd')=='cantida')
			{
				var Trmaestro=$(Input).parent('td').parent('tr');
				
				var ProPrice=parseFloat($(Trmaestro).find('td:eq(7) label').html());
				var cantida=parseFloat($(Input).val());
				
				var total=cantida*ProPrice;
						cantida+=0.00;
						total+=0.00;
				
				$(Trmaestro).find('td:eq(11) label').html(total.toFixed(2));
				CalcularTotalFactura();
						
				
				//Grabar info en tabla
				var tdContenedor=$(Input).parent('td');
				$(tdContenedor).html("");
				
				var htmlNormal='<label onclick="javascript:ModificarCelda(this);" id="Input" itipo="'+$(Input).attr("type")+'" min="1" step="any" valAdd="cantida"';
				htmlNormal+=' style="cursor:pointer">'+cantida.toFixed(2)+'</label>';
				
				$(tdContenedor).html(htmlNormal);
				CalcularTotalFactura();
			}		
			
			if($(Input).attr('valAdd')=='')
			{
				//Grabar info en tabla
				var tdContenedor=$(Input).parent('td');
				$(tdContenedor).html("");
				
				var htmlNormal='<label onclick="javascript:ModificarCelda(this);" id="Input" itipo="'+$(Input).attr("type")+'" valAdd=""';
				htmlNormal+=' style="cursor:pointer">'+$(Input).val()+'</label>';
				
				$(tdContenedor).html(htmlNormal);
			}
		}
		if($(Input).attr('tipo')=='Cbo')
		{
			var tdContenedor=$(Input).parent('td');
			$(tdContenedor).html("");
			
			var htmlNormal='<label onclick="javascript:ModificarCelda(this);" id="Cbo" itipo="Cbo" step="" valadd="" min="" style="cursor:pointer" codpais="'+$(Input).val()+'">'+$("option:selected", $(Input)).text()+'</label>';
			$(tdContenedor).html(htmlNormal);			
		}
	}
	else
	{
		$("#btn_input").trigger("click");
	}
	
}
function validarFactura()
{
	CalcularTotalFactura();
	$("#btn_verificar").attr("disabled", true);
	$("#btn_grabarFactura").attr("disabled", true);	
	$("#btn_eleminarFactura").attr("disabled", true);
	
	var Net=parseFloat($('#txt_netValue').val()).toFixed(2);
	var Tot=parseFloat($('#H_total').val()).toFixed(2);
	
	//var Tolerancia=parseFloat($('#H_Tolerancia').val()).toFixed(2);
	/**
	var diferencia=Net-Tot;
	if(diferencia<0)
	{
		diferencia=diferencia*-1;
	}
	
	var porcentaje= Tot*(Tolerancia/100);
	
	var validacion=false;
	
	//alert(diferencia+" VS "+porcentaje);
	
	if(diferencia<=porcentaje)
	{
		validacion=true;
	}
	else
	{
		alert("La diferencia entre el Total y el Net Value sobrepasa la tolerancia permitida ("+Tolerancia+"%)");
		validacion=false;
		return validacion;
	}
	*/
		
	//validacion netvalue
	if(parseFloat($('#txt_netValue').val()).toFixed(2)!=parseFloat($('#H_total').val()).toFixed(2))
	{
		alert('Invoice Net Value no es igual al Total de la factura ('+parseFloat($('#H_total').val()).toFixed(2)+')');
		$("#txt_netValue").focus();
		validacion=false;
		return validacion;
	}
	
	//Validacion gross value
	var ValNet=parseFloat($('#txt_netValue').val());
	var ValGastos=parseFloat($('#txt_invoiceGastos').val());
	var sumaValNetGast=ValNet+ValGastos+0.00;
	sumaValNetGast+="";
	
	if(parseFloat(sumaValNetGast).toFixed(2)==parseFloat($('#txt_grossvalue').val()).toFixed(2))
	{
		validacion=true;
	}
	else
	{
		alert('Invoice Gross Value ingresado no es valido');
		$("#txt_grossvalue").focus();
		validacion=false;
		return validacion;
	}
	
	return validacion;
}

function GrabarFactura(PAIS_ORIGEN)
{
	var afectadasHeader;
	var accionGrabar="UpdateEditarInvoiceDetailHeader";
	if($("#H_accionPagina").val()=="Ingreso")
	{
		accionGrabar="InsertEditarInvoiceDetailHeader";
	}
	else
	{
		accionGrabar="UpdateEditarInvoiceDetailHeader";
	}
	
	var errorProveedor=0;
	
	if($("#txtIdProveedor").val() == " ")
	{
		$("#cboProvedor").val("");
		$("#SeleccionProveedor").html("");
		errorProveedor++;
	}
	
	if(errorProveedor>0)
	{
		$("#btn_validarFormularioH810").trigger("click");
		$("#btn_grabarFactura").attr("disabled", false );
		$("#btn_eleminarFactura").attr("disabled", false );
		CerrarThickBox();
	}
	else
	{
		//Validar si estan seleccionados los registros
		if($("#SelAll").prop("checked"))
		{
			$('[name=bas]').each(function(){
				$(this).prop('checked', false);
				$(this).parents('tr').removeAttr('class');
				$(this).parent('td').parent('tr').find('td:eq(1)').attr('class','sorting_1');
			});
			
			$("#SelAll").prop("checked",false);
			$("#btn_grabarFactura").attr("disabled", false);
		}
		
		$.ajax({
					data:  {accion: accionGrabar,InvoiceNumber: $('#txt_invoiceNumber').val(), InvoiceDate: $('#invoice_date').val()
						   ,InvoiceCurrency: $('#currency').val(), InvoiceNetValue: $('#txt_netValue').val(), InvoiceGrossValue: $('#txt_grossvalue').val()
						   ,InvoiceGastos: $('#txt_invoiceGastos').val(), InvoiceVendor: $('#txtIdProveedor').val(), Sociedad:$('#cboSociedadEditar810').val()
						   },	
					url:   'ajax/editar810.php',
					type:  'post',
					beforeSend: function () {
					$("#AbrirCargando").trigger("click");
					},
					success:  function (response) {
						afectadasHeader=parseFloat(response);
						//alert(response);
						},
						complete: function(){
							
							if(afectadasHeader>0)
							{
								GrabarInvoiceDetail(PAIS_ORIGEN);
								$("#H_accionPagina").val("Modificar");
							}
							else{
								alert("Error: No se pudo registrar el invoice headersss");
							}
						}
			});
	}
	
}
function GrabarInvoiceDetail(PAIS_ORIGEN)
{	
	//Crearemos Json para el servidor php
	var ArrayDetalle = new Array();
	
	//Validar para que tome toda la factura en su detalle
	if($("[class='sorting_1']").find('label').size()==0)
	{
		$('[name=bas]').each(function(){
			$(this).prop('checked', false);
			$(this).parents('tr').removeAttr('class');
			$(this).parent('td').parent('tr').find('td:eq(1)').attr('class','sorting_1');
		});
	}
	
	
	$("[class='sorting_1']").find('label').each(function(){
	
			var paisOrigenHTML=$(this).parent('td').parent('tr').find('td:eq(8) label').html();
			
			var item = {
			"InvoicePosition": $(this).parent('td').parent('tr').find('td:eq(0) label').html(),
			"PONumber": $(this).parent('td').parent('tr').find('td:eq(1) label').html(),
			"POPosition": $(this).parent('td').parent('tr').find('td:eq(2) label').html(),
			"ProductID": $(this).parent('td').parent('tr').find('td:eq(3) label').html(),
			"ProductDesciption": $(this).parent('td').parent('tr').find('td:eq(4) label').html(),
			"ProductMeasure": $(this).parent('td').parent('tr').find('td:eq(5) label').html(),
			"ProductQuantity": $(this).parent('td').parent('tr').find('td:eq(6) label').html(),
			"PorductPrice": $(this).parent('td').parent('tr').find('td:eq(7) label').html(),
			"PaisOrigen": PAIS_ORIGEN[paisOrigenHTML],
			"EntregaEntrante": $(this).parent('td').parent('tr').find('td:eq(10)').html()			
		};
		//ImprimirObjeto(item);
		
		ArrayDetalle.push(item);
		});
		JSON_ArrayDetail = JSON.stringify({ArrayDetalle: ArrayDetalle});
		//alert(JSON_ArrayDetail);
		
		var afectadasDetail;
		
		$.ajax({
            data:  {accion: 'UpdateEditarInvoiceDetail',
			InvoiceNumber: $('#txt_invoiceNumber').val(),
			JsonDetail: JSON_ArrayDetail
			},	
            url:   'ajax/editar810.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				afectadasDetail=parseFloat(response);
				//alert(response);
                },
				complete: function(){
					if(afectadasDetail>0)
					{
						CerrarThickBox();
						$("#btn_grabarFactura").attr("disabled", false );
						$("#btn_eleminarFactura").attr("disabled", false );
						alert("Grabado correctamente");
					}
					else
					{
						CerrarThickBox();
						alert("Error: No se pudo registrar el invoice detail");
					}
				}
		});
}
function validarTablaDetalle()
{
	var valido=false;
	if(frm_input.checkValidity())
	{						
		$("[cambio='cambio']").each(function(){
			$(this).blur();
		});
						
		if(frm_input.checkValidity())
		{
			valido=true;
		}
		else
		{
			$("#btn_input").trigger("click");
		}
	}
	else
	{
		$("#btn_input").trigger("click");
	}
	return valido;
}
function EliminarFactura()
{
	var afectadasEliminar;
	var respuesta;
	$.ajax({
            data:  {accion:"EliminarFactura810",InvoiceNumber: $('#txt_invoiceNumber').val()},	
            url:   'ajax/editar810.php',
            type:  'post',
            beforeSend: function () {
                $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				afectadasEliminar=parseFloat(response);
				respuesta=response;
                },
				complete: function(){
					if(afectadasEliminar>0)
					{
						alert("Factura eliminada");
						$("#btn_grabarFactura").attr("disabled", true);
					}
					else
					{
						alert("Error no se pudo eliminar la factura :"+respuesta);
					}
					CerrarThickBox();
				}
	});
}
function BuscarOC()
{
	//CargandoBuscarOC
	var tbodyDetOC="";
	$.ajax({
            data:  { NumeroOC: $("#ADDOC_txtNumOC").val()},	
            url:   'ajax/ws_buscarOC_855.php',
            type:  'post',
            beforeSend: function () {
                $("#ADDOC_btnBuscarOC").css("display","none");
				$("#btn_agregarDetalleOC").attr("disabled", true );				
				$("#CargandoBuscarOC").css("display","inline");
            },
             success:  function (response) {
			 //alert(response);
			 
			 json=jQuery.parseJSON(response);
			 
			 
			 $.each(json, function(i, d) {
				$('#H_ocBuscada').val(d.cAMP2);
				tbodyDetOC+='<tr><td><input id="ADDDeOC" name="ADDDeOC" class="check_ADDDeOC" type="checkbox" onchange="javascript:CambiarAlClicChk(this);" value="'+d.EBELP+'"></td><td>'+d.EBELP+'</td><td>'+d.MATNR+'</td><td>'+d.TXZ01+'</td><td>'+d.MEINS+'</td>';
				tbodyDetOC+='<td>'+d.MENGE+'</td><td>'+d.NETPR+'</td><td>'+d.CAMP1+'</td><td>'+d.CAMP2+'</td></tr>';
			 });
			 $('#tabla_OC').dataTable().fnDestroy();
			 $('#tdBodyOC').html("");
			 $('#tdBodyOC').html(tbodyDetOC);
			 $('#tabla_OC').dataTable({
					"scrollY":        "200px",
					"scrollCollapse": true,
					"paging":         false,
					"language": LAN_ESPANOL
				});
			$("#tabla_OC_filter").find('label input').keypress(function() {
				eliminarTheadOC();
			});
			
			$("#tabla_OC_filter").find('label input').keyup(function() {
				eliminarTheadOC();
			});
			eliminarTheadOC();
			
			$("#tabla_OC_filter").find('label input').keypress(function() {
				eliminarTheadOC();
			});
			
			$("#tabla_OC_filter").find('label input').keyup(function() {
				eliminarTheadOC();
			});
			
             },
			 complete: function(){
				$("#ADDOC_btnBuscarOC").css("display","inline");
				$("#CargandoBuscarOC").css("display","none");
				$("#btn_agregarDetalleOC").attr("disabled", false );
			 }
	});
}
function AgregarOCAFactura()
{
	$("[name='ADDDeOC']").each(function(){
	  //.is(':checked')
	  if($(this).is(':checked'))
	  {
		calcularSiguienteEinvoicePosition();
		var cantida=parseFloat($(this).parent('td').parent('tr').find('td:eq(5)').html());
		var precio=parseFloat($(this).parent('td').parent('tr').find('td:eq(6)').html());
		var total=parseFloat(cantida*precio);
		total+=0.00;
		var t = $('#tabla_detalle').DataTable();
		
		var NumeroInvoicePosition=""+$("#Detailtxt_InvoicePosition").val();
		
		NumeroInvoicePosition=FormatearInvoicePosition(NumeroInvoicePosition);
		
		var NuevaFila='<tr>';
		NuevaFila+='<td><label id="InvPos" style="cursor:pointer">'+NumeroInvoicePosition+'</label></td>';
		NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+$(this).parent('td').parent('tr').find('td:eq(8)').html()+'</label></td>';
		NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+$(this).parent('td').parent('tr').find('td:eq(1)').html()+'</label></td>';
		NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+$(this).parent('td').parent('tr').find('td:eq(2)').html()+'</label></td>';
		NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+$(this).parent('td').parent('tr').find('td:eq(3)').html()+'</label></td>';
		NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+$(this).parent('td').parent('tr').find('td:eq(4)').html()+'</label></td>';
		NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="cantida" min="1" style="cursor:pointer">'+$(this).parent('td').parent('tr').find('td:eq(5)').html()+'</label></td>';
		NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="ProPrice" min="" style="cursor:pointer">'+$(this).parent('td').parent('tr').find('td:eq(6)').html()+'</label></td>';
		NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Cbo" itipo="Cbo" step="" valAdd="" min="" style="cursor:pointer" codPais="'+$(this).parent('td').parent('tr').find('td:eq(7)').html()+'">'+$(this).parent('td').parent('tr').find('td:eq(7)').html()+'</label></td>';
		NuevaFila+='<td><input type="checkbox" class="marca_chk" name="bas" id="bas" value="'+NumeroInvoicePosition+'" onchange="javascript:CambiarAlClicChk(this);"></td>';
		NuevaFila+='<td>0</td>';
		NuevaFila+='<td><label name="det_tot">'+parseFloat(total).toFixed(2)+'</label></td>';
		NuevaFila+='<td align="center" title="Nok" data-order="0"><img src="../../images/delete_16.png" width="13" height="13"></td>';
		NuevaFila+="</tr>";
		
		t.row.add($(NuevaFila)).draw();
		
		$("#Detailtxt_InvoicePosition").val("");			
		$("#thInvoicePosition").trigger("click");
	  }	  
	});
	renombrarPaisOrigen();
	CalcularTotalFactura(); 
	eliminarTheadDetalle();
}
function agregarLineaDetalle()
{
	calcularSiguienteEinvoicePosition();
	var t = $('#tabla_detalle').DataTable();
	
	var NumeroInvoicePosition=""+$("#Detailtxt_InvoicePosition").val();
	
	NumeroInvoicePosition=FormatearInvoicePosition(NumeroInvoicePosition);
	
	
	var NuevaFila='<tr>';
	NuevaFila+='<td><label id="InvPos" style="cursor:pointer">'+NumeroInvoicePosition+'</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">0</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">0</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">0-0</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">Description</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">Measure</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="cantida" min="1" style="cursor:pointer">0</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="ProPrice" min="" style="cursor:pointer">0</label></td>';
	NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Cbo" itipo="Cbo" step="" valAdd="" min="" style="cursor:pointer" codPais="">-Seleccione-</label></td>';
	NuevaFila+='<td><input type="checkbox" class="marca_chk" name="bas" id="bas" value="'+NumeroInvoicePosition+'" onchange="javascript:CambiarAlClicChk(this);"></td>';
	NuevaFila+='<td>0</td>';
	NuevaFila+='<td><label name="det_tot">0</label></td>';
	NuevaFila+='<td align="center" title="Nok" data-order="0"><img src="../../images/delete_16.png" width="13" height="13"></td>';
	NuevaFila+="</tr>";
	
	t.row.add($(NuevaFila)).draw();
	
	eliminarTheadDetalle();
	$("#Detailtxt_InvoicePosition").val("");			
}
function renombrarPaisOrigen()
{	 
	$("[id=InvPos]").each(function(){
		$("#cboPaisOrigen").val($(this).parent('td').parent('tr').find('td:eq(8) label').html());
		var textoPais=$("#cboPaisOrigen :selected").text();
		if(textoPais=="")
		{
			textoPais='-Seleccione-';
		}
		$(this).parent('td').parent('tr').find('td:eq(8) label').html(textoPais);
	});
	 
	CargarTotalEnBaseDeTabla();
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
function resultadoErroneoXLS(Error){
	var htmlRes=$("#respuestaXLS").html();
	if($.trim($("#respuestaXLS").html())=="")
	{
    $("#respuestaXLS").html(Error);
	}
	else{
	$("#respuestaXLS").html(htmlRes+'<br />'+Error);
	}
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
function resultadoActualXLS(respuesta){
	var htmlRes=$("#respuestaXLS").html();
	if($.trim($("#respuestaXLS").html())=="")
	{
    $("#respuestaXLS").html(respuesta);
	}
	else{
	$("#respuestaXLS").html(htmlRes+'<br />'+respuesta);
	}
}
function DetenerCarga()
{
	$("#boton_subir_archivo").css("display","inline");
	$("#btn_CancelarCsv").css("display","inline");	
	$("#ImagenCargando").css("display","none");
	$("#archivo").attr("disabled", false );
	//$("#btn_CancelarCsv").trigger("click");	
}
function DetenerCargaXLS()
{
	$("#boton_subir_archivoXLS").css("display","inline");
	$("#btn_CancelarXLS").css("display","inline");	
	$("#ImagenCargandoXLS").css("display","none");
	$("#archivoXLS").attr("disabled", false );
	//$("#btn_CancelarXLS").trigger("click");	
}
function limpiarSearchDetalle()
{
	var CampoSearch= $('#tabla_detalle_filter').find('label input');
		
	$(CampoSearch).val("");
		
	ev = $.Event('keyup');
	ev.keyCode= 13; // enter
	$(CampoSearch).trigger(ev);
}
function ProcesarCSV810(idUsuario,ruta)
{
	var JsonDeProveedores;
	$.ajax({
            data:  {},	
            url:   'ajax/ws_buscarProvedores.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				JsonDeProveedores=response;
				},
				complete: function(){
						//Enviando datos para ProcesarCSV
						var errores=0;
						var mensajeError="";
						
						$.ajax({
								data:  {idUsuario:idUsuario,
										rutaCSV: ruta,
										JsonDeProveedores:JsonDeProveedores
										},	
								url:   'ajax/procesarCSV.php',
								type:  'post',
								beforeSend: function () {
								},
								success:  function (response) {
									var json = jQuery.parseJSON(response);
									$.each(json, function(i, d) {
											if(d.Resultado=="Correcto")
											{
																								
												//Cargar el Header
												var jsonH = jQuery.parseJSON(d.Header);
												//alert(JSON.stringify(jsonH));
												$.each(jsonH, function(j, h) {
												
													if($("#H_accionPagina").val()!="Ingreso" && h.NumeroFactura!=$("#txt_invoiceNumber").val())
													{
														errores++;
														resultadoErroneo("Error el archivo ingresado no corresponde al numero de factura "+$("#txt_invoiceNumber").val());
													}
													
													if(errores==0)
													{
														$("#txt_invoiceNumber").val(h.NumeroFactura);
														$("#cboSociedadEditar810").val(h.sociedad);
															
														$("#proveedoresHidden").val(h.Vendedor);
														$("#cboProvedor").val($("#proveedoresHidden option:selected").text());
														$("#SeleccionProveedor").html($("#proveedoresHidden option:selected").text());
																
														$("#invoice_date").val(h.FechaFactura);
														$("#currency").val(h.currency);
															
														var NetValue=parseFloat(h.NetValue);
														NetValue+=0.00;						
														$("#txt_netValue").val(NetValue.toFixed(2));
																
														var GrossValue=parseFloat(h.GrossValue);
														GrossValue+=0.00;	
														$("#txt_grossvalue").val(GrossValue.toFixed(2));
																
														var GastosValue=parseFloat(h.Gastos);
														GastosValue+=0.00;	
														$("#txt_invoiceGastos").val(GastosValue.toFixed(2));
													}
													
												});
												
												if(errores==0)
												{
													//Cargar el Detail
													var jsonD = jQuery.parseJSON(d.Detalle);
													$.each(jsonD, function(k, dt) {						
														var cantida=parseFloat(dt.ProductQuantity);
														var precio=parseFloat(dt.ProductPrice);
														var total=cantida*precio;
														var t = $('#tabla_detalle').DataTable();
																	
														var NumeroInvoicePosition=""+dt.InvoicePosition;
																	
														NumeroInvoicePosition=FormatearInvoicePosition(NumeroInvoicePosition);
															
														$("#cboPaisOrigen").val(dt.PaisOrigen);
														
														var EE=0;
														try {EE=parseFloat($("#H_OldEE").val())+0;}catch(err) {}
														if(isNaN(EE)){EE=0}
														
														var NuevaFila='<tr>';
														NuevaFila+='<td><label id="InvPos" style="cursor:pointer">'+NumeroInvoicePosition+'</label></td>';
														NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+dt.PONumber+'</label></td>';
														NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+dt.POPosition+'</label></td>';
														NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+dt.ProductID+'</label></td>';
														NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+dt.ProductDescription+'</label></td>';
														NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+dt.ProductMeasure+'</label></td>';
														NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="cantida" min="1" style="cursor:pointer">'+dt.ProductQuantity+'</label></td>';
														NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="ProPrice" min="" style="cursor:pointer">'+dt.ProductPrice+'</label></td>';
														NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Cbo" itipo="Cbo" step="" valAdd="" min="" style="cursor:pointer" codPais="'+$("#cboPaisOrigen").val()+'">'+$("#cboPaisOrigen :selected").text()+'</label></td>';
														NuevaFila+='<td><input type="checkbox" class="marca_chk" name="bas" id="bas" value="'+NumeroInvoicePosition+'" onchange="javascript:CambiarAlClicChk(this);"></td>';
														NuevaFila+='<td>'+EE+'</td>';
														NuevaFila+='<td><label name="det_tot">'+parseFloat(total).toFixed(2)+'</label></td>';
														NuevaFila+='<td align="center" title="Nok" data-order="0"><img src="../../images/delete_16.png" width="13" height="13"></td>';
														NuevaFila+="</tr>";
														
														t.row.add($(NuevaFila)).draw();
														
													});
													renombrarPaisOrigen();
													CalcularTotalFactura();
													eliminarTheadDetalle();
												}												
											}
									       });
									},
									complete: function(){
										//alert("FIN");
										resultadoActual('<br />====================== Proceso terminado CSV ====================== <br /><br />');
										DetenerCarga();
										$("#btn_CancelarCsv").trigger("click");
									}
						});
				}
	});
}
function ProcesarXLS810(ruta)
{
	$.ajax({
            data:  {
					ArchivoExcel:ruta
					},	
            url:   'ajax/procesarExcel810.php',
            type:  'post',
            beforeSend: function () {
               //$("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				var json = jQuery.parseJSON(response);
					$.each(json, function(i, dt) {
						
						var cantida=parseFloat(dt.Product_Quantity);
							cantida+=0.00;
							
						var precio=parseFloat(dt.Porduct_Price);
							precio+=0.00;
						
						var total=cantida*precio;
							total+=0.00;
						var t = $('#tabla_detalle').DataTable();
																	
						var NumeroInvoicePosition=""+dt.Invoice_Position;
																	
						NumeroInvoicePosition=FormatearInvoicePosition(NumeroInvoicePosition);
															
						$("#cboPaisOrigen").val(dt.Pais_Origen);
						
						var EE=0;
						try {EE=parseFloat($("#H_OldEE").val())+0;}catch(err) {}
						if(isNaN(EE)){EE=0}
						
						var NuevaFila='<tr>';
						NuevaFila+='<td><label id="InvPos" style="cursor:pointer">'+NumeroInvoicePosition+'</label></td>';
						NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+dt.PO_Number+'</label></td>';
						NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" min="1" valAdd="" style="cursor:pointer">'+dt.PO_Position+'</label></td>';
						NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+dt.Product_ID+'</label></td>';
						NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+dt.Product_Description+'</label></td>';
						NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="" valAdd="" style="cursor:pointer">'+dt.Product_Measure+'</label></td>';
						NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="cantida" min="1" style="cursor:pointer">'+cantida+'</label></td>';
						NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Input" itipo="number" step="any" valAdd="ProPrice" min="" style="cursor:pointer">'+precio.toFixed(2)+'</label></td>';
						NuevaFila+='<td><label onclick="javascript:ModificarCelda(this);" id="Cbo" itipo="Cbo" step="" valAdd="" min="" style="cursor:pointer" codPais="'+$("#cboPaisOrigen").val()+'">'+$("#cboPaisOrigen :selected").text()+'</label></td>';
						NuevaFila+='<td><input type="checkbox" class="marca_chk" name="bas" id="bas" value="'+NumeroInvoicePosition+'" onchange="javascript:CambiarAlClicChk(this);"></td>';
						NuevaFila+='<td>'+EE+'</td>';
						NuevaFila+='<td><label name="det_tot">'+total.toFixed(2)+'</label></td>';
						NuevaFila+='<td align="center" title="Nok" data-order="0"><img src="../../images/delete_16.png" width="13" height="13"></td>';
						NuevaFila+="</tr>";
						
						t.row.add($(NuevaFila)).draw();	
					});	
					eliminarTheadDetalle();
			},
			complete: function(){
				
				DetenerCargaXLS();
				$("#btn_CancelarXLS").trigger("click");
				CalcularTotalFactura();
			}
	});
}
function eliminarTheadDetalle()
{
	$("#tabla_detalle").find('thead').css("display","none");
}
function eliminarTheadOC()
{
	$("#tabla_OC").find('thead').css("display","none");
}
function SelecionarTodas()
{
	if($('#SelAll').is(':checked'))
	{
		$('[name=bas]').each(function(){
			$(this).prop('checked', true);
			$(this).parents('tr').removeAttr('class');
			$(this).parents('tr').attr('class', 'selected'); 
		});
	}
	else
	{
		$('[name=bas]').each(function(){
			$(this).prop('checked', false);
			$(this).parents('tr').removeAttr('class');
		});
	}
}
function CargarToleranciaPorSociedad()
{
	$("#btn_verificar").attr("disabled", true);
	$("#btn_grabarFactura").attr("disabled", true);
	
	$.post('ajax/editar810.php',{
			accion		: 'obtenerToleranciaPorSociedad',
			Sociedad	: $('#cboSociedadEditar810').val()
			}, 
	function(response) {			
		var json = jQuery.parseJSON(response);
		
		$("#H_Tolerancia").val(0);
		
		$.each(json, function(i, d) {
			$("#H_Tolerancia").val(parseFloat(d.tolerancia)+0);
		});
	}).done(function(response) {
		$("#btn_verificar").attr("disabled", false);
		$("#btn_grabarFactura").attr("disabled", false);
	});
}
function dejarTablabusquedaNormal()
{
	var divHead=$('#tabla_facturas_wrapper').find('div div div');
	$(divHead).css('width','');
	
	var tableDeHead=$(divHead).find('table');
	$(tableDeHead).css('width','');
}