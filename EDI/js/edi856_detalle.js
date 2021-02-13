$( document ).ready(function() {
	//tabla detalle
	$('#tabla_datos_oc_detalle').dataTable( {
		"scrollY": "200px",
		"scrollCollapse": true,
		"paging": false,
		"language": LAN_ESPANOL,
		"fnInitComplete": function () {
			this.fnAdjustColumnSizing();
			this.fnDraw();
		}
	});
	
	//tabla modal
	$('#tabla_nro_oc_modal').dataTable( {
		"scrollY": "200px",
		"scrollCollapse": true,
		"paging": false,
		"language": LAN_ESPANOL,
		"fnInitComplete": function () {
			this.fnAdjustColumnSizing();
			this.fnDraw();
		}
	});

    $("#ver_mas_detalle").click(function(e) {      
		infoAdicional();    
    });
    $("#volver_detalle").click(function(e) {
		$("#Divbtn_volverHome").css("display","block");
		$("#Divbtn_volverHome").css("margin-left","25px");
		volver_principal();    
    });
    
    cargaDetalle(); 
});

function volver_principal() 
{
	//$("#Divbtn_volverHome").css("margin-left","25px");
	$("#div_detalle").html("");
	$("#div_detalle").hide();
	$("#wrapper").show();
}

function infoAdicional() 
{
	var factura = $("#nro_factura_detalle").val();
	$('#tabla_nro_oc_modal tbody').html("");
	
	$.post('ajax/edi856.php',{ accion: 'cargaInfoAdicional', factura:factura }, function(response) {
	}).done(function(response) {
		var json = jQuery.parseJSON(response);
		var content = "";
		$.each(json, function(i, d) {
				content += '<tr>';
				content += '<td >'+d.identidad+'</td>';
				content += '<td >'+d.idcodigo+'</td>';
				content += '<td >'+d.idcalificador+'</td>';
				content += '</tr>';				
		});
		$('#tabla_nro_oc_modal').dataTable().fnClearTable();
		$('#tabla_nro_oc_modal').dataTable().fnDestroy();
		$('#tabla_nro_oc_modal tbody').html(content);
		
		$('#tabla_nro_oc_modal').dataTable( {
			"scrollY": "200px",
			"scrollCollapse": true,
			"paging": false,
			"language": LAN_ESPANOL,
			"fnInitComplete": function () {
				this.fnAdjustColumnSizing();
				this.fnDraw();
			}
		});
	});
}

function cargaDetalle() 
{
	$('#tabla_datos_oc_detalle tbody').html("");
	var nro_factura = $("#nro_factura_detalle").val();

	$.post('ajax/edi856.php',{ accion: 'cargaDetalle', nro_factura:nro_factura }, function(response) {
	}).done(function(response) {

		var json = jQuery.parseJSON(response);
		var content = "";
		$.each(json, function(i, d) 
		{	
			content += '<tr id="fact_'+d.nro_factura+'" >';
			content += '<td align="center">'+d.nro_factura+'</td>';
			content += '<td align="center">'+d.nro_parte+'</td>';
			content += '<td align="center">'+d.orden_compra+'</td>';
			content += '<td align="center">'+d.PoPosition+'</td>';			
			content += '<td align="center">'+d.unidad_medicion+'</td>';
			content += '<td align="center">'+d.tracking_number+'</td>';
			content += '<td align="center">'+d.cantidad_despachada+'</td>';
			content += '<td align="center"><label style="cursor: pointer" onClick="cargaDetalleBulto(event,this)">'+d.packing_slip+'</label></td>';
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
			"fnInitComplete": function () {
				this.fnAdjustColumnSizing();
				this.fnDraw();
			}
		});
		CerrarThickBox();
	});
}
function cargaDetalleBulto(e,Celda) 
{
	e.preventDefault();
	var IdBulto = $(Celda).html();
	$('#btnAbrirModalBulto').trigger('click');
	
	$.post('ajax/edi856.php',{
		accion: 'cargaDetalleBulto', 
		factura:$('#nro_factura_detalle').val(),
		IdBulto:IdBulto 
	}, function(response) {
	}).done(function(response) {

		$('#lblModalTitulo').html('- Bulto '+IdBulto);
	
		var json = jQuery.parseJSON(response);
		var content = "";
		$.each(json, function(i, d) 
		{	
			var Fromfecha_despacho = d.fechaDespacho.split("-");
			var Fecha_despacho = new Date(Fromfecha_despacho[2]+"/"+Fromfecha_despacho[1]+"/"+Fromfecha_despacho[0]+" 00:00:00");
			var FechaNumericaFecha_despacho = Fecha_despacho.getTime() / 1000;
			
			content += '<tr id="Bulto_'+IdBulto+'" >';
			content += '<td align="center">'+d.tipoBulto+'</td>';
			content += '<td align="center">'+d.peso+'</td>';
			content += '<td align="center">'+d.unidPeso+'</td>';
			content += '<td align="center">'+d.volumen+'</td>';			
			content += '<td align="center">'+d.unidVolumen+'</td>';
			content += '<td align="center">'+d.longitud+'</td>';
			content += '<td align="center">'+d.ancho+'</td>';
			content += '<td align="center">'+d.alto+'</td>';
			content += '<td align="center">'+d.unidDimension+'</td>';
			content += '<td align="center" data-order="'+FechaNumericaFecha_despacho+'">'+d.fechaDespacho+'</td>';
			content += '<td align="center">'+d.instEspeciales+'</td>';
			content += '</tr>';
		});
		
		$('#tabla_bulto_modal').dataTable().fnClearTable();
		$('#tabla_bulto_modal').dataTable().fnDestroy();
		$('#tabla_bulto_modal tbody').html(content);
		
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