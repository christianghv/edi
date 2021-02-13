$( document ).ready(function() {
    $("#btn_modificar_modal").click(function() {    
    });
	TraerTipoEs();
	TraerFormatos();
	TraerProvedores(1);	

         
	$("#btn_agregarProveedor").click(function() {  
		limpiarModal();
		$(".modal-content").css("display","block");
		$("#Proveedor_id").attr("disabled", false );
		$('#modalAgregarProveedorLabel').html("Ingresar Proveedor");
		
		$("#btn_grabarProv810").html("Insertar 810");
		$("#btn_grabarProv855").html("Insertar 855");
		$("#btn_grabarProv856").html("Insertar 856");
	
	});
	
	$("#btn_xcbl810").click(function(){
		replicarDatos(810);
    });
	
	$("#btn_xcbl855").click(function(){
		replicarDatos(855);
    });
	
	$("#btn_xcbl856").click(function(){
		replicarDatos(856);
    });
		
	$("#btn_volver").click(function(){
		var url="../../internos.php";
		$(location).attr('href',url);
    });
	
	$("#btn_grabarProveedor").click(function() {
		
		var errores=0;
		var grabados=0;
		
		// EDI 810 //
		var ValidarCboFormato810=($('#divCboFormato810').find('select'));
		var ValidarCboFormato855=($('#divCboFormato855').find('select'));
		var ValidarCboFormato856=($('#divCboFormato856').find('select'));
		
		//Validando el formato 810
		if($(ValidarCboFormato810).val() != "")
		{
			//Validar si la coneccion es valida
			if($('#H_Validacion_'+810).val()=='0')
			{
				alert('Debe verificar la Conexión de EDI'+810);
				$('#btn_VerificarConeccionProveedor').css('border-color','red');
				$('#btn_VerificarConeccionProveedor').css('color','red');
				activarPanelyTab(810);
				return;
			}		
		}
		
		//Validando el formato 855
		if($(ValidarCboFormato855).val() != "" && ValidarCboFormato810.val()!='2')
		{
			//Validar si la coneccion es valida
			if($('#H_Validacion_'+855).val()=='0')
			{
				alert('Debe verificar la Conexión de EDI'+855);
				$('#btn_VerificarConeccionProveedor').css('border-color','red');
				$('#btn_VerificarConeccionProveedor').css('color','red');
				activarPanelyTab(855);
				return;
			}		
		}
		
		//Validando el formato 856
		if($(ValidarCboFormato856).val() != "" && ValidarCboFormato810.val()!='2')
		{	
			//Validar si la coneccion es valida
			if($('#H_Validacion_'+856).val()=='0')
			{
				alert('Debe verificar la Conexión de EDI'+856);
				$('#btn_VerificarConeccionProveedor').css('border-color','red');
				$('#btn_VerificarConeccionProveedor').css('color','red');
				activarPanelyTab(856);
				return;
			}		
		}
		
		if($(ValidarCboFormato810).val() != "")
		{
			//Grabar EDI 810
			if(FormIngresarProvedorCabecera.checkValidity())
			{
				var valID=ValidarIngresoDeIdProvedor();
				if(valID==true)
				{
					//Se validara la parte 810				
					if(Form810.checkValidity())
					{
						grabarCabeceraProvedor(810);
					}
					else
					{	
						if($("#H_Validacion_810").val()=="0")
						{
							activarPanelyTab(810);
							$("#btn_Validar810").trigger("click");
						}
					}
				}
				else
				{
					alert("El ID PROVEDOR ingresado, ya se encuentra en uso, por favor ingrese otro");
					$("#Proveedor_id").focus();
				}
					
			}
			else
			{
				activarPanelyTab(810);
				$("#btn_ValidarCabecera").trigger("click")
				//--Fin Tab 855 activa --//
			}
		}
		// #EDI 810 //
		
		// EDI 855 //		
		if($(ValidarCboFormato855).val() != "")
		{
			//Grabar EDI 855
			if(FormIngresarProvedorCabecera.checkValidity())
			{
				var valID=ValidarIngresoDeIdProvedor();
				if(valID==true)
				{
					//Se validara la parte 855				
					if(Form855.checkValidity())
					{
						grabados++;
						grabarCabeceraProvedor(855);
					}
					else
					{
						if($("#H_Validacion_855").val()=="0")
						{
							activarPanelyTab(855);
							$("#btn_Validar855").trigger("click");
						}

					}
				}
				else
				{
					alert("El ID PROVEDOR ingresado, ya se encuentra en uso, por favor ingrese otro");
					$("#Proveedor_id").focus();
				}
			}
			else
			{
				activarPanelyTab(855);
				$("#btn_ValidarCabecera").trigger("click")
				//--Fin Tab 855 activa --//
			};
		}
		// #EDI 855 //
		
		// EDI 856 //
			
		if($(ValidarCboFormato856).val() != "")
		{
			//Grabar EDI 856
			if(FormIngresarProvedorCabecera.checkValidity())
			{
				var valID=ValidarIngresoDeIdProvedor();
				if(valID==true)
				{
					//Se validara la parte 856				
					if(Form856.checkValidity())
					{
						grabados++;
						grabarCabeceraProvedor(856);
					}
					else
					{	
						if($("#H_Validacion_856").val()=="0")
						{
							activarPanelyTab(856);
							$("#btn_Validar856").trigger("click");
						}
					}
				}
				else
				{
					alert("El ID PROVEDOR ingresado, ya se encuentra en uso, por favor ingrese otro");
					$("#Proveedor_id").focus();
				}
					
			}
			else
			{
				activarPanelyTab(856);
				$("#btn_ValidarCabecera").trigger("click")
				//--Fin Tab 856 activa --//
			};
		}
		// #EDI 856 //
		// EDI 850 //		
		if($("#Proveedor850_Servidor").val() != "" || $("#Proveedor850_Puerto").val() != "" || $("#Proveedor850_Usuario").val() != "" || $("#Proveedor850_RutaRemota").val() != "" || $("#Proveedor850_RutaLocal").val() != "" || $("#Proveedor850_Patron").val() != "")
		{
			//Validar si la coneccion es valida
			if($('#H_Validacion_'+850).val()=='0')
			{
				alert('Debe verificar la Conexión de EDI'+850);
				$('#btn_VerificarConeccionProveedor').css('border-color','red');
				$('#btn_VerificarConeccionProveedor').css('color','red');
				activarPanelyTab(850);
					return;
			}
			
			//Grabar EDI 850
			if(FormIngresarProvedorCabecera.checkValidity())
			{
				var valID=ValidarIngresoDeIdProvedor();
				if(valID==true)
				{
					//Se validara la parte 850				
					if(Form850.checkValidity())
					{
						grabados++;
						grabarCabeceraProvedor(850);
					}
					else
					{	
						if($("#H_Validacion_850").val()=="0")
						{
							activarPanelyTab(850);
							$("#btn_Validar850").trigger("click");
						}
					}
				}
				else
				{
					alert("El ID PROVEDOR ingresado, ya se encuentra en uso, por favor ingrese otro");
					$("#Proveedor_id").focus();
				}
			}
			else
			{
				activarPanelyTab(850);
				$("#btn_ValidarCabecera").trigger("click")
				//--Fin Tab 856 activa --//
			};
		}
		// #EDI 850 //
		//Solo guardara la cabecera
		if(FormIngresarProvedorCabecera.checkValidity())
		{
			var valID=ValidarIngresoDeIdProvedor();
			var ValidarCboFormato810=($('#divCboFormato810').find('select'));
			var ValidarCboFormato855=($('#divCboFormato855').find('select'));
			var ValidarCboFormato856=($('#divCboFormato856').find('select'));
			var ValidarCboFormato850=($('#divCboFormato850').find('select'));
				
			if(valID==true)
			{
				if($(ValidarCboFormato810).val()=="" && $(ValidarCboFormato855).val()=="" && $(ValidarCboFormato856).val()=="" && $(ValidarCboFormato850).val()=="")
				{
					grabarCabeceraProvedor(1);
				}
			}
			else
			{
				if(grabados==0)
				{
					alert("El ID PROVEDOR ingresado, ya se encuentra en uso, por favor ingrese otro");
					$("#Proveedor_id").focus();
				}	
			}		
		}
		else
		{
			$("#btn_ValidarCabecera").trigger("click")
			//--Fin Tab 856 activa --//
		};
	});
		
	$("#tab_855").click(function() {  
		$("#tab_810").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_810").removeAttr('class');	
		$("#Pro_810").attr('class', 'tab-pane fade contenedorTab');
		$("#Pro_855").removeAttr('class');	
		$("#Pro_855").attr('class', 'tab-pane fade contenedorTab active in');
	});
	$("#tab_856").click(function() {  
		$("#tab_810").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_810").removeAttr('class');	
		$("#Pro_810").attr('class', 'tab-pane fade contenedorTab');
		$("#Pro_856").removeAttr('class');	
		$("#Pro_856").attr('class', 'tab-pane fade contenedorTab active in');
	});
	$("#tab_850").click(function() {  
		$("#tab_810").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_810").removeAttr('class');	
		$("#Pro_810").attr('class', 'tab-pane fade contenedorTab');
		$("#Pro_850").removeAttr('class');	
		$("#Pro_850").attr('class', 'tab-pane fade contenedorTab active in');
	});
	
	$("#btn_EliminarProveedor").click(function() {
		if (confirm('¿Está seguro de eliminar él o los proveedores seleccionados?')) {
				EliminarProveedores();
			} else {
				// Do nothing!
		}
    });
});
function RespuestaSubidaKey(mensaje,EDI)
{
	alert(mensaje);
	$('#H_Llave_'+EDI).val("");
	$('#btn_key_'+EDI).removeAttr('disabled');
	$('#H_Validacion_'+EDI).val('0');
	$('#Icono_'+EDI).attr('class','fa fa-key fa-lg');
}
function KeySubidaOK(Key64,EDI)
{
	$('#H_Llave_'+EDI).val(Key64);
	$('#btn_key_'+EDI).removeAttr('disabled');
	$('#H_Validacion_'+EDI).val('0');
	$('#Icono_'+EDI).attr('class','fa fa-key fa-lg');
}
function SubirKey()
{
	var EDI = $('#Proveedor_key').attr('edi');
	$('#btn_key_'+EDI).attr('disabled','disabled');
	$('#Icono_'+EDI).attr('class','fa fa-cog fa-spin fa-lg fa-fw margin-bottom');

	$("#FormSubirLLave").submit();
}
function CargarKeyProveedor(e,EDI)
{
	e.preventDefault();
	$('#Proveedor_key').attr('edi',EDI);
	$('#Subir_Key_EDI').val(EDI);
	$('#Proveedor_key').trigger('click');
}
function OnChangeTipoEntrada(e)
{
	e.preventDefault();
	if($('#cbo_TipoEs').val()=='2')
	{
		$('[name=key_sftp]').each(function(i){
		   	$(this).show('fade');
		});
		$('[name=Proveedor_Contrasena]').each(function(i){
		   	$(this).removeAttr('required');
		});
	}
	else
	{
		$('[name=key_sftp]').each(function(i){
			$(this).hide('fade');
		});
		$('[name=Proveedor_Contrasena]').each(function(i){
		   	$(this).attr('required','required');
		});
	}
}
function GetHtmlFTP(EDI)
{
	var HTML='<label>Contraseña: </label><br />';
	HTML+='<input type="text" class="form-control" placeholder="Contraseña" name="Proveedor'+EDI+'_Contrasena" id="Proveedor'+EDI+'_Contrasena" size="25" style="width:230px;"  required="required" title="Ingrese Contraseña"/>';
	return HTML;
}
function GetHtmlSFTP(EDI)
{
	
}
function TabActivo(e,EDI)
{
	e.preventDefault();
	$('#H_TabActivo').val(EDI);
}
function VerificarConectividad(e)
{
	e.preventDefault();
	//Validar cabecera
	
	if(FormIngresarProvedorCabecera.checkValidity())
	{
		var valID=ValidarIngresoDeIdProvedor();
		if(valID==true)
		{
			//Validando el tab 
			var fomulario=$('#Form'+$('#H_TabActivo').val()+'');
			//Validar el formulario primero 
			if(!$(fomulario)[0].checkValidity())
			{
				$('#btn_Validar'+$('#H_TabActivo').val()).trigger('click');
			}
			else
			{
				//Validar si selecciono SFTP
				if($('#cbo_TipoEs').val()=='2')
				{
					//Validar si no ingreso llave
					if($.trim($('#Proveedor'+$('#H_TabActivo').val()+'_Contrasena').val())=='')
					{
						//Validar si si esta cargada la llave del formulario actual
						if($('#H_Llave_'+$('#H_TabActivo').val()).val()=='0')
						{
							$('#btn_key_'+$('#H_TabActivo').val()).css('border-color','red');
							$('#btn_key_'+$('#H_TabActivo').val()).css('color','red');
							alert("Debe ingresar la llave de acceso para SFTP");
							return;
						}
						else
						{
							$('#btn_key_'+$('#H_TabActivo').val()).css('border-color','');
							$('#btn_key_'+$('#H_TabActivo').val()).css('color','');
						}
					}
				}
				
				//Verificando coneccion
				$.ajax({
					data:  {
							tipo_ftp:$('#cbo_TipoEs').val(),
							Servidor:$('#Proveedor'+$('#H_TabActivo').val()+'_Servidor').val(),
							Puerto:$('#Proveedor'+$('#H_TabActivo').val()+'_Puerto').val(),
							Usuario:$('#Proveedor'+$('#H_TabActivo').val()+'_Usuario').val(),
							Contrasena:$('#Proveedor'+$('#H_TabActivo').val()+'_Contrasena').val(),
							Llave:$('#H_Llave_'+$('#H_TabActivo').val()+'').val(),
							RutaRemota:$('#Proveedor'+$('#H_TabActivo').val()+'_RutaRemota').val()
							},	
					url:   'ajax/test_coneccionFTP.php',
					type:  'post',
					beforeSend: function () {
						$('#btn_VerificarConeccionProveedor').attr('disabled','disabled');
						$('#IconBtnVerificar').attr('class','fa fa-cog fa-spin fa-lg fa-fw margin-bottom');
					},
					success:  function (response) {
						
						var json = jQuery.parseJSON(response);
				
						$.each(json, function(i, d) {
							
							var HtmlLog='';
							
							$.each(d.Detalle, function(j, e) {
								HtmlLog+=e+'<br />';
							});
							$("#log"+$('#H_TabActivo').val()).html(HtmlLog);
							
							if(d.Resultado=="ok")
							{
								$('#btn_VerificarConeccionProveedor').css('border-color','');
								$('#btn_VerificarConeccionProveedor').css('color','');
								$("#Proveedor_key").val("");
								$('#H_Validacion_'+$('#H_TabActivo').val()).val('1');
							}
							else
							{
								$('#H_Validacion_'+$('#H_TabActivo').val()).val('0');
							}
						});
					},
					complete: function(){
						$('#btn_VerificarConeccionProveedor').removeAttr('disabled');
						$("#IconBtnVerificar").attr('class','fa fa-cog');
					}
			});
			}
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
	};
}
////// FUNCIONES PAGINA PRINCIPAL //////////////////////////////////////
function activarPanelyTab(idTab)
{
	$('#H_TabActivo').val(idTab);
	if(idTab==810)
	{
		$("#tab_855").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_855").removeAttr('class');	
		$("#Pro_855").attr('class', 'tab-pane fade contenedorTab');
						
		$("#tab_856").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_856").removeAttr('class');	
		$("#Pro_856").attr('class', 'tab-pane fade contenedorTab');
		
		$("#tab_850").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_850").removeAttr('class');	
		$("#Pro_850").attr('class', 'tab-pane fade contenedorTab');		
						
		//--Tab 810 activa--//
		$("#tab_810").removeAttr('class');
		$("#tab_810").parent("li").attr('class', 'tab-pane contenedorTab active');
					
		$("#Pro_810").removeAttr('class');		
		$("#Pro_810").attr('class', 'tab-pane fade contenedorTab active in');
		//--Fin Tab 810 activa --//
	}
	
	if(idTab==855)
	{
		$("#tab_810").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_810").removeAttr('class');	
		$("#Pro_810").attr('class', 'tab-pane fade contenedorTab');
						
		$("#tab_856").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_856").removeAttr('class');	
		$("#Pro_856").attr('class', 'tab-pane fade contenedorTab');
		
		$("#tab_850").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_850").removeAttr('class');	
		$("#Pro_850").attr('class', 'tab-pane fade contenedorTab');		
						
		//--Tab 855 activa--//
		$("#tab_855").removeAttr('class');
		$("#tab_855").parent("li").attr('class', 'tab-pane contenedorTab active');
					
		$("#Pro_855").removeAttr('class');		
		$("#Pro_855").attr('class', 'tab-pane fade contenedorTab active in');
		//--Fin Tab 855 activa --//
	}
	
	if(idTab==856)
	{
		$("#tab_855").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_855").removeAttr('class');	
		$("#Pro_855").attr('class', 'tab-pane fade contenedorTab');
						
		$("#tab_810").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_810").removeAttr('class');	
		$("#Pro_810").attr('class', 'tab-pane fade contenedorTab');
		
		$("#tab_850").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_850").removeAttr('class');	
		$("#Pro_850").attr('class', 'tab-pane fade contenedorTab');		
						
		//--Tab 856 activa--//
		$("#tab_856").removeAttr('class');
		$("#tab_856").parent("li").attr('class', 'tab-pane contenedorTab active');
					
		$("#Pro_856").removeAttr('class');		
		$("#Pro_856").attr('class', 'tab-pane fade contenedorTab active in');
		//--Fin Tab 856 activa --//
	}
	
	if(idTab==850)
	{	
		$("#tab_855").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_855").removeAttr('class');	
		$("#Pro_855").attr('class', 'tab-pane fade contenedorTab');
		
		$("#tab_856").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_856").removeAttr('class');	
		$("#Pro_856").attr('class', 'tab-pane fade contenedorTab');
						
		$("#tab_810").parent("li").attr('class', 'tab-pane contenedorTab');
		$("#Pro_810").removeAttr('class');	
		$("#Pro_810").attr('class', 'tab-pane fade contenedorTab');		
						
		//--Tab 850 activa--//
		$("#tab_850").removeAttr('class');
		$("#tab_850").parent("li").attr('class', 'tab-pane contenedorTab active');
					
		$("#Pro_850").removeAttr('class');		
		$("#Pro_850").attr('class', 'tab-pane fade contenedorTab active in');
		//--Fin Tab 856 activa --//
	}
}
function EliminarProveedores()
{
	var ArrayIdProv = new Array();
	$('[class="selected"]').each(function(){
	var item = {
			"ID_Proveedor": $(this).find("td:eq(0) label").html()		
		};
		//ImprimirObjeto(item);
		
		ArrayIdProv.push(item);
		});
	JSON_ArrayIdProv = JSON.stringify({ArrayIdProv: ArrayIdProv});
	
		$.ajax({
					data:  {accion: "EliminarProveedores",
							JSON_ArrayIdProv:  JSON_ArrayIdProv
							},	
					url:   'ajax/proveedores.php',
					type:  'post',
					beforeSend: function () {
						$("#AbrirCargando").trigger("click");
					},
					success:  function (response) {
							afectadasProveedores=parseFloat(response);
						},
						complete: function(){
								//CerrarThickBox();
								//alert("Seleccionados eliminados");								
								TraerProvedores(0);
							
													
						}
		});
}
function ValidarIngresoDeIdProvedor()
{
	var validacion=true;
	
	MostrarTodosLosRegistros();
	
	if($('#Old_IdProv').val()=="")
	{
		$("[id=IdProveedor]").each(function(){
			if($(this).html()==$('#Proveedor_id').val())
			{
				validacion=false;
			}		
		});
	}	
	
	DejarDataTableDinamicaNormal();
	return 	validacion;
}

function remplazarCarateresBarras(valor)
{
	var str = valor;
	str = str.replace(/\//g, "");
	str = str.replace(/\\/g, "\\");
	return str;
}
function formatearOnchange(texto)
{
	var valor=$(texto).val();
	var limpio=remplazarCarateresBarras(valor);
	$(texto).val(limpio);
}
function validarRuta(ruta)
{
	var errores=0;
	var respuesta=false;

	var largo = ruta.length;
	var array = ruta.split("");
	
	for (i = 0; i < array.length; i++) {
		var caracter=array[i];
		if(caracter=="\\")
		{
			//Encontramos un backslach
			
			//Si esta al princio osea parte con back slash esta malo
			if(i==0)
			{
				errores++;
				//alert("Error esta al principio");
			}
			else
			{
				//ahora vemos si estan juntos los basckSlash con el siguiente valor
				if(array[i-1]=="\\" || array[i+1]=="\\")
				{
					//Estan juntos y esta malo
					//alert("Error estan juntos: antecesor: "+array[i-1]+" sucesor: "+array[i+1]);					
				}
				else
				{
					//viene una palabra despues del caracter
					errores++;
				}
			}
		}
		
	}
	
	
	var ValorUltimo=array[largo-1];
	
	if(ValorUltimo=="\\")
	{
		//RUTA FINAL CORRECTA
	}
	else
	{
		errores++;
		//alert("Error al final");
	}
	if(errores==0)
	{
		respuesta=true;
	}
	
	
	return respuesta;
}

function validarRutas(id_edi)
{
	validacion=false;
	var errores=0;
	
	//RutaLocal
	var RespuestaRutaLocal=validarRuta($("#Proveedor"+id_edi+"_RutaLocal").val());
	if(!RespuestaRutaLocal)
	{
		errores++;
		$("#Proveedor"+id_edi+"_RutaLocal").css("border-color","#a94442");
		activarPanelyTab(id_edi);
	}
	else
	{
		$("#Proveedor"+id_edi+"_RutaLocal").css("border","1px solid #ccc");
	}
	
	
	//Verificar ruta remota
	if($('#H_Validacion_'+id_edi).val()=='0')
	{
		errores++;
		activarPanelyTab(id_edi);
		$('#btn_VerificarConeccionProveedor').css('border-color','red');
		$('#btn_VerificarConeccionProveedor').css('color','red');
	}
	else
	{
		$('#btn_VerificarConeccionProveedor').css('border-color','');
		$('#btn_VerificarConeccionProveedor').css('color','');
	}
	
	if(errores==0)
	{
		validacion=true;
	}
	
	return validacion;
}

function limpiarModal(){
	
	//---Cabecera
	$("#Proveedor_id").val("");
	$("#Proveedor_CodigoSap").val("");
	$("#Proveedor_Nombre").val("");
	$("#Proveedor_Contrasena").val("");
	
	//Combobox Cabecera
	var cboTipoEntrada=($('#divCboTipoConeccion').find('select'));	
	$(cboTipoEntrada).val("");
	
	//---Detalle
	//Combobox detalle
	var cboFormato810=($('#divCboFormato810').find('select'));	
	$(cboFormato810).val("");
	var cboFormato855=($('#divCboFormato855').find('select'));	
	$(cboFormato855).val("");
	var cboFormato856=($('#divCboFormato856').find('select'));	
	$(cboFormato856).val("");
	var cboFormato850=($('#divCboFormato850').find('select'));	
	$(cboFormato850).val("");
	
	var cboTipoSalidaFormato850=($('#divCboSalidaFormato850').find('select'));	
	$(cboTipoSalidaFormato850).val("");
	
	$("#Proveedor810_Servidor").val("");
	$("#Proveedor810_Puerto").val("");
	$("#Proveedor810_Usuario").val("");
	$("#Proveedor810_Contrasena").val("");
	$("#Proveedor810_RutaRemota").val("");	
	$("#Proveedor810_RutaLocal").val("");
	$("#Proveedor810_RutaLocal").css("border","1px solid #ccc");	
	$("#Proveedor810_Patron").val("");	
	
	$("#Proveedor855_Servidor").val("");
	$("#Proveedor855_Puerto").val("");
	$("#Proveedor855_Usuario").val("");
	$("#Proveedor855_Contrasena").val("");
	$("#Proveedor855_RutaRemota").val("");	
	$("#Proveedor855_RutaLocal").val("");
	$("#Proveedor855_RutaLocal").css("border","1px solid #ccc");
	$("#Proveedor855_Patron").val("");	
	
	$("#Proveedor856_Servidor").val("");
	$("#Proveedor856_Puerto").val("");
	$("#Proveedor856_Usuario").val("");
	$("#Proveedor856_Contrasena").val("");
	$("#Proveedor856_RutaRemota").val("");	
	$("#Proveedor856_RutaLocal").val("");
	$("#Proveedor856_RutaLocal").css("border","1px solid #ccc");
	$("#Proveedor856_Patron").val("");
	
	$("#Proveedor850_Servidor").val("");
	$("#Proveedor850_Puerto").val("");
	$("#Proveedor850_Usuario").val("");
	$("#Proveedor850_Contrasena").val("");
	$("#Proveedor850_RutaRemota").val("");	
	$("#Proveedor850_RutaLocal").val("");
	$("#Proveedor850_RutaLocal").css("border","1px solid #ccc");
	$("#Proveedor850_Patron").val("");
	$("#Proveedor850_CarpetaFtpEntrada").val("");	
	
	$("#H_OldFormato810").val("");	
	$("#H_OldFormato855").val("");	
	$("#H_OldFormato856").val("");
	$("#H_OldFormato850").val("");
	
	$("#Old_IdProv").val("");
		
	$("#tab_810").parent("li").removeAttr('class');
	$("#tab_855").parent("li").removeAttr('class');
	$("#tab_856").parent("li").removeAttr('class');
	$("#tab_850").parent("li").removeAttr('class');
	
	$("#tab_810").parent("li").attr('class', 'tab-pane fade contenedorTab active in');
	$("#tab_855").parent("li").attr('class', 'tab-pane contenedorTab');
	$("#tab_856").parent("li").attr('class', 'tab-pane contenedorTab');
	$("#tab_850").parent("li").attr('class', 'tab-pane contenedorTab');	
	
	$("#Pro_810").removeAttr('class');
	$("#Pro_855").removeAttr('class');
	$("#Pro_856").removeAttr('class');
	$("#Pro_850").removeAttr('class');
	
	$("#Pro_810").attr('class', 'tab-pane fade contenedorTab active in');
	$("#Pro_855").attr('class', 'tab-pane fade contenedorTab');
	$("#Pro_856").attr('class', 'tab-pane fade contenedorTab');
	$("#Pro_850").attr('class', 'tab-pane fade contenedorTab');
	$("#btn_xcbl810").hide();
	$("#btn_xcbl855").hide();
	$("#btn_xcbl856").hide();
	
	
	$("#H_Validacion_810").val("0");
	$("#H_Validacion_855").val("0");
	$("#H_Validacion_856").val("0");
	$("#H_Validacion_850").val("0");
	
	$("#H_Llave_810").val("0");
	$("#H_Llave_855").val("0");
	$("#H_Llave_856").val("0");
	$("#H_Llave_850").val("0");
	
	$("#Proveedor_key").val("");
	
	$('#btn_key_850').hide('fade');
	
	$('#cbo_TipoEs').val('');
	$('#cbo_TipoEs').trigger('change');
	
	$('#btn_VerificarConeccionProveedor').css('border-color','');
	$('#btn_VerificarConeccionProveedor').css('color','');
	
	$('[name=key_sftp]').each(function(i){
		$(this).css('border-color','');
		$(this).css('color','');
	});
	
	$('#log810').html("");
	$('#log855').html("");
	$('#log856').html("");
	$('#log850').html("");
	
	$('#DivInfoXCBL').hide();
	
}
function GrabarConfiguracion(Tipo)
{
	var cboFormato=($('#divCboFormato'+Tipo).find('select'));
	
	$.ajax({
            data:  {
					accion:'grabarDetalleProveedor',
					Proveedor_id:$('#Proveedor_id').val(),
					Formato:$(cboFormato).val(),
					Tipo: Tipo,
					CarpetaFtpEntrada:$('#Proveedor'+Tipo+'_CarpetaFtpEntrada').val(),
					OldFormato:$('#H_OldFormato'+Tipo).val(),
					Servidor:$('#Proveedor'+Tipo+'_Servidor').val(),
					Puerto:$('#Proveedor'+Tipo+'_Puerto').val(),
					Usuario:$('#Proveedor'+Tipo+'_Usuario').val(),
					Contrasena:$('#Proveedor'+Tipo+'_Contrasena').val(),
					Llave: $('#H_Llave_'+Tipo+'').val(),
					RutaRemota:$('#Proveedor'+Tipo+'_RutaRemota').val(),
					RutaLocal:$('#Proveedor'+Tipo+'_RutaLocal').val(),
					Patron:$('#Proveedor'+Tipo+'_Patron').val(),
					TipoSalida: $('#cbo_TipoEs').val()
					},	
            url:   'ajax/proveedores.php',
            type:  'post',
            beforeSend: function () {
                //$("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);		                    
            },
			complete: function(){
				//CerrarThickBox();	
				$("#btn_CancelarProveedor").trigger("click");						
				TraerProvedores(0);
			}
	});
}
function grabarCabeceraProvedor(TipoFuncion)
{
	$('.modal-content').fadeOut(500);
	$('.modal-backdrop').fadeOut(500);
	//modal-backdrop fade in	
	var CboTipoConeccion=($('#divCboTipoConeccion').find('select'));	
	
	$("#AbrirCargando").trigger("click");	
	$.ajax({
            data:  {
					accion:"grabarCabeceraProvedor",
					Proveedor_id : $("#Proveedor_id").val(),
					Proveedor_CodigoSap : $("#Proveedor_CodigoSap").val(),
					Proveedor_Nombre : $("#Proveedor_Nombre").val(),
					Proveedor_Contrasena : $("#Proveedor_Contrasena").val(),
					CambioClave: $("#H_Cambio_Clave").val(),
					CboTipoEntrada : $(CboTipoConeccion).val(),
					CboTipoSalida :  $(CboTipoConeccion).val()
					},	
            url:   'ajax/proveedores.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				//alert(response);		                    
            },
			complete: function(){
				$('#Old_IdProv').val("");
				if(TipoFuncion!=1)
				{
					GrabarConfiguracion(TipoFuncion);					
				}
				else
				{
					$("#btn_CancelarProveedor").trigger("click");
					TraerProvedores(0);
				}
			}
	});
}

function CargarDetailOnClick(label)
{	
	limpiarModal();
	$(".modal-content").css("display","block");
	$("#btn_grabarProv810").html("Grabar 810");
	$("#btn_grabarProv855").html("Grabar 855");
	$("#btn_grabarProv856").html("Grabar 856");
	$("#btn_grabarProv850").html("Grabar 850");

	$("#AbrirCargando").trigger("click");
	$("#Proveedor_id").attr("disabled", true );
	$('#modalAgregarProveedorLabel').html("Editar Proveedor");
	$('#Proveedor_id').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	
	$('#Old_IdProv').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());	
	$('#H_OldIdProveedor').val($(label).parent('td').parent('tr').find('td:eq(0) label').html());
	$('#Proveedor_CodigoSap').val($(label).parent('td').parent('tr').find('td:eq(1)').html());
	$('#Proveedor_Nombre').val($(label).parent('td').parent('tr').find('td:eq(2)').html());
	var Entrada=($('#divCboTipoConeccion').find('select'));	
	var valorEntradaID=TipoEsDeTextoAID(label,3);
	$(Entrada).val(valorEntradaID);
	
	var Salida=($('#divCboSalidaFormato850').find('select'));	
	var valorSalidaID=TipoEsDeTextoAID(label,4);
	$(Salida).val(valorSalidaID);
	
	var id_provedorEnTabla=$(label).parent('td').parent('tr').find('td:eq(0) label').html();
	$('#cbo_TipoEs').trigger('change');
	$(Salida).trigger('change');
	
	$('#H_Cambio_Clave').val('0');
	$('#Proveedor_Contrasena').val('');
	$('#Proveedor_Contrasena').removeAttr('required');
	
	
	
	TraerProcesosPorID(id_provedorEnTabla);
	//$("#btn_grabarUsuario").css("display","inline");
	//$("#btn_insertarUsuario").css("display","none");
}
function OnChangeCambioPass()
{
	$('#H_Cambio_Clave').val('1');
	$('#Proveedor_Contrasena').attr('required','required');
}
function TraerProcesosPorID(IdProvedor)
{
	$.ajax({
            data:  {
					accion:"BuscarTipoProcesoPorProveedor",
					IdProvedor:IdProvedor
					},	
            url:   'ajax/proveedores.php',
            type:  'post',
            beforeSend: function () {
               // $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
			
				var json = jQuery.parseJSON(response);
				
				$.each(json, function(i, d) {
					//Si encontramos registro de EDI 810
					if(d.id_tipoProceso=='1' || d.id_tipoProceso=='0')
					{
						var cboFormato810=($('#divCboFormato810').find('select'));	
						$(cboFormato810).val(d.id_formato);
						$("#H_OldFormato810").val(d.id_formato);
						$("#Proveedor810_Servidor").val(d.servidor);
						$("#Proveedor810_Puerto").val(d.puerto);
						$("#Proveedor810_Usuario").val(d.usr);
						$("#Proveedor810_Contrasena").val($.trim(d.pass));
						$("#Proveedor810_RutaRemota").val(d.ruta_remota);	
						$("#Proveedor810_RutaLocal").val(d.ruta_local);	
						$("#Proveedor810_Patron").val(d.patron);
						$("#H_Llave_810").val(d.llave);
					}
					//Si encontramos registro de EDI 855
					if(d.id_tipoProceso=='2')
					{
						var cboFormato855=($('#divCboFormato855').find('select'));	
						$(cboFormato855).val(d.id_formato);
						$("#H_OldFormato855").val(d.id_formato);
						$("#Proveedor855_Servidor").val(d.servidor);
						$("#Proveedor855_Puerto").val(d.puerto);
						$("#Proveedor855_Usuario").val(d.usr);
						$("#Proveedor855_Contrasena").val($.trim(d.pass));
						$("#Proveedor855_RutaRemota").val(d.ruta_remota);	
						$("#Proveedor855_RutaLocal").val(d.ruta_local);	
						$("#Proveedor855_Patron").val(d.patron);
						$("#H_Llave_855").val(d.llave);
					}
					//Si encontramos registro de EDI 856
					if(d.id_tipoProceso=='3')
					{
						var cboFormato856=($('#divCboFormato856').find('select'));	
						$(cboFormato856).val(d.id_formato);
						$("#H_OldFormato856").val(d.id_formato);
						$("#Proveedor856_Servidor").val(d.servidor);
						$("#Proveedor856_Puerto").val(d.puerto);
						$("#Proveedor856_Usuario").val(d.usr);
						$("#Proveedor856_Contrasena").val($.trim(d.pass));
						$("#Proveedor856_RutaRemota").val(d.ruta_remota);	
						$("#Proveedor856_RutaLocal").val(d.ruta_local);	
						$("#Proveedor856_Patron").val(d.patron);
						$("#H_Llave_856").val(d.llave);
						if(d.id_formato==2)
						{
							$('#btn_xcbl810').fadeIn();
							$('#btn_xcbl855').fadeOut();
							$('#btn_xcbl856').fadeOut();
						}
						else
						{
							$('#btn_xcbl810').fadeOut();
							$('#btn_xcbl855').fadeOut();
							$('#btn_xcbl856').fadeOut();
						}
					}
					
					//Si encontramos registro de EDI 856
					if(d.id_tipoProceso=='5')
					{
						var cboFormato850=($('#divCboFormato850').find('select'));	
						$(cboFormato850).val(d.id_formato);
						$("#H_OldFormato850").val(d.id_formato);
						$("#Proveedor850_CarpetaFtpEntrada").val(d.ruta_remota_ftp);
						$("#Proveedor850_Servidor").val(d.servidor);
						$("#Proveedor850_Puerto").val(d.puerto);
						$("#Proveedor850_Usuario").val(d.usr);
						$("#Proveedor850_Contrasena").val($.trim(d.pass));
						$("#Proveedor850_RutaRemota").val(d.ruta_remota);	
						$("#Proveedor850_RutaLocal").val(d.ruta_local);	
						$("#Proveedor850_Patron").val(d.patron);
						$("#H_Llave_850").val(d.llave);
					}
					
					$("#H_Validacion_810").val("1");
					$("#H_Validacion_855").val("1");
					$("#H_Validacion_856").val("1");
					$("#H_Validacion_850").val("1");
				});
							
                },
				complete: function(){
						CerrarThickBox();
						$("#btn_AbrirModalProveedor").trigger("click");
				}
	});
}

