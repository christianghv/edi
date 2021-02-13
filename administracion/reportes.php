<?php
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
	$AccesoPermiso=VerificarPermiso(4);
	
	if($AccesoPermiso==false)
	{
		header('Location: ../internos.php');
	}
	
?>
<!DOCTYPE html>
<html>

<head>  
    <title>REPORTES</title>
    <?php include("includes.php"); ?> 
	<!--<script src="../EDI/js/funciones.js" type="text/javascript"  charset="UTF-8"></script>-->
    <script src="js/reportes.js"></script>
	<script  src="../plugins/JqueryUI/jquery-ui.js" type="text/javascript" language="javascript" ></script>

</head>

<body id="bodyProvedores">
<a href="templates/iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>


<?
	$template = new templates("templates/barra_nav.html");
	$template->setParams( array (
	'reporte' => "REPORTE FACTURAS"
	));
	$template->show();
?>
<div style="display:none;">
<button type="button" class="btn btn-primary" id="btn_open_modal" data-toggle="modal" data-target="#myModal"></button>
</div>

<div id="div_contenido"></div>
<div style="margin-left:10px; margin-top:-5px;" class="row">
		<div align="left">
    		<button name="btn_volver" id="btn_volver" class="btn btn-info" type="button" title="Volver a Menú Principal"> 
			<span class="glyphicon glyphicon-home"></span>&nbsp;Volver</button>               
		</div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg"  align="left">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="btn_cerrar_x">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Detalle Proveedor.</h4>
			</div>
			<div class="modal-body" align="left" id="form">
				
				<div class="row">
					
					<div class="col-lg-4">
						<label for="val_sociedad">Sociedad:</label>
						<input class="form-control" id="val_sociedad" name="val_sociedad" title="ID Sociedad" disabled />
					</div>
					
					<div class="col-lg-2">
						<label for="val_tipoproceso">Proceso:</label>
						<input class="form-control" id="val_tipoproceso" name="val_tipoproceso" title="Proceso" disabled />
					</div>
					
					<div class="col-lg-4">
						<label for="val_proveedor">Proveedor:</label>
						<input class="form-control" id="val_proveedor" name="val_proveedor" title="Proveedor" disabled />
					</div>
					
					<div class="col-lg-2">
						<label for="val_fecha">Fecha:</label>
						<input class="form-control" id="val_fecha" name="val_fecha" title="Fecha" disabled />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover" cellspacing="0" id="tabla_detalle" style="width:100%">
								<thead>
									<tr>
										<th>Hora</th>
										<th>Comentario</th>
										<th id="thIdFormato">Nro Factura</th>
										<th>Estatus</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" >
				<button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
			</div>
		</div>
	</div>
</div>

	
<script src="../js/main.js"></script>
<script src="../js/jquery.metisMenu.js"></script>
</body>
</html>
