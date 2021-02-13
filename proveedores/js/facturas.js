$( document ).ready(function() {
    cargaSociedades("(*) Sociedad: ", "sociedad");
	traerCboTipoProceso("(*) Tipo: ","cboTipo","divCboTipo",true);
	$('#bodyDescargarOC [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
      style: 'cream' // Give it some style
   });
    
	$("#boton_subir_archivo").click(function(){
		$("#respuesta").html("");
		validarIngresoArchivo();
    });
	
	$("#btn_volver").click(function(){
		var url="../../proveedores.php";
		$(location).attr('href',url);
    });

	
   });
////// FUNCIONES PAGINA PRINCIPAL //////////////////////////////////////
function validarIngresoArchivo()
{
	$("#sociedad").attr("required", "required");
	$("#cboTipo").attr("required", "required");
	$("#btnValidarFormulario").trigger("click");
	if(formSubirFactura.checkValidity())
	{
		cargandoDatos();
		formSubirFactura.submit();
	}
}
function cargandoDatos()
{
	$("#boton_subir_archivo").css("display","none");
	$("#ImagenCargando").css("display","inline");
	$("#sociedad").attr("disabled", true );
	$("#rdb_edi").attr("disabled", true );
	$("#rdb_csv").attr("disabled", true );
	$("#cboTipo").attr("disabled", true );
	$("#archivo").attr("disabled", true );
}
function DetenerCarga()
{
	$("#boton_subir_archivo").css("display","inline");
	$("#ImagenCargando").css("display","none");
	$("#sociedad").attr("disabled", false );
	$("#rdb_edi").attr("disabled", false );
	$("#rdb_csv").attr("disabled", false );
	$("#cboTipo").attr("disabled", false );
	$("#archivo").attr("disabled", false );

}

function cargando(){
    $("#respuesta").html('Cargando');
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

function traerFormatoFactura(texto,id_select,divUbicacion,thickCargando)
{
	$.ajax({
            data:  {accion:"traerFormatos"},	
            url:   'ajax/facturas.php',
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
						content += '<option value='+d.id_formato+'>'+d.descripcion+'</option>';
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

function traerCboTipoProceso(texto,id_select,divUbicacion,thickCargando)
{
	$.ajax({
            data:  {accion:"traerTipoProcesos"},	
            url:   'ajax/facturas.php',
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
						content += '<option value='+d.id_tipo+'>'+d.descripcion+'</option>';
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
function ProcesarXML810(sociedadEnviada,ruta)
{
	$.ajax({
            data:  {sociedad:sociedadEnviada,rutaXML: ruta},	
            url:   'ajax/ProcesarXML810.php',
            type:  'post',
            beforeSend: function () {
				//if(thickCargando==true)
				//{
               // $("#AbrirCargando").trigger("click");
				//}
            },
            success:  function (response) {				
				resultadoActual(response);
                },
				complete: function(){						
					//alert("FIN");
					resultadoActual('<br />====================== Proceso EDI810 terminado ====================== <br /><br />');
					DetenerCarga();
				}
	});
}
function ProcesarCSV810(sociedadEnviada,ruta)
{
	$.ajax({
            data:  {sociedad:sociedadEnviada,rutaCSV: ruta},	
            url:   'ajax/procesarCSV.php',
            type:  'post',
            beforeSend: function () {
				//if(thickCargando==true)
				//{
               // $("#AbrirCargando").trigger("click");
				//}
            },
            success:  function (response) {				
				resultadoActual(response);
                },
				complete: function(){						
					//alert("FIN");
					resultadoActual('<br />====================== Proceso terminado CSV ====================== <br /><br />');
					DetenerCarga();
				}
	});
}

/////////////////FIN FUNCIONES PAGINA DETALLE //////////////////////////




