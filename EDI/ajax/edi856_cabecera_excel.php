<?
error_reporting(0);
$sociedad = $_REQUEST['sociedad'];
$nro_oc = $_REQUEST['nro_oc'];
$inicio= $_REQUEST['inicio'];
$termino = $_REQUEST['termino'];
$factura = $_REQUEST['factura'];
include("../../conect/conect.php");
include_once("SegAjax.php");
require_once('Writer.php');
	//////////// F O R M A T O //////////////////
	
	$nombrearchivo = "EDI856_CABECERA";
	$fecha = date("Ymd");
	$arc = $nombrearchivo.'_'.$fecha.'.xls';
	$workbook = new Spreadsheet_Excel_Writer($arc);
	$num_format =& $workbook->addFormat();
	$num_format->setNumFormat('###,##0.00');
	$num_format1 =& $workbook->addFormat();
	$num_format1->setNumFormat('###,##0');
	$format_bold =& $workbook->addFormat();
	$format_bold->setBold();
	$format_bold->setBorder(2);
	$format_bold->setFgColor(23);
	$format_bold->setTextWrap();
	///////////////////////////////////////////
	$verde =& $workbook->addFormat();
	$verde->setFgColor('green');
	$verde->setBorder(1);
	$rojo =& $workbook->addFormat();
	$rojo->setFgColor('red');
	$rojo->setBorder(1);
	$amarillo =& $workbook->addFormat();
	$amarillo->setFgColor('yellow');
	$amarillo->setBorder(1);
	$celda =& $workbook->addFormat();
	$celda->setBorder(1);
	///////////////////////////////////////////
	
	//$workbook->send($nombrearchivo.'_'.$fecha.'.xls');
	//////////////////////////////////////////	
	$worksheet =& $workbook->addWorksheet('CABECERA');
	//$worksheet->write(0, 0, 'INFORME CONFIG. LIMITES X ELEMENTO' ,$format_bold);
	
	//////////////////// CABECERAS /////////////////////////////////////////////
	$worksheet->write(0, 0, 'Nro Factura', $format_bold);
	$worksheet->write(0, 1, 'Fecha Despacho', $format_bold);
	$worksheet->write(0, 2, 'Hora', $format_bold);
	$worksheet->write(0, 3, 'Peso Carga',$format_bold );
	$worksheet->write(0, 4, 'Unidad Medida',$format_bold );
	$worksheet->write(0, 5, 'Cod Embalaje', $format_bold);
	$worksheet->write(0, 6, 'Tot Unid Embarcadas', $format_bold);
	$worksheet->write(0, 7, 'Tipo Transporte', $format_bold);
	$worksheet->write(0, 8, utf8_decode('Descripción'), $format_bold);
	$worksheet->write(0, 9, utf8_decode('Nro Camión'), $format_bold);		
	$worksheet->write(0, 10, 'Sociedad', $format_bold);
	$worksheet->write(0, 11, 'Cant Bulto', $format_bold);		
	$data = array();
	$data = getDataExcel($sociedad, $nro_oc, $inicio, $termino, $factura);
	$fil = 1;
	foreach($data as $registro)
	{	
		//////////// VALORES CABECERA //////////////////////////////////////////////
		$worksheet->write($fil, 0, $registro["nro_factura"], $celda);
		$worksheet->write($fil, 1, $registro["fecha_despacho"], $celda);
		$worksheet->write($fil, 2, $registro["hora"], $celda);
		$worksheet->write($fil, 3, $registro["peso_carga"], $celda);
		$worksheet->write($fil, 4, $registro["unidad_medida"], $celda);
		$worksheet->write($fil, 5, $registro["cod_embalaje"], $celda);
		$worksheet->write($fil, 6, $registro["tot_unid_embarcadas"], $celda);
		$worksheet->write($fil, 7, $registro["tipo_transporte"], $celda);
		$worksheet->write($fil, 8, utf8_decode($registro["descripcion"]), $celda);
		$worksheet->write($fil, 9, $registro["nro_camion"], $celda);
		$worksheet->write($fil, 10, $registro["sociedad"], $celda);
		$worksheet->write($fil, 11, $registro["cant_bulto"], $celda);
		$fil++;	
	}
	$workbook->close();	
	echo $arc;
	header("location: $arc");
