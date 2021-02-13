<?
	require '../clases/class.templates.php';
	session_start();
	if (!isset($_SESSION["email"]) && !isset($_SESSION["id_embarcador"])){
		header('Location:../index.php');
	}

	//Verificar Permisos
	include_once("../ajax/ValPemi.php");
	$AccesoPermiso=VerificarPermiso(13);
	
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
<!DOCTYPE html>
<html>

<head>
    <title>DESCARGAR OC 856</title>
    <?php include("includes.php"); ?>
    <script src="../EDI/js/funciones.js"></script>
    <script src="js/descargarOC856.js"></script>

	<script>
		window.onbeforeunload = function(e) {
		  return "Esta a punto de abandonar DESCARGAR OC 856. Para regresar presione botón 'Volver'";
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
	</style>
</head>
<body id="bodyDescargarOC856">
<a href="../EDI/iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>
<iframe id="secretIFrame" src="" style="display:none; visibility:hidden;"></iframe>
<div id="div_detalle" style="display:none"></div>
	<div id="wrapper">
<?
	$template = new templates("templates/barra_nav.html");
	$template->setParams( array (
	'reporte' => "DESCARGAR OC 856"
	));
	$template->show();
?>
            <div class="row" style="margin-left:10px; margin-right:10px; margin-top:15px;">
				<form name="frm_filtros" id="frm_filtros" method="post" action="" onSubmit="return false;">
					<input type="submit" id="submit_filtros" style="display:none"/>
<?
				#FILTROS 
				$tmp_soc = new templates("templates/f_sociedad.html");
				$tmp_soc->show();
				$tmp_f_oc = new templates("templates/f_oc.html");
				$tmp_f_oc->show();
				$tmp_fac = new templates("templates/f_factura.html");
				$tmp_fac->show();
				$tmp_fecha = new templates("templates/f_fecha.html");
				
				$fini = strtotime ( '-1 month' , strtotime ( date('Y-m-d') ) ) ;
				$fini = date ( 'd/m/Y' , $fini );

				$tmp_fecha->setParams( array (
								'label'	=>'Fecha Inicio',
								'id' 	=> 'fecha1_principal',
								'valor' => $fini,
								'title' => 'Seleccione fecha de inicio'
								));
				$tmp_fecha->show();
				
				$tmp_fecha->setParams( array (
								'label'	=>'Fecha Término',
								'id' 	=> 'fecha2_principal',
								'valor' => date("d/m/Y"),
								'title' => 'Seleccione fecha de termino'
								));
				$tmp_fecha->show();
?>
				
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
				</form>
			</div>
  			<!-- TABLA -->
            <div class="row" style="margin-left:10px; margin-right:10px; margin-top:10px;">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" id="div_table_data"></div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div><!-- /.panel -->
                </div>
            </div><!-- FIN TABLA -->
</div><!-- /#page-wrapper -->
<br /><br />
<div style="margin-left:25px; margin-top:-5px;" class="row" id="Divbtn_volverHome">
		<div align="left">
    		<button name="btn_volver" id="btn_volver" class="btn btn-info" type="button" title="Volver a Menú Principal"> 
			<span class="glyphicon glyphicon-home"></span>&nbsp;Volver</button>               
		</div>
</div>
<!--------------------------------------------------------------------->
    <script src="../js/jquery.metisMenu.js"></script>

</body>

</html>
