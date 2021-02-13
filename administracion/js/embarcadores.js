$( document ).ready(function() {
    $("#btn_modificar_modal").click(function() {    
    });
	
	TraerEmbarcadores(1);
	/**
	$('#bodyProvedores [title]').qtip({
      content: {
         text: false // Use each elements title attribute
      },
	  hide: {
        event: 'blur',
        leave: false
		},
	  show: {
        event: 'focus',
        leave: false
		},
      style: 'cream' // Give it some style
	  
   });*/
         
	$("#btn_agregarProveedor").click(function() {  
		limpiarModal();
		$(".modal-content").css("display","block");
		$("#Embarcador_id").attr("disabled", false );
		$('#modalAgregarProveedorLabel').html("Ingresar Embarcador");
	
	});
	
	$("#btn_volver").click(function(){
		var url="../../internos.php";
		$(location).attr('href',url);
    });
	
	$("#btn_grabarEmbarcador").click(function() {
		
		// Detalle //
		if($('#Embarcadores_Servidor').val()!="")
		{
		//Grabar Detalle
			if(FormIngresarEmbarcadorCabecera.checkValidity())
				{
					var valID=ValidarIngresoDeIdEmbarcador();
					if(valID==true)
					{
						//Se validara la parte de Detalle			
						if(EmbarcadoresDetalle.checkValidity())
						{
							grabarCabeceraEmbarcador(2);
						}
						else
						{
							$("#btn_ValidarDetalle").trigger("click");
						}
					}
					else
					{
						alert("El ID Embarcador ingresado, ya se encuentra en uso, por favor ingrese otro");
						$("#Embarcador_id").focus();
					}
					
				}
			else
				{
					$("#btn_ValidarCabecera").trigger("click")
					//--Fin Tab 855 activa --//
				};
		// #Grabar Detalle //
		}
		else
		{	
		//Solo guardara la cabecera
		if(FormIngresarEmbarcadorCabecera.checkValidity())
			{
				var valID=ValidarIngresoDeIdEmbarcador();
				
					if(valID==true)
					{
						grabarCabeceraEmbarcador(1);
					}
					else
					{
						alert("El ID PROVEDOR ingresado, ya se encuentra en uso, por favor ingrese otro");
						$("#Proveedor_id").focus();
					}
					
			}
			else
			{
				$("#btn_ValidarCabecera").trigger("click")
				//--Fin Tab 856 activa --//
			};
		}
		

	});		
	
$("#btn_EliminarProveedor").click(function() { 
	if (confirm('¿Está seguro de eliminar él o los embarcadores seleccionados?')) {
			EliminarEmbarcadores();
		} else {
			// Do nothing!
	}
	
    });
	
	
});
function formatearOnchange(texto)
{
	var valor=$(texto).val();
	var limpio=remplazarCarateresBarras(valor);
	$(texto).val(limpio);
}
function remplazarCarateresBarras(valor)
{
	var str = valor;
	str = str.replace(/\//g, "");
	str = str.replace(/\\/g, "\\");
	return str;
}
function EliminarEmbarcadores()
{
	var ArrayIdEmab = new Array();
	
	$('[class="selected"]').each(function(){
	var item = {
			"ID_Embarcador": $(this).find("td:eq(0) label").html()
		};
		//ImprimirObjeto(item);
		
		ArrayIdEmab.push(item);
	});
	
	JSON_ArrayIdEmbarcador = JSON.stringify({ArrayIdEmab: ArrayIdEmab});
	
		$.ajax({
					data:  {accion: "EliminarEmbarcadores",
							JSON_ArrayIdEmbarcador:  JSON_ArrayIdEmbarcador
							},	
					url:   'ajax/embarcadores.php',
					type:  'post',
					beforeSend: function () {
						$("#AbrirCargando").trigger("click");
					},
					success:  function (response) {
							//alert(response);
							afectadasProveedores=parseFloat(response);
						},
						complete: function(){
								//CerrarThickBox();
								//alert("Seleccionados eliminados");								
								TraerEmbarcadores(0);							
						}
		});
}
function ValidarIngresoDeIdEmbarcador()
{
	var validacion=true;
	
	MostrarTodosLosRegistros();
	
	if($('#Old_IdEmbar').val()=="")
	{
		$("[id=IdEmbarcador]").each(function(){
			if($(this).html()==$('#Embarcador_id').val())
			{
				validacion=false;
			}		
		});
	}	
	
	DejarDataTableDinamicaNormal();
	return 	validacion;
}

function limpiarModal(){
	
	//---Cabecera
	$("#Embarcador_id").val("");
	$("#Embarcador_CodigoSap").val("");
	$("#Embarcador_Nombre").val("");
	$("#Embarcador_Contrasena").val("");
	
	//Combobox Cabecera
	
	//---Detalle
	//Combobox detalle
	
	$("#Embarcadores_Servidor").val("");
	$("#Embarcadores_Puerto").val("");
	$("#Embarcadores_Usuario").val("");
	$("#Embarcadores_Contrasena").val("");
	$("#Embarcadores_RutaRemota").val("");	
	$("#Embarcadores_RutaLocal").val("");
	$("#Embarcadores_RutaSalida").val("");	
	$("#Embarcadores_Patron").val("");	
	
}
function grabarDetalleEmbarcador()
{						
	$.ajax({
            data:  {
					accion:"grabarDetalleEmbarcador",
					Embarcador_id:$("#Embarcador_id").val(),
					Servidor:$("#Embarcadores_Servidor").val(),
					Puerto:$("#Embarcadores_Puerto").val(),
					Usuario:$("#Embarcadores_Usuario").val(),
					Contrasena:$("#Embarcadores_Contrasena").val(),
					RutaRemota:$("#Embarcadores_RutaRemota").val(),
					RutaLocal:$("#Embarcadores_RutaLocal").val(),
					RutaSalida: $("#Embarcadores_RutaSalida").val(),
					Patron:$("#Embarcadores_Patron").val()
					},	
            url:   'ajax/embarcadores.php',
            type:  'post',
            beforeSend: function () {
                //$("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);		                    
            },
			complete: function(){
				//CerrarThickBox();	
				$("#btn_CancelarEmbarcador").trigger("click");						
				TraerEmbarcadores(0);
			}
	});
}

function grabarCabeceraEmbarcador(TipoFuncion)
{
	$('.modal-content').fadeOut(500);
	$('.modal-backdrop').fadeOut(500);
	//modal-backdrop fade in	
	$("#AbrirCargando").trigger("click");	
	$.ajax({
            data:  {
					accion:"grabarCabeceraEmbarcador",
					Embarcador_id : $("#Embarcador_id").val(),
					Embarcador_CodigoSap : $("#Embarcador_CodigoSap").val(),
					Embarcador_Nombre : $("#Embarcador_Nombre").val(),
					Embarcador_Contrasena : $("#Embarcador_Contrasena").val(),
					},	
            url:   'ajax/embarcadores.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);		                    
            },
			complete: function(){
				$('#Old_IdEmbar').val("");
				if(TipoFuncion==2)
				{
					grabarDetalleEmbarcador();					
				}
				if(TipoFuncion==1)
				{
					$("#btn_CancelarEmbarcador").trigger("click");
					TraerEmbarcadores(0);
				}
			}
	});
}

