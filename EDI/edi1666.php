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
	$AccesoPermiso=VerificarPermiso(17);
	
	if($AccesoPermiso==false)
	{
		header('Location: ../internos.php');
	}
	
?>
<!DOCTYPE html>
<html>

<head>
    <title>Status de importaciones facturadas</title>
    <?php include("includes.php"); ?>
    <script src="js/edi1666.js"></script>

	<script>
		window.onbeforeunload = function(e) {
		  return "Esta a punto de abandonar EDI1666. Para regresar presione botón 'Volver'";
		};
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
		.thFecha
		{
			min-width: 70px;
		}
		.thMaterial
		{
			min-width: 90px;
		}
		th { font-size: 11px; }
		td { font-size: 10px; }
		#tabla_cabecera_principal thead {visibility: collapse;}
	</style>
</head>

<body id="body_edi1666">

<a href="iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>
<div id="div_detalle" style="display:none"></div>
	<div id="wrapper">
<?
	$template = new templates("templates/barra_nav.html");
	$template->setParams( array (
	'reporte' => "Status de importaciones facturadas"
	));
	$template->show();
?>
            <div class="row" style="margin-left:10px; margin-right:10px; margin-top:15px;">
				<form name="frm_filtros" id="frm_filtros" method="post" action="" onSubmit="return false;">
					<input type="submit" id="submit_filtros" style="display:none"/>
					<div class="row">
					<?
					#FILTROS 
					$tmp_soc = new templates("templates/f_sociedad.html");
					$tmp_soc->show();
					$tmp_fac = new templates("templates/f_factura.html");
					$tmp_fac->show();
					$tmp_f_oc = new templates("templates/f_oc.html");
					$tmp_f_oc->show();
					?>
					<div class="col-lg-3" style="min-height: 65px;">
						<div class="form-group" id="div_proveedores" title="Ingrese proveedor">
							<label class="control-label">Proveedor: </label>
							<img src="../images/loadingAnimation.gif">
						</div>
					</div>
					<div class="col-lg-2">
						<div class="form-group">
							<label>Documento Embarque: </label>
							<span style="visibility:hidden" class="glyphicon glyphicon-plus-sign" id="ingreso_docuemnto_embarque" name="ingreso_docuemnto_embarque" data-toggle="modal" data-target="#myModalIngresarDocumentoEmbarque"></span>
							<input class="form-control" placeholder="Ingrese documento de embarque" id="n_documento_embarque" name="linea_1" title="Ingrese documento de embarque">
						</div>
					</div>
					</div>
					<div class="row">
						<div class="col-lg-2">
						   <div class="form-group">
								<label for="txt_ee">Entrega Entrante</label>
								<span style="visibility:hidden" class="glyphicon glyphicon-plus-sign" id="ingreso_multiple_ee" name="ingreso_multiple_ee" data-toggle="modal" data-target="#myModalIngresarEntregaEntrante"></span>
								<input type="text" class="form-control" id="txt_ee" placeholder="Entrega Entrante" name="txt_ee" value=""  title="Ingrese Entrega Entrante">
							</div>
						</div>
						<?
						$tmp_fecha = new templates("templates/f_fecha.html");
						
						//$fini = strtotime ( '-1 month' , strtotime ( date('Y-m-d') ) ) ;
						//$fini = date ( 'd/m/Y' , $fini );

						$tmp_fecha->setParams( array (
										'label'	=>'Fecha Inicio (Facturación)',
										'id' 	=> 'fecha1_principal',
										'valor' => '',
										'title' => 'Seleccione fecha de inicio'
										));
						$tmp_fecha->show();
						
						$tmp_fecha->setParams( array (
										'label'	=>'Fecha Término (Facturación)',
										'id' 	=> 'fecha2_principal',
										'valor' => '',
										'title' => 'Seleccione fecha de termino'
										));
						$tmp_fecha->show();
						
						$tmp_fecha->setParams( array (
										'label'	=>'Fecha Ingreso SAP (Desde)',
										'id' 	=> 'fecha_ingreso_sap_inicio',
										'valor' => '',
										'title' => 'Seleccione fecha de ingreso sap'
										));
						$tmp_fecha->show();
						
						$tmp_fecha->setParams( array (
										'label'	=>'Fecha Ingreso SAP (Hasta)',
										'id' 	=> 'fecha_ingreso_sap_termino',
										'valor' => '',
										'title' => 'Seleccione fecha de ingreso sap'
										));
						$tmp_fecha->show();
						?>
					</div>
			</div>
			<div class="row" style="margin-left:10px; margin-right:10px;"> 
				<div class="col-lg-1">
				<?
					$btn_primary	= new templates("templates/boton.html");
					$btn_primary->setParams( array (
									'text'=>'Buscar',
									'id' => 'btn_buscar_principal',
									'icon' => 'glyphicon glyphicon-search',
									'type' => 'primary',
									'btn_type'=>'button'
									));
					$btn_primary->show();
				?>
				</div>
				<div class="col-lg-1">
				<?
					$btn_primary	= new templates("templates/boton.html");
					$btn_primary->setParams( array (
									'text'=>'Limpiar',
									'id' => 'boton_limpiar_edi1666',
									'icon' => 'glyphicon glyphicon-repeat',
									'type' => 'info',
									'btn_type'=>'button'
									));
					$btn_primary->show();
				?>
				</div>
				</form>
			</div>
  			<!-- TABLA -->
            <div class="row" style="margin-left:10px; margin-right:10px; margin-top:10px;">
                <div class="col-lg-12" style="padding-left: 0px;padding-right: 0px;">
                    <div class="panel panel-default">
                        <div class="panel-heading" >
                            <div align="left">
							<?
								$btn_primary->setParams( array (
												'text'=>'Descargar todo',
												'id' => 'btn_descargar_todos',
												'icon' => 'glyphicon glyphicon-download-alt',
												'type' => 'info',
												'btn_type'=>'button'
												));
								$btn_primary->show();
							?>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" style="padding-left: 0px;padding-right: 0px;">
                            <div class="table-responsive" id="div_table_data"></div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div><!-- /.panel -->
                </div>
            </div><!-- FIN TABLA -->
			<!-- Modal -->
			<?
				$ModalIngresarFacturas	= new templates("templates/ModalIngresarFacturas.html");
				$ModalIngresarFacturas->setParams( array ());
				$ModalIngresarFacturas->show();
				
				$ModalIngresarOC	= new templates("templates/ModalIngresarOC.html");
				$ModalIngresarOC->setParams( array ());
				$ModalIngresarOC->show();
				
				$ModalIngresarNumeroDocumento	= new templates("templates/ModalIngresarDocumentoEmbarque.html");
				$ModalIngresarNumeroDocumento->setParams( array ());
				$ModalIngresarNumeroDocumento->show();
				
				$ModalIngresarEE	= new templates("templates/ModalIngresarEntregaEntrante.html");
				$ModalIngresarEE->setParams( array ());
				$ModalIngresarEE->show();
			?>
</div><!-- /#page-wrapper -->
<br /><br />
<div style="margin-left:25px; margin-top:-5px;" class="row" id="Divbtn_volverHome">
		<div align="left">
    		<button name="btn_inicio" id="btn_inicio" class="btn btn-info" type="button" title="Volver a Menú Principal"> 
			<span class="glyphicon glyphicon-home"></span>&nbsp;Inicio</button>               
		</div>
</div>
<div style="display:none">
	<textarea id="Json_InvoiceNumber"></textarea> 
	<iframe width="1" height="1" frameborder="0" id="contenedor_generarexcel" name="contenedor_generarexcel" style="display: none" ></iframe>
	<div id="div_proveedoresHidden" style="display:none"></div>
	<input type="hidden" id="txtIdProveedor" name="txtIdProveedor" value=""/>
</div>
<!--------------------------------------------------------------------->
    <script src="../js/jquery.metisMenu.js"></script>
</body>

</html>