function TipoEsDeTextoAID(variable,ubicacion)
{
	var idObtenido=0;
	$("[id=OpcionTipoEs]").each(function(){
		if($(this).html()== $(variable).parent('td').parent('tr').find('td:eq('+ubicacion+')').html())
		{
			idObtenido=$(this).val();			
		}
	});
	return idObtenido;
}


function TraerProvedores(AbrirCargando)
{
	$.ajax({
            data:  {
					accion:"buscarProveedores"
					},	
            url:   'ajax/proveedores.php',
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
						content += '<tr id="'+d.id_proveedor+'" class="editar">';
						content += '<td><label onclick="javascript:CargarDetailOnClick(this);" id="IdProveedor" style="cursor:pointer">'+d.id_proveedor+'</label></td>';
						content += '<td>'+d.cod_sap+'</td>';
						content += '<td>'+d.nombre+'</td>';
						content += '<td>'+d.descripcion_entrada+'</td>';
						content += '<td>'+d.descripcion_salida+'</td>';
						content += '<td><input type="checkbox" class="marca_chk" name="chk_proveedor" id="chk_proveedor" value="'+d.id_proveedor+'" onchange="javascript:CambiarAlClicChk(this);">'+'</td>';
						content += '</tr>';
					});
					content += '</tbody>';
					$('#tabla_proveedores').dataTable().fnClearTable();
					$('#tabla_proveedores').dataTable().fnDestroy();
					$('#tabla_proveedores tbody').replaceWith(content);
					$('#tabla_proveedores').dataTable({
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
function SeleccionFormato(Cbo)
{
	if($(Cbo).val()==2)
	{
		$('#DivInfoXCBL').show('fade');
	}
	else
	{
		$('#DivInfoXCBL').hide('fade');
	}
}
function TraerFormatos()
{
	$.ajax({
            data:  {
				accion:"buscarFormatos"
					},	
            url:   'ajax/proveedores.php',
            type:  'post',
            beforeSend: function () {
                $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				var content810 	 = "";
				var contentOtros = "";
				$('#divCboFormato810').html("");
				$('#divCboFormato855').html("");
				$('#divCboFormato856').html("");
				var json = jQuery.parseJSON(response);
				content810 += "<label>Formato: </label>";
				content810 += '<select class="form-control" id="cbo_Formato" title="Ingrese Tipo de formato" name="cbo_Formato" required="required">';
				content810 += "<option value=''>-Seleccione-</option>";
				contentOtros+=content810; 
				$.each(json, function(i, d) {
					content810 += "<option id='OpcionCboFormato' value='"+d.id_formato+"'>"+d.descripcion+"</option>";
					if(i==0)
					{
						contentOtros += "<option id='OpcionCboFormato' value='"+d.id_formato+"'>"+d.descripcion+"</option>";
					}
					
				});
				content810 += "</select>";
				contentOtros+= "</select>";
				
				$('#divCboFormato810').html(content810);
				var CboTipo810=$('#divCboFormato810').find('select');
				$(CboTipo810).attr('onchange','javascript:SeleccionFormato(this);');
				$('#divCboFormato855').html(contentOtros);
				$('#divCboFormato856').html(contentOtros);
				
				
				ActivarQtip();

                },
				complete: function(){
						CerrarThickBox();				
				}
	});
}

function TraerTipoEs()
{
	$.ajax({
            data:  {
					accion:"buscarTipoEs"
					},	
            url:   'ajax/proveedores.php',
            type:  'post',
            beforeSend: function () {
                $("#AbrirCargando").trigger("click");
            },
            success:  function (response) {
				$('#divCboTipoConeccion').html("");				
				var content = "";
				var json = jQuery.parseJSON(response);
				content += '<select class="form-control" id="cbo_TipoEs" onchange="javascript:OnChangeTipoEntrada(event)" name="cbo_TipoEs" required="required" >';
				content += "<option value=''>-Seleccione-</option>";
				$.each(json, function(i, d) {
					content += "<option id='OpcionTipoEs' value='"+d.id_tipoes+"'>"+d.descripcion+"</option>";
				});
				content += "</select>";
				$('#divCboTipoConeccion').html(content);
				
				var cboTipoEntrada=($('#divCboTipoConeccion').find('select'));	
				$(cboTipoEntrada).attr("title","Ingrese Tipo Entrada");
				
				ActivarQtip();
				
                },
				complete: function(){

						//oldtitle="Ingrese Tipo Salida" 
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
	$('#tabla_proveedores').dataTable().fnDestroy();
	$('#tabla_proveedores').dataTable({
		"language": LAN_ESPANOL,
		"aLengthMenu": [[10,25, 50, 75, -1], [10,25, 50, 75, "All"]],
		"iDisplayLength": -1
	});
}
function DejarDataTableDinamicaNormal()
{
	$('#tabla_proveedores').dataTable().fnDestroy();
	$('#tabla_proveedores').dataTable({
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
      style: 'cream' // Give it some style					  
				   });
}
function replicarDatos(recibido)
{
	//Var combos
	var CboFormato810=($('#divCboFormato810').find('select'));	
	var CboFormato855=($('#divCboFormato855').find('select'));	
	var CboFormato856=($('#divCboFormato856').find('select'));
	var CboFormato850=($('#divCboFormato850').find('select'));

	if(recibido==810)
	{
		//Se validara la parte 810				
		if(Form810.checkValidity())
		{
			$(CboFormato855).val("2");
			$(CboFormato856).val("2");
			
			$('#Proveedor855_Servidor').val($('#Proveedor810_Servidor').val());
			$('#Proveedor856_Servidor').val($('#Proveedor810_Servidor').val());
			
			$('#Proveedor855_Puerto').val($('#Proveedor810_Puerto').val());
			$('#Proveedor856_Puerto').val($('#Proveedor810_Puerto').val());
			
			$('#Proveedor855_Usuario').val($('#Proveedor810_Usuario').val());
			$('#Proveedor856_Usuario').val($('#Proveedor810_Usuario').val());
			
			$('#Proveedor855_Contrasena').val($('#Proveedor810_Contrasena').val());
			$('#Proveedor856_Contrasena').val($('#Proveedor810_Contrasena').val());
			
			$('#Proveedor855_RutaRemota').val($('#Proveedor810_RutaRemota').val());
			$('#Proveedor856_RutaRemota').val($('#Proveedor810_RutaRemota').val());
			
			$('#Proveedor855_RutaLocal').val($('#Proveedor810_RutaLocal').val());
			$('#Proveedor856_RutaLocal').val($('#Proveedor810_RutaLocal').val());
			
			$('#Proveedor855_Patron').val($('#Proveedor810_Patron').val());
			$('#Proveedor856_Patron').val($('#Proveedor810_Patron').val());
			
			$('#H_Llave_855').val($('#H_Llave_810').val());
			$('#H_Llave_856').val($('#H_Llave_810').val());
			
			$('#H_Validacion_855').val($('#H_Validacion_810').val());
			$('#H_Validacion_856').val($('#H_Validacion_810').val());
			
		}
		else
		{	
			$("#btn_Validar810").trigger("click");
		}
	}
	
	if(recibido==855)
	{
		//Se validara la parte 810				
		if(Form855.checkValidity())
		{
		
			$(CboFormato810).val("2");
			$(CboFormato856).val("2");
			
			$('#Proveedor810_Servidor').val($('#Proveedor855_Servidor').val());
			$('#Proveedor856_Servidor').val($('#Proveedor855_Servidor').val());
			
			$('#Proveedor810_Puerto').val($('#Proveedor855_Puerto').val());
			$('#Proveedor856_Puerto').val($('#Proveedor855_Puerto').val());
			
			$('#Proveedor810_Usuario').val($('#Proveedor855_Usuario').val());
			$('#Proveedor856_Usuario').val($('#Proveedor855_Usuario').val());
			
			$('#Proveedor810_Contrasena').val($('#Proveedor855_Contrasena').val());
			$('#Proveedor856_Contrasena').val($('#Proveedor855_Contrasena').val());
			
			$('#Proveedor810_RutaRemota').val($('#Proveedor855_RutaRemota').val());
			$('#Proveedor856_RutaRemota').val($('#Proveedor855_RutaRemota').val());
			
			$('#Proveedor810_RutaLocal').val($('#Proveedor855_RutaLocal').val());
			$('#Proveedor856_RutaLocal').val($('#Proveedor855_RutaLocal').val());
			
			$('#Proveedor810_Patron').val($('#Proveedor855_Patron').val());
			$('#Proveedor856_Patron').val($('#Proveedor855_Patron').val());
			
			$('#H_Llave_810').val($('#H_Llave_855').val());
			$('#H_Llave_856').val($('#H_Llave_855').val());
			
			$('#H_Validacion_810').val($('#H_Validacion_855').val());
			$('#H_Validacion_856').val($('#H_Validacion_855').val());
		
		}
		else
		{	
			$("#btn_Validar855").trigger("click");
		}
	}
	
	if(recibido==856)
	{
		//Se validara la parte 856				
		if(Form856.checkValidity())
		{
			$(CboFormato810).val("2");
			$(CboFormato855).val("2");
			
			$('#Proveedor810_Servidor').val($('#Proveedor856_Servidor').val());
			$('#Proveedor855_Servidor').val($('#Proveedor856_Servidor').val());
			
			$('#Proveedor810_Puerto').val($('#Proveedor856_Puerto').val());
			$('#Proveedor855_Puerto').val($('#Proveedor856_Puerto').val());
			
			$('#Proveedor810_Usuario').val($('#Proveedor856_Usuario').val());
			$('#Proveedor855_Usuario').val($('#Proveedor856_Usuario').val());
			
			$('#Proveedor810_Contrasena').val($('#Proveedor856_Contrasena').val());
			$('#Proveedor855_Contrasena').val($('#Proveedor856_Contrasena').val());
			
			$('#Proveedor810_RutaRemota').val($('#Proveedor856_RutaRemota').val());
			$('#Proveedor855_RutaRemota').val($('#Proveedor856_RutaRemota').val());
			
			$('#Proveedor810_RutaLocal').val($('#Proveedor856_RutaLocal').val());
			$('#Proveedor855_RutaLocal').val($('#Proveedor856_RutaLocal').val());
			
			$('#Proveedor810_Patron').val($('#Proveedor856_Patron').val());
			$('#Proveedor855_Patron').val($('#Proveedor856_Patron').val());
			
			$('#H_Llave_810').val($('#H_Llave_856').val());
			$('#H_Llave_855').val($('#H_Llave_856').val());
			
			$('#H_Validacion_810').val($('#H_Validacion_856').val());
			$('#H_Validacion_855').val($('#H_Validacion_856').val());
		}
		else
		{	
			$("#btn_Validar856").trigger("click");
		}
	}
}
function DetectaCambioConeccion(EDI)
{
	$("#H_Validacion_"+EDI+"").val("0");
}

/////////////////FIN FUNCIONES PAGINA DETALLE //////////////////////////