function CargarDetailOnClick(label)
{	
	limpiarModal();
	$(".modal-content").css("display","block");
	
	$("#AbrirCargando").trigger("click");
	$("#Embarcador_id").attr("disabled", true );
	$('#modalAgregarProveedorLabel').html("Editar Embarcador");
	$('#Embarcador_id').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#Old_IdEmbar').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());	
	$('#H_OldIdEmbarcador').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#Embarcador_CodigoSap').val($(label).parent('td').parent('tr').find('td:eq(1)').html());
	$('#Embarcador_Nombre').val($(label).parent('td').parent('tr').find('td:eq(2)').html());

	//alert(valor1);
	var id_embarcadorEnTabla=$(label).parent('td').parent('tr').find('td:eq(0) label').html();
	
	TraerProcesosPorID(id_embarcadorEnTabla);
	//$("#btn_grabarUsuario").css("display","inline");
	//$("#btn_insertarUsuario").css("display","none");
		
	

}

function TraerProcesosPorID(IdEmbarcador)
{
	$.ajax({
            data:  {
					accion:"BuscarTipoProcesoPorEmbarcador",
					IdEmbarcador:IdEmbarcador
					},	
            url:   'ajax/embarcadores.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
			
				var json = jQuery.parseJSON(response);
				
				$.each(json, function(i, d) {
					//Si encontramos registro
						$("#Embarcadores_Servidor").val(d.servidor);
						$("#Embarcadores_Puerto").val(d.puerto);
						$("#Embarcadores_Usuario").val(d.usr);
						$("#Embarcadores_Contrasena").val(d.pass);
						$("#Embarcadores_RutaRemota").val(d.ruta_remota);	
						$("#Embarcadores_RutaLocal").val(d.ruta_local);	
						$("#Embarcadores_RutaSalida").val(d.ruta_salida);	
						$("#Embarcadores_Patron").val(d.patron);							
				});
							
                },
				complete: function(){
						CerrarThickBox();
						$("#btn_AbrirModalEmbarcador").trigger("click");
				}
	});
}

