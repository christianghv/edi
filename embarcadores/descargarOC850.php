<!DOCTYPE html>
<?
	require '../clases/class.templates.php';
	session_start();
	if (!isset($_SESSION["email"]) && !isset($_SESSION["id_embarcador"])){
		header('Location:../index.php');
	}

	//Verificar Permisos
	include_once("../ajax/ValPemi.php");
	$AccesoPermiso=VerificarPermiso(12);
	
	if($AccesoPermiso==false)
	{
		//header('Location: ../internos.php');
		if (isset($_SESSION["id_embarcador"]))
		{
			
			$idEmbarcador=$_SESSION["id_embarcador"];
			$veridicarPro=VerificarSiEsEmbarcador($idEmbarcador);
			
			if($veridicarPro==false)
			{
				header('Location:../index.php');
			}
		}
		else
		{
			header('Location:../index.php');
		}		
	}
	

?>
<html>

<head>  
    <title>DESCARGAR OC 850</title>
    <?php include("includes.php"); ?> 
	<script src="../EDI/js/funciones.js" type="text/javascript"  charset="UTF-8"></script>
    <script src="js/descargarOC850.js"></script>
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
	#tabla_oc th { text-align: center; }
	#tabla_oc td { text-align: center; }
	</style>
</head>

<body id="bodyDescargarOC">	
<a href="../EDI/iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>
<iframe id="secretIFrame" src="" style="display:none; visibility:hidden;"></iframe>
<div id="pagina_principal" style="display:block;"><!--DIV PAGINA PRINCIPAL-->
	<div id="page_header_principal">
		<div id="wrapper_principal">

        <?
			$template = new templates("templates/barra_nav.html");
			$template->setParams( array (
			'reporte' => "DESCARGAR OC 850",
			'hrefTitle'=>"href='descargarOC850.php'"
			));
			$template->show();
			#FILTROS 
			$tmp_fecha = new templates("templates/f_fecha.html");
			$fini = strtotime ( '-1 month' , strtotime ( date('Y-m-d') ) ) ;
			$fini = date ( 'd/m/Y' , $fini );
		?>
		
        <!--<div id="page-wrapper">-->
			<br>
  			<!-- TABLA -->
			<form action="#" class="formularios" method="post" id="formDescargarArchivo" name="formDescargarArchivo" onSubmit="return false;">
            <div class="row" style="margin-left:10px; margin-right:10px;">
                <div class="col-lg-12">
					<div align="left">
						<div class="col-lg-3">
						   <div class="form-group" id="sociedades" title="Seleccione sociedad">
                           <br />
						   <img src="../images/loadingAnimation.gif" style="height:15px; display:inline;" id="ImagenCargando" name="ImagenCargando"/>
							</div>
						</div>
						<div class="col-lg-3">
						   <div class="form-group" id="proveedores">
                           <br />
						   <img src="../images/loadingAnimation.gif" style="height:15px; display:inline;" id="ImagenCargando" name="ImagenCargando"/>
							</div>
						</div>
						<?php
							$tmp_fecha->setParams( array (
								'label'=>'Fecha Inicio',
								'id' => 'fecha_inicio',
								'title' => 'Seleccione fecha de inicio',
								'valor' => $fini
								));
							$tmp_fecha->show();
							$tmp_fecha->setParams( array (
								'label'=>'Fecha Termino',
								'id' => 'fecha_termino',
								'title' => 'Seleccione fecha de termino',
								'valor' => date("d/m/Y")
								));
							$tmp_fecha->show();
						?>
						<div class="col-lg-2">
							<div class="form-group">
							<label>Nro. OC: </label>
							<input id="nro_factura" class="form-control" name="linea_1" placeholder="Nro. OC" req data-hasqtip="2" title="Ingrese Nro. OC" aria-describedby="qtip-2">
							</div>
						</div>
						
					</div>
					<br /><br /><br /><br />
					<div class="row" style="margin-left:0px; margin-right:10px;margin-bottom:10px">
					<center><img src="../images/loadingAnimation.gif" style="height:15px; display:none" id="ImagenCargando" name="ImagenCargando"/>
					</center>
						<div class="col-lg-1">
							<button type="button" id="boton_descargar_archivo" name="boton_descargar_archivo" class="btn btn-primary" {more}="" {estilo}="">
							<span class="glyphicon glyphicon-search"></span>
							Buscar
							</button>
						</div>
					</div>
					<br />
                    <div class="panel panel-default">
                       <!-- <div class="panel-heading" >
					   </div>-->
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" id="respuesta">
								<table id="tabla_oc" class="display table-condensed" cellspacing="0" width="100%">
								<thead><tr>
								<th>N° OC</th><th>Fecha</th><th>Monto</th><th>&nbsp;&nbsp;</th>
								</tr></thead>
								<tfoot><tr>
								<th>N° OC</th><th>Fecha</th><th>Monto</th><th>&nbsp;&nbsp;</th>
								</tr></tfoot>
								<tbody></tbody>
								
								</table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div><!-- /.panel -->
                </div>
            </div><!-- FIN TABLA -->  
			<input type="submit" id="btnValidarFormulario" name="btnValidarFormulario" style="display:none"/>
        </form>
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
<!-- #Modal -->
<!-- HIDDEN -->
<input type="hidden" id="H_OldIdProveedor" name="H_OldIdProveedor">
<input type="hidden" id="txtIdProveedor" name="txtIdProveedor">
<button type="button" class="btn btn-primary" id="btn_AbrirModalProveedor" name="btn_AbrirModalProveedor" data-toggle="modal" data-target="#modalAgregarProveedor" style="display:none">
<!-- #HIDDEN -->
<!--////// TOOLTIP ///////////////////////////////////////////////-->
    <script src="../js/jquery.metisMenu.js"></script>

</body>

</html>
