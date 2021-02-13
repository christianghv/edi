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
	$AccesoPermiso=VerificarPermiso(6);
	
	if($AccesoPermiso==false)
	{
		header('Location: ../internos.php');
	}
	
?>
	<style>
	#tabla_detalle th {text-align: center;}
	#tabla_detalle td {text-align: center;}
	#tabla_detalle thead {visibility: hidden;}
	#tabla_OC thead {visibility: hidden;}
	</style>
    <title>Editar Factura</title>
	<script src="js/editar_edi810.js"></script>
<body id="bodyEditar_edi810">
<a id="AbrirEditarProducto" class="thickbox" style="display:none" title="add a caption to title attribute / or leave blank" href="#">AbrirEditarProducto</a>
<span class="glyphicon glyphicon-plus-sign" id="modificarDetail" name="modificarDetail" data-toggle="modal" data-target="#myModal" style="display:none"></span>
	<a href="iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>

<?
	
	$template = new templates("templates/barra_nav.html");
	$template->setParams( array (
	'reporte' => "EDI810 DETALLE - INVOICE: ".$_POST["invoice"]
	));
	$template->show();
?>
		<form name="frm_editar_cabecera" id="frm_editar_cabecera" method="post" action="" onSubmit="return false;">
		    <div class="row"style="margin-left:10px; margin-right:10px; margin-top:5px;">		
<?
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Invoice Number:',
								'id'   =>'txt_invoiceNumber',
								'val'=>$_POST["invoice"],
								'bloqueo'=>'disabled',
								'place'=>'Invoice Number',
								'requerido'=>'required',
								'type'=>'type'
								));
				$tmp->show();
				
				$tmp_soc = new templates("templates/f_sociedad.html");
				$tmp_soc->show();			
?>					
				<div class="col-lg-4">
				   <div class="form-group" id="div_proveedores" title="Ingrese proveedor">
						<label class="control-label">Proveedor: </label>
						<img src="../images/loadingAnimation.gif">
					</div>
				</div>
<?
				$tmp_fecha = new templates("templates/f_fecha.html");
				$tmp_fecha->setParams( array (
								'label'=>'Invoice Date',
								'id' => 'invoice_date',
								'title' => 'Ingrese invoice date',
								'valor' => $_POST["invoiceDate"],
								'requerido'=>'required',
								'place'=>'Invoice Date'
								));
				$tmp_fecha->show();

?>
			</div>
			
			<div class="row"style="margin-left:10px; margin-right:10px; margin-top:0px;">
				
				<div class="col-lg-2" title="Ingrese Currency">
					<label>Invoice Currency: </label>
					<select class="form-control" id="currency" name="currency">
						<option value=''>-Seleccione-</option>
						<option value='CLP'>CLP - Pesos Chilenos</option>
						<option value='USD'>USD - Dolar Americano</option>
						<option value='EUR'>EUR - Euro Europeo</option>
						<option value='GBP'>GBP - Libra Esterlina</option>
						<option value='SEK'>SEK - Corona Sueca</option>
					</select>
				</div> 
<?
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Invoice Net Value:',
								'id'   =>'txt_netValue',
								'val'  =>'',
								'title' =>'Ingrese Net Value',
								'bloqueo'=>'',
								'place'=>'Invoice Net Value',
								'requerido'=>'required',
								'more' =>'step="any"',
								'type'=>'number'
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Invoice Gross:',
								'id'   =>'txt_grossvalue',
								'title'   =>'Ingrese Gross Value',
								'val'  =>'',
								'bloqueo'=>'',
								'place'=>'Invoice Gross',
								'requerido'=>'required',
								'more' =>'step="any"',
								'type'=>'number'
								));
				$tmp->show();
				
				$tmp = new templates("templates/campo.html");
				$tmp->setParams( array (
								'class'=>'col-lg-2',
								'label'=>'Invoice Gastos:',
								'id'   =>'txt_invoiceGastos',
								'val'  =>'',
								'title'   =>'Ingrese Invoice Gastos',
								'bloqueo'=>'',
								'place'=>'Invoice Gastos',
								'requerido'=>'required',
								'more' =>'step="any"',
								'type'=>'number'
								));
				$tmp->show();

