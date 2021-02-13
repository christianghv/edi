$( document ).ready(function() {

	$('#tabla_detalle').dataTable( {
		"language": LAN_ESPANOL,
		"bPaginate": false,
		"bAutoWidth": true,
		"bFilter":false,
		"bInfo": false,
		"fnInitComplete": function () {
			this.fnAdjustColumnSizing();
			this.fnDraw();
		}
	});
	inicioCarga();
	$("#btn_volver").click(function(){
		var url="../../internos.php";
		$(location).attr('href',url);
    });
});

function inicioCarga()
{
	$("#div_contenido").html("");
	
	$.post('ajax/reportes.php',{ accion: 'getSociedades'}, function(response) {
	}).done(function(response) {
		var json = jQuery.parseJSON(response);
		$(function () 
		{
			var content = "<br />";
			$.each(json, function(i, d) { //GENERA CONTENEDORES PARA CADA SOCIEDAD
				
				content += '<div class="row" style="margin-left:10px; margin-top:5px; margin-bottom:0px; width:98%;"  id="div_'+d.idsociedad+'"></div>';
			});
			$("#div_contenido").append(content);
			
			$.each(json, function(i, d) { //CONTENIDO PARA CADA SOCIEDAD
				$.post('ajax/reportes.php',{ accion: 'getNumProcesos', idsociedad:d.idsociedad}, function(response) {
				}).done(function(nprocesos) {
					generaPanelSociedad(d.idsociedad, d.descripcion);			
				});
			});
		});
	});
}

function generaPanelSociedad(idsociedad, descripcion)
{
	$("#div_"+idsociedad).html("");
	
	$.post('ajax/reportes.php',{ accion: 'getProcesos', idsociedad:idsociedad}, function(response) {
	}).done(function(response) {
		//alert(response);
		var json = jQuery.parseJSON(response);
		$(function () {
			
			var content = "";
			content += '<div class="panel panel-default">';
			
			content += '<div class="panel-heading">';
			content += '<div class="row">';
			
			content += '	<div class="col-lg-3">';
			content += '		<label for="sociedad_'+idsociedad+'">Sociedad:</label>';
			content += '		<input class="form-control" id="sociedad_'+idsociedad+'" name="sociedad_'+idsociedad+'" title="Sociedad" value="'+descripcion+'" disabled />';
			content += '	</div>';
			
			content += '	<div class="col-lg-2">';
			content += '    	<label for="fecha_'+idsociedad+'">Fecha:</label>';
			content += '		<input class="form-control" placeholder="" id="fecha_'+idsociedad+'" name="fecha_'+idsociedad+'" title="Seleccione Fecha" />';
			content += '		<script>$("#fecha_'+idsociedad+'").datepicker({dateFormat:"dd/mm/yy"}).datepicker("setDate",new Date());</script>';
			content += ' 	</div>';
			
			content += ' 	<div class="col-lg-1" style="margin-top:12px; margin-bottom:-2px;" align="center" >';
			content += '			<button type="button" class="btn btn-primary" id="btn_'+idsociedad+'" onclick="refrescarSociedad(\''+idsociedad+'\')" style="display:block;">';
			content += '    		<span class="glyphicon glyphicon-refresh"></span> Refrescar</button>';
			content += '			<div id="div_refrescando_'+idsociedad+'" style="display:none; margin-top:10px; font-size:14px; font-weight:bold;">Refrescando...</div>';
			content += ' 	</div>';
			
			content += '</div></div>';
			
			content += '<div class="panel-body">';
			content += '<div class="row" style="overflow-y:scroll; max-height:250px;">';
			
			var cont = 0;
			$.each(json, function(i, d) { //GENERA CONTENEDORES PARA CADA SOCIEDAD
				
				content += '<div class="col-lg-4">';
				content += '<div class="panel panel-default">';
				content += '<div class="panel-heading">'+d.descripcion_tipoproceso+'</div>';
				content += '<div class="panel-body">';
				content += '<div class="table-responsive">';
				content += '<table class="table table-striped table-bordered table-hover" id="tabla_'+idsociedad+'_'+d.tipoproceso+'" >';
				content += '<thead><tr><th>Proveedor</th><th>Correlativo</th><th>Cargadas</th><th>Erroneas</th></tr></thead>';
				content += '<tbody></tbody>';
				content += '</table></div></div></div></div>';
				cont++;
			});
			
			content += '</div></div></div>';
			$("#div_"+idsociedad).append(content);
			
			$.each(json, function(i, d) {
							
				$('#tabla_'+idsociedad+'_'+d.tipoproceso).dataTable( {
					"language": LAN_ESPANOL,
					"bPaginate": false,
					"bAutoWidth": true,
					"bRetrieve": true,
					"bFilter": false,
					"bInfo":false,
					"oLanguage": {"sEmptyTable": " "},
					"fnInitComplete": function () {
						this.fnAdjustColumnSizing();
						this.fnDraw();
					}
				}); //SETEA TABLA//
				if(cont > 0)
					llenaTabla(idsociedad, d.tipoproceso);
			});
		});
	});
}

