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
	$AccesoPermiso=VerificarPermiso(7);
	
	if($AccesoPermiso==false)
	{
		header('Location: ../internos.php');
	}

?>
	<style>
	#tabla_datos_oc_detalle thead {visibility: hidden;}
	#tabla_nro_oc_modal thead {visibility: hidden;}
	</style>
<script src="js/edi855_detalle.js"></script>
<?
	$template = new templates("templates/barra_nav.html");
	$template->setParams( array (
	'reporte' => "EDI855 DETALLE  -  OC: ".$_POST["nro_oc"]
	));
	$template->show();
?>
		<input type="hidden" id="modificada" value="<? echo $_POST["modificada"]; ?>" />
		<div class="row" style="margin-left:10px; margin-right:10px; margin-top:15px;">
<?
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Nro Orden Compra:',
								'id'   =>'nro_oc_detalle',
								'val'=>$_POST["nro_oc"],
								'bloqueo'=>'disabled',
								'type'=>'text'		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Fecha OC:',
								'id'   =>'fecha_detalle',
								'val'=>$_POST["fecha_oc"],
								'bloqueo'=>'disabled',
								'type'=>'text' 		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Fecha ACK:',
								'id'   =>'fecha_promesa_detalle',
								'val'=>$_POST["fecha_promesa"],
								'bloqueo'=>'disabled',
								'type'=>'text'		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-1',
								'label'=>'Items:',
								'id'   =>'items_detalle',
								'val'=>$_POST["nro_item"],
								'bloqueo'=>'disabled',
								'type'=>'text'	
								));
				$tmp->show();
				
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Descripción:',
								'id'   =>'descrip_detalle',
								'val'=>'',
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>''		
								));
				$tmp->show();
				
?>               
				
				<div class="col-lg-2">
				   <div class="form-group">
						<label for="semaforo_detalle">Modificado:</label><br>
						<div id="div_img"></div>
					</div>
				</div>
		</div>
		
		<!-- TABLA -->
		<div class="row" style="margin-left:10px; margin-right:10px;">
			<div class="col-lg-20">
				<div class="panel panel-default">
					
					<div class="panel-heading" >
						<div align="right">
<?
							$btn_primary	= new templates("templates/boton.html");
							$btn_primary->setParams( array (
											'text'=>'Modificar OC',
											'id' => 'modificar_detalle',
											'icon' => 'glyphicon glyphicon-edit',
											'type' => 'primary',
											//'estilo'=> 'style="padding-top:2px;padding-botom:2px; vertical-align:central;"'
											'estilo'=> '',
											'more'=>'data-toggle="modal" data-target="#myModal"',
											'btn_type'=>'button'
											));
							$btn_primary->show();

?>
						</div>
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body" >
						<div class="table-responsive" >
							<table class="display" cellspacing="0" width="100%"  id="tabla_datos_oc_detalle">
								<thead>
									<tr>
										<th>Pos OC</th>
										<th>Nro Parte</th>
										<th>Descripción</th>
										<th>Cantidad</th>
										<th>Unidad</th>
										<th>Precio</th>
										<th>Moneda</th>
										<th>Nro Parte ACK</th>
										<th>Cantidad ACK</th>
										<th>Unidad ACK</th>
										<th>Precio ACK</th>
										<th>Fecha Promesa</th>
										<th>Modificada</th>
										<th>hidden</th>
									</tr>
								</thead>
								<tbody></tbody>
									<tfoot>
										<tr>
                                            <th>Pos OC</th>
											<th>Nro Parte</th>
											<th>Descripción</th>
											<th>Cantidad</th>
											<th>Unidad</th>
											<th>Precio</th>
											<th>Moneda</th>
											<th>Nro Parte ACK</th>
											<th>Cantidad ACK</th>
											<th>Unidad ACK</th>
											<th>Precio ACK</th>
											<th>Fecha Promesa</th>
											<th>Modificada</th>
											<th>hidden</th>
                                        </tr>
                                    </tfoot>
							</table>
						</div>
						<!-- /.table-responsive -->
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
			</div>
		</div>
	   
		<!-- FIN TABLA -->
		<div class="row" style="margin-left:10px; margin-top:-5px;">
			<div align="left">