?> 
				<input type="hidden" id="H_total" name="H_total">
				<input type="submit" value="prueba" id="btn_validarFormularioH810" name="btn_validarFormularioH810" style="display:none">
			</form>  		
			
			<!-- TABLA -->
            <div class="row" style="margin-left:0px; margin-right:0px; margin-top:0px;">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
<?
							$boton	= new templates("templates/boton.html");
							$boton->setParams( array (
											'text'=>'Ingresar OC',
											'id' => 'btn_ingresarOC',
											'icon' => 'glyphicon glyphicon-paperclip',
											'type' => 'primary',
											'more'=>'data-toggle="modal" data-target="#modalOC"',
											'btn_type'=>'button'
											));
							$boton->show();
							
							$boton	= new templates("templates/boton.html");
							$boton->setParams( array (
											'text'=>'Agregar Línea',
											'id' => 'btn_agregarDetalle',
											'icon' => 'glyphicon glyphicon-plus',
											'type' => 'primary',
											'btn_type'=>'button'
											));
							$boton->show();
							
							$boton	= new templates("templates/boton.html");
							$boton->setParams( array (
											'text'=>'Eliminar Línea',
											'id' => 'btn_eleminar',
											'icon' => 'glyphicon glyphicon-trash',
											'type' => 'danger',
											'btn_type'=>'button'
											));
							$boton->show();
							
							/**
							$boton	= new templates("templates/boton.html");
							$boton->setParams( array (
											'text' => 'Cargar Detalle',
											'id'   => 'btn_load_detalle',
											'icon' => 'glyphicon glyphicon-paperclip',
											'type' => 'primary',
											'more' => 'data-toggle="modal" data-target="#modalCargaDetalle"',
											'btn_type' => 'button'
											));
							$boton->show();
							*/
							
							$boton	= new templates("templates/boton.html");
							$boton->setParams( array (
											'text' => 'Descargar Detalle',
											'id'   => 'btn_descargarDetalle',
											'icon' => 'glyphicon glyphicon-cloud-download',
											'type' => 'primary',
											'more' => '',
											'btn_type' => 'button'
											));
							$boton->show();
							
							$boton	= new templates("templates/boton.html");
							$boton->setParams( array (
											'text' => 'Cargar Detalle',
											'id'   => 'btn_load_detalleExcel',
											'icon' => 'glyphicon glyphicon-paperclip',
											'type' => 'primary',
											'more' => 'data-toggle="modal" data-target="#modalCargarXLS"',
											'btn_type' => 'button'
											));
							$boton->show();							
							
?>
						<div id="DivFinalizarFactura" style="float:right">
<?
							$boton	= new templates("templates/boton.html");
							$boton->setParams( array (
											'text'=>'Eliminar Factura',
											'id' => 'btn_eleminarFactura',
											'icon' => 'glyphicon glyphicon-trash',
											'type' => 'danger',
											'btn_type'=>'button'
											));
							$boton->show();
							
							$boton	= new templates("templates/boton.html");
							$boton->setParams( array (
											'text'=>'Verificar Valores',
											'id' => 'btn_verificar',
											'icon' => 'glyphicon glyphicon-ok',
											'type' => 'info',
											'btn_type'=>'button'
											));
							$boton->show();
							
							$boton	= new templates("templates/boton.html");
							$boton->setParams( array (
											'text'=>'Grabar Factura',
											'id' => 'btn_grabarFactura',
											'icon' => 'glyphicon glyphicon-save',
											'type' => 'primary',
											'btn_type'=>'button'
											));
							$boton->show();	
