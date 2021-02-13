$( document ).ready(function() {
	$('#bodyDescargarOC856 [title]').qtip({
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
	
	$("#btn_volver").click(function(){
		var url="../embarcadores.php";
		$(location).attr('href',url);
		$("#Divbtn_volverHome").css("margin-left","25px");
    });
	
});

////// FUNCIONES PAGINA PRINCIPAL //////////////////////////////////////

function cargaCabecera() 
{
	$("#exportar_cabecera_principal").attr("disabled",true);
	$("#exportar_detalle_principal").attr("disabled",true);
	
	var sociedad = $("#sociedad").val();
	var nro_oc   = $("#n_oc_principal").val();
	var factura  = $("#nro_factura").val();
	var inicio   = $("#fecha1_principal").val();
	var termino  = $("#fecha2_principal").val();

	$.post('ajax/descargarOC856.php',{ accion: 'cargaCabecera', sociedad:sociedad, nro_oc:nro_oc, factura:factura, inicio:inicio, termino:termino }, function(response) {
	}).done(function(response) {

		$('#div_table_data').html("");
		var json = jQuery.parseJSON(response);
			var content = "";
			var count = 0;
			
			content += '<table class="display" cellspacing="0" width="100%" id="tabla_cabecera_principal">';
			content += '<thead><tr>';
			content += '<th>Nro Factura</th><th>Fecha Despacho</th><th>Hora</th><th>Peso Carga</th><th>Unidad Medida</th>';
            content += '<th>Cod Embalaje</th><th>Tot Unid Embarcadas</th><th>Tipo Transporte</th><th>Descripción</th>';
            content += '<th>Nro Camión</th><th>Sociedad</th><th>Cant Bulto</th><th>Descargar</th>';
            content += '</tr></thead><tbody>';
            
			$.each(json, function(i, d) {
				content += '<tr id="oc_'+d.nro_factura+'" style="cursor:pointer;" ';
				content += '>';
				content += '<td align="center">'+d.nro_factura+'</td>';
				content += '<td align="center">'+d.fecha_despacho+'</td>';
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
				content +='<td align="center"><img src="../../images/edi856_32.png" id="imgDescargarOCEDI" style="cursor:pointer" onclick="javascript:descargarOCEDI856(this);" /></td>'
				content += '</tr>';
				count++;
			});
			
			content += '</tbody><tfoot><tr>';
			content += '<th>Nro Factura</th><th>Fecha Despacho</th><th>Hora</th><th>Peso Carga</th><th>Unidad Medida</th>';
            content += '<th>Cod Embalaje</th><th>Tot Unid Embarcadas</th><th>Tipo Transporte</th><th>Descripción</th>';
            content += '<th>Nro Camión</th><th>Sociedad</th><th>Cant Bulto</th><th>Descargar</th>';
            content += '</tr></tfoot></table>';
			
			$('#div_table_data').html(content);
			$('#tabla_cabecera_principal').dataTable({
				"language": LAN_ESPANOL
			});
			CerrarThickBox();
			
			if(count > 0) {
				$("#exportar_cabecera_principal").attr("disabled",false);
				$("#exportar_detalle_principal").attr("disabled",false);
			}
	});
}

function descargarOCEDI856(Fila)
{
	//
	$("#btn_buscar_principal").attr("disabled", true );
	//Variables
	var NumeroDeOC=$(Fila).parent('td').parent('tr').find('td:eq(0)').html();
	var respuesta;
	
	$.ajax({
            data:  {
					accion:"obtenerDetalleOC856EDI",
					NumeroOC: NumeroDeOC	
					},	
            url:   'ajax/descargarOC856.php',
            type:  'post',
            beforeSend: function () {
                $("#AbrirCargando").trigger("click");
				$("#btn_buscar_principal").attr("disabled", true);
            },
            success:  function (response) {
				CerrarThickBox();
				$("#secretIFrame").attr("src","ajax/descargarArchivoForzado.php?ruta="+String(response));
            },
			complete: function(){
				$("#btn_buscar_principal").attr("disabled", false );
			}
	});
}



