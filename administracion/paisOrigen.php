<!DOCTYPE html>
<html>
<?
	session_start();
	$email = $_SESSION["email"];
	if (!isset($email))
	 header('Location: ../index.php');
	require '../clases/class.templates.php';
	if (!isset($_SESSION["email"])){
    header('Location:../index.php');
	}
	
	//Verificar Permisos
	include_once("../ajax/ValPemi.php");
	$AccesoPermiso=VerificarPermiso(16);
	
	if($AccesoPermiso==false)
	{
		header('Location: ../internos.php');
	}
	
?>
<head>
    <title>PAIS ORIGEN</title>
    <?php include("includes.php");?> 
	<script src="../EDI/js/funciones.js" type="text/javascript"  charset="UTF-8"></script>
    <script src="js/paisOrigen.js"></script>
	<script  src="../plugins/JqueryUI/jquery-ui.js" type="text/javascript" language="javascript" ></script>
	
	<script>
		/**
		window.onbeforeunload = function(e) {
		  return "Esta a punto de abandonar EDI855. Para regresar a las Ordenes de Compra presion 'Volver'";
		};*/
	
	</script>
	<style>
	ul, menu, dir {
		list-style-type: none
	}
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
	.ui-autocomplete {
		z-index: 9999 !important;
	}
	#tabla_pais_origen th { text-align: center }
	#tabla_pais_origen td { text-align: center }
	</style>
</head>
<body id="bodyPaisOrigen">
<a href="../EDI/iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>
<div id="pagina_principal" style="display:block;"><!--DIV PAGINA PRINCIPAL-->
	<div id="page_header_principal">
		<div id="wrapper_principal">
        <?
			$template = new templates("templates/barra_nav.html");
			$template->setParams( array (
			'reporte' => "PAIS ORIGEN"
			));
			$template->show();
		?>
        <!--<div id="page-wrapper">-->
			<br>
			<div class="row" style="margin-left:10px; margin-right:10px; margin-top:5px;">		
                <div class="col-lg-4">
                   	<div class="form-group" id="div_proveedores" title="Ingrese proveedor">
						<label class="control-label">Proveedor: </label>
						<img src="../images/loadingAnimation.gif">
					</div>
                </div>
                <div class="col-lg-3" style="margin-top: 15px;">
					<div class="form-group">
						<button type="button" class="btn btn-info" id="btn_limpiar" name="btn_limpiar">
							<span class="glyphicon glyphicon-repeat"></span> &nbsp;Limpiar
						</button>
					</div>
                </div>
			</div>
  			<!-- TABLA -->
            <div class="row" style="margin-left:10px; margin-right:10px;">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading" >
                            <div align="left">
								<button type="button" class="btn btn-primary" id="btn_agregarPaisOrigen" name="btn_agregarPaisOrigen">
									<span class="glyphicon glyphicon-plus"></span> &nbsp;Agregar
								</button>
								<button type="button" class="btn btn-danger" id="btn_EliminarPaisOrigen" name="btn_EliminarPaisOrigen">
									<span class="glyphicon glyphicon-trash"></span> &nbsp;Eliminar
								</button>
							</div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" >
                                <table class="display" cellspacing="0" width="100%" id="tabla_pais_origen" name="tabla_pais_origen">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Pais Origen</th>
											<th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyPaisOrigen">
                                    </tbody>
                                    <tfoot id="tdFootPaisOrigen">
										<tr>
                                            <th>Codigo</th>
                                            <th>Pais Origen</th>
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
            <div class="modal fade" id="modalAgregarPaisOrigen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            	<div class="modal-dialog">
                	<div class="modal-content">
                  		<div class="modal-header">
                    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    		<h4 class="modal-title" id="modalPaisOrigenLabel">Agregar Pais Origen</h4>
                  		</div>
                  		<div class="modal-body">
                    		<div class="panel panel-default">
                                <div class="panel-heading">
									<div id="divCabecera">
										<form name="FormIngresarPaisOrigen" id="FormIngresarPaisOrigen" method="post" action="" onSubmit="return false;">
											<div class="row">
												<div class="col-lg-4" style="width:270px">
													<div class="form-group" >
														<label>Codigo: </label>
														<br />
														<input type="text" class="form-control" placeholder="Codigo" name="txt_codigo" id="txt_codigo" style="width:230px;" required title="Ingrese Codigo" />    
													</div>
												</div>	
												<div class="col-lg-4" style="width:270px" >
													<div class="form-group" id="div_PaisOrigen">
														<label>Pais Origen: </label>
														<br />
														<input type="text" class="form-control" placeholder="Pais Origen" name="txt_paisOrigen" id="txt_paisOrigen" value="" size="25" style="width:230px;"  required="required" title="Ingrese Tolerancia"/>         
													</div>
												</div>												
											</div>
										   <input type="submit" id="btn_ValidarFormIngresarSociedad" name="btn_ValidarFormIngresarSociedad" value="validarCabecera" style="display:none">
										</form>
								    </div>
								    <!-- Tab Menu-->
									<div class="row" style="margin-left:0px; width:547px">
										<div id="SocieadadTab" class="tab-content">							  
										   <div class="row" style="float:right; margin-top:10px; margin-right:30px">
												<div class="form-group">
													<button type="button" class="btn btn-default" data-dismiss="modal" id="btn_CancelarPaisOrigen">Cancelar</button>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
													<button type="button" class="btn btn-primary" id="btn_grabarModalPaisOrigen" name="btn_grabarModalPaisOrigen" >Grabar</button>
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
<input type="hidden" id="H_OldCodigo" name="H_OldCodigo"/>
<input type="hidden" id="H_IdProveedor" name="H_IdProveedor"/>
<input type="hidden" id="H_PaisOrigen" name="H_PaisOrigen"/>
<button type="button" class="btn btn-primary" id="btn_AbrirModalPaisOrigen" name="btn_AbrirModalPaisOrigen" data-toggle="modal" data-target="#modalAgregarPaisOrigen" style="display:none">
<div style="display:none">
<div id="DivCboPaisWS"></div>
</div>
<!-- #HIDDEN -->
<!--////// TOOLTIP ///////////////////////////////////////////////-->
<script src="../js/jquery.metisMenu.js"></script>
</body>
</html>