$( document ).ready(function() {	
	$('#body_edi855 [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
      style: 'cream' // Give it some style
	});
	cargaSociedades("(*) Sociedad: ", "sociedad");
	//////////////////////////////////////////////////
	$("#btn_buscar_principal").click(function() {  
		
		$("#sociedad").attr('required', 'required');
		$("#submit_filtros").trigger("click");		
		if(frm_filtros.checkValidity()){
			$("#AbrirCargando").trigger("click");    
			cargaOC();
		}
    });

	$("#exportar_principal").click(function() {
		exportarExcel();
    });
	
	$("#btn_incio").click(function(){
		var url="../../internos.php";
		$(location).attr('href',url);
		$("#Divbtn_volverHome").css("margin-left","25px");
    });
	
    $("#exportar_principal").attr("disabled",true);
	
	$('#n_oc_principal').dblclick(function() {
		$("#ingreso_oc").trigger("click");
	});
});

function exportarExcel() {
	var sociedad = $('#sociedad').val();
	var nro_oc   = $('#n_oc_principal').val();
	var inicio   = $('#fecha1_principal').val();
	var termino  = $('#fecha2_principal').val();
	
	if(nro_oc=="")
	{
		var textArea=$('#txtNumeroOC').val();
		var value = textArea.replace(/\r?\n/g, "|");
		nro_ocs=value;
		
		if(nro_oc != "") {
			nro_ocs += nro_oc;
		}
		nro_oc=nro_ocs;
	}
	
	url = 'ajax/descargaexcel.php?sociedad='+sociedad+'&nro_oc='+nro_oc+'&inicio='+inicio+'&termino='+termino;
	
	window.open(url, '_blank');
}
function cargaOC() 
{
	$("#exportar_principal").attr("disabled",true);
	var sociedad = $("#sociedad").val();
	var nro_oc   = $("#n_oc_principal").val();
	var inicio   = $("#fecha1_principal").val();
	var termino  = $("#fecha2_principal").val();
	var nro_ocs 		= '';
	
	var textArea=$('#txtNumeroOC').val();
	
	var value = textArea.replace(/\r?\n/g, "|");
	nro_ocs=value;
	
	if(nro_oc != "") {
		nro_ocs += nro_oc;
	}

	$.post('ajax/edi855.php',{ accion: 'cargaOC', sociedad:sociedad, nro_ocs:nro_ocs, inicio:inicio, termino:termino }, function(response) {
	}).done(function(response) {

			$('#div_table_data').html("");
			var json = jQuery.parseJSON(response);
			var content = "";
			var count = 0;
			content += '<table class="display" cellspacing="0" width="100%" id="tabla_oc_principal">';
			content += '<thead><tr>';
			content += '<th>Nro OC</th><th>Fecha OC</th><th>Fecha ACK</th><th>Embarque</th>';
			content += '<th>Nro Item</th><th>Sociedad</th><th>Dif Enviado</th><th>Modificado</th>';
			content += '</tr></thead><tbody>';
			
			$.each(json, function(i, d) {
				
				var Fromfecha_oc = d.fecha_oc.split("-");
				var Fecha_oc = new Date(Fromfecha_oc[2]+"/"+Fromfecha_oc[1]+"/"+Fromfecha_oc[0]+" 00:00:00");
				var FechaNumericaFechaOc = Fecha_oc.getTime() / 1000;
				
				var Fromfecha_ack = d.fecha_ack.split("-");
				var Fecha_ack = new Date(Fromfecha_ack[2]+"/"+Fromfecha_ack[1]+"/"+Fromfecha_ack[0]+" 00:00:00");
				var FechaNumericaFecha_ack = Fecha_ack.getTime() / 1000;
				
				content += '<tr id="oc_'+d.nro_oc+'" style="cursor:pointer;" ';
				content += 'onClick="verDetalleOC(\''+d.nro_oc+'\', \''+d.fecha_oc+'\', \''+d.fecha_ack+'\', ';
				content += '\''+d.embarque+'\', \''+d.nro_item+'\', \''+d.sociedad+'\', \''+d.checkid+'\', \''+d.modificada+'\');">';
				content += '<td align="center">'+d.nro_oc+'</td>';
				content += '<td align="center" data-order="'+FechaNumericaFechaOc+'">'+d.fecha_oc+'</td>';
				content += '<td align="center" data-order="'+FechaNumericaFecha_ack+'">'+d.fecha_ack+'</td>';
				content += '<td align="center">'+d.embarque+'</td>';
				content += '<td align="center">'+d.nro_item+'</td>';
				content += '<td align="center">'+d.sociedad+'</td>';
				
				if(d.checkid == "1") {
					content += '<td align="center"><img src="../images/tick_16.png" height="13" width="13"></td>';
                } else {
					content += '<td align="center"><img src="../images/delete_16.png" height="13" width="13"></td>';
				}

				if(d.modificada == '1') {
					content += '<td align="center"><img src="../images/tick_16.png" height="13" width="13"></td>';
				}else if(d.modificada == '2') {
					content += '<td align="center"><img src="../images/warning_16.png" height="13" width="13"></td>';
				}else if(d.modificada == '3') {
					content += '<td align="center"><img src="../images/delete_16.png" height="13" width="13"></td>';
				}else {
					content += '<td alig="center"></td>';
				}
				
				content += '</tr>';
				count++;
			});
			
			content += '</tbody><tfoot><tr>';
			content += '<th>Nro OC</th><th>Fecha OC</th><th>Fecha ACK</th><th>Embarque</th>';
            content += '<th>Nro Item</th><th>Sociedad</th><th>Dif Enviado</th><th>Modificado</th>';
            content += '</tr></tfoot></table>';

			$('#div_table_data').html(content);
			
			$('#tabla_oc_principal').dataTable({
			  "scrollY": "200px",
			  "scrollCollapse": true,
			  "paging": false,
			  "language": LAN_ESPANOL
			});
			
			CerrarThickBox();
			if(count > 0) {
				$("#exportar_principal").attr("disabled",false);
			}
	});
}

function verDetalleOC(nro_oc, fecha_oc, fecha_promesa, embarque, nro_item, sociedad, checkid, modificada)
{
	$("#Divbtn_volverHome").css("display","none");
	$("#Divbtn_volverHome").css("margin-left","10px");
	$("#AbrirCargando").trigger("click");
	$("#div_detalle").load("edi855_detalle.php",{
		
		nro_oc:nro_oc,
		fecha_oc: fecha_oc,
		fecha_promesa: fecha_promesa,
		embarque: embarque,
		nro_item: nro_item,
		sociedad: sociedad,
		checkid: checkid,
		modificada: modificada
		
		}, function(response, status, xhr) {
				if (status == "error") {
					var msg = "Error!, algo ha sucedido: ";
					$("#div_detalle").html(msg + xhr.status + " " + xhr.statusText);
				}
	});
	$("#div_detalle").show();
	//$("#Divbtn_volverHome").css("margin-left","10px");
	$("#wrapper").hide();	
}