
function cargaSociedades(texto, id_select)
{
	$.post('ajax/sociedades.php',{ }, function(response) {
	}).done(function(response) {
			$('#sociedades').html("");
			var json = jQuery.parseJSON(response);

				var content = "";
				content += "<label for='sociedad'>"+texto+"</label>";
				content += '<select class="form-control" id="'+id_select+'" name="'+id_select+'">';
				content += "<option value=''>-Seleccione-</option>";
				$.each(json, function(i, d) {
					content += "<option value='"+d.id_sociedad+"'>"+d.desc_sociedad+"</option>";
				});
				content += "</select>";
				$('#sociedades').html(content);
	}).error(function() {
		$('#sociedades').html("Error al intentar cargar Zonas");
	});	
}
function cargaProveedores(div_proveedoresHiddenSelect,div_proveedores,txtIdProveedor,required)
{
	var data_ws = [];
	$.ajax({
            data:  {},	
            url:   'ajax/ws_buscarProvedores.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				   var json = jQuery.parseJSON(response);
                   $.each(json, function(i, d) {
                          data_ws.push({value:d.NAME1, label:d.NAME1+" ("+d.LIFNR+")", id:d.LIFNR});
                          });
					
					var content='<select class="form-control" id="proveedoresHidden" name="proveedoresHidden">';
					content+="<option value='nada'>-Seleccione-</option>";
					$.each(json, function(i, d) {
						content += '<option value='+d.LIFNR+'>'+d.NAME1+' ('+d.LIFNR+')'+'</option>';
					});
					content +='</select>';
					
					$('#'+div_proveedoresHiddenSelect+'').html(content);      
	 
					 var cajaTexto='<label class="control-label">Proveedor: </label><span class="control-label" id="SeleccionProveedor"></span><br /> ';
						 cajaTexto+='<input type="text" class="form-control" placeholder="Proveedor" name="cboProvedor" id="cboProvedor" '
						 cajaTexto+='value="" size="25" ';
						 if(required)
						 {
							 cajaTexto+=' required="required" ';
						 }
						 cajaTexto+='title="Ingrese Proveedor">';
					 
					$('#'+div_proveedores+'').html(cajaTexto);
					
				},
				complete: function(){
					
					$("#cboProvedor").autocomplete({
								 source: data_ws,
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
										 $("#cboProvedor").val(ui.item.label);
										 $("#"+txtIdProveedor+"").val(ui.item.id);
										 $("#SeleccionProveedor").html(ui.item.label);					 
								 }
					});
				}
	});
}
function traerProvedoresComboBox(texto, id_select,divUbicacion, thickCargando)
{
	$.ajax({
            data:  {},	
            url:   'ajax/ws_buscarProvedores.php',
            type:  'post',
            beforeSend: function () {
				if(thickCargando==true)
				{
                $("#AbrirCargando").trigger("click");
				}
            },
            success:  function (response) {
				$('#'+divUbicacion+'').html("");
				var json = jQuery.parseJSON(response);
					var content='<label>'+texto+'</label><select class="form-control" id="'+id_select+'" name="'+id_select+'">';
					content+="<option value=''>-Seleccione-</option>";
					$.each(json, function(i, d) {	
						content += '<option value='+d.lIFNR+'>'+d.nAME1+'</option>';
					});
					content +='</select>';
				$('#'+divUbicacion+'').html(content);                       
                },
				complete: function(){
					if(thickCargando==true)
					{
					CerrarThickBox();
					}
				}
	});
}
function traerPaisesComboBox(texto,id_select,divUbicacion,thickCargando)
{
	$.ajax({
            data:  {},	
            url:   'ajax/ws_buscarPaises.php',
            type:  'post',
            beforeSend: function () {
				if(thickCargando==true)
				{
                $("#AbrirCargando").trigger("click");
				}
            },
            success:  function (response) {				
				var json = jQuery.parseJSON(response);
				//alert(response);
					var content='<label>'+texto+'</label><select class="form-control" id="'+id_select+'" name="'+id_select+'">';
					content+="<option value=''>-Seleccione-</option>";
					$.each(json, function(i, d) {	
						content += '<option value='+d.Land1+'>'+d.Land1+' - '+d.Landx50+'</option>';
					});
					content +='</select>';
				$('#'+divUbicacion+'').html("");
				$('#'+divUbicacion+'').html(content);                       
                },
				complete: function(){						
					if(thickCargando==true)
					{
						CerrarThickBox();
					}				
				}
	});
}

function CerrarThickBox()
{
	self.parent.tb_remove();
}
function ImprimirObjeto(o) {
  var salida = '';
  for (var p in o) {
    salida += p + ': ' + o[p] + '\n';
  }
  //alert(salida);
}
function ObtenerFechaNumerica(FechaNormal)
{
	var FechaDividida = FechaNormal.split("-");
	var Fecha_Completa = new Date(FechaDividida[2]+"/"+FechaDividida[1]+"/"+FechaDividida[0]+" 00:00:00");
	var FechaNumerica= Fecha_Completa.getTime() / 1000;
	return FechaNumerica;
}
function numberToColumnName(number) 
{
	var abc = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var abc_len = abc.length;
		
	var result_len = 1; // how much characters the column's name will have
	var pow = 0;
	while((pow += Math.pow(abc_len, result_len) ) < number ){
		result_len++;
	}
		
	var result = "";
	var next = false;
		
	// add each character to the result...
	for(i = 1; i<=result_len; i++){
		
		var index = (number % abc_len) - 1; // calculate the module

		// sometimes the index should be decreased by 1
		if(next){
			index--;
			next = false ;
		}
		// this is the point that will be calculated in the next iteration
		number = Math.floor(number / abc.length);

		// if the index is negative, convert it to positive
		if(next = (index < 0) ) {
			index = abc_len + index;
		}

		result = abc[index]+result; // concatenate the letter
	}

	return result;
}
function VieneEE(EE)
{
	EE_String = String(EE);
	
	if(EE_String=='0' || EE_String=='null' || EE_String=='')
	{
		return false;
	}
	else
	{
		return true;
	}
}