?>							
							</div>
						</div>
						   <!-- /.panel-heading -->
                        <div class="panel-body">
							<form name="frm_input" id="frm_input" method="post" action="#" onSubmit="return false;">
                            <div class="table-responsive" id="divTablaDinamica" style="overflow:auto">
                                <div id="divTabla">
									<table id="tabla_detalle" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th id="thInvoicePosition">Invoice<BR>Position</th>
												<th>PO<BR>Number</th>
												<th>PO<BR>Position</th>
												<th>Product ID</th>
												<th>Description</th>
												<th>Measure</th>
												<th>Quantity</th>
												<th>Price</th>
												<th>Pais<BR>Origen</th>
												<th id="thSeleccionarTodas"><input type="checkbox" onChange="javascript:SelecionarTodas();" id="SelAll"></th>
												<th>EE</th>
												<th>Total</th>
												<th>856</th>
											</tr>
										</thead>
										<tbody id="tdBodytableDetalle"></tbody>
										<tfoot id="tdFoot">
											<tr>
												<th id="thInvoicePosition">Invoice<BR>Position</th>
												<th>PO<BR>Number</th>
												<th>PO<BR>Position</th>
												<th>Product ID</th>
												<th>Description</th>
												<th>Measure</th>
												<th>Quantity</th>
												<th>Price</th>
												<th>Pais<BR>Origen</th>
												<th>Eliminar</th>
												<th>EE</th>
												<th>Total</th>
												<th>856</th>
											</tr>
										</tfoot>
									</table>
								</div>
                            </div>
							<input type="submit" id="btn_input" style="display:none"/>
							</form>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            </div>
			<div class="row" style="margin-left:15px; margin-right:10px; margin-top:5px;">	
				<div align="left">
<?
					$btn_primary	= new templates("templates/boton.html");
					$btn_primary->setParams( array (
									'text'=>'Volver',
									'id' => 'btn_volver',
									'icon' => 'glyphicon glyphicon-arrow-left',
									'type' => 'info',
									'estilo'=> '',
									'btn_type'=>'button'
									));
					$btn_primary->show();
