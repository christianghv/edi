<?
$return = array('ok'=>true);
$upload_folder 	= 'detalle_factura/';
$nombre_archivo = $_FILES['archivo']['name'];
$tipo_archivo 	= $_FILES['archivo']['type'];
$tamano_archivo = $_FILES['archivo']['size'];
$tmp_archivo 	= $_FILES['archivo']['tmp_name'];
$archivador 	= $upload_folder.$nombre_archivo;
////////////////////////////////////////////////////////////////////////
if( file_exists($archivador) )
{
	borraExistente($archivador);	
}
	
if (move_uploaded_file($tmp_archivo, $archivador)) 
{

}

function borraExistente($archivo)
{
	$result = false;
	if(unlink($archivo)) 
		$result = true;

	return $result;
}

?>