function llenaTabla(idsociedad, tipoproceso)
{
	$('#tabla_'+idsociedad+'_'+tipoproceso+' tbody').html("");
	var fecha = $('#fecha_'+idsociedad).val();	
	
	$.post('ajax/reportes.php',{ 
									accion: 'getDatos', 
									idsociedad:idsociedad, 
									tipoproceso:tipoproceso,
									fecha: fecha
								})
	.done(function(response2) {
		
		$(function () {
			
			var content = "";
			
			if($.trim(response2)!="")
			{
				var json2 = jQuery.parseJSON(response2);
				
				$.each(json2, function(i, d) {
											
					content += '<tr style="cursor:pointer" ';
					content += ' onclick="verDetalle(\''+ idsociedad+'\', \''+ tipoproceso+'\',\''+ d.idproveedor+'\', \''+fecha+'\', \''+d.correlativo+'\', \''+d.proveedor+'\', \''+d.sociedad+'\', \''+d.tipoproceso+'\')">';
					content += '<td >'+d.proveedor+'</td>';
					content += '<td >'+d.correlativo+'</td>';
					content += '<td >'+d.correctas+'</td>';
					content += '<td >'+d.erroneas+'</td>';
					content += '</tr>';			
				});
			}
			
			$('#tabla_'+idsociedad+'_'+tipoproceso).dataTable().fnClearTable();
			$('#tabla_'+idsociedad+'_'+tipoproceso).dataTable().fnDestroy();
			$('#tabla_'+idsociedad+'_'+tipoproceso+' tbody').html(content);
			$('#tabla_'+idsociedad+'_'+tipoproceso).dataTable( {
				"language": LAN_ESPANOL,
				"bPaginate": false,
				"bAutoWidth": true,
				"bRetrieve": true,
				"bFilter": false,
				"bInfo":false,
				"oLanguage": {"sEmptyTable": " "},
					"fnInitComplete": function () {
						this.fnAdjustColumnSizing();
						this.fnDraw();
					}
			});	
		});
	});
}

function refrescarSociedad(idsociedad)
{
	//$("#btn_"+idsociedad).hide();
	//$("#div_refrescando_"+idsociedad).show();
	
	$.post('ajax/reportes.php',{ accion: 'getProcesos', idsociedad:idsociedad}, function(response) {
	}).done(function(response) {
		var json = jQuery.parseJSON(response);
		$(function () {
			$.each(json, function(i, d) { 
				llenaTabla(idsociedad, d.tipoproceso);
			});
		});
	});
}

function verDetalle(idsociedad, tipoproceso, idproveedor, fecha, correlativo, proveedor, sociedad, tipoproceso)
{
	$("#val_tipoproceso").val(tipoproceso);
	if(tipoproceso=="EDI855")
	{
		$("#thIdFormato").html("Orden de Compra");
	}
	else
	{
		$("#thIdFormato").html("Nro Factura");
	}
	$("#val_proveedor").val(proveedor);
	$("#val_fecha").val(fecha);
	$("#val_sociedad").val(sociedad);
	
	$('#tabla_detalle tbody').html("");
	var fecha = $('#fecha_'+idsociedad).val();
	////////////////////////////////////////////////////////
	$.post('ajax/reportes.php',{ 
									accion: 'getDatosDetalle', 
									correlativo:correlativo,
								}, function(response) {
	}).done(function(response) {
		
		var json = jQuery.parseJSON(response);
		$(function () {
			
			var content = "";
			$.each(json, function(i, d) {

				content += '<tr style="cursor:pointer" >';
				content += '<td >'+d.hora+'</td>';
				content += '<td >'+d.descripcion+'</td>';
				content += '<td >'+d.nro_factura+'</td>';
				
				if(d.estatus == 1) {
					content += '<td align="center"><img src="../images/tick_16.png" height="13" width="13" ></td>';
                } else  {
					content += '<td align="center"><img src="../images/delete_16.png" height="13" width="13"></td>';
				} 
				
				content += '</tr>';
			});
			$('#tabla_detalle').dataTable().fnClearTable();
			$('#tabla_detalle').dataTable().fnDestroy();
			$('#tabla_detalle tbody').html(content);
			$('#tabla_detalle').dataTable( {
				"language": LAN_ESPANOL,
				 "bPaginate": false,
				 "bAutoWidth": true,
				 "bRetrieve": true,
				 "bFilter": false,
				 "bInfo":false,
				 "oLanguage": {"sEmptyTable": " "},
				 "fnInitComplete": function () {
						this.fnAdjustColumnSizing();
						this.fnDraw();
					}
			});
		});
		$( "#btn_open_modal" ).trigger( "click" );
	});
}