?>
				</div>
			</div>
            <!-- FIN TABLA -->
			<form name="FormEditarDetalle" id="FormEditarDetalle" method="post" action="" onSubmit="return false;">
            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"    data-backdrop="static" 
   data-keyboard="false" >
            	<div class="modal-dialog">
                	<div class="modal-content">
                  		<div class="modal-header">
                    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    		<h4 class="modal-title" id="myModalLabel">Editar Invoice Detail</h4>
                  		</div>
                  		<div class="modal-body">
                    		<div class="panel panel-default">
                                <div class="panel-heading">
							<div class="row">
								<div class="col-lg-4" style="width:270px" >
								<div class="form-group" >
								 <label>Invoice Number: </label>
								 <br />
								<input class="form-control" placeholder="Invoice Number" name="Detailtxt_invoiceNumber" id="Detailtxt_invoiceNumber"
								value="" size="25" style="width:230px;" readonly title="Ingrese Invoice Number" >         
								</div>
								</div>
								
								<div class="col-lg-4" style="width:270px">
								<div class="form-group" >
								 <label>Invoice Position: </label>
								 <br />
								<input type="number" class="form-control" placeholder="Invoice Position" name="Detailtxt_InvoicePosition" id="Detailtxt_InvoicePosition"
								value="" style="width:230px;" required title="Ingrese Invoice Position">    
								</div>
								</div>								
						   </div>
						   <div class="row">
								<div class="col-lg-4" style="width:270px" >
								<div class="form-group"  >
									 <label>PO Number: </label>
									 <br />
									<input type="number" class="form-control" placeholder="PO Number" name="Detailtxt_PONumber" id="Detailtxt_PONumber"
									value="" size="25" style="width:230px;" title="Ingrese PO Number"  required>         
								</div>
						   </div>
						   <div class="col-lg-4" style="width:270px">
								<div class="form-group"  >
									 <label>PO Position: </label>
									 <br />
									<input type="number" class="form-control" placeholder="PO Position" name="Detailtxt_POPosition" id="Detailtxt_POPosition"
									value="" size="25" style="width:230px;" title="Ingrese PO Position" required >         
								</div>
						   </div>
						   </div>
						   <div class="row">
								<div class="col-lg-4" style="width:270px" >
									<div class="form-group"  >
									 <label>Product ID: </label>
									 <br />
									<input class="form-control" placeholder="Product ID" name="Detailtxt_Product_id" id="Detailtxt_Product_id"
									value="" size="25" style="width:230px;" title="Ingrese Product ID" required>         
									</div>
							   </div>
							   <div class="col-lg-4" style="width:270px" >
									<div class="form-group"  >
									 <label>Product Description: </label>
									 <br />
									<input class="form-control" placeholder="Product Desciption" name="Detailtxt_ProductDesciption" id="Detailtxt_ProductDesciption"
									value="" size="25" style="width:230px;" title="Ingrese Product Desciption" required>         
									</div>
							   </div>
						   </div>
						   <div class="row">
								<div class="col-lg-4" style="width:270px" >
								<div class="form-group"  >
									 <label>Product Measure: </label>
									 <br />
									<input class="form-control" placeholder="Product Measure" name="Detailtxt_ProductMeasure" id="Detailtxt_ProductMeasure"
									value="" size="25" style="width:230px;" title="Ingrese Product Measure" required>         
								</div>
							   </div>
							   <div class="col-lg-4" style="width:270px">
									<div class="form-group"  >
									 <label>Product Quantity: </label>
									 <br />
									<input type="number" class="form-control" placeholder="Product Quantity" name="Detailtxt_ProductQuantity" id="Detailtxt_ProductQuantity"
									value="" size="25" style="width:230px;" title="INgrese Product Quantity" required>         
									</div>
							   </div>	   
						   </div>
						   <div class="row">
								 <div class="col-lg-4" style="width:270px">
									<div class="form-group"  >
									 <label>Product Price: </label>
									 <br />
									<input type="number" step="any" class="form-control" placeholder="Product Price" name="Detailtxt_ProductPrice" id="Detailtxt_ProductPrice"
									value="" size="25" style="width:230px;" title="Ingrese Product Price" required>         
									</div>
							   </div>	
								<div class="col-lg-4" style="width:270px" >
								<input type="hidden" id="DetailPaisOrigen" name="DetailPaisOrigen">
								<div class="form-group" id="divComboBoxPaises" title="Seleccione un Pais">  
								<br />
								<img src="../images/loadingAnimation.gif">
								</div>
								</div> 
						   </div>
						   <div class="row">
						   <div class="col-lg-4" style="width:300px; float:right; margin-top:40px">
								<div class="form-group">
<?
								$btn_primary	= new templates("templates/boton.html");
								$btn_primary->setParams( array (
												'text'=>'Cancelar',
												'id' => 'btn_Cancelar',
												'icon' => 'glyphicon glyphicon-floppy-remove',
												'type' => 'default',
												'estilo'=> '',
												'more'=>'data-dismiss="modal"',
												'btn_type'=>'button'
												));
								$btn_primary->show();
								
								$btn_primary	= new templates("templates/boton.html");
								$btn_primary->setParams( array (
												'text'=>'Grabar Detalle',
												'id' => 'btn_grabarDetalle',
												'icon' => 'glyphicon glyphicon-floppy-saved',
												'type' => 'primary',
												'estilo'=> '',
												'more'=>'',
												'btn_type'=>'submit'
												));
								$btn_primary->show();
								
								$btn_primary	= new templates("templates/boton.html");
								$btn_primary->setParams( array (
												'text'=>'Insertar Detalle',
												'id' => 'btn_insertarDetalle',
												'icon' => 'glyphicon glyphicon-floppy-saved',
												'type' => 'primary',
												'estilo'=> 'style="display:none"',
												'more'=>'',
												'btn_type'=>'submit'
												));
								$btn_primary->show();
