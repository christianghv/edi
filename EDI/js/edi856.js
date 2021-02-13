$( document ).ready(function() {
	$('#body_edi856 [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
      style: 'cream' // Give it some style
	});
	
	cargaSociedades("(*) Sociedad: ", "sociedad");
	$( "#fecha2_principal" ).datepicker({dateFormat:"dd/mm/yy"}).datepicker("setDate",new Date());
	var date1 = new Date();
	date1.setDate(date1.getDate() - 30);
	$( "#fecha1_principal" ).datepicker({dateFormat:"dd/mm/yy"}).datepicker("setDate",date1);	
	//////////////////////////////////////////////////
	$("#btn_buscar_principal").click(function() {  
		
		$("#sociedad").attr('required', 'required');
		$("#submit_filtros").trigger("click");		
		if(frm_filtros.checkValidity()){
			$("#AbrirCargando").trigger("click");    
			cargaCabecera();
		}
    });

	$("#exportar_cabecera_principal").click(function() {      
		exportarExcel("header");    
    });
    $("#exportar_detalle_principal").click(function() {      
		exportarExcel("detail");    
    });
    // FIN PAGINA PRINCIPAL ////////////////////////////////////////////
    $("#exportar_cabecera_principal").attr("disabled",true);
    $("#exportar_detalle_principal").attr("disabled",true);
	
	$("#btn_inicio").click(function(){
		var url="../../internos.php";
		$(location).attr('href',url);
		$("#Divbtn_volverHome").css("margin-left","25px");
    });
	
	$('#nro_factura').dblclick(function() {
		$("#ingreso_factura").trigger("click");
	});
	
	$('#n_oc_principal').dblclick(function() {
		$("#ingreso_oc").trigger("click");
	});
	
});

////// FUNCIONES PAGINA PRINCIPAL //////////////////////////////////////

function exportarExcel(modo) {
	
	var sociedad = $("#sociedad").val();
	var nro_oc_principal   = $("#n_oc_principal").val();
	var factura_principal  = $("#nro_factura").val();
	var inicio   = $("#fecha1_principal").val();
	var termino  = $("#fecha2_principal").val();
	
	var factura 		= '';
	var facturas 		= '';
	var nro_oc 		= '';
	var nro_ocs 		= '';
	
	if(factura_principal=="")
	{
		var textArea=$('#txtNumeroFacturas').val();
		
		var value = textArea.replace(/\r?\n/g, "|");
		facturas=value;
		
		if(factura != "") {
			facturas += factura;
		}	
	}
	else
	{
		facturas=factura_principal;
	}
	
	if(nro_oc_principal=="")
	{
		var textArea=$('#txtNumeroOC').val();
		
		var value = textArea.replace(/\r?\n/g, "|");
		nro_ocs=value;
		
		if(nro_oc != "") {
			nro_ocs += nro_oc;
		}
	}
	else
	{
		nro_ocs=nro_oc_principal;
	}
	
	
	
	if(modo == "header")
		url = 'ajax/edi856_cabecera_excel.php?sociedad='+sociedad+'&nro_oc='+nro_ocs+'&inicio='+inicio+'&termino='+termino+'&factura='+facturas;
	if(modo == "detail")
		url = 'ajax/edi856_detalle_excel.php?sociedad='+sociedad+'&nro_oc='+nro_ocs+'&inicio='+inicio+'&termino='+termino+'&factura='+facturas;
		
	window.location.href = url;
}

