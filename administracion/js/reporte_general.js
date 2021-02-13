$( document ).ready(function() {
	
	$( "#fecha" ).datepicker({dateFormat:"dd/mm/yy"}).datepicker("setDate",new Date());
	
	$('#tabla_log_general').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
		"iDisplayLength": 10
	});
		
	$('#tabla_detalle').dataTable( {
		"language": LAN_ESPANOL,
		 "bPaginate": true,
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
	
	loadLogGeneral();
	
	$("#btn_refrescar").click(function() {      
		loadLogGeneral();
    });
	
	$("#btn_volver").click(function(){
		var url="../../internos.php";
		$(location).attr('href',url);
    });
    
});

function loadLogGeneral()
{
	$("#AbrirCargando").trigger("click");
	var fecha = $("#fecha").val();
	var estatus = $("#estatus").val();
	
	$.post('ajax/reporte_general.php',{ accion: 'getCabecera', fecha:fecha }, function(response) {
	}).done(function(response) {
		
		var json = jQuery.parseJSON(response);
		var content = "";
		var cont = 1;
		var count = 0 ;
		
			$.each(json, function(i, d) {
				count = count+1;				
				if(d.erroneas == 0 && estatus=="1") 
				{
					content += '<tr style="cursor:pointer;" onclick="verDetalle(this,\''+d.correlativo+'\', \''+d.idproveedor+'\', \''+d.proveedor+'\')">';
					content += '<td >'+d.correlativo+'</td>';
					content += '<td >'+d.idproveedor+'</td>';
					content += '<td >'+d.proveedor+'</td>';
					content += '<td align="center">'+d.hora+'</td>';
					content += '<td align="center">'+getNombreIdFormato(d.id_formato)+'</td>';
					content += '<td align="center">'+getNombreTipoDocumento(d.id_tipo)+'</td>';
					content += '<td align="center" id="'+cont+'" ><img src="../images/tick_16.png" height="13" width="13" ></td>';
					content += '</tr>';
                } 
				if(d.erroneas > 0 && estatus=="0") 
				{
					//alert(d.erroneas);
					content += '<tr style="cursor:pointer;" onclick="verDetalle(this,\''+d.correlativo+'\', \''+d.idproveedor+'\', \''+d.proveedor+'\')">';
					content += '<td >'+d.correlativo+'</td>';
					content += '<td >'+d.idproveedor+'</td>';
					content += '<td >'+d.proveedor+'</td>';
					content += '<td align="center">'+d.hora+'</td>';
					content += '<td align="center">'+getNombreIdFormato(d.id_formato)+'</td>';
					content += '<td align="center">'+getNombreTipoDocumento(d.id_tipo)+'</td>';
					content += '<td align="center"><img src="../images/delete_16.png" height="13" width="13" ></td>';
					content += '</tr>';
				}
				if(estatus=="")
				{
					content += '<tr style="cursor:pointer;" onclick="verDetalle(this,\''+d.correlativo+'\', \''+d.idproveedor+'\', \''+d.proveedor+'\')">';
					content += '<td >'+d.correlativo+'</td>';
					content += '<td >'+d.idproveedor+'</td>';
					content += '<td >'+d.proveedor+'</td>';
					content += '<td align="center">'+d.hora+'</td>';
					content += '<td align="center">'+getNombreIdFormato(d.id_formato)+'</td>';
					content += '<td align="center">'+getNombreTipoDocumento(d.id_tipo)+'</td>';
				
					if(d.erroneas == 0) 
					{
						content += '<td align="center" id="'+cont+'" ><img src="../images/tick_16.png" height="13" width="13" ></td>';
					} else {
						content += '<td align="center"><img src="../images/delete_16.png" height="13" width="13" ></td>';
					} 
					content += '</tr>';
				}				
			});
			$('#tabla_log_general').dataTable().fnDestroy();
			
			$('#tdBodyLogGeneral').html(content);
						
			$('#tabla_log_general').dataTable({
				"language": LAN_ESPANOL,
				"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
				"iDisplayLength": 10
			});
						
			CerrarThickBox();
			/**
			if(count == 0) {
				var fecha_busqueda = $("#fecha").val();
				alert("No se encontraron datos con Fecha: "+fecha_busqueda);
			} else {
				filtraFilas(estatus);
			}*/
			
	});
}

function verDetalle(celda,correlativo, idproveedor, proveedor)
{
	$('#myModalLabel').html('Detalle '+$(celda).find('td:eq(2)').html()+' - '+$(celda).find('td:eq(4)').html()+' '+$(celda).find('td:eq(5)').html());
	
	
	$('#tbodyDetalleGral').html("");
	var fecha   = $('#fecha').val();
	var estatus = $('#estatus').val();
	
	$("#correlativo").val(correlativo);
	$("#val_idproveedor").val(idproveedor);
	$("#val_proveedor").val(proveedor);
	$("#val_fecha").val(fecha);
	////////////////////////////////////////////////////////
	$.post('ajax/reporte_general.php',{ 
									accion: 'getDetalle', 
									correlativo:correlativo,
									estatus:estatus
								}, function(response) {
	}).done(function(response) {
		//alert(response);
		var json = jQuery.parseJSON(response);
		$(function () {
			
			var content = "";
			$.each(json, function(i, d) {
				
				content += '<tr >';
				content += '<td>'+d.hora+'</td>';
				content += '<td>'+d.descripcion+'</td>';
				content += '<td>'+d.archivo+'</td>';
				
				if(d.status == 1) {
					content += '<td align="center"><img src="../images/tick_16.png" height="13" width="13" ></td>';
                } else  {
					content += '<td align="center"><img src="../images/delete_16.png" height="13" width="13"></td>';
				} 
				content += '</tr>';
			});
			$('#tabla_detalle').dataTable().fnDestroy();
			
			$('#tbodyDetalleGral').html(content);
			
			$('#tabla_detalle').dataTable({
				"language": LAN_ESPANOL,
				"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
				"iDisplayLength": 10
			});
		});
		$( "#btn_open_modal" ).trigger( "click" );
	});
}

function filtraFilas(filtro)
{
	$("#tabla_log_general tbody tr").each(function (index) {

		if(filtro == '1') //correctas
		{	
			$(this).children("td").each(function (index2) {
				
				switch (index2) {
					case 0:
						break;
					case 1:
						break;
					case 2:
						break;
					case 3:
						break;
					case 4:
						var html = $(this).html();
						if(html == '<img src="../images/tick_16.png" height="13" width="13">') {
							$(this).parent().show();
						}
						else {
							$(this).parent().hide();
						}
						break;
				}
			});
		}
		if(filtro == '0') //erroneas
		{
			
			$(this).children("td").each(function (index2) {
				
				switch (index2) {
					
					case 0:
						break;
					case 1:
						break;
					case 2:
						break;
					case 3:
						break;
					case 4:
						var html = $(this).html();
						if(html == '<img src="../images/delete_16.png" height="13" width="13">') {
							$(this).parent().show();
						}
						else {
							$(this).parent().hide();
						}
						break;
				}
			});
		}
		if(filtro == '2' || filtro == '') //todas
		{
			$(this).show();
		}
	});
}


