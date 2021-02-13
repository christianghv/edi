<!DOCTYPE html>
<html>
<?
	session_start();
	$email = $_SESSION['email'];
	if (!isset($email))
	 header('Location: ../index.php');
	require '../clases/class.templates.php';
	if (!isset($_SESSION["email"])){
    header('Location:../index.php');
	}
	
	//Verificar Permisos
	include_once("../ajax/ValPemi.php");
	$AccesoPermiso=VerificarPermiso(2);
	
	if($AccesoPermiso==false)
	{
		header('Location: ../internos.php');
	}
	
?>
<head>
    <title>PROVEEDORES</title>
    <?php include("includes.php");?> 
	<script src="../EDI/js/funciones.js" type="text/javascript"  charset="UTF-8"></script>
    <script src="js/proveedores.js"></script>
	<script  src="../plugins/JqueryUI/jquery-ui.js" type="text/javascript" language="javascript" ></script>
	
	<script>
		/**
		window.onbeforeunload = function(e) {
		  return "Esta a punto de abandonar EDI855. Para regresar a las Ordenes de Compra presion 'Volver'";
		};*/
	
	</script>
	<style>
	.table_tooltip{
		text-align:center;
		margin:5px;
		padding:5px;
		border: 1px #333 solid;
	}
	.row1{
		width: 80px;
		text-align:left;
		margin:5px;
		padding:5px;
		border: 1px #333 solid;
		font-weight:bold;
	}
	.row2{
		width: 70px;
		text-align:center;
		margin:5px;
		padding:5px;
		border: 1px #333 solid;
	}
	.tab-content .contenedorTab{
	background-color: #FFFFFF;
	border-color: #DDDDDD #DDDDDD rgba(0, 0, 0, 0.2);
	border-image: none;
	border-style: solid;
	border-width: 1px;
	border-top:none; width:100%;
	}
	.contenedorTab .row .col-lg-4{
	margin-left: 10px;
	}
	#tabla_proveedores th { text-align: center }
	#tabla_proveedores td { text-align: center }
	</style>
</head>
<body id="bodyProvedores">
<a href="../EDI/iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>
<div id="pagina_principal" style="display:block;"><!--DIV PAGINA PRINCIPAL-->
	<div id="page_header_principal">
		<div id="wrapper_principal">

        <?
			$template = new templates("templates/barra_nav.html");
			$template->setParams( array (
			'reporte' => "PROVEEDORES"
			));
			$template->show();
		?>
		
        <!--<div id="page-wrapper">-->
			<br>
  			<!-- TABLA -->
            <div class="row" style="margin-left:10px; margin-right:10px;">
                <div class="col-lg-12">
                    <div class="panel panel-default">
						
                        <div class="panel-heading" >
                            <div align="left">
								<button type="button" class="btn btn-primary" id="btn_agregarProveedor" name="btn_agregarUsuario" data-toggle="modal" data-target="#modalAgregarProveedor">
									<span class="glyphicon glyphicon-plus"></span> &nbsp;Agregar
								</button>
								<button type="button" class="btn btn-danger" id="btn_EliminarProveedor" name="btn_EliminarUsuario">
									<span class="glyphicon glyphicon-trash"></span> &nbsp;Eliminar
								</button>
							</div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" >
                                <table class="display" cellspacing="0" width="100%" id="tabla_proveedores" name="tabla_proveedores">
                                    <thead>
                                        <tr>
                                            <th id="thProvedor">ID Proveedor</th>
                                            <th>Codigo SAP</th>
                                            <th>Nombre</th>
											<th>Tipo Entrada</th>
											<th>Tipo Salida</th>
											<th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyProveedor">
                                    
                                    </tbody>
                                    <tfoot id="tdFootProveedor">
										<tr>
                                            <th>ID Proveedor</th>
                                            <th>Codigo SAP</th>
                                            <th>Nombre</th>
											<th>Tipo Entrada</th>
											<th>Tipo Salida</th>
											<th>&nbsp;</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div><!-- /.panel -->
                </div>
            </div><!-- FIN TABLA -->       