////////////////// FUNCIONES //////////////////////
function getDataExcel($sociedad, $nro_ocs, $inicio, $termino, $facturas)
{
	$sql = "";
		$sql = " SELECT DISTINCT he.segm_id as nro_factura, convert(char(10),he.segm_date,103) as fecha_despacho, he.segm_time as hora, he.ship_measurement as peso_carga, ";
		$sql.= " he.ship_unit as unidad_medida, he.ship_packing as cod_embalaje, he.ship_lading as tot_unid_embarcadas, ";
		$sql.= " he.ship_transport as tipo_transporte, he.ship_transname as descripcion, he.ship_trailernumber as nro_camion, ";
		$sql.= " he.Sociedad as sociedad, he.enviado as enviado, he.ship_cantBultos as cant_bulto ";
		$sql.= " FROM [856HEADER] as he ";
		$sql.= " INNER JOIN [856DETAIL] as de ON he.segm_id = de.segm_Id ";
		//$sql.= " LEFT JOIN [856BULTO] as bu ON he.segm_id = bu.invNumber ";  
		$sql.= " WHERE 1=1 ";
		
		
	if($nro_ocs != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND SUBSTRING(de.it_po, 0, 11) in ( ";
		$arr_oc = explode('|',$nro_ocs);
		$nfacturas = count($arr_oc);
		
		for($i = 0; $i < $nfacturas; $i++)
		{			
			$OC = trim($arr_oc[$i]);
			if($OC != "") 
			{
				$sqlNumeros.= " SUBSTRING('$OC', 0, 11), ";
				$NumerosEncontrados++;
			}
		}
		$aux = trim($arr_oc[0]);
		$sqlNumeros.= " '$aux' ";
		$sqlNumeros.= " ) ";
		if($NumerosEncontrados>0)
		{
			$sql.=$sqlNumeros;
		}
	}
	
	if($facturas != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND de.segm_Id in ( ";
		$arr_facturas = explode('|',$facturas);
		$nfacturas = count($arr_facturas);
		
		for($i = 0; $i < $nfacturas; $i++)
		{			
			$factura = trim($arr_facturas[$i]);
			if($factura != "") 
			{
				$sqlNumeros.= " '$factura', ";
				$NumerosEncontrados++;
			}			
		}
		$aux = trim($arr_facturas[0]);
		$sqlNumeros.= " '$aux' ";
		$sqlNumeros.= " ) ";
		if($NumerosEncontrados>0)
		{
			$sql.=$sqlNumeros;
		}
	}
	
	if($nro_ocs == '' && $facturas == '')
	{
		if($inicio != "" && $inicio != null) {
			$sql.= " AND he.segm_date >= CONVERT(DATETIME, '".$inicio." 00:00:00', 103) ";
		}
		if($termino != "" && $termino != null) {
			$sql.= " AND he.segm_date <= CONVERT(DATETIME, '".$termino." 23:59:59', 103) ";
		}
	}
	$conexion = conectar_srvdev();
	$result = sqlsrv_query($conexion, $sql);
	$retorno = array();	
    while ($row = sqlsrv_fetch_array($result))
	{
		$registro 						= array();	
		//$row["precio_ack"]   			= trataPrecio($row["precio_ack"]);	
		$registro["nro_factura"] 		= $row["nro_factura"];
		$registro["fecha_despacho"] 	= $row["fecha_despacho"]; //date("d-m-Y", strtotime($row["fecha_despacho"]));
		$registro["hora"] 				= $row["hora"];
		$registro["peso_carga"] 		= $row["peso_carga"];
		$registro["unidad_medida"] 		= $row["unidad_medida"];
		$registro["cod_embalaje"] 		= $row["cod_embalaje"];
		$registro["tot_unid_embarcadas"]= $row["tot_unid_embarcadas"];
		$registro["tipo_transporte"] 	= $row["tipo_transporte"];
		$registro["descripcion"] 		= utf8_decode($row["descripcion"]);
		$registro["nro_camion"] 		= $row["nro_camion"];
		$registro["sociedad"] 			= $row["sociedad"];
		$registro["cant_bulto"] 		= $row["cant_bulto"];
		$retorno[] = $registro;
	}
	sqlsrv_close($conexion);
	return $retorno;
}

?>