function TipoEsDeTextoAID(variable)
{
	var idObtenido=0;
	$("[id=OpcionTipoEs]").each(function(){
		if($(this).html()== $(variable).parent('td').parent('tr').find('td:eq(3)').html())
		{
			idObtenido=$(this).val();			
		}
	});
	return idObtenido;
}

function TraerEmbarcadores(AbrirCargando)
{
	$.ajax({
            data:  {
					accion:"buscarEmbarcadores"
					},	
            url:   'ajax/embarcadores.php',
            type:  'post',
            beforeSend: function () {
				if(AbrirCargando==1)
				{
					$("#AbrirCargando").trigger("click");
				}
            },
            success:  function (response) {
				var content="";
				content += '<tbody>';
				var json = jQuery.parseJSON(response);
					$.each(json, function(i, d) {	
						content += '<tr id="'+d.id_embarcador+'" class="editar">';
						content += '<td><label onclick="javascript:CargarDetailOnClick(this);" id="IdEmbarcador" style="cursor:pointer">'+d.id_embarcador+'</label></td>';
						content += '<td>'+d.cod_sap+'</td>';
						content += '<td>'+d.nombre+'</td>';
						content += '<td>'+d.descripcion+'</td>';
						content += '<td><input type="checkbox" class="marca_chk" name="chk_embarcador" id="chk_embarcador" value="'+d.id_embarcador+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
						content += '</tr>';
					});
					content += '</tbody>';
					$('#tabla_embarcadores').dataTable().fnClearTable();
					$('#tabla_embarcadores').dataTable().fnDestroy();
					$('#tabla_embarcadores tbody').replaceWith(content);
					$('#tabla_embarcadores').dataTable({
						"language": LAN_ESPANOL,
						"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
						"iDisplayLength": 10
					});
				                    
                },
				complete: function(){
						CerrarThickBox();					
				}
	});
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
function MostrarTodosLosRegistros()
{
	$('#tabla_embarcadores').dataTable().fnDestroy();
	$('#tabla_embarcadores').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
		"iDisplayLength": -1
	});
}
function DejarDataTableDinamicaNormal()
{
	$('#tabla_embarcadores').dataTable().fnDestroy();
	$('#tabla_embarcadores').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
		"iDisplayLength": 10
	});
}
function ActivarQtip()
{
	$('#bodyProvedores [title]').qtip({
			content: {
				text: false // Use each elements title attribute
					  },
			hide: {
				event: 'blur',
				leave: false
				},
			show: {
				event: 'focus',
				leave: false
					},
			style: 'cream' // Give it some style					  
				   });
}

/////////////////FIN FUNCIONES PAGINA DETALLE //////////////////////////




