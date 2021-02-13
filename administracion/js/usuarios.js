$( document ).ready(function() {
	$('#tabla_usuarios').dataTable({
		"language": LAN_ESPANOL
	});
    $("#btn_modificar_modal").click(function() {    
    });
	TraerPerfiles();
	
	$("#btn_agregarUsuario").click(function() {  
		$('#modalAgregarUsuarioLabel').html("Agregar Usuario");
		$("#divPermisos").css("display","none");
		$("#estadoPermisos").val("0");
		$("#btn_grabarUsuario").css("display","none");
		$("#btn_insertarUsuario").css("display","inline");
		$("#Usuario_email").val("");
		$("#Usuario_Nombre").val("");
		$("#Usuario_Perfil").val("");
		limpiarPermisos();
		$("#cbo_PerfilUsuario").val("");
	});
	
	$("#btn_Prueba").click(function() {
		//$("#divPermisos").css("display","none");
	});
	
	$("#btn_grabarUsuario").click(function() { 
		if(FormIngresarUsuario.checkValidity())
		{
		var validacionEmail=ValidarElNoReingresoDeEmail();
		if(validacionEmail==true)
			{
				modificarUsuario();
			}
			else
			{
				alert("El correo electrónico ingresado ya se encuentra registrado, por favor ingrese otro");
			}
		
		}
	});
	
	$("#btn_Permisos").click(function(e) {
		e.preventDefault();
		if(String($("#estadoPermisos").val())=="0")
		{
			$("#divPermisos").fadeIn("slow");
			$("#estadoPermisos").val("1");
		}
		else
		{
			$("#divPermisos").fadeOut( "slow" );
			$("#estadoPermisos").val("0");
		}
	});
	
	$("#btn_volver").click(function(){
		var url="../../internos.php";
		$(location).attr('href',url);
    });
	
	
	
	$("#btn_insertarUsuario").click(function() {  
		if(FormIngresarUsuario.checkValidity())
		{
			var validacionEmail=ValidarIngresoDeEmail();
			if(validacionEmail==true)
			{
				var afectadasUsuario;
				var emailUsuario;
				$.ajax({
					data:  {
							accion: "GrabarUsuario",
							Usuario_email:  $("#Usuario_email").val(),
							Usuario_Nombre: $("#Usuario_Nombre").val(),
							PerfilUsuario:  $('#cbo_PerfilUsuario').val()
							},	
					url:   'ajax/usuarios.php',
					type:  'post',
					beforeSend: function () {
						$("#AbrirCargando").trigger("click");
					},
					success:  function (response) {
							afectadasUsuario=parseFloat(response);
						},
						complete: function(){
							if(afectadasUsuario>0)
							{
								//CerrarThickBox();
								emailUsuario=$("#Usuario_email").val();
								//alert("Usuario registrado correctamente");
								limpiarModal();			
								$("#Usuario_email").focus();
								grabarPermisos(emailUsuario,false);								
							}
													
						}
				});
			}
			else
			{
				alert("El correo electrónico ingresado ya se encuentra registrado, por favor ingrese otro");
			}
		}
	});
	$("#btn_EliminarUsuario").click(function() { 
	if (confirm('¿Está seguro de eliminar el o los usuarios seleccionados?')) {
			EliminarUsuarios();
		} else {
			// Do nothing!
	}
	
    });
	
	
});
////// FUNCIONES PAGINA PRINCIPAL //////////////////////////////////////
function TipoPerfilAId(texto)
{	
	var id=0;
	$("[id=OpcionCboPerfil]").each(function(){
		if($(this).html()==texto)
		{
			id=$(this).val();
		}
	});
	
	return id;
}
function TipoPerfilATexto(id)
{
	var perfilDescripcion;
	$("[id=OpcionCboPerfil]").each(function(){
		if($(this).val()==id)
			{
				perfilDescripcion=$(this).html();
			}
	});
	return perfilDescripcion;
}
function EliminarUsuarios()
{
	var ArrayEmail = new Array();
	$('[class="selected"]').each(function(){
	var item = {
			"email": $(this).find("td:eq(0) label").html()		
		};
		//ImprimirObjeto(item);
		
		ArrayEmail.push(item);
		});
		JSON_ArrayEmail = JSON.stringify({ArrayEmail: ArrayEmail});
		$.ajax({
					data:  {
							accion: "EliminarUsuarios",
							JSON_ArrayEmail:  JSON_ArrayEmail
							},	
					url:   'ajax/usuarios.php',
					type:  'post',
					beforeSend: function () {
						$("#AbrirCargando").trigger("click");
					},
					success:  function (response) {
							afectadasUsuario=parseFloat(response);
						},
						complete: function(){
							if(afectadasUsuario>0)
							{
								CerrarThickBox();
								//alert("Seleccionados eliminados");								
								TraerUsuarios();
							}
													
						}
		});
}
function ValidarIngresoDeEmail()
{
	var validacion=true;
	
	MostrarTodosLosRegistros();
	
	$("[id=UssMail]").each(function(){
		if($(this).html()==$('#Usuario_email').val())
		{
			validacion=false;
		}
		
	});
	
	DejarDataTableDinamicaNormal();
	return 	validacion;
}
function ValidarElNoReingresoDeEmail()
{
	var validacion=true;
	
	MostrarTodosLosRegistros();
	
	if($('#Usuario_email').val()==$('#H_OldUssEmail').val())
		{
			//Modificara los otros campos y no el email
		}
		else
		{
			$("[id=UssMail]").each(function(){		
				if($(this).html()==$('#Usuario_email').val())
				{
					validacion=false;
				}
				
			});
		}
		
	DejarDataTableDinamicaNormal();
	
	return 	validacion;
}
function limpiarModal()
{
	$("#H_OldUssEmail").val("");
	$("#Usuario_email").val("");
	$("#Usuario_Nombre").val("");
	$('#cbo_PerfilUsuario').val("");
	
}
function limpiarPermisos()
{
	$('[name=chk_permi]').each(function(){
        if($(this).is(':checked'))
		{
			$(this).prop('checked', false);
			$(this).parents('tr').removeAttr('class');
        }
    });
}
function modificarUsuario()
{
	var afectadasUsuario;
	$.ajax({
			data:  {
						accion: "ActualizarUsuario",
						Usuario_email_old:  $("#H_OldUssEmail").val(),
						Usuario_email:  $("#Usuario_email").val(),
						Usuario_Nombre: $("#Usuario_Nombre").val(),
						PerfilUsuario:  $('#cbo_PerfilUsuario').val()
					},	
			url:   'ajax/usuarios.php',
			type:  'post',
			beforeSend: function () {
						$("#AbrirCargando").trigger("click");
			},
			success:  function (response) {
						//alert(response);
						afectadasUsuario=parseFloat(response);
						
			},
			complete: function(){
				if(afectadasUsuario>0)
					{
						grabarPermisos($("#Usuario_email").val(),true);
						//alert("Usuario modificado correctamente");
						//TraerUsuarios();
					}
													
			}
	});
	
	$("#btn_Cancelar").trigger("click");
}
function CargarDetailOnClick(label)
{	
	limpiarPermisos();
	$("#divPermisos").css("display","none");
	$("#estadoPermisos").val("0");
	
	$('#modalAgregarUsuarioLabel').html("Editar Usuario");
	$('#Usuario_email').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#H_OldUssEmail').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#Usuario_Nombre').val($(label).parent('td').parent('tr').find('td:eq(1)').html());
	
	traerPermisosUsuario(String($(label).parent('td').parent('tr').find('td:eq(0) label').html()));
	traerSociedadesUsuario(String($(label).parent('td').parent('tr').find('td:eq(0) label').html()));
	
	$("[id=OpcionCboPerfil]").each(function(){
		if($(this).html()==$(label).parent('td').parent('tr').find('td:eq(2)').html())
		{
			$('#cbo_PerfilUsuario').val($(this).val());			
		}
	});
	//$('#Usuario_Perfil').val($(label).parent('td').parent('tr').find('td:eq(2)').html());
	
	$("#btn_grabarUsuario").css("display","inline");
	$("#btn_insertarUsuario").css("display","none");	
		
	

}
function TraerUsuarios()
{
	$.ajax({
            data:  {accion:"buscarUsuarios"},	
            url:   'ajax/usuarios.php',
            type:  'post',
            beforeSend: function () {
                //$("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				
				var content="";
				content += '<tbody>';
				var json = jQuery.parseJSON(response);
					$.each(json, function(i, d) {	
						content += '<tr id="'+d.email+'" class="editar">';
						content += '<td><label onclick="javascript:CargarDetailOnClick(this);" id="UssMail" style="cursor:pointer">'+d.email+'</label></td>';
						content += '<td>'+d.nombre+'</td>';
						content += '<td>'+d.descripcion+'</td>';
						content += '<td><input type="checkbox" class="marca_chk" name="chk_usuario" id="chk_usuario" value="'+d.email+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
						content += '</tr>';
					});
					content += '</tbody>';
					$('#tabla_usuarios').dataTable().fnClearTable();
					$('#tabla_usuarios').dataTable().fnDestroy();
					$('#tabla_usuarios tbody').replaceWith(content);
					$('#tabla_usuarios').dataTable({
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

function TraerPerfiles()
{
	$.ajax({
            data:  {accion:"buscarPerfiles"},	
            url:   'ajax/usuarios.php',
            type:  'post',
            beforeSend: function () {
               $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				var content = "";
				var json = jQuery.parseJSON(response);
				content += "<label>Perfil: </label>";
				content += '<select class="form-control" id="cbo_PerfilUsuario" name="cbo_PerfilUsuario" required="required">';
				content += "<option value=''>-Seleccione-</option>";
				$.each(json, function(i, d) {
					content += "<option id='OpcionCboPerfil' value='"+d.id_perfil+"'>"+d.descripcion+"</option>";
				});
				content += "</select>";
				$('#divCboPerfil').html(content);
                },
				complete: function(){
						TraerTodosLosPermisos();						
						//CerrarThickBox();				
				}
	});
}

function TraerTodosLosPermisos()
{
	$.ajax({
            data:  {accion:"buscarTodosLosPermisos"},	
            url:   'ajax/usuarios.php',
            type:  'post',
            beforeSend: function () {
                //$("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				var contentAdmin = "";
				var contentEdi = "";
				var contentProveedores = "";
				var contentEmbarcador = "";
				var json = jQuery.parseJSON(response);
				$.each(json, function(i, d) {				
					//Si es Administracion
					//alert('tipo permiso: ' + d.id_tipoPermiso);
					if(String(d.id_tipoPermiso)=="1")
					{
						contentAdmin += '<tr><td>'+d.nombre+'</td>';
						contentAdmin += '<td><input id="chk_perAdmin" name="chk_permi" type="checkbox" onchange="javascript:CambiarAlClicChk(this);" value="'+d.id_permiso+'"></td></tr>';
					}
					
					//Si es EDI
					if(String(d.id_tipoPermiso)=="2")
					{
						contentEdi += '<tr><td>'+d.nombre+'</td>';
						contentEdi += '<td><input id="chk_perEdi" name="chk_permi" type="checkbox" onchange="javascript:CambiarAlClicChk(this);" value="'+d.id_permiso+'"></td></tr>';
					}
					
					//Si es Proveedores
					if(String(d.id_tipoPermiso)=="3")
					{
						contentProveedores += '<tr><td>'+d.nombre+'</td>';
						contentProveedores += '<td><input id="chk_perProv" name="chk_permi" type="checkbox" onchange="javascript:CambiarAlClicChk(this);" value="'+d.id_permiso+'"></td></tr>';
					}
					
					//Si es Embarcador
					if(String(d.id_tipoPermiso)=="4")
					{
						contentEmbarcador += '<tr><td>'+d.nombre+'</td>';
						contentEmbarcador += '<td><input id="chk_perEmbar" name="chk_permi" type="checkbox" onchange="javascript:CambiarAlClicChk(this);" value="'+d.id_permiso+'"></td></tr>';
					}					
				});
				
				$('#tBodyPerAdmin').html(contentAdmin);
				$('#tBodyPerEdi').html(contentEdi);
				$('#tBodyPerProv').html(contentProveedores);
				$('#tBodyPerEmbar').html(contentEmbarcador);
				
				$('#tbl_PerAdmin').dataTable({
					"language": LAN_ESPANOL,
					"aLengthMenu": [[10], [10]],
					"iDisplayLength": 10,
					"bFilter": false,
				});
				
				$('#tbl_PerEdi').dataTable({
					"language": LAN_ESPANOL,
					"aLengthMenu": [[10], [10]],
					"iDisplayLength": 10,
					"bFilter": false,
				});
				
				$('#tbl_PerProv').dataTable({
					"language": LAN_ESPANOL,
					"aLengthMenu": [[10], [10]],
					"iDisplayLength": 10,
					"bFilter": false,
				});
				
				$('#tbl_PerEmba').dataTable({
					"language": LAN_ESPANOL,
					"aLengthMenu": [[10], [10]],
					"iDisplayLength": 10,
					"bFilter": false,
				});
				
				
                },
			complete: function(){
					//CerrarThickBox();	
					TraerPermisosSociedades();
				}
	});
}
function TraerPermisosSociedades()
{
	$.ajax({
            data:  {accion:"buscarTodosLasSociedades"},	
            url:   'ajax/usuarios.php',
            type:  'post',
            beforeSend: function () {
                //$("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);
				var contentSociedades = "";
				var json = jQuery.parseJSON(response);
				$.each(json, function(i, d) {				
					contentSociedades += '<tr><td>'+d.desc_sociedad+'</td>';
					contentSociedades += '<td><input id="chk_perSociedades" name="chk_permisoc" type="checkbox" onchange="javascript:CambiarAlClicChk(this);" value="'+d.id_sociedad+'"></td></tr>';	
				});
				
				$('#tBodyPerSociedades').html(contentSociedades);
				
				$('#tbl_PerSociedades').dataTable({
					"language": LAN_ESPANOL,
					"aLengthMenu": [[10], [10]],
					"iDisplayLength": 10,
					"bFilter": false,
				});
				
				
                },
			complete: function(){
					//CerrarThickBox();	
					TraerUsuarios();
				}
	});
}
function CambiarAlClicChk(chek)
{
	if($(chek).is(':checked')) {  
            $(chek).parents('tr').removeAttr('class');
			$(chek).parents('tr').attr('class', 'selected'); 
        } else {  
            $(chek).parents('tr').removeAttr('class');  
        }  
}
function MostrarTodosLosRegistros()
{
	$('#tabla_usuarios').dataTable().fnDestroy();
	$('#tabla_usuarios').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
		"iDisplayLength": -1
	});
}
function DejarDataTableDinamicaNormal()
{
	$('#tabla_usuarios').dataTable().fnDestroy();
	$('#tabla_usuarios').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
		"iDisplayLength": 10
	});
}
function grabarPermisos(emailUsuario,soloTraerUsuarios)
{
	var ArrayDePerm = new Array();
	var ArrayDeSociedades = new Array();
	var JSON_ArrayDePermisos = "";
	var JSON_ArrayDeSociedades = "";
	
	var chekeados=0;
	
	//Permisos
	$('[name=chk_permi]').each(function(){
        if($(this).is(':checked')){		
			var StringId=String($(this).attr('id'));
				var item = {
						"TipoPermiso": interpretarIDPorTipo(StringId),
						"Permiso": $(this).val()			
					};	
                    ArrayDePerm.push(item);
					chekeados++;
                }
				
    });
		
	JSON_ArrayDePermisos = JSON.stringify({ArrayDePerm: ArrayDePerm});
	
	//Sociedades
	$('[name=chk_permisoc]').each(function(){
        if($(this).is(':checked')){
			var item = {
					"Sociedad": $(this).val()
				};	
                ArrayDeSociedades.push(item);
				chekeados++;
        }		
    });
	
	JSON_ArrayDeSociedades = JSON.stringify({ArrayDeSociedades: ArrayDeSociedades});
	
	$.ajax({
				data:  {accion:"grabarPermisos",
						email: emailUsuario,
						JSON_Permisos: JSON_ArrayDePermisos,
						JSON_Sociedades: JSON_ArrayDeSociedades
						},
				url:   'ajax/usuarios.php',
				type:  'post',
				beforeSend: function () {
				   //$("#AbrirCargando").trigger("click");
				},
				success:  function (response) {
					var content = "";
					if(String(response)=="OK")
					{
							
					}
					else
					{
						alert("Error al grabar permisos: "+response);
					}
					},
					complete: function(){
						if(soloTraerUsuarios==true)
						{
							TraerUsuarios();
						}
						else
						{
						CerrarThickBox();
						limpiarPermisos();
						TraerUsuarios();
						}
					}
		});
}
function interpretarIDPorTipo(idCheckBoxString)
{
	var idTipo=0;
	if(idCheckBoxString=="chk_perAdmin")
	{
		idTipo=1;
	}
	
	if(idCheckBoxString=="chk_perEdi")
	{
		idTipo=2;
	}
	
	if(idCheckBoxString=="chk_perProv")
	{
		idTipo=3;
	}
	
	if(idCheckBoxString=="chk_perEmbar")
	{
		idTipo=4;
	}
	return idTipo;
}
function traerPermisosUsuario(emailUsuario)
{
	$.ajax({
				data:  {accion:"ObtenerPermisosUsuario",
						email: emailUsuario
						},
				url:   'ajax/usuarios.php',
				type:  'post',
				beforeSend: function () {
				   //$("#AbrirCargando").trigger("click");
				},
				success:  function (response) {
					var json = jQuery.parseJSON(response);
					$.each(json, function(i, d) {
							//Recorremos los checkbox
							$('[name=chk_permi]').each(function(){
								if(String(d.id_permiso)==String($(this).val()))
								{
									$(this).prop('checked', true);
									$(this).parents('tr').removeAttr('class');
									$(this).parents('tr').attr('class', 'selected'); 
								}
							});					
					});
					},
					complete: function(){
					//CerrarThickBox();
					//$("#btn_AbrirModalUsuario").trigger("click");
					}
		});
}
function traerSociedadesUsuario(emailUsuario)
{
	$.ajax({
				data:  {accion:"ObtenerSociedadesUsuario",
						email: emailUsuario
						},
				url:   'ajax/usuarios.php',
				type:  'post',
				beforeSend: function () {
				   //$("#AbrirCargando").trigger("click");
				},
				success:  function (response) {
					var json = jQuery.parseJSON(response);
					$.each(json, function(i, d) {
							//Recorremos los checkbox
							$('[name=chk_permisoc]').each(function(){
								if(String(d.id_sociedad)==String($(this).val()))
								{
									$(this).prop('checked', true);
									$(this).parents('tr').removeAttr('class');
									$(this).parents('tr').attr('class', 'selected'); 
								}
							});					
					});
					},
					complete: function(){
					//CerrarThickBox();
					$("#btn_AbrirModalUsuario").trigger("click");
					}
		});
}

/////////////////FIN FUNCIONES PAGINA DETALLE //////////////////////////




