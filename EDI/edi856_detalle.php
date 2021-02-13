<?
	session_start();
$email = $_SESSION["email"];
	if (!isset($email))
	 header('Location: ../index.php');
	require '../clases/class.templates.php';
	if (!isset($_SESSION["email"])){
    	header('Location:../index.php');
	}
?>
	<style>
	#tabla_datos_oc_detalle thead {visibility: hidden;}
	#tabla_nro_oc_modal thead {visibility: hidden;}
	#tabla_bulto_modal thead {visibility: hidden;}
	</style>
    <script src="js/edi856_detalle.js"></script>
<?
	$template = new templates("templates/barra_nav.html");
	$template->setParams( array (
	'reporte' => "EDI856 DETALLE  -  FACTURA: ".$_POST["nro_factura"]
	));
	$template->show();
?>
		<div class="row" style="margin-left:10px; margin-right:10px; margin-top:10px;">
<?
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Nro Factura:',
								'id'   =>'nro_factura_detalle',
								'val'=>$_POST["nro_factura"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>''		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Fecha Despacho:',
								'id'   =>'fecha_despacho_detalle',
								'val'=>$_POST["fecha_despacho"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>'' 		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Descripción:',
								'id'   =>'descrip_detalle',
								'val'=>$_POST["descripcion"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>''		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Nro Camión:',
								'id'   =>'nro_camion_detalle',
								'val'=>$_POST["nro_camion"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>'' 		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-1',
								'label'=>'Cod Embalaje:',
								'id'   =>'cod_embalaje_detalle',
								'val'=>$_POST["cod_embalaje"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>'' 		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-1',
								'label'=>'Hora:',
								'id'   =>'hora_detalle',
								'val'=>$_POST["hora"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>''		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-1',
								'label'=>'Cant Bulto:',
								'id'   =>'cant_bulto_detalle',
								'val'=>$_POST["cant_bulto"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>''		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-1',
								'label'=>'Peso Carga:',
								'id'   =>'peso_carga_detalle',
								'val'=>$_POST["peso_carga"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>''		
								));
				$tmp->show();
?>
		</div>
		<div class="row" style="margin-left:10px; margin-right:10px; margin-top:0px;">

<?
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-1',
								'label'=>'Tot Unid Embarcadas:',
								'id'   =>'tot_unid_embarcadas_detalle',
								'val'=>$_POST["tot_unid_embarcadas"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>''		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-1',
								'label'=>'Tipo Transporte:',
								'id'   =>'tipo_transporte_detalle',
								'val'=>$_POST["tipo_transporte"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>'' 		
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-1',
								'label'=>'Unidad Medida:',
								'id'   =>'unidad_medida_detalle',
								'val'=>$_POST["unidad_medida"],
								'bloqueo'=>'disabled',
								'type'=>'text',
								'place'=>'' 		
								));
				$tmp->show();
?>
				<div class="col-lg-1">
					<div>&nbsp;</div>
					<div>&nbsp;</div>
<?
				$btn_primary	= new templates("templates/boton.html");
				$btn_primary->setParams( array (
								'text'=>'Ver más',
								'id' => 'ver_mas_detalle',
								'icon' => 'glyphicon glyphicon-plus',
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
		<!-- TABLA -->
		<div class="row" style="margin-left:10px; margin-right:10px;">
			<div class="col-lg-20">
				<div class="panel panel-default">
					<div class="panel-body" >
						<div class="table-responsive" >
							<table class="display" cellspacing="0" width="100%"  id="tabla_datos_oc_detalle">
								<thead>
									<tr>
										<th>Nro Factura</th>
										<th>Nro Parte</th>
										<th>Orden Compra</th>
										<th>Po Position</th>										
										<th>Unidad Medición</th>
										<th>Tracking Number</th>
										<th>Cantidad Despachada</th>
										<th>Packing Slip</th>
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
											<th>Packing Slip</th>
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
		<!-- FIN CUERPO PAGINA DETALLE-->
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
							'more'=>'title="Volver a EDI856"',
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
		<div class="modal-content">
			<div class="modal-body">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="table-responsive">
							<table class="display" cellspacing="0" width="100%" id="tabla_nro_oc_modal">
								<thead>
									<tr>
										<th>Id Entidad</th>
										<th>Id Código</th>
										<th>Id Calificador</th>
									</tr>
								</thead>
								<tbody></tbody>
								<tfoot>
									<tr>
										<th>Id Entidad</th>
										<th>Id Código</th>
										<th>Id Calificador</th>
									</tr>
								</tfoot>
							</table>
						</div><!-- /.table-responsive -->
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
<!-- Modal Bulto -->
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
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="table-responsive">
							<table class="display" cellspacing="0" width="100%" id="tabla_bulto_modal">
								<thead>
									<tr>
										<th>Tipo Bulto</th>
										<th>Peso</th>
										<th>Unidad de Peso</th>
										<th>Volumen</th>
										<th>Unidad de Volumen</th>
										<th>Longitud</th>
										<th>Ancho</th>
										<th>Alto</th>
										<th>Unidad de Dimensión</th>
										<th>Fecha despacho</th>
										<th>Instancia Especiales</th>
									</tr>
								</thead>
								<tbody></tbody>
								<tfoot>
									<tr>
										<th>Tipo Bulto</th>
										<th>Peso</th>
										<th>Unidad de Peso</th>
										<th>Volumen</th>
										<th>Unidad de Volumen</th>
										<th>Longitud</th>
										<th>Ancho</th>
										<th>Alto</th>
										<th>Unidad de Dimensión</th>
										<th>Fecha despacho</th>
										<th>Instancia Especiales</th>
									</tr>
								</tfoot>
							</table>
						</div><!-- /.table-responsive -->
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
<div id="DivHidden" style="display:none">
	<button type="button" class="btn btn-primary" id="btnAbrirModalBulto" name="btnAbrirModalBulto" data-toggle="modal" data-target="#ModalBulto">AbrirModalBulto</button>
</div>