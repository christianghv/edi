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
	$AccesoPermiso=VerificarPermiso(13);
	
	if($AccesoPermiso==false)
	{
		header('Location: ../internos.php');
	}
	
?>
<head>
    <title>SOCIEDAD</title>
    <?php include("includes.php");?> 
	<script src="../EDI/js/funciones.js" type="text/javascript"  charset="UTF-8"></script>
    <script src="js/sociedad.js"></script>
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
	input[type=number]::-webkit-outer-spin-button,input[type=number]::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	
	#tabla_sociedades th { text-align: center }
	#tabla_sociedades td { text-align: center }
	#tabla_sociedades thead {display: none}
	#tbl_IdReceiverAsociados thead {display: none}
	#tbl_IdReceiverAsociados td { text-align: center }
	#tbl_ParametrosDistribucion thead {display: none}
	#tbl_ParametrosDistribucion td { text-align: center }
	
	
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
			'reporte' => "SOCIEDADES"
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
								<button type="button" class="btn btn-primary" id="btn_AgregarSociedad" name="btn_grabarSociedad">
									<span class="glyphicon glyphicon-plus"></span> &nbsp;Agregar
								</button>
								<button type="button" class="btn btn-danger" id="btn_EliminarSociedad" name="btn_EliminarSociedad">
									<span class="glyphicon glyphicon-trash"></span> &nbsp;Eliminar
								</button>
							</div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" >
                                <table class="display" cellspacing="0" width="100%" id="tabla_sociedades" name="tabla_sociedades">
                                    <thead>
                                        <tr>
                                            <th id="thSociedad">ID Sociedad</th>
                                            <th>Sociedad</th>
                                            <th>Tolerancia</th>
											<th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySociedad">
                                    </tbody>
                                    <tfoot id="tdFootSociedad">
										<tr>
                                            <th>ID Sociedad</th>
                                            <th>Sociedad</th>
                                            <th>Tolerancia</th>
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
            <div class="modal fade" id="modalAgregarSociedad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            	<div class="modal-dialog">
                	<div class="modal-content">
                  		<div class="modal-header">
                    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    		<h4 class="modal-title" id="modalAgregarSociedadLabel">Agregar Sociedad</h4>
                  		</div>
                  		<div class="modal-body">
                    		<div class="panel panel-default">
                                <div class="panel-heading">
									<div id="divCabecera">
										<form name="FormIngresarSociedad" id="FormIngresarSociedad" method="post" action="" onSubmit="return false;">
											<div class="row">
												<div class="col-lg-4" style="width:270px" >
													<div class="form-group">
														<label>ID Sociedad: </label>
														 <input type="hidden" name="Old_IdEmbar" id="Old_IdEmbar"/>
														 <br />
														<input type="text" class="form-control" placeholder="ID Sociedad" name="Sociedad_id_sociedad" id="Sociedad_id_sociedad" size="25" style="width:230px;" required title="Ingrese ID Sociedad" />         
													</div>
												</div>
												<div class="col-lg-4" style="width:270px">
													<div class="form-group" >
														<label>Nombre Sociedad: </label>
														<br />
														<input type="text" class="form-control" placeholder="Nombre Sociedad" name="Sociedad_nombre" id="Sociedad_nombre" style="width:230px;" required title="Ingrese Nombre Sociedad" />    
													</div>
												</div>								
											</div>
											<div class="row">
												<div class="col-lg-4" style="width:270px" >
													<div class="form-group">
														<label>Tolerancia(USD): </label>
														<br />
														<input type="number" class="form-control" placeholder="Tolerancia" name="Sociedad_tolerancia" id="Sociedad_tolerancia" value="" size="25" style="width:230px;"  required="required" title="Ingrese Tolerancia"/>        
													</div>
												</div>
												
											</div>
										   <input type="submit" id="btn_ValidarCabecera" name="btn_ValidarCabecera" value="validarCabecera" style="display:none">
										</form>
									</div>
									<!-- Tab Menu-->
									<div class="row" style="margin-left:0px">
										<div class="col-md-4" style="padding-left: 0px;padding-right: 0px;max-width: 170px;">
											<button type="button" class="btn btn-primary" style="display:none" id="btn_CodigoAsociados">ID Receiver Asociados</button>
										</div>
										<div class="col-md-4" style="padding-left: 0px;padding-right: 0px;max-width: 170px;">
											<button type="button" class="btn btn-primary" style="display:none" id="btn_ParametrosDistribucion">Parametros Distribución</button>
										</div>
										<div class="col-md-4" id="SocieadadTab" style="float:right;padding-left: 0px;">
											<div id="SocieadadTab" class="tab-content">
												<button type="button" class="btn btn-default" data-dismiss="modal" id="btn_CancelarSociedad">Cancelar</button>
												&nbsp;
												<button type="button" class="btn btn-primary" id="btn_grabarModalSociedad" name="btn_grabarModalSociedad" >Grabar</button>
											</div>								
										</div>
									</div>
									<div class="row" style="margin-right: 0px;margin-left: 0px;">
										<div id="divCodigoAsociados" style="display:none">
											<input type="hidden" id="estadoCodigoAsociados" value="0"/>
											<br />
											<br />
											<div class="row" style="margin-right: 0px;margin-left: 0px;">
												<form name="FormIngresarIdReceiver" id="FormIngresarIdReceiver" method="post" action="" onSubmit="return false;" class="navbar-form navbar-left" role="search">
												<input type="submit" id="btn_ValidarFormIngresarIdReceiver" name="btn_ValidarFormIngresarIdReceiver" value="Validar810" style="display:none">
													<div class="col-lg-2" style="padding-right: 0px;padding-left: 0px;min-width: 80px;">
														<label style="padding-top: 10px;">ID Receiver: </label>
													</div>
													<div class="col-lg-6">
														<img src="../images/loadingAnimation.gif" style="height:15px;width: 260px;margin-top: 10px; display:none" id="ImagenCargandoIdReceiver" name="ImagenCargandoIdReceiver"/>
														<div id="DivMenuIdReceiver" class="input-group">
															  <input type="text" class="form-control" id="txt_AddIdReceiver" name="txt_AddIdReceiver" placeholder="ID Receiver" required="required" style="height: 34px;">
															  <span class="input-group-btn">
																<button class="btn btn-primary" id="btn_AgregarIdReceiver" name="btn_AgregarIdReceiver" type="button">Agregar</button>
															  </span>
														</div>
													</div>
												</form>
											</div>
											<br />
											<div class="row" style="margin-right: 0px;margin-left: 0px;">
												<div class="table-responsive" style="border: 1px solid #DDDDDD;background-color:#FFF">						  
													<table id="tbl_IdReceiverAsociados" class="display" cellspacing="0" width="100%">
														<thead>
															<tr>
																<th>ID Receiver</th>
																<th>&nbsp&nbsp&nbsp</th>
															</tr>
														</thead>
														<tbody id="tBodyIdReceiverAsociados">
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div id="divParametrosDistribucion" style="display:none">
											<input type="hidden" id="estadoParametrosDistribucion" value="0"/>
											<br />
											<br />
											<div class="row" style="margin-right: 0px;margin-left: 0px;">
												<div class="row" style="display:none;text-align: center" id="DivImagenCargandoIngresarParametro" name="DivImagenCargandoIngresarParametro">
													<div class="col-md-12">
														<img src="../images/loadingAnimation.gif" style="height:15px;width: 260px"/>
													</div>
												</div>
												<form name="FormIngresarParametroDistribucion" id="FormIngresarParametroDistribucion" method="post" action="" onSubmit="return false;" class="navbar-form navbar-left" role="search">
												<input type="submit" id="btn_ValidarFormIngresarParametro" name="btn_ValidarFormIngresarParametro" value="ValidarIngresarParametro" style="display:none">
													<div class="row" id="DivMenuIngresarParametro">
														<div class="col-md-2" style="padding-left: 5px;padding-right: 5px;">
															<label for="txtUrgencia">Urgencia:</label>
															<input type="number" onkeydown="javascript: return event.keyCode == 69 ? false : true" min="1" class="form-control" id="txtUrgencia" name="txtUrgencia" title="" required="required" style="max-width: 80px">
														</div>
														<div class="col-md-2" style="padding-left: 5px;padding-right: 5px;">
															<label for="txtPeso">Peso(KG):</label>
															<input type="number" onkeydown="javascript: return event.keyCode == 69 ? false : true" min="1" class="form-control" id="txtPeso" name="txtPeso" title="" required="required" style="max-width: 80px"/>
														</div>
														<div class="col-md-2" style="padding-left: 5px;padding-right: 5px;">
															<label for="txtLargo">Largo(CM):</label>
															<input type="number" onkeydown="javascript: return event.keyCode == 69 ? false : true" min="1" class="form-control" id="txtLargo" name="txtLargo" title="" required="required" style="max-width: 80px"/>
														</div>
														<div class="col-md-2" style="padding-left: 5px;padding-right: 5px;">
															<label for="txtAncho">Ancho(CM):</label>
															<input type="number" onkeydown="javascript: return event.keyCode == 69 ? false : true" min="1" class="form-control" id="txtAncho" name="txtAncho" title="" required="required" style="max-width: 80px"/>
														</div>
														<div class="col-md-2" style="padding-left: 5px;padding-right: 5px;">
															<label for="txtAlto">Alto(CM):</label>
															<input type="number" onkeydown="javascript: return event.keyCode == 69 ? false : true" min="1" class="form-control" id="txtAlto" name="txtAlto" title="" required="required" style="max-width: 80px"/>
														</div>
														<div class="col-md-2" style="padding-top: 10px;padding-bottom: 0px;">
															<button type="button" class="btn btn-primary" id="btn_AgregarParametroDistribucion" name="btn_AgregarParametroDistribucion">Agregar</button>
														</div>
													</div>
												</form>
											</div>
											<br />
											<div class="row" style="margin-right: 0px;margin-left: 0px;">
												<div class="table-responsive" style="border: 1px solid #DDDDDD;background-color:#FFF">						  
													<table id="tbl_ParametrosDistribucion" class="display" cellspacing="0" width="100%">
														<thead>
															<tr>
																<th>Urgencia</th>
																<th>Peso(KG)</th>
																<th>Largo(CM)</th>
																<th>Ancho(CM)</th>
																<th>Alto(CM)</th>
																<th>&nbsp&nbsp&nbsp</th>
															</tr>
														</thead>
														<tbody id="tBodyParametroDistribucion">
														</tbody>
													</table>
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
<input type="hidden" id="H_OldIdSociedad" name="H_OldIdSociedad"/>
<input type="hidden" id="H_OldIdReceiver" name="H_OldIdReceiver"/>
<input type="hidden" id="H_OldUrgencia" name="H_OldUrgencia"/>
<button type="button" class="btn btn-primary" id="btn_AbrirModalSociedad" name="btn_AbrirModalSociedad" data-toggle="modal" data-target="#modalAgregarSociedad" style="display:none">
<!-- #HIDDEN -->
<!--////// TOOLTIP ///////////////////////////////////////////////-->
    <script src="../js/jquery.metisMenu.js"></script>

</body>

</html>