?>  
								<!--<button type="submit" class="btn btn-primary" id="btn_insertarDetalle" name="btn_insertarDetalle" style="display:none">Insertar Detalle</button> 
								<button type="submit" class="btn btn-primary" id="btn_grabarDetalle" name="btn_grabarDetalle">Grabar Detalle</button> 
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<button type="button" class="btn btn-default" data-dismiss="modal" id="btn_Cancelar">Cancelar</button>			
								-->
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
			</form>

    </div>
    </form>
	</div>
	<form name="FormOC" id="FormOC" method="post" action="#" onSubmit="return false;">
		<div class="modal fade" id="modalOC" tabindex="-1" role="dialog" aria-labelledby="modalOC" aria-hidden="true"    data-backdrop="static" data-keyboard="false" >
            	<div class="modal-dialog">
                	<div class="modal-content" style="width:1000px; margin-left:-200px;">
                  		<div class="modal-header">
                    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    		<h4 class="modal-title" id="myModalLabel">Agregar OC</h4>
                  		</div>
                  		<div class="modal-body">
                    		<div class="panel panel-default">
                                <div class="panel-heading">
							<div class="row">								   
								<div class="col-lg-1" style="width:60px">
								<br />
								<label>OC: </label> 
								</div>	
								<div class="col-lg-3" style="float:left; margin-top:10px">						
								<input type="text" class="form-control" placeholder="OC" name="ADDOC_txtNumOC" id="ADDOC_txtNumOC"
								value="" size="25" maxlength="10" title="Ingrese OC" required><label>&nbsp;</label> 
								</div>
								<div class="col-lg-3" style="float:left; margin-top:10px">
								<img src="../images/loadingAnimation.gif" id="CargandoBuscarOC" style="display:none"  >
<?
								$btn_primary	= new templates("templates/boton.html");
								$btn_primary->setParams( array (
												'text'=>'Buscar OC',
												'id' => 'ADDOC_btnBuscarOC',
												'icon' => 'glyphicon glyphicon-search',
												'type' => 'primary',
												'estilo'=> '',
												'more'=>'',
												'btn_type'=>'submit'
												));
								$btn_primary->show();

?>
								<!--<button type="submit" class="btn btn-primary" id="ADDOC_btnBuscarOC" name="ADDOC_btnBuscarOC">Buscar OC</button> -->
								</div>
														
						   </div>
						   <br /><br />
						   <div class="table-responsive" style="border: 1px solid #DDDDDD;background-color:#FFF">						  
						   <table id="tabla_OC" class="display" cellspacing="0" width="100%">
                                <thead><tr><th>X</th><th>PO Position</th><th>Product ID</th><th>Product Description</th>
								<th>Product Measure</th><th>Product Quantity</th><th>Porduct Price</th><th>Pais de Origen</th><th>PO Number</th></tr></thead>
                                <tfoot><tr><th>X</th><th>PO Position</th><th>Product ID</th><th>Product Description</th>
								<th>Product Measure</th><th>Product Quantity</th><th>Porduct Price</th><th>Pais de Origen</th><th>PO Number‏</th></tr></tfoot>
                               <tbody id=tdBodyOC>
                               </tbody>
							</table>
						   </div>
						   <div class="row">
						   <div class="col-lg-4" style="width:240px; float:right; margin-top:40px">
								<div class="form-group">
<?
								$btn_primary	= new templates("templates/boton.html");
								$btn_primary->setParams( array (
												'text'=>'Finalizar',
												'id' => 'btn_CancelarDetOC',
												'icon' => 'glyphicon glyphicon-saved',
												'type' => 'default',
												'estilo'=> '',
												'more'=>'data-dismiss="modal"',
												'btn_type'=>'button'
												));
								$btn_primary->show();
	
								$btn_primary	= new templates("templates/boton.html");
								$btn_primary->setParams( array (
												'text'=>'Agregar',
												'id' => 'btn_agregarDetalleOC',
												'icon' => 'glyphicon glyphicon-save',
												'type' => 'primary',
												'estilo'=> '',
												'more'=>'',
												'btn_type'=>'submit'
												));
								$btn_primary->show();

