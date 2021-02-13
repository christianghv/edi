<?php
header("Pragma: public");
header("Expires: 0");
$filename = "roles.xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//	echo $datos;		
?>

<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#000000">
  <tr>
    <td width="23%"><strong>Usuario</strong></td>
    <td width="77%"><strong>Rol</strong></td>
  </tr>
<?php 
$datos = str_replace("-00000-","<tr><td>",$datos);
$datos = str_replace("-11111-","</td><td>",$datos);
$datos = str_replace("-22222-","</td></tr>",$datos);

echo $datos;	
?>
</table>