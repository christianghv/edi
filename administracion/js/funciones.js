
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
						content += '<option value='+d.lAND1+'>'+d.lANDX50+'</option>';
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
function getNombreIdFormato(IdFormato)
{
	var id_formato=IdFormato+"";
	var NombreFormato="";
	
	switch (id_formato) {
		case "1":
			NombreFormato = "EDI";
			break;
		case "2":
			NombreFormato = "XCBL";
			break;
	}
	return NombreFormato;
}
function getNombreTipoDocumento(Tipo)
{
	var id_tipo=Tipo+"";
	var NombreTipo="";
	
	switch (id_tipo) {
		case "1":
			NombreTipo = "810";
			break;
		case "2":
			NombreTipo = "855";
			break;
		case "3":
			NombreTipo = "856";
			break;
		case "4":
			NombreTipo = "Emabarcador";
			break;
		case "5":
			NombreTipo = "850";
			break;
	}
	return NombreTipo;
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
  alert(salida);
}