?>								
								<!--<button type="submit" class="btn btn-primary" id="btn_agregarDetalleOC" name="btn_agregarDetalleOC">Agregar</button> 
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<button type="button" class="btn btn-default" data-dismiss="modal" id="btn_CancelarDetOC" name="btn_CancelarDetOC">Finalizar</button>			
								-->
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
		</form>
		
	<!-- INICIO MODAL CARGA DETALLE -->
	<form action="ajax/subir_archivo.php" class="formularios" method="post" id="formSubirFactura" name="formSubirFactura" enctype="multipart/form-data" target="contenedor_subir_archivo">
		<div class="modal fade" id="modalCargaDetalle" tabindex="-1" role="dialog" aria-labelledby="modalCargaDetalle" aria-hidden="true"    data-backdrop="static" data-keyboard="false" >
			<div class="modal-dialog">
				<div class="modal-content" >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    	<h4 class="modal-title" id="myModalLabel">Cargar Detalle OC.</h4>
                  	</div>
                  	
                  	<div class="modal-body">
						<div class="panel panel-default">

							<div class="panel-heading">
								<div class="row">								   
									<div class="col-lg-2" style="width:115px;" >
										<h5>Archivo CSV: </h5> 
									</div>	
									
									<div class="col-lg-3" style="float:left; margin-top:10px">						
										<input type="file" name="archivo" id="archivo" required="required" />
									</div>					
								</div>
								<br />
								
								<div class="row">
									<div class="col-lg-4" style="width:240px; float:right; margin-top:40px">
										<div class="form-group" id="divCargarCSV">
										<img src="../images/loadingAnimation.gif" style="height:15px; display:inline;width: 90%;display:none" id="ImagenCargando" name="ImagenCargando"/>
<?											
											$btn_primary	= new templates("templates/boton.html");
											$btn_primary->setParams( array (
															'text'=>'Cancelar',
															'id' => 'btn_CancelarCsv',
															'icon' => 'glyphicon glyphicon-floppy-remove',
															'type' => 'default',
															'estilo'=> '',
															'more'=>'data-dismiss="modal"',
															'btn_type'=>'button'
															));
											$btn_primary->show();
				
											$btn_primary	= new templates("templates/boton.html");
											$btn_primary->setParams( array (
															'text'=>'Cargar',
															'id' => 'boton_subir_archivo',
															'icon' => 'glyphicon glyphicon-save',
															'type' => 'primary',
															'estilo'=> '',
															'more'=>'',
															'btn_type'=>'submit'
															));
											$btn_primary->show();
?>								
										</div>
									</div>	
								</div>
									<label>Log: </label>
									<div  id="respuesta" class="panel panel-default" style="overflow:auto;min-height:120px">
									</div>									
									<iframe width="1" height="1" frameborder="0" name="contenedor_subir_archivo" style="display:none"></iframe>
						   </div>
						</div>						
					</div>
				</div>
			</div>
		</div>
		<input type="submit" id="btnValidarFormulario" name="btnValidarFormulario" style="display:none"/>
		<input type="hidden" id="idUsuario" name="idUsuario" value="<?php echo $_SESSION["email"]; ?>" />
	</form>
	<!-- FIN MODAL CARGA DETALLE -->
	
	<!-- INICIO MODAL CARGA DETALLE XSL-->
	<form action="ajax/subir_archivoExcel.php" class="formularios" method="post" id="formSubirXLS" name="formSubirXLS" enctype="multipart/form-data" target="contenedor_subir_archivoXLS">
		<div class="modal fade" id="modalCargarXLS" tabindex="-1" role="dialog" aria-labelledby="modalCargarXLS" aria-hidden="true" data-backdrop="static" data-keyboard="false" >
			<div class="modal-dialog">
				<div class="modal-content" >
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    	<h4 class="modal-title" id="myModalLabel">Cargar Archivo Excel.</h4>
                  	</div>
                  	
                  	<div class="modal-body">
						<div class="panel panel-default">

							<div class="panel-heading">
								<div class="row">								   
									<div class="col-lg-2" style="width:115px;" >
										<h5>Archivo Excel: </h5> 
									</div>	
									
									<div class="col-lg-3" style="float:left; margin-top:10px">						
										<input type="file" name="archivoXLS" id="archivoXLS" required="required" />
									</div>					
								</div>
								<br />
								
								<div class="row">
									<div class="col-lg-4" style="width:240px; float:right; margin-top:40px">
										<div class="form-group" id="divCargarXLS">
										<img src="../images/loadingAnimation.gif" style="height:15px; display:inline;width: 90%;display:none" id="ImagenCargandoXLS" name="ImagenCargandoXLS"/>