<?
			$btn_primary	= new templates("templates/boton.html");
			$btn_primary->setParams( array (
							'text'=>'Volver',
							'id' => 'volver_detalle',
							'icon' => 'glyphicon glyphicon-arrow-left',
							'type' => 'info',
							//'estilo'=> 'style="padding-top:2px;padding-botom:2px; vertical-align:central;"'
							'estilo'=> '',
							'more'=>'title="Volver a EDI855"',
							'btn_type'=>'button'
							));
			$btn_primary->show();
?>
			</div>
		</div>
<!---------------------------------------------------------------------> 
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content" >
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" >
					<div id="oc_modal" name="oc_modal"></div>
				</h4>
			</div>
			
			<div class="modal-body" id="div_tabla_modal">
				<div class="panel panel-default">
					<div class="panel-heading">
					<div class="alert alert-success" id="alertSuccess" style="display:none;width:545px">
					</div>
					<div class="alert alert-info" id="alertInfo" style="display:none;width:545px">
					</div>
					<div class="alert alert-warning" id="alertWarning" style="display:none;width:545px">
					</div>
					<div class="alert alert-danger" id="alertDanger" style="display:none;width:545px">
					</div>
<?
							$btn_primary	= new templates("templates/boton.html");
							$btn_primary->setParams( array (
											'text'=>'Seleccionar',
											'id' => 'selectall',
											'icon' => 'glyphicon glyphicon-check',
											'type' => 'info',
											'estilo'=> '',
											'more'=>'',
											'btn_type'=>'button'
											));
							$btn_primary->show();
							
							$btn_primary	= new templates("templates/boton.html");
							$btn_primary->setParams( array (
											'text'=>'Deseleccionar',
											'id' => 'deselect_all_modal',
											'icon' => 'glyphicon glyphicon-unchecked',
											'type' => 'info',
											'estilo'=> '',
											'more'=>'',
											'btn_type'=>'button'
											));
							$btn_primary->show();

?>					
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<div class="table-responsive" >
							<table class="display" cellspacing="0" width="100%" id="tabla_nro_oc_modal">
								<thead>
									<tr>
										<th>Pos</th>
										<th>Motivo</th>
										<th>Orig</th>
										<th>ACK</th>
										<th>Modificar</th>
									</tr>
								</thead>
								<tbody></tbody>
								<tfoot>
									<tr>
										<th>Pos</th>
										<th>Motivo</th>
										<th>Orig</th>
										<th>ACK</th>
										<th>Modificar</th>
									</tr>
								</tfoot>
							</table>
						</div><!-- /.table-responsive -->
					</div><!-- /.panel-body -->
				
				</div>
				<!-- /.panel -->
			</div><!--modal boody-->
			
			<div class="modal-footer">
				<div id="div_modificar" align="right" style="display:block;">
				<img src="../images/loadingAnimation.gif" style="height:15px; display:none" id="ImagenCargando" name="ImagenCargando"/>

<?
					$btn_primary	= new templates("templates/boton.html");
					$btn_primary->setParams( array (
									'text'=>'Cancelar',
									'id' => 'cerrar',
									'icon' => 'glyphicon glyphicon-floppy-remove',
									'type' => 'default',
									'estilo'=> '',
									'more'=>'data-dismiss="modal"',
									'btn_type'=>'button'
									));
					$btn_primary->show();
					
					$btn_primary	= new templates("templates/boton.html");
					$btn_primary->setParams( array (
									'text'=>'Modificar',
									'id' => 'btn_modificar_modal',
									'icon' => 'glyphicon glyphicon-floppy-saved',
									'type' => 'info',
									'estilo'=> '',
									'more'=>'',
									'btn_type'=>'button'
									));
					$btn_primary->show();

?>
				</div>
				
				<div id="div_modificar2" style="display:none;" align="center"><b>Modificando...</b></div>
			</div>
			
		</div>
	</div>
</div>
<!--////// TOOLTIP ///////////////////////////////////////////////-->
<div id="div_tooltip" style="display:none;">
	<table >
		<thead>
			<tr>
				<td>Tipo ACK</td>
				<td>Dif Precio</td>
				<td>Dif Cantidad</td>
				<td>Dif Nro Parte</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td id="tipo_ack"></td>
				<td id="dif_precio"></td>
				<td id="dif_cantidad"></td>
				<td id="dif_nparte"></td>
			</tr>
		</tbody>
	</table>

</div>