</div><!-- /#page-wrapper -->
</div> <!-- page_header -->
</div><!--FIN PAGINA PRINCIPAL-->
</div>
<div style="margin-left:25px; margin-top:-5px;" class="row">
		<div align="left">
    		<button name="btn_volver" id="btn_volver" class="btn btn-info" type="button"> 
			<span class="glyphicon glyphicon-home"></span>
			Volver</button>               
		</div>
</div>
<!--------------------------------------------------------------------->
<!-- Modal -->
<!-- FIN TABLA -->
			
            <!-- Modal -->
            <div class="modal fade" id="modalAgregarProveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"    data-backdrop="static" 
   data-keyboard="false">
            	<div class="modal-dialog">
                	<div class="modal-content">
                  		<div class="modal-header">
                    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    		<h4 class="modal-title" id="modalAgregarProveedorLabel">Agregar Proveedor</h4>
                  		</div>
                  		<div class="modal-body">
                    		<div class="panel panel-default">
                                <div class="panel-heading">
								<div id="divCabecera">
								<form name="FormIngresarProvedorCabecera" id="FormIngresarProvedorCabecera" method="post" action="" onSubmit="return false;">
							<div class="row">
								<div class="col-lg-4" style="width:270px" >
								<div class="form-group">
								 <label>ID Proveedor: </label>
								 <input type="hidden" name="Old_IdProv" id="Old_IdProv"/>
								 <br />
								<input type="text" class="form-control" placeholder="ID Proveedor" name="Proveedor_id" id="Proveedor_id"
								size="25" style="width:230px;" required title="Ingrese ID Proveedor" />         
								</div>
								</div>
								
								<div class="col-lg-4" style="width:270px">
								<div class="form-group" >
								 <label>Codigo SAP: </label>
								 <br />
								<input type="number" class="form-control" placeholder="Codigo SAP" name="Proveedor_CodigoSap" id="Proveedor_CodigoSap" style="width:230px;" required title="Ingrese Codigo SAP" />    
								</div>
								</div>								
						   </div>
						   <div class="row">
								<div class="col-lg-4" style="width:270px" >
									<div class="form-group">
										 <label>Nombre: </label>
										 <br />
										<input type="text" class="form-control" placeholder="Nombre" name="Proveedor_Nombre" id="Proveedor_Nombre"
										value="" size="25" style="width:230px;"  required="required" title="Ingrese Nombre Proveedor"/>         
									</div>
								</div>
								<div class="col-lg-4" style="width:270px">
									<label>Tipo Conexión: </label>
									<div class="form-group" id="divCboTipoConeccion" title="Ingrese Tipo Entrada">       
									</div>
								</div>
						   </div>
						   <div class="row">
								<div class="col-lg-4" style="width:270px">
								<div class="form-group" >
								 <label>Contraseña: </label>
								 <br />
								<input type="password" class="form-control" placeholder="Contraseña" name="Proveedor_Contrasena" id="Proveedor_Contrasena" style="width:230px;" onChange="javascript:OnChangeCambioPass()" required="required" title="Ingrese contraseña de Provedor" />    
								</div>
								</div>	
						   </div>
						   <input type="submit" id="btn_ValidarCabecera" name="btn_ValidarCabecera" value="validarCabecera" style="display:none">
						   </form>
						   </div>
						   
						   <!-- Tab Menu-->
						   <div class="row" style="margin-left:0px; width:547px">
						   	<ul id="myTab" class="nav nav-tabs">
							   <li class="active">
								  <a href="#Pro_810" onclick="javascript:TabActivo(event,810)" id="tab_810" data-toggle="tab">EDI 810</a>
							   </li>
							   <li>
								  <a href="#Pro_855" onclick="javascript:TabActivo(event,855)" id="tab_855" data-toggle="tab">EDI 855</a>
							   </li>
							   <li>
								  <a href="#Pro_856" onclick="javascript:TabActivo(event,856)" id="tab_856" data-toggle="tab">EDI 856</a>
							   </li>
							   <li>
								  <a href="#Pro_850" onclick="javascript:TabActivo(event,850)" id="tab_850" data-toggle="tab">EDI 850</a>
							   </li>
							</ul>
							
							<div id="ProveedoresTab" class="tab-content">
							   <!-- Tab 810-->							   
							   <div class="tab-pane fade in active contenedorTab" id="Pro_810">
							   <form name="Form810" id="Form810" method="post" action="" onSubmit="return false;">
							   <input type="hidden" id="H_OldFormato810" name="H_OldFormato810">

								  <div class="row">
									<div class="col-lg-4" style="width:270px" >
										<br />
										<div class="form-group" id="divCboFormato810"  title="Ingrese Tipo de formato">     
										</div>
									</div>
									<div class="col-lg-4" style="width:270px" >
										<br />
										<br />
										<div class="alert alert-info" id="DivInfoXCBL" style="padding-top: 0px;padding-left: 5px;padding-bottom: 0px;padding-right: 5px;display:none">
											<label>Se extraera automaticamente EDI 810, EDI 855 y EDI 856</label>
										</div>	
									</div>
								   </div>
								  <div class="row">
									<div class="col-lg-4" style="width:270px" >
											<label>Servidor: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(810);" placeholder="Servidor" name="Proveedor810_Servidor" id="Proveedor810_Servidor"
											size="25" style="width:230px;"  required="required" title="Ingrese Servidor"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Puerto: </label>
											 <br />
											<input type="number" class="form-control" onChange="javascript:DetectaCambioConeccion(810);" placeholder="Puerto" name="Proveedor810_Puerto" id="Proveedor810_Puerto"
											size="25" style="width:230px;"  required="required" title="Ingrese Puerto"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Usuario: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(810);" placeholder="Usuario" name="Proveedor810_Usuario" id="Proveedor810_Usuario"
											size="25" style="width:230px;"  required="required" title="Ingrese Usuario"/>         
									</div>
									<div class="col-lg-4" style="width:270px" name="contrasenas">
										<label>Contraseña: </label>
										<br />
										<div class="input-group">
											<input type="password" class="form-control" onChange="javascript:DetectaCambioConeccion(810);" placeholder="Contraseña" name="Proveedor_Contrasena" id="Proveedor810_Contrasena" size="25"  required="required" title="Ingrese Contraseña"/>   
											<span class="input-group-btn">
												<button class="btn btn-info" id="btn_key_810" name="key_sftp" edi="810" type="button" style="height: 30px;padding-top: 0px;padding-bottom: 0px; display:none" title="Ingrese archivo de llave" onclick="javascript:CargarKeyProveedor(event,810);">
													<i id="Icono_810" class="fa fa-key fa-lg"></i>
												</button>
											</span>
										</div>
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Ruta Remota: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(810);" placeholder="Ruta Remota" name="Proveedor810_RutaRemota" id="Proveedor810_RutaRemota"
											size="25" style="width:230px;"  required="required" title="Ingrese Ruta Remota"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Ruta Local: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(810);" placeholder="Ruta Local" name="Proveedor810_RutaLocal" id="Proveedor810_RutaLocal"
											size="25" style="width:230px;"  required="required" title="Ingrese Ruta Local" onChange="javascript:formatearOnchange(this)"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Patron: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(810);" placeholder="Patron" name="Proveedor810_Patron" id="Proveedor810_Patron"
											size="25" style="width:230px;"  required="required" title="Ingrese Patron"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
										<br />
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:540px" >
										<label>Log autentificación: </label>
										<br />
										<div class="alert alert-info" id="log810">
										</div>									
									</div>
								  </div>
								  <br />
							   <input type="submit" id="btn_Validar810" name="btn_Validar810" value="Validar810" style="display:none">
							   </form>
							   </div>
							   
							   <!-- #Tab 810-->
							   <!-- Tab 855-->
							   <div class="tab-pane fade contenedorTab" id="Pro_855">
							   <form name="Form855" id="Form855" method="post" action="" onSubmit="return false;">
							   <input type="hidden" id="H_OldFormato855" name="H_OldFormato855">
								  <div class="row">
									<div class="col-lg-4" style="width:270px" >
										<br />
										<div class="form-group" id="divCboFormato855" title="Ingrese Formato">     
										</div>
									</div>
								   </div>
								  <div class="row">
									<div class="col-lg-4" style="width:270px" >
											 <label>Servidor: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(855);" placeholder="Servidor" name="Proveedor855_Servidor" id="Proveedor855_Servidor"
											size="25" style="width:230px;"  required="required" title="Ingrese Servidor"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Puerto: </label>
											 <br />
											<input type="number" class="form-control" onChange="javascript:DetectaCambioConeccion(855);" placeholder="Puerto" name="Proveedor855_Puerto" id="Proveedor855_Puerto"
											size="25" style="width:230px;"  required="required" title="Puerto"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Usuario: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(855);" placeholder="Usuario" name="Proveedor855_Usuario" id="Proveedor855_Usuario"
											size="25" style="width:230px;"  required="required" title="Usuario"/>         
									</div>
									<div class="col-lg-4" style="width:270px" name="contrasenas">
										<label>Contraseña: </label>
										<br />
										<div class="input-group">
											<input type="password" class="form-control" onChange="javascript:DetectaCambioConeccion(855);" placeholder="Contraseña" name="Proveedor_Contrasena" id="Proveedor855_Contrasena" size="25"  required="required" title="Ingrese Contraseña"/>   
											<span class="input-group-btn">
												<button class="btn btn-info" id="btn_key_855" name="key_sftp" edi="855" type="button" style="height: 30px;padding-top: 0px;padding-bottom: 0px; display:none" onclick="javascript:CargarKeyProveedor(event,855);" title="Ingrese archivo de llave">
													<i id="Icono_855" class="fa fa-key fa-lg"></i>
												</button>
											</span>
										</div>
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Ruta Remota: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(855);" placeholder="Ruta Remota" name="Proveedor855_RutaRemota" id="Proveedor855_RutaRemota"
											size="25" style="width:230px;"  required="required" title="Ingrese Ruta Remota"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Ruta Local: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(855);" placeholder="Ruta Local" name="Proveedor855_RutaLocal" id="Proveedor855_RutaLocal"
											size="25" style="width:230px;"  required="required" title="Ingrese Ruta Local"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Patron: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(855);" placeholder="Patron" name="Proveedor855_Patron" id="Proveedor855_Patron"
											size="25" style="width:230px;"  required="required" title="Ingrese Tipo de formato"/>
									<br />										
									</div>
									<div class="col-lg-4" style="width:270px" >
										<br />
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:540px" >
										<label>Log autentificación: </label>
										<br />
										<div class="alert alert-info" id="log855">
										</div>									
									</div>
								  </div>
								 <input type="submit" id="btn_Validar855" name="btn_Validar855" value="Validar855" style="display:none">
								 </form>
							   </div>
							   <!-- #Tab 855-->
							   <!-- Tab 856-->
							   <div class="tab-pane fade contenedorTab" id="Pro_856">
							   <form name="Form856" id="Form856" method="post" action="" onSubmit="return false;">
							   <input type="hidden" id="H_OldFormato856" name="H_OldFormato856">
								  <div class="row">
									<div class="col-lg-4" style="width:270px" >
										<br />
										<div class="form-group" id="divCboFormato856" title="Ingrese Tipo de formato">     
										</div>
									</div>
								   </div>
								  <div class="row">
									<div class="col-lg-4" style="width:270px" >
										<label>Servidor: </label>
										<br />
										<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(856);" placeholder="Servidor" name="Proveedor856_Servidor" id="Proveedor856_Servidor"	size="25" style="width:230px;"  required="required" title="Servidor"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Puerto: </label>
											 <br />
											<input type="number" class="form-control" onChange="javascript:DetectaCambioConeccion(856);" placeholder="Puerto" name="Proveedor856_Puerto" id="Proveedor856_Puerto"
											size="25" style="width:230px;"  required="required" title="Ingrese Puerto"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Usuario: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(856);" placeholder="Usuario" name="Proveedor856_Usuario" id="Proveedor856_Usuario"
											size="25" style="width:230px;"  required="required" title="Ingrese Usuario"/>         
									</div>
									<div class="col-lg-4" style="width:270px" name="contrasenas">
										<label>Contraseña: </label>
										<br />
										<div class="input-group">
											<input type="password" class="form-control" onChange="javascript:DetectaCambioConeccion(856);" placeholder="Contraseña" name="Proveedor_Contrasena" id="Proveedor856_Contrasena" size="25"  required="required" title="Ingrese Contraseña"/>   
											<span class="input-group-btn">
												<button class="btn btn-info" id="btn_key_856" name="key_sftp" edi="856" type="button" style="height: 30px;padding-top: 0px;padding-bottom: 0px; display:none" onclick="javascript:CargarKeyProveedor(event,856);" title="Ingrese archivo de llave">
													<i id="Icono_856" class="fa fa-key fa-lg"></i>
												</button>
											</span>
										</div>
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Ruta Remota: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(856);" placeholder="Ruta Remota" name="Proveedor856_RutaRemota" id="Proveedor856_RutaRemota"
											size="25" style="width:230px;"  required="required" title="Ingrese Ruta Remota"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Ruta Local: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(856);" placeholder="Ruta Local" name="Proveedor856_RutaLocal" id="Proveedor856_RutaLocal"
											size="25" style="width:230px;"  required="required" title="Ingrese Ruta Local"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Patron: </label>
											 <br />
											<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(856);" placeholder="Patron" name="Proveedor856_Patron" id="Proveedor856_Patron"
											size="25" style="width:230px;"  required="required" title="Ingrese Patron"/>
											<br />													
									</div>
									<div class="col-lg-4" style="width:270px" >
										<br />
									</div>
									<br />
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:540px" >
										<label>Log autentificación: </label>
										<br />
										<div class="alert alert-info" id="log856">
										</div>									
									</div>
								  </div>
								 <input type="submit" id="btn_Validar856" name="btn_Validar856" value="Validar856" style="display:none">
								 </form>
							   </div>
							   <!-- #Tab 856-->
							   <!-- Tab 850-->
							   <div class="tab-pane fade contenedorTab" id="Pro_850">
							   <form name="Form850" id="Form850" method="post" action="" onSubmit="return false;">
							   <input type="hidden" id="H_OldFormato850" name="H_OldFormato850">
								  <div class="row">
									<br />
									<div class="col-lg-4" style="width:270px" >
										<label>Servidor: </label>
										<br />
										<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(850);" placeholder="Servidor" name="Proveedor850_Servidor" id="Proveedor850_Servidor" size="25" style="width:230px;"  required="required" title="Servidor"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
										<label>Puerto: </label>
										<br />
										<input type="number" class="form-control" onChange="javascript:DetectaCambioConeccion(850);" placeholder="Puerto" name="Proveedor850_Puerto" id="Proveedor850_Puerto" size="25" style="width:230px;"  required="required" title="Ingrese Puerto"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
										<label>Usuario: </label>
										<br />
										<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(850);" placeholder="Usuario" name="Proveedor850_Usuario" id="Proveedor850_Usuario" size="25" style="width:230px;"  required="required" title="Ingrese Usuario"/>         
									</div>
									<div class="col-lg-4" style="width:270px" name="contrasenas">
										<label>Contraseña: </label>
										<br />
										<div class="input-group">
											<input type="password" class="form-control" onChange="javascript:DetectaCambioConeccion(850);" placeholder="Contraseña" name="Proveedor_Contrasena" id="Proveedor850_Contrasena" size="25"  required="required" title="Ingrese Contraseña"/>   
											<span class="input-group-btn">
												<button class="btn btn-info" id="btn_key_850" name="key_sftp" edi="850" type="button" style="height: 30px;padding-top: 0px;padding-bottom: 0px; display:none" onclick="javascript:CargarKeyProveedor(event,850);" title="Ingrese archivo de llave">
													<i id="Icono_850" class="fa fa-key fa-lg"></i>
												</button>
											</span>
										</div>
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
										<label>Ruta Remota(Aca quedaran los EDI): </label>
										<br />
										<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(850);" placeholder="Ruta Remota" name="Proveedor850_RutaRemota" id="Proveedor850_RutaRemota" size="25" style="width:230px;"  required="required" title="Ingrese Ruta Remota"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
										<label>Ruta Local(Ruta de trabajo): </label>
										<br />
										<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(850);" placeholder="Ruta Local" name="Proveedor850_RutaLocal" id="Proveedor850_RutaLocal" size="25" style="width:230px;"  required="required" title="Ingrese Ruta Local"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
										<label>Carpeta FTP de Entrada: </label>
										<br />
										<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(850);" placeholder="Carpeta FTP de Entrada" name="Proveedor850_CarpetaFtpEntrada" id="Proveedor850_CarpetaFtpEntrada" size="25" style="width:230px;"  required="required" title="Carpeta FTP de Entrada"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
										<label>Patron: </label>
										<br />
										<input type="text" class="form-control" onChange="javascript:DetectaCambioConeccion(850);" placeholder="Patron" name="Proveedor850_Patron" id="Proveedor850_Patron" size="25" style="width:230px;"  required="required" title="Ingrese Patron"/>												
									</div>
									<br />
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:540px" >
										<label>Log autentificación: </label>
										<br />
										<div class="alert alert-info" id="log850">
										</div>									
									</div>
								  </div>
								 <input type="submit" id="btn_Validar850" name="btn_Validar850" value="Validar850" style="display:none">
								 </form>
							   </div>
							   <!-- #Tab 850-->
							   <div class="row" style="float:right; margin-top:10px; margin-right:30px">
										<div class="form-group">
										<button type="button" class="btn btn-default" data-dismiss="modal" id="btn_CancelarProveedor">Cancelar</button>	
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<button type="button" class="btn btn-info" onclick="javascript:VerificarConectividad(event)" id="btn_VerificarConeccionProveedor" name="btn_VerificarConeccionProveedor" >
											<i id="IconBtnVerificar" class="fa fa-cog" aria-hidden="true"></i> Verificar
										</button> 
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<button type="button" class="btn btn-primary" id="btn_grabarProveedor" name="btn_grabarProveedor" >Grabar</button>
								   </div>	
								</div>
							</div>
						   </div>
						   <!-- #Tab Menu-->
							<div style="height:4px"></div>
						   </div>
                                </div>
                                <!-- /.panel-heading -->
                                <!-- /.panel-body -->
                            </div>
                            <!-- /.panel -->
                  		</div>
                	</div>
            </div>
			