<?											
											$btn_primary	= new templates("templates/boton.html");
											$btn_primary->setParams( array (
															'text'=>'Cancelar',
															'id' => 'btn_CancelarXLS',
															'icon' => 'glyphicon glyphicon-floppy-remove',
															'type' => 'default',
															'estilo'=> '',
															'more'=>'data-dismiss="modal"',
															'btn_type'=>'button'
															));
											$btn_primary->show();
				
											$btn_primary	= new templates("templates/boton.html");
											$btn_primary->setParams( array (
															'text'=>'Cargar',
															'id' => 'boton_subir_archivoXLS',
															'icon' => 'glyphicon glyphicon-save',
															'type' => 'primary',
															'estilo'=> '',
															'more'=>'',
															'btn_type'=>'submit'
															));
											$btn_primary->show();
?>								
										</div>
									</div>	
								</div>
									<label>Log: </label>
									<div  id="respuestaXLS" class="panel panel-default" style="overflow:auto;min-height:120px">
									</div>									
									<iframe width="1" height="1" frameborder="0" name="contenedor_subir_archivoXLS" style="display:none"></iframe>
						   </div>
						</div>						
					</div>
				</div>
			</div>
		</div>
		<input type="submit" id="btnValidarFormularioXLS" name="btnValidarFormularioXLS" style="display:none"/>
		<input type="hidden" id="idUsuario" name="idUsuario" value="<?php echo $_SESSION["email"]; ?>" />
	</form>
	<!-- FIN MODAL CARGA DETALLE -->
	<!-- hidden -->
    <div id="divhidden">
	<div id="div_proveedoresHidden" style="display:none"></div>
	<input type="hidden" id="txtIdProveedor" name="txtIdProveedor" value=""/>
	<input type="hidden" id="txtIdProveedor_old" name="txtIdProveedor_old" value=""/>
	<input type="hidden" id="txtTextoProveedor_old" name="txtTextoProveedor_old" value=""/>
    <input type="hidden" id="H_accionPagina" name="H_accionPagina" value=""/>
	<input type="hidden" id="H_ocBuscada" name="H_ocBuscada" value=""/>
	<input type="hidden" id="H_OldPosition" name="H_OldPosition" value=""/>
	<input type="hidden" id="H_OldEE" name="H_OldEE" value=""/>
	<input type="hidden" id="H_Tolerancia" name="H_Tolerancia" value=""/>
	<form action="ajax/descargarDetalle810.php" class="formularios" method="post" id="formDescDet810" name="formDescDet810" target="contenedor_descargar" style="display:none">
		<input type="hidden" id="Desc_jsonDetalle" name="Desc_jsonDetalle" />
		<input type="hidden" id="Desc_invoiceNumber" name="Desc_invoiceNumber" />
	</form>
	<iframe width="1" height="1" frameborder="0" name="contenedor_descargar" style="display: none"></iframe>
    </div>
    <!-- #hidden -->
    
    

