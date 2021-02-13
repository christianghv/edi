<?
session_start();
$acceso = false;

$email = $_SESSION['email'];
$perfil = $_SESSION['perfil'];

if (isset($email) && $perfil == 1)
	$acceso = true;
if (!isset($_SESSION["email"])){
    header('Location:../index.php');
	}

if (!$acceso)
	header('Location: ../index.php');
	
		require '../clases/class.templates.php';
		
	//Verificar Permisos
	include_once("../ajax/ValPemi.php");
	$AccesoPermiso=VerificarPermiso(1);
	
	if($AccesoPermiso==false)
	{
		header('Location: ../internos.php');
	}
	
?>
<!DOCTYPE html>
<html>

<head>  
    <title>USUARIOS</title>
    <?php include("includes.php"); ?> 
	<script src="../EDI/js/funciones.js" type="text/javascript"  charset="UTF-8"></script>
    <script src="js/usuarios.js"></script>
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
	#tabla_usuarios th { text-align: center; }
	#tabla_usuarios td { text-align: center; }
	</style>
</head>

<body>	
<a href="../EDI/iframe.html?placeValuesBeforeTB_=savedValues&TB_iframe=true&height=190&width=160&modal=true" title="add a caption to title attribute / or leave blank" class="thickbox" id="AbrirCargando" style="display:none">AbrirCargando</a>
<div id="pagina_principal" style="display:block;"><!--DIV PAGINA PRINCIPAL-->
	<div id="page_header_principal">
		<div id="wrapper_principal">

        <?
		
			$template = new templates("templates/barra_nav.html");
			$template->setParams( array (
			'reporte' => "ADMINISTRACIÓN  USUARIOS"
			));
			$template->show();
			
		?>
		
        <!--<div id="page-wrapper">-->
			<br>
  			<!-- TABLA -->
            <div class="row" style="margin-left:10px; margin-right:10px;">
                <div class="col-lg-12">
                    <div class="panel panel-default">
						
                        <div class="panel-heading" >
                            <div align="left">
								<button type="button" class="btn btn-primary" id="btn_agregarUsuario" name="btn_agregarUsuario" data-toggle="modal" data-target="#modalAgregarUsuario">
									<span class="glyphicon glyphicon-plus"></span> &nbsp;Agregar
								</button>
								<button type="button" class="btn btn-danger" id="btn_EliminarUsuario" name="btn_EliminarUsuario">
									<span class="glyphicon glyphicon-trash"></span> &nbsp;Eliminar
								</button>
							</div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive" >
                                <table class="display" cellspacing="0" width="100%" id="tabla_usuarios" name="tabla_usuarios">
                                    <thead>
                                        <tr>
                                            <th id="thEmail">E-mail</th>
                                            <th>Nombre</th>
                                            <th>Perfil</th>
											<th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyUsuarios">
                                    
                                    </tbody>
                                    <tfoot id="tdFootUsuarios">
										<tr>
                                            <th>E-mail</th>
                                            <th>Nombre</th>
                                            <th>Perfil</th>
											<th>&nbsp;</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div><!-- /.panel -->
                </div>
            </div><!-- FIN TABLA -->       
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
			<form name="FormIngresarUsuario" id="FormEditarDetalle" method="post" action="" onSubmit="return false;">
            <!-- Modal -->
            <div class="modal fade" id="modalAgregarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true"    data-backdrop="static" 
   data-keyboard="false" >
            	<div class="modal-dialog modal-lg">
                	<div class="modal-content">
                  		<div class="modal-header">
                    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    		<h4 class="modal-title" id="modalAgregarUsuarioLabel">Agregar Usuario</h4>
                  		</div>
                  		<div class="modal-body">
                    		<div class="panel panel-default">
                                <div class="panel-heading">
							<div class="row">
								<div class="col-lg-4" style="width:270px" >
								<div class="form-group" >
								 <label>E-mail: </label>
								 <br />
								<input type="email" class="form-control" placeholder="E-mail" name="Usuario_email" id="Usuario_email"
								value="" size="25" style="width:230px;" required>         
								</div>
								</div>
								
								<div class="col-lg-4" style="width:270px">
								<div class="form-group" >
								 <label>Nombre: </label>
								 <br />
								<input class="form-control" placeholder="Nombre" name="Usuario_Nombre" id="Usuario_Nombre" value="" style="width:230px;" required>    
								</div>
								</div>
								<div class="col-lg-4" style="width:270px" >
									<div class="form-group" id="divCboPerfil">
										 <label>Perfil: </label>
										 <br />
										<input type="number" class="form-control" placeholder="Perfil" name="Usuario_Perfil" id="Usuario_Perfil"
										value="" size="25" style="width:230px;"  required="required">         
									</div>
								</div>								
						   </div>
						   <div class="row">
							<div class="col-lg-4" style="float:left; margin-top:40px; margin-left:40px">
								<button type="button" class="btn btn-primary" id="btn_Permisos" name="Permisos" >Permisos</button>
							</div>
							<div class="col-lg-3" style="width:300px; float:right; margin-top:40px">
								<div class="form-group">
									<button type="button" class="btn btn-default" data-dismiss="modal" id="btn_Cancelar">Cancelar</button>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<button type="submit" class="btn btn-primary" id="btn_insertarUsuario" name="btn_insertarUsuario" >Insertar Usuario</button> 
									<button type="submit" class="btn btn-primary" id="btn_grabarUsuario" name="btn_grabarUsuario">Grabar Usuario</button> 									
								</div>
							</div>	
						   </div>
						   <div id="divPermisos" style="display:none">
						   <input type="hidden" id="estadoPermisos" value="0"/>
						   <ul id="myTab" class="nav nav-tabs">
							   <li class="active">
								  <a href="#Per_Admini" id="tab_admin" data-toggle="tab">ADMINISTRACIÓN</a>
							   </li>
							   <li>
								  <a href="#Per_Edi" id="tab_edi" data-toggle="tab">EDI</a>
							   </li>
							   <li>
								  <a href="#Per_Prov" id="tab_prov" data-toggle="tab">PROVEEDORES</a>
							   </li>
							   <li>
								  <a href="#Per_Embar" id="tab_embar" data-toggle="tab">EMBARCADORES</a>
							   </li>
							   <li>
								  <a href="#Per_Sociedades" id="tab_sociedades" data-toggle="tab">SOCIEDADES</a>
							   </li>
							</ul>
							
							<div id="ProveedoresTab" class="tab-content">
							   <!-- Tab ADMINISTRACIÓN-->							   
							   <div class="tab-pane fade in active contenedorTab" id="Per_Admini">
							   <div class="table-responsive" style="border: 1px solid #DDDDDD;background-color:#FFF">						  
								   <table id="tbl_PerAdmin" class="display" cellspacing="0" width="100%">
										<thead><tr><th>Permiso</th><th>&nbsp&nbsp&nbsp </th></tr>
										</thead>
										<tbody id=tBodyPerAdmin>
										</tbody>
									</table>
							   </div>
							   <input type="submit" id="btn_Validar810" name="btn_Validar810" value="Validar810" style="display:none">
							   </div>							   
							   <!-- #Tab ADMINISTRACIÓN-->
							   <!-- Tab EDI-->
							   <div class="tab-pane fade contenedorTab" id="Per_Edi">
							   	<div class="table-responsive" style="border: 1px solid #DDDDDD;background-color:#FFF">						  
								   <table id="tbl_PerEdi" class="display" cellspacing="0" width="100%">
										<thead><tr><th>Permiso</th><th>&nbsp&nbsp&nbsp </th></tr>
										</thead>
										<tbody id=tBodyPerEdi>
										</tbody>
									</table>
							    </div>
							   </div>
							   <!-- #Tab EDI-->
							   <!-- Tab PROVEEDORES-->
							   <div class="tab-pane fade contenedorTab" id="Per_Prov">
							   	<div class="table-responsive" style="border: 1px solid #DDDDDD;background-color:#FFF">						  
								   <table id="tbl_PerProv" class="display" cellspacing="0" width="100%">
										<thead><tr><th>Permiso</th><th>&nbsp&nbsp&nbsp </th></tr>
										</thead>
										<tbody id=tBodyPerProv>
										</tbody>
									</table>
							    </div>
							   </div>
							   <!-- #Tab PROVEEDORES-->
							   <!-- Tab EMBARCADORES-->
							   <div class="tab-pane fade contenedorTab" id="Per_Embar">
							   	<div class="table-responsive" style="border: 1px solid #DDDDDD;background-color:#FFF">						  
								   <table id="tbl_PerEmba" class="display" cellspacing="0" width="100%">
										<thead><tr><th>Permiso</th><th>&nbsp&nbsp&nbsp </th></tr>
										</thead>
										<tbody id=tBodyPerEmbar>
										</tbody>
									</table>
							    </div>
							   </div>
							   <!-- #Tab EMBARCADORES-->
							   <!-- Tab SOCIEDADES-->
							   <div class="tab-pane fade contenedorTab" id="Per_Sociedades">
							   	<div class="table-responsive" style="border: 1px solid #DDDDDD;background-color:#FFF">						  
								   <table id="tbl_PerSociedades" class="display" cellspacing="0" width="100%">
										<thead><tr><th>Sociedad</th><th>&nbsp&nbsp&nbsp </th></tr>
										</thead>
										<tbody id=tBodyPerSociedades>
										</tbody>
									</table>
							    </div>
							   </div>
							   <!-- #Tab SOCIEDADES-->
							</div>
							
						   </div><!-- DIV PERMISOS -->
						   </div><!-- DIV Panel Healding -->
                                </div>

                                <!-- /.panel-heading -->
                                <!-- /.panel-body -->
                            </div>
                            <!-- /.panel -->
                  		</div>
                	</div>
            </div>
			</form>
<!-- #Modal -->
<!-- HIDDEN -->
<input type="hidden" id="H_OldUssEmail" name="H_OldUssEmail">
<button type="button" class="btn btn-primary" id="btn_AbrirModalUsuario" name="btn_AbrirModalUsuario" data-toggle="modal" data-target="#modalAgregarUsuario" style="display:none">
<!-- #HIDDEN -->
<!--////// TOOLTIP ///////////////////////////////////////////////-->
    <script src="../js/jquery.metisMenu.js"></script>

</body>
</html>
