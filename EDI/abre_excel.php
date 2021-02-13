<?php
header('Content-Type: application/force-download');  
header('Content-disposition: attachment; filename=export.xls');  
// Fix for crappy IE bug in download.  
header("Pragma: ");  
header("Cache-Control: "); 
echo utf8_decode($_POST['datos_a_enviar']);
?>