function cargaCabecera() 
{
	$("#exportar_cabecera_principal").attr("disabled",true);
	$("#exportar_detalle_principal").attr("disabled",true);
	
	var sociedad = $("#sociedad").val();
	var nro_oc   = $("#n_oc_principal").val();
	var factura  = $("#nro_factura").val();
	var inicio   = $("#fecha1_principal").val();
	var termino  = $("#fecha2_principal").val();
	
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
	

	$.post('ajax/edi856.php',{ accion: 'cargaCabecera', sociedad:sociedad, nro_ocs:nro_ocs, facturas:facturas, inicio:inicio, termino:termino }, function(response) {
	}).done(function(response) {

		$('#div_table_data').html("");
		var json = jQuery.parseJSON(response);
			var content = "";
			var count = 0;
			
			content += '<table class="display" cellspacing="0" width="100%" id="tabla_cabecera_principal">';
			content += '<thead><tr>';
			content += '<th>Nro Factura</th><th>Fecha Despacho</th><th>Hora</th><th>Peso Carga</th><th>Unidad Medida</th>';
            content += '<th>Cod Embalaje</th><th>Tot Unid Embarcadas</th><th>Tipo Transporte</th><th>Descripción</th>';
            content += '<th>Nro Camión</th><th>Sociedad</th><th>Cant Bulto</th>';
            content += '</tr></thead><tbody>';
            
			$.each(json, function(i, d) {		
				var Fromfecha_despacho = d.fecha_despacho.split("-");
				var Fecha_despacho = new Date(Fromfecha_despacho[2]+"/"+Fromfecha_despacho[1]+"/"+Fromfecha_despacho[0]+" 00:00:00");
				var FechaNumericaFecha_despacho = Fecha_despacho.getTime() / 1000;
				
				content += '<tr id="oc_'+d.nro_factura+'" style="cursor:pointer;" ';
				content += 'onClick="verDetalle(\''+d.nro_factura+'\', \''+d.fecha_despacho+'\', \''+d.hora+'\', ';
				content += '\''+d.peso_carga+'\', \''+d.unidad_medida+'\', \''+d.cod_embalaje+'\', \''+d.tot_unid_embarcadas+'\', ';
				content += '\''+d.tipo_transporte+'\', \''+d.descripcion+'\', \''+d.nro_camion+'\', \''+d.sociedad+'\', \''+d.cant_bulto+'\');">';
				content += '<td align="center">'+d.nro_factura+'</td>';
				content += '<td align="center" data-order="'+FechaNumericaFecha_despacho+'">'+d.fecha_despacho+'</td>';
				content += '<td align="center">'+d.hora+'</td>';
				content += '<td align="center">'+d.peso_carga+'</td>';
				content += '<td align="center">'+d.unidad_medida+'</td>';
				content += '<td align="center">'+d.cod_embalaje+'</td>';
				content += '<td align="center">'+d.tot_unid_embarcadas+'</td>';
				content += '<td align="center">'+d.tipo_transporte+'</td>';
				content += '<td align="center">'+d.descripcion+'</td>';
				content += '<td align="center">'+d.nro_camion+'</td>';
				content += '<td align="center">'+d.sociedad+'</td>';
				content += '<td align="center">'+d.cant_bulto+'</td>';
				content += '</tr>';
				count++;
			});
			
			content += '</tbody><tfoot><tr>';
			content += '<th>Nro Factura</th><th>Fecha Despacho</th><th>Hora</th><th>Peso Carga</th><th>Unidad Medida</th>';
            content += '<th>Cod Embalaje</th><th>Tot Unid Embarcadas</th><th>Tipo Transporte</th><th>Descripción</th>';
            content += '<th>Nro Camión</th><th>Sociedad</th><th>Cant Bulto</th>';
            content += '</tr></tfoot></table>';
			
			$('#div_table_data').html(content);
			$('#tabla_cabecera_principal').dataTable({
							"scrollY":        "200px",
							"scrollCollapse": true,
							"paging":         false,
							"language": LAN_ESPANOL
						});
			CerrarThickBox();
			
			if(count > 0) {
				$("#exportar_cabecera_principal").attr("disabled",false);
				$("#exportar_detalle_principal").attr("disabled",false);
			}
	});
}

function verDetalle(nro_factura, fecha_despacho, hora, peso_carga, unidad_medida, cod_embalaje, tot_unid_embarcadas, tipo_transporte, descripcion, nro_camion, sociedad, cant_bulto)
{
	$("#Divbtn_volverHome").css("display","none");
	$("#Divbtn_volverHome").css("margin-left","10px");
	$("#AbrirCargando").trigger("click");
	$("#div_detalle").load("edi856_detalle.php",{
		nro_factura:nro_factura,
		fecha_despacho: fecha_despacho,
		hora: hora,
		peso_carga: peso_carga,
		unidad_medida: unidad_medida,
		cod_embalaje: cod_embalaje,
		tot_unid_embarcadas: tot_unid_embarcadas,
		tipo_transporte: tipo_transporte,
		descripcion: descripcion,
		nro_camion: nro_camion,
		sociedad: sociedad,
		cant_bulto: cant_bulto
		
		}, function(response, status, xhr) {
				if (status == "error") {
					var msg = "Error!, algo ha sucedido: ";
					$("#div_detalle").html(msg + xhr.status + " " + xhr.statusText);
				}
	});
	$("#div_detalle").show();
	$("#wrapper").hide();
}