<!-- #Modal -->
<!-- HIDDEN -->
<div style="display:none">
<form name="FormSubirLLave" id="FormSubirLLave" action="../uploader/proveedores.php" class="formularios" method="post" enctype="multipart/form-data" target="contenedor_subirKeyProveedor">
  <input type="file" name="Proveedor_key" id="Proveedor_key" edi="" onChange="javascript:SubirKey()"/>
  <input type="hidden" id="Subir_Key_EDI" name="Subir_Key_EDI"/>
</form>
<iframe width="1" height="1" frameborder="0" id="contenedor_subirKeyProveedor" name="contenedor_subirKeyProveedor" style="display: none"></iframe>
</div>
<input type="hidden" id="H_OldIdProveedor" name="H_OldIdProveedor" value="0" />
<input type="hidden" id="H_TabActivo" name="H_TabActivo" value="810" />
<input type="hidden" id="H_Validacion_810" name="H_Validacion_810" value="0" />
<input type="hidden" id="H_Validacion_855" name="H_Validacion_855" value="0" />
<input type="hidden" id="H_Validacion_856" name="H_Validacion_856" value="0" />
<input type="hidden" id="H_Validacion_850" name="H_Validacion_850" value="0" />
<input type="hidden" id="H_Llave_810" name="H_Llave_810" value="0" />
<input type="hidden" id="H_Llave_855" name="H_Llave_855" value="0" />
<input type="hidden" id="H_Llave_856" name="H_Llave_856" value="0" />
<input type="hidden" id="H_Llave_850" name="H_Llave_850" value="0" />
<input type="hidden" id="H_Cambio_Clave" name="H_Cambio_Clave" value="0" />
<button type="button" class="btn btn-primary" id="btn_AbrirModalProveedor" name="btn_AbrirModalProveedor" data-toggle="modal" data-target="#modalAgregarProveedor" style="display:none">
<!-- #HIDDEN -->
<!--////// TOOLTIP ///////////////////////////////////////////////-->
    <script src="../js/jquery.metisMenu.js"></script>

</body>

</html>
