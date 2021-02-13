<?
	session_start();
	
	$email = $_SESSION["email"];
$perfil = $_SESSION["perfil"];
	//require '../clases/class.templates.php';
	if (!isset($email))
	 header('Location: ../index.php');
	 require '../clases/class.templates.php';
	 if (!isset($_SESSION["email"])){
		header('Location:../index.php');

		//Verificar Permisos	
		include_once("../ajax/ValPemi.php");
		$AccesoPermiso=VerificarPermiso(6);
		
		if($AccesoPermiso==false)
		{
			header('Location: ../internos.php');
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
    <title>EDI 810</title>
    <?php include("includes.php"); ?>     
    <script src="js/edi810.js"></script>    
    <script>
		window.onbeforeunload = function(e) {
		  return "Esta a punto de abandonar EDI810. Para regresar presione botón 'Volver'";
		};
	</script>
</head>
	<style>
	#tabla_EE th { text-align: center }
	#tabla_EE td { text-align: center }
	#tabla_facturas thead {visibility: hidden;}
	#tabla_AEE thead {display: none;}
	#tabla_EE thead {display: none;}
	#tabla_bulto_modal thead {display: none;}
	#tabla_bulto_modal td {text-align: center;}
	</style>
<body id="bodyEdi810">
	
	<a href="iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>
    <div id="div_detalle" style="display:none"></div>
	<div id="wrapper">
<?
	$template = new templates("templates/barra_nav.html");
	$template->setParams( array (
	'reporte' => "EDI 810"
	));
	$template->show();
?>

		<div class="row" style="margin-left:10px; margin-right:10px; margin-top:5px;">
			<form name="frm_filtros" id="frm_filtros" method="post" action="" onSubmit="return false;">
				<input type="submit" id="submit_filtros" style="display:none"/>
<?
				#FILTROS 
				
				$tmp_soc = new templates("templates/f_sociedad.html");
				$tmp_soc->show();
				?>
				<div class="col-lg-3">
                   <div class="form-group">
                        <label><input type="radio" id="rdb_BuscarFactura" name="optradio" checked="checked">Nro. Factura: </label>
						<label><input type="radio" id="rdb_BuscarMawbBL" name="optradio">Documento Embarque: </label>
						<span style="visibility:hidden" class="glyphicon glyphicon-plus-sign" id="ingreso_factura" name="ingreso_factura" data-toggle="modal" data-target="#myModalIngresarFacturas"></span>
                        <input class="form-control" placeholder="Ingrese factura.." id="nro_factura" name="linea_1" data-hasqtip="2" oldtitle="Ingrese numero de factura" title="" aria-describedby="qtip-2">
                    </div>
                </div>
				<?
				$tmp_ee = new templates("templates/f_ee.html");
				$tmp_ee->show();
				$tmp_fecha = new templates("templates/f_fecha.html");
				
				$fini = strtotime ( '-1 month' , strtotime ( date('Y-m-d') ) ) ;
				$fini = date ( 'd/m/Y' , $fini );

				$tmp_fecha->setParams( array (
								'label'=>'Fecha Inicio',
								'id' => 'fecha1_cabecera',
								'title' => 'Seleccione fecha de inicio',
								'valor' => $fini
								));
				$tmp_fecha->show();
				
				$tmp_fecha->setParams( array (
								'label'=>'Fecha Término',
								'id' => 'fecha2_cabecera',
								'title' => 'Seleccione fecha de termino',
								'valor' => date("d/m/Y")
								));
				$tmp_fecha->show();
?>			
				</form>
			</div>
			
            <div class="row" style="margin-left:10px; margin-right:10px;margin-bottom:10px">                
                <div class="col-lg-1">
<?
				$btn_primary	= new templates("templates/boton.html");
				$btn_primary->setParams( array (
								'text'=>'Buscar',
								'id' => 'btn_buscar',
								'icon' => 'glyphicon glyphicon-search',
								'type' => 'primary',
								'btn_type'=>'button'
								
								));
				$btn_primary->show();
?>
				</div>
            </div>
            
  			<!-- TABLA -->
            <div class="row" style="margin-left:10px; margin-right:10px">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                                        
<?
				$btn_primary->setParams( array (
								'text'=>'Seleccionar',
								'id' => 'btn_selec',
								'icon' => 'glyphicon glyphicon-check',
								'type' => 'info',
								'btn_type'=>'button'
								));
				$btn_primary->show();

				$btn_primary->setParams( array (
								'text'=>'Deseleccionar',
								'id' => 'btn_deselec',
								'icon' => 'glyphicon glyphicon-unchecked',
								'type' => 'info',
								'btn_type'=>'button'
								));
				$btn_primary->show();	
				$btn_primary->setParams( array (
								'text'=>'Ingresar Factura',
								'id' => 'btn_ingreso_factura',
								'icon' => 'glyphicon glyphicon-plus',
								'type' => 'success',
								'btn_type'=>'button'
								));
				$btn_primary->show();
				
				$btn_primary->setParams( array (
								'text'=>'Generar Entrega Entrante',
								'id' => 'btn_tabla_generar_entrega',
								'icon' => 'glyphicon glyphicon-list-alt',
								'type' => 'success',
								'btn_type'=>'button'
								));
				$btn_primary->show();
				
				$btn_primary->setParams( array (
								'text'=>'Actualizar EE',
								'id' => 'btn_tabla_ActualizarEE',
								'icon' => 'glyphicon glyphicon-list-alt',
								'type' => 'success',
								'btn_type'=>'button'
								));
				$btn_primary->show();
?>				
						<br />
						<br />
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" >
								<div class="table-responsive" id="div_tabla_cabecera">
								</div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            </div><!-- FIN TABLA -->
			
			<form name="frm_cabecera" method="post" action="editar_edi810.php" onSubmit="return false;">
				<input type="hidden" name="invoice" id="invoice">
			</form>
			
            <!-- Modal -->
            <?
				$ModalIngresarFacturas	= new templates("templates/ModalIngresarFacturas.html");
				$ModalIngresarFacturas->setParams( array ());
				$ModalIngresarFacturas->show();
			?>
		<!-- Modal modalGEE  -->
			<div class="modal fade" id="modalGEE" tabindex="-1" role="dialog" aria-labelledby="modalGEE"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
            	<div class="modal-dialog modal-xl">
                	<div class="modal-content" >
                  		<div class="modal-header">
                    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    		<h4 class="modal-title" id="myModalLabel">Generar Entrega Entrante</h4>
                  		</div>
                  		<div class="modal-body">
                    		<div class="panel panel-default">
                                <div class="panel-heading">
								<div class="row">
									<div style="padding-top: 1em;" class="container">
										<div class="alert alert-success" id="alertSuccess" style="display:none;">
											<table id="tbl_GEE" class="table table-bordered">
												<thead>
													<tr>
													  <th>Nª AWB/BL</th>
													  <th>FACTURA</th>
													  <th>PEDIDO</th>
													  <th>CENTRO</th>
													  <th>EE</th>
													  <th>FECHA E/E</th>
													</tr>
												</thead>
												<tbody id="tBodySucessEE" style="margin-top: 2px">
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="row">
									<div style="padding-top: 1em;" class="container">
										<div class="alert alert-success" id="alertSuccessMensaje" style="display:none;"></div>
										<div class="alert alert-info" id="alertInfo" style="display:none;"></div>
										<div class="alert alert-warning" id="alertWarning" style="display:none;"></div>
										<div class="alert alert-danger" id="alertDanger" style="display:none;"></div>
									</div>
								</div>
							<div class="row">
								<form name="frm_gee" id="frm_gee" method="post" action="#" onSubmit="return false;">
									<input type="submit" name="btnValidarGee" id="btnValidarGee" style="display:none"/>
									<div class="col-md-3">
										<div class="form-group" >
											<label>Cant. Bulto: </label>
											<br />
											<input type="number" class="form-control" placeholder="Cant. Bulto" name="Gee_txtCantBulto" id="Gee_txtCantBulto" size="25" min="1" title="Ingrese la cantidad de Bulto" required />        
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group" >
											<label>Carta Porte: </label>
											<br />
											<input class="form-control" placeholder="Carta Porte" name="Gee_txtCartaPorte" id="Gee_txtCartaPorte" title="Ingrese Carta Porte" required />    
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group"  >
											<label>ETA: </label>
											<br />
											<input class="form-control" placeholder="ETA" name="Gee_txtEta" id="Gee_txtEta" size="25" title="Ingrese ETA" required />         
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group"  >
											<label>Ruta: </label><span id="SeleccionRuta"></span>
											<br />
											<input type="text" class="form-control" placeholder="Ruta" name="Gee_txtRuta" id="Gee_txtRuta" size="25" required title="Ingrese Ruta" />         
										</div>
								   </div>
							   </form>
						   </div>
							<div class="row">
								<div class="col-md-6 form-group form-inline">
									<div class="col-md-2" style="padding-right: 0px;padding-left: 0px;padding-top: 8px;padding-bottom: 8px;">
										<label for="exampleInputEmail1" style="">Buscar Material</label>
									</div>
									<div class="col-md-6">
										<form name="frm_GeeBuscarMaterial" id="frm_GeeBuscarMaterial" method="post" action="" onSubmit="return false;">
										<input type="submit" id="submit_GeeBuscarMaterial" style="display:none"/>
                                        <div class="input-group">		
											<input class="form-control" placeholder="Buscar..." id="txtBuscarMaterialGee" maxlength="20"/>
											<span class="input-group-btn">
												<button type="button" id="btnBuscarMaterial" class="btn btn-primary" style="padding-top: 4px;padding-bottom: 4px;">
													<span id="SpanBuscarMaterial" class="glyphicon glyphicon-search" aria-hidden="true"></span>
												</button>
											</span>
										</div>
										</form>
									</div>
								</div>
								<div class="col-md-3" style="float: right;text-align:right">
									<div class="form-group"  >
									<button id="btn_EESelect" class="btn btn-info" name="btn_selec" type="button">
										<span class="glyphicon glyphicon-check"></span>
									</button>
									&nbsp;&nbsp;
									<button id="btn_EEDeselc" class="btn btn-info" name="btn_selec" type="button">
										<span class="glyphicon glyphicon-unchecked"></span>
									</button>       
									</div>
							   </div>						
							</div>
							<div class="row">
								<form name="frm_detalle_gee" id="frm_detalle_gee" method="post" action="#" onSubmit="return false;">
								<input type="submit" name="btn_validar_detalle_gee" id="btn_validar_detalle_gee" style="display:none"/>
								<div class="container">
									<div id="DivGee" style="visibility:hidden;border: 1px solid #DDDDDD;background-color:#FFF">						  
										<table id="tabla_EE" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th id="thInvoicePositionEE">Invoice Number</th>
													<th>PO Number</th>
													<th>Centro</th>
													<th>Incoterms</th>
													<th>Urg</th>
													<th>Bulto</th>
													<th>Peso(KG)</th>
													<th>Largo(CM)</th>
													<th>Ancho(CM)</th>
													<th>Alto(CM)</th>
													<th id="ThIdContenedor" style="display:none">ID Contenedor</th>
													<th>Agrupación</th>
													<th>
													</th>
												</tr>
											</thead>
											<tbody id="tdBodyEE">
											</tbody>
										</table>
									</div>
								</div>
								</form>
							</div>
							<div class="row">
								<div class="col-md-3" style="float:right;margin-top:40px;padding-right: 0px;padding-left: 0px;width: 245px;">
									<div class="row">
										<img src="../images/loadingAnimation.gif" style="height:15px; display:inline" id="ImagenCargando" name="ImagenCargando"/>
									</div>
									<div class="row">
										<?
											$btn_primary->setParams( array (
															'text'=>'Cancelar',
															'id' => 'btn_CancelarEE',
															'icon' => 'glyphicon glyphicon-floppy-remove',
															'type' => 'default',
															'estilo'=> '',
															'more'=>'data-dismiss="modal"',
															'btn_type'=>'button'
															));
											$btn_primary->show();
											
											$btn_primary->setParams( array (
															'text'=>'Generar EE',
															'id' => 'btn_generararEE',
															'icon' => 'glyphicon glyphicon-floppy-saved',
															'type' => 'primary',
															'estilo'=> '',
															'btn_type'=>'submit',
															'btn_type'=>'button'
															));
											$btn_primary->show();
											
										?>
									</div>
								</div>	
						   </div>
						   </div>
                                </div>
                                <!-- /.panel-heading -->
                                <!-- /.panel-body -->
                            </div>
                            <!-- /.panel -->
                  		</div>
                	</div>
            </div>
		<!-- #Modal2 -->
        <!-- /#page-wrapper -->
		
		<!-- Modal modalGEE  -->
			<div class="modal fade" id="modalActualizarEE" tabindex="-1" role="dialog" aria-labelledby="modalActualizarEE"  aria-hidden="true" data-backdrop="static" data-keyboard="false" width="900">
            	<div class="modal-dialog">
                	<div class="modal-content" style="width:700px">
                  		<div class="modal-header">
                    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    		<h4 class="modal-title" id="myModalLabel">Actualizar EE</h4>
                  		</div>
                  		<div class="modal-body">
                    		<div class="panel panel-default">
                                <div class="panel-heading">
								<div style="padding-top: 1em;" class="container">
								  <div class="alert alert-success" id="alertSuccessAEE" style="display:none;width:600px">
								  </div>
								  <div class="alert alert-info" id="alertInfoAEE" style="display:none;width:600px">
								  </div>
								  <div class="alert alert-warning" id="alertWarningAEE" style="display:none;width:600px">
								  </div>
								  <div class="alert alert-danger" id="alertDangerAEE" style="display:none;width:600px">
								  </div>
								</div>
								<div class="row">
									<form action="ajax/subir_archivo.php" method="post" id="formCargaActualizaEE" name="formCargaActualizaEE" enctype="multipart/form-data" target="contenedor_subir_archivo_edi810" onSubmit="return false;">
									<input type="submit" id="btn_FormCargaActualizaEE" name="btn_FormCargaActualizaEE" value="validarCabecera" style="display:none">
									<div class="col-lg-3">	
									</div>
									<div class="col-lg-6">
										<img src="../images/loadingAnimation.gif" style="height:15px; display:inline;width: 90%;display:none" id="ImagenCargandoCargaActualizaEE" name="ImagenCargandoCargaActualizaEE"/>
										<input type="file" name="archivo" id="archivo_Actualizar_EE" required="required" />
									</div>
									<div class="col-lg-2">						
										<?
											$btn_primary	= new templates("templates/boton.html");
											$btn_primary->setParams( array (
															'text'=>'Cargar',
															'id' => 'boton_subir_archivo_actualizar_ee',
															'icon' => 'glyphicon glyphicon-save',
															'type' => 'primary',
															'estilo'=> '',
															'more'=>'',
															'btn_type'=>'submit'
															));
											$btn_primary->show();
										?>
									</div>
									<input type="hidden" id="accion_subida" name="accion_subida" value="SUBIR_ACTUALIZAR_EE"/>
									</form>
								</div>
								<br />
							<div id="DivTablaActualizarEE" style="overflow-y: scroll;overflow-y: hidden">
								<form name="frm_actualizarEE" id="frm_actualizarEE" method="post" action="#" onSubmit="return false;">
								<input type="submit" name="btnValidarActuEE" id="btnValidarActuEE" style="display:none"/>
								<div class="table-responsive" style="border: 1px solid #DDDDDD;background-color:#FFF">						  
								<table id="tabla_AEE" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th id="thInvoiceNumberActualizarEE">Invoice Number</th>
											<th>PO Number</th>
											<th>Centro</th>
											<th>EE</th>
										</tr>
									</thead>
									<tbody id=tdBodyAEE>
									</tbody>
								</table>
								</div>
								</form>
							</div>
							<div class="row">
						   <div class="col-lg-5" style="float:right; margin-top:40px" id="DivOpcionesActualizarEE">
								<div class="form-group">
								<center>
								<img src="../images/loadingAnimation.gif" style="height:15px; display:none" id="ImagenCargandoAEE" name="ImagenCargandoAEE"/>
                                </center>
<?
									$btn_primary->setParams( array (
													'text'=>'Cancelar',
													'id' => 'btn_CancelarAEE',
													'icon' => 'glyphicon glyphicon-floppy-remove',
													'type' => 'default',
													'estilo'=> '',
													'more'=>'data-dismiss="modal"',
													'btn_type'=>'button'
													));
									$btn_primary->show();
									
									$btn_primary->setParams( array (
													'text'=>'Actualizar EE',
													'id' => 'btn_ActualizarEE',
													'icon' => 'glyphicon glyphicon-floppy-saved',
													'type' => 'primary',
													'estilo'=> '',
													'btn_type'=>'submit',
													'btn_type'=>'button'
													));
									$btn_primary->show();
?>
								</div>
						   </div>	
						   </div>
						   </div>
                                </div>
                                <!-- /.panel-heading -->
                                <!-- /.panel-body -->
                            </div>
                            <!-- /.panel -->
                  		</div>
                	</div>
            </div>
		<!-- #Modal2 -->
		<!-- Modal ModalBulto -->
		<div class="modal fade" id="ModalBulto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-xl">
				<div class="modal-content" >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" >
							<div id="paquete_modal" name="paquete_modal"><label id="lblModalTitulo">-Bulto </label></div>
						</h4>
					</div>
					<div class="modal-body" style="padding-left: 5px;padding-right: 5px;">
						<div class="row" style="margin-left: 0px;margin-right: 0px;">
							<div class="col-md-2" style="max-width: 160px">
								<div class="form-group">
									<label for="txtTipoBulto">Tipo bulto:</label>
									<input type="text" class="form-control" id="txtTipoBulto" name="txtTipoBulto" title="Tipo de Bulto" disabled="disabled"/>
								</div>
							</div>
							<div class="col-md-2" style="max-width: 160px">
								<div class="form-group">
									<label for="txtPesoBulto">Peso(KG):</label>
									<input type="text" class="form-control" id="txtPesoBulto" name="txtPesoBulto" title="Pedo de bulto" disabled="disabled"/>
								</div>
							</div>
							<div class="col-md-2" style="max-width: 160px">
								<div class="form-group">
									<label for="txtVolumenBulto">Volumen(MT³):</label>
									<input type="text" class="form-control" id="txtVolumenBulto" name="txtVolumenBulto" title="Volumen de Bulto" disabled="disabled"/>
								</div>
							</div>
							<div class="col-md-2" style="max-width: 160px">
								<div class="form-group">
									<label for="txtLargoBulto">Largo(CM):</label>
									<input type="text" class="form-control" id="txtLargoBulto" name="txtLargoBulto" title="Largo de Bulto" disabled="disabled"/>
								</div>
							</div>
							<div class="col-md-2" style="max-width: 160px">
								<div class="form-group">
									<label for="txtAnchoBulto">Ancho(CM):</label>
									<input type="text" class="form-control" id="txtAnchoBulto" name="txtAnchoBulto" title="Ancho de Bulto" disabled="disabled"/>
								</div>
							</div>
							<div class="col-md-2" style="max-width: 160px">
								<div class="form-group">
									<label for="txtAltoBulto">Alto(CM):</label>
									<input type="text" class="form-control" id="txtAltoBulto" name="txtAltoBulto" title="Alto de Bulto" disabled="disabled"/>
								</div>
							</div>
							<div class="col-md-2" style="max-width: 160px">
								<div class="form-group">
									<label for="txtFechaDespachoBulto">Fecha despacho:</label>
									<input type="text" class="form-control" id="txtFechaDespachoBulto" name="txtFechaDespachoBulto" title="Fecha despacho de Bulto" disabled="disabled"/>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="table-responsive">
										<table class="display" cellspacing="0" width="100%" id="tabla_bulto_modal">
											<thead>
												<tr>
													<th>Nro Factura</th>
													<th>Nro Parte</th>
													<th>Orden Compra</th>
													<th>Po Position</th>
													<th>Unidad Medición</th>
													<th>Tracking Number</th>
													<th>Cantidad Despachada</th>
												</tr>
											</thead>
											<tbody></tbody>
											<tfoot>
												<tr>
													<th>Nro Factura</th>
													<th>Nro Parte</th>
													<th>Orden Compra</th>
													<th>Po Position</th>
													<th>Unidad Medición</th>
													<th>Tracking Number</th>
													<th>Cantidad Despachada</th>
												</tr>
											</tfoot>
										</table>
									</div><!-- /.table-responsive -->
								</div>
							</div><!-- /.panel-body -->
						</div>
						<!-- /.panel -->
					</div><!--modal boody-->
					<div class="modal-footer">
						<div  align="right">
							<button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
	<br /><br />
	<div style="margin-left:25px; margin-top:-5px;" class="row">
		<div align="left">
    		<button name="btn_inicio" id="btn_inicio" class="btn btn-info" type="button" title="Volver a Menú Principal"> 
				<span class="glyphicon glyphicon-home"></span>&nbsp;Inicio</button>               
		</div>
	</div>
    <!-- /#wrapper -->
    <!-- Error Dialog -->
    
	<!-- HIDDEN-->
	<div style="display:none">
		<textarea id="JSON_ArrayDeGEE"></textarea> 
		<button type="button" class="btn btn-info" id="btn_GEE" name="btn_GEE" data-toggle="modal" data-target="#modalGEE" style="display:none">Generar EE</button>
		<button type="button" class="btn btn-info" id="btn_AEE" name="btn_AEE" data-toggle="modal" data-target="#modalActualizarEE" style="display:none">Actualizar EE</button>
		<button type="button" class="btn btn-primary" id="btnAbrirModalBulto" name="btnAbrirModalBulto" data-toggle="modal" data-target="#ModalBulto">AbrirModalBulto</button>
		<input type="hidden" id="txtIdRuta" name="txtIdRuta"/>
		<input type="hidden" id="RegistrosTabla" name="RegistrosTabla" value="0"/>
		<input type="hidden" id="TipoDocumentoBusqueda" name="TipoDocumentoBusqueda" value="Factura"/>
		<div id="divHidden" style="display:none">
			<div id="DivRutasH">
			</div>
		</div>
		<iframe width="1" height="1" frameborder="0" id="contenedor_subir_archivo_edi810" name="contenedor_subir_archivo_edi810" style="display:none"></iframe>
	</div>
	<!-- #HIDDEN-->
    <script src="../js/plugins/metisMenu/jquery.metisMenu.js"></script>

</body>

</html>
