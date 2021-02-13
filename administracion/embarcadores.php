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
    <title>EMBARCADORES</title>
    <?php include("includes.php");?> 
	<script src="../EDI/js/funciones.js" type="text/javascript"  charset="UTF-8"></script>
    <script src="js/embarcadores.js"></script>
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
	#tabla_embarcadores th { text-align: center }
	#tabla_embarcadores td { text-align: center }
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
			'reporte' => "EMBARCADORES"
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
                                <table class="display" cellspacing="0" width="100%" id="tabla_embarcadores" name="tabla_embarcadores">
                                    <thead>
                                        <tr>
                                            <th id="thProvedor">ID Proveedor</th>
                                            <th>Codigo SAP</th>
                                            <th>Nombre</th>
											<th>Tipo Entrada</th>
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
                    		<h4 class="modal-title" id="modalAgregarProveedorLabel">Agregar Embarcador</h4>
                  		</div>
                  		<div class="modal-body">
                    		<div class="panel panel-default">
                                <div class="panel-heading">
								<div id="divCabecera">
								<form name="FormIngresarEmbarcadorCabecera" id="FormIngresarEmbarcadorCabecera" method="post" action="" onSubmit="return false;">
							<div class="row">
								<div class="col-lg-4" style="width:270px" >
								<div class="form-group">
								 <label>ID Embarcador: </label>
								 <input type="hidden" name="Old_IdEmbar" id="Old_IdEmbar"/>
								 <br />
								<input type="text" class="form-control" placeholder="ID Embarcador" name="Embarcador_id" id="Embarcador_id"
								size="25" style="width:230px;" required title="Ingrese ID Embarcador" />         
								</div>
								</div>
								
								<div class="col-lg-4" style="width:270px">
								<div class="form-group" >
								 <label>Codigo SAP: </label>
								 <br />
								<input type="number" class="form-control" placeholder="Codigo SAP" name="Embarcador_CodigoSap" id="Embarcador_CodigoSap" style="width:230px;" required title="Ingrese Codigo SAP" />    
								</div>
								</div>								
						   </div>
						   <div class="row">
								<div class="col-lg-4" style="width:270px" >
									<div class="form-group">
										 <label>Nombre: </label>
										 <br />
										<input type="text" class="form-control" placeholder="Nombre" name="Embarcador_Nombre" id="Embarcador_Nombre"
										value="" size="25" style="width:230px;"  required="required" title="Ingrese Nombre Embarcador"/>         
									</div>
								</div>
								<div class="col-lg-4" style="width:270px">
									<div class="form-group" >
									 <label>Contraseña: </label>
									 <br />
									<input class="form-control" type="password" placeholder="Contraseña" name="Embarcador_Contrasena" id="Embarcador_Contrasena" style="width:230px;" required title="Ingrese contraseña de Embarcador" />    
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
								  <a id="tab_810" data-toggle="tab"><label>FTP</label></a>
							   </li>
							</ul>
							
							<div id="EmbarcadoresTab" class="tab-content">
							   <!-- Tab_FTP-->							   
							   <div class="tab-pane fade in active contenedorTab" id="Tab_FTP">
							   <form name="EmbarcadoresDetalle" id="EmbarcadoresDetalle" method="post" action="" onSubmit="return false;">
								  <div class="row">
									<div class="col-lg-4" style="width:270px" >
											<label>Servidor: </label>
											 <br />
											<input type="text" class="form-control" placeholder="Servidor" name="Embarcadores_Servidor" id="Embarcadores_Servidor"
											size="25" style="width:230px;"  required="required" title="Ingrese Servidor"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Puerto: </label>
											 <br />
											<input type="number" class="form-control" placeholder="Puerto" name="Embarcadores_Puerto" id="Embarcadores_Puerto"
											size="25" style="width:230px;"  required="required" title="Ingrese Puerto"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Usuario: </label>
											 <br />
											<input type="text" class="form-control" placeholder="Usuario" name="Embarcadores_Usuario" id="Embarcadores_Usuario"
											size="25" style="width:230px;"  required="required" title="Ingrese Usuario"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Contraseña: </label>
											 <br />
											<input type="text" class="form-control" placeholder="Contraseña" name="Embarcadores_Contrasena" id="Embarcadores_Contrasena"
											size="25" style="width:230px;"  required="required" title="Ingrese Contraseña"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Ruta Remota: </label>
											 <br />
											<input type="text" class="form-control" placeholder="Ruta Remota" name="Embarcadores_RutaRemota" id="Embarcadores_RutaRemota"
											size="25" style="width:230px;"  required="required" title="Ingrese Ruta Remota"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Ruta Local: </label>
											 <br />
											<input type="text" class="form-control" placeholder="Ruta Local" name="Embarcadores_RutaLocal" id="Embarcadores_RutaLocal"
											size="25" style="width:230px;"  required="required" title="Ingrese Ruta Local" onChange="javascript:formatearOnchange(this)"/>         
									</div>
								  </div>
								  <div class="row">
								  <br />
									<div class="col-lg-4" style="width:270px" >
											 <label>Ruta Salida: </label>
											 <br />
											<input type="text" class="form-control" placeholder="Ruta Salida" name="Embarcadores_RutaSalida" id="Embarcadores_RutaSalida"
											size="25" style="width:230px;"  required="required" title="Ingrese Ruta Salida"/>         
									</div>
									<div class="col-lg-4" style="width:270px" >
											 <label>Patron: </label>
											 <br />
											<input type="text" class="form-control" placeholder="Patron" name="Embarcadores_Patron" id="Embarcadores_Patron"
											size="25" style="width:230px;"  required="required" title="Ingrese Patron"/>         
									</div>
								  </div>
								  <br />
							   <input type="submit" id="btn_ValidarDetalle" name="btn_ValidarDetalle" value="Validar810" style="display:none">
							   </form>
							   </div>
							   
							   <!-- #Tab_FTP-->
							  
							   <div class="row" style="float:right; margin-top:10px; margin-right:30px">
									<div class="form-group">
										<button type="button" class="btn btn-default" data-dismiss="modal" id="btn_CancelarEmbarcador">Cancelar</button>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<button type="button" class="btn btn-primary" id="btn_grabarEmbarcador" name="btn_grabarEmbarcador" >Grabar</button>
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
<input type="hidden" id="H_OldIdEmbarcador" name="H_OldIdEmbarcador">
<button type="button" class="btn btn-primary" id="btn_AbrirModalEmbarcador" name="btn_AbrirModalEmbarcador" data-toggle="modal" data-target="#modalAgregarProveedor" style="display:none">
<!-- #HIDDEN -->
<!--////// TOOLTIP ///////////////////////////////////////////////-->
    <script src="../js/jquery.metisMenu.js"></script>

</body>

</html>
