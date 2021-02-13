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
	$AccesoPermiso=VerificarPermiso(5);
	
	if($AccesoPermiso==false)
	{
		header('Location: ../internos.php');
	}
?>
<!DOCTYPE html>
<html>

<head>  
    <title>REPORTE GENERAL</title>
    <?php include("includes.php"); ?> 
	<!--<script src="../EDI/js/funciones.js" type="text/javascript"  charset="UTF-8"></script>-->
    <script src="js/reporte_general.js"></script>
	<script  src="../plugins/JqueryUI/jquery-ui.js" type="text/javascript" language="javascript" ></script>

</head>

<body id="bodyProvedores">	
<a href="templates/iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>

<div style="display:none;">
<button type="button" class="btn btn-primary" id="btn_open_modal" data-toggle="modal" data-target="#myModal"></button>
</div>

<div id="wrapper">
<?
	$template = new templates("templates/barra_nav.html");
	$template->setParams( array (
	'reporte' => "REPORTE GENERAL"
	));
	$template->show();
?>
	<br />
	<div class="row" style="margin-left:10px; margin-right:10px;">
		<div class="panel panel-default">
			<div class="panel-heading" >
				<div class="row" >
					
					<div class="col-lg-2">
						<label for="fecha">Fecha:</label>
						<input class="form-control" placeholder="" id="fecha" name="fecha" title="Seleccione Fecha" />
					</div>
					
					<div class="col-lg-2">
						<label for='estatus'>Estatus</label>
						<select class="form-control" id="estatus" name="estatus">
							<option value=''>-Seleccione-</option>
							<option value='1'>Correctas</option>
							<option value='0'>Erroneas</option>
						</select>
					</div>
					
					<div class="col-lg-2" style="margin-top:13px; margin-bottom:-2px;">
						<button type="button" class="btn btn-primary" id="btn_refrescar" >
						<span class="glyphicon glyphicon-refresh"></span> Refrescar</button>
					</div>
					
				</div>
			</div>
			
			<div class="panel-body">
				<div class="row" >
					<div class="col-lg-12">
					<div class="table-responsive">
						<table class="display" id="tabla_log_general">
							<thead>
								<tr>
									<th>Correlativo</th>
									<th>ID Proveedor</th>
									<th>Proveedor</th>
									<th>Hora</th>
									<th>Formato</th>
									<th>Tipo</th>
									<th>Estatus</th>
								</tr>
							</thead>
							<tbody id="tdBodyLogGeneral"></tbody>
						</table>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
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
				
				<div class="row" >
					<div class="col-lg-2">
						<label for="correlativo">Correlativo:</label>
						<input class="form-control" id="correlativo" name="correlativo" title="Correlativo" disabled />
					</div>
					
					<div class="col-lg-3">
						<label for="val_idproveedor">ID Proveedor:</label>
						<input class="form-control" id="val_idproveedor" name="val_idproveedor" title="ID Proveedor" disabled />
					</div>
					
					<div class="col-lg-5">
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
						<div class="table-responsive" style="overflow:auto; width:100%; overflow-y: hidden">
							<table class="display" cellspacing="0" id="tabla_detalle">
								<thead>
									<tr>
										<th>Hora</th>
										<th>Descripción</th>
										<th>Archivo</th>
										<th>Estatus</th>
									</tr>
								</thead>
								<tbody id="tbodyDetalleGral"></tbody>
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



<script src="../js/jquery.metisMenu.js"></script>
</body>
</html>
