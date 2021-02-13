	<!DOCTYPE html>
<?
require '../clases/class.templates.php';
session_start();
if (!isset($_SESSION["email"]) && !isset($_SESSION["id_embarcador"])){
		header('Location:../index.php');
	}

	//Verificar Permisos
	include_once("../ajax/ValPemi.php");
	$AccesoPermiso=VerificarPermiso(14);
	
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
    <title>CARGA MANUAL EMBARCADOR</title>
   <?php include("includes.php"); ?>
    <script src="js/carga_manual.js"></script>	
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
	#tabla_proveedores th { text-align: center }
	#tabla_proveedores td { text-align: center }
	</style>
</head>

<body id="bodyDescargarOC">	
<a href="iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>
<div id="pagina_principal" style="display:block;"><!--DIV PAGINA PRINCIPAL-->
	<div id="page_header_principal">
		<div id="wrapper_principal">

        <?
			$template = new templates("templates/barra_nav.html");
			$template->setParams( array (
			'reporte' => "CARGAR ARCHIVO EMBARCADOR",
			'hrefTitle'=>"href='facturas.php'"
			));
			$template->show();
		?>
		
        <!--<div id="page-wrapper">-->
			<br>
  			<!-- TABLA  -->
			<!--  -->
			<div class="row">
				<form action="ajax/subir_archivo.php" method="post" id="formSubirExcel" name="formSubirExcel" enctype="multipart/form-data" target="contenedor_subir_archivo">
				<input type="submit" id="btn_FormCargaMasiva" name="btn_FormCargaMasiva" value="validarCabecera" style="display:none">
				<div class="col-lg-4">
					<input type="hidden" id="accion_subida" name="accion_subida" value="SUBIR_CARGA_MANUAL"/>
					<input type="hidden" id="validacion_confirmacion" name="validacion_confirmacion" value="Nok"/>
					<input type="submit" id="btnValidarFormulario" name="btnValidarFormulario" style="display:none"/>
					<div align="left">
						<div class="col-lg-3">	
							<label for="archivo" style="margin-bottom:5px;" >(*) Archivo: </label>
						   <input type="file" id="archivo" name="archivo" required="required" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
						</div>
					</div>					
					<br />
				</div>
				</form>
				<div class="col-lg-1" style="padding-top: 15px;padding-bottom: 15px;">
					<button type="button" id="boton_subir_archivo" name="boton_subir_archivo" class="btn btn-primary" {more}="" {estilo}="">
						<span class="glyphicon glyphicon-import"></span>&nbsp;Cargar 
					</button>
				</div>
				<div class="col-lg-2" style="padding-top: 15px;padding-bottom: 15px;">
					<label><input type="checkbox" id="chk_confirmacion"> Confirmo que deseo registrar los bultos informados por embarcador en la base de datos</label>
				</div>
				<div class="col-lg-1" style="padding-top: 15px;padding-bottom: 15px;">
					<button type="button" id="boton_limpiar_carga_manual" name="boton_limpiar_carga_manual" class="btn btn-info" {more}="" {estilo}="">
						<span class="glyphicon glyphicon-repeat"></span>&nbsp;Limpiar 
					</button>
				</div>
			</div>
			<div class="row">
				<br /><br />
				<div class="row" style="margin-left:0px; margin-right:10px;margin-bottom:10px">
					<center>
						<img src="../images/loadingAnimation.gif" style="height:15px; display:none" id="ImagenCargando" name="ImagenCargando"/>
					</center>
				</div>
			</div>
            <div class="row" style="margin-left:10px; margin-right:10px;">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                       <!-- <div class="panel-heading" >
					   </div>-->
                        <!-- /.panel-heading -->
                        <div class="panel-body" style="height:350px;overflow:auto">
                            <div class="table-responsive" id="respuesta">                       
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div><!-- /.panel -->
                </div>
            </div><!-- FIN TABLA -->  
		<iframe width="0" height="0" frameborder="0" name="contenedor_subir_archivo" style="display: none"></iframe>
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
<!-- #HIDDEN -->
<!--////// TOOLTIP ///////////////////////////////////////////////-->
    <script src="../js/jquery.metisMenu.js"></script>

</body>

</html>
