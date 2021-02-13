<?php 
	
	require_once($_SERVER['DOCUMENT_ROOT']."/funciones/fx_util.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/funciones/Funciones_carga.php');
	
	//Funciones
	function getComprasComex()
	{
		$ComprasComex=array(
							'MAWB_B_L'=>'',
							'Invoice'=>'',
							'PackingSlip'=>'',
							'Embarcador'=>'',
							'RespCompras'=>'',
							'RespComex'=>'',
							'RespPartOperations'=>'',
							'StatusAWB_BL_ParaEE'=>'',
							'ObservacionesCompras'=>'');
		return $ComprasComex;
	}
	function ValidarDatosNoVaciosComprasComex($ArregloComex)
	{
		$Respuesta='ok';
		$Campo_errado='';
		
		if($ArregloComex['MAWB_B_L']=="")
		{
			$Campo_errado.=' '.'MAWB_B_L';
		}
		
		if($ArregloComex['Invoice']=="")
		{
			$Campo_errado.=' '.'Invoice';
		}
		
		if($ArregloComex['PackingSlip']=="")
		{
			$Campo_errado.=' '.'PackingSlip';
		}
		
		if($Campo_errado!='')
		{
			$Respuesta='nok';
		}
			
		$RespuestaValidarDatosNoVacios=array(
			'Respuesta'=>$Respuesta,
			'Campo_errado'=>$Campo_errado
		);
			
		return $RespuestaValidarDatosNoVacios;
	}
	function IngresarDatosCargaComex_Manual($ComprasComex)
	{
		//Obtener datos que tienen datos
		$DatosComprasComexNoVacios=getArregloDatosNoVacios($ComprasComex);
		
		global $CONNECCION_UNICA_FUNCIONES_EDI;
		
		$query= "SELECT COUNT([MAWB_B_L]) as DatoRegistrado
					FROM [ComprasComex]
					WHERE [MAWB_B_L]='".ms_escape_string($DatosComprasComexNoVacios[0]['MAWB_B_L'])."'
					AND [Invoice]='".ms_escape_string($DatosComprasComexNoVacios[1]['Invoice'])."'
					AND [PackingSlip]='".ms_escape_string($DatosComprasComexNoVacios[2]['PackingSlip'])."';";
		
		$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
		
		if (!$result) {
			$CONNECCION_UNICA_FUNCIONES_EDI = conectar_srvdev();
			$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
		}
		
		while($row = sqlsrv_fetch_array($result) )
		{
			$DatoRegistrado	= $row['DatoRegistrado'];
		}
		
		//Validar si existe se debe actualizar solo los datos 
		if($DatoRegistrado==1)
		{
			//Se debe hacer un UPDATE
			$query="UPDATE [ComprasComex] SET ";
			
			$QuerySet="";
			
			foreach($DatosComprasComexNoVacios as $Item)
			{
				$key=key($Item);
				$QuerySet.="[$key]=".getCampoSegunTipo($key,$Item[$key]);
			}
			$QuerySet=substr($QuerySet, 0, -1);
			
			
			$query.=$QuerySet." WHERE [MAWB_B_L]='".ms_escape_string($DatosComprasComexNoVacios[0]['MAWB_B_L'])."'
					AND [Invoice]='".ms_escape_string($DatosComprasComexNoVacios[1]['Invoice'])."'
					AND [PackingSlip]='".ms_escape_string($DatosComprasComexNoVacios[2]['PackingSlip'])."';";
		}
		else
		{
			//Se debe hacer un INSERT
			$query="INSERT INTO [ComprasComex](";
			
			$QueryCampos="";
			$QueryValues="";
			
			foreach($DatosComprasComexNoVacios as $Item)
			{
				$key=key($Item);
				$QueryCampos.="[".$key."],";
				$QueryValues.=getCampoSegunTipo($key,$Item[$key]);
			}
			
			$QueryCampos=substr($QueryCampos, 0, -1);
			$QueryValues=substr($QueryValues, 0, -1);
			
			$query.=$QueryCampos.") VALUES (".$QueryValues.") ";
		}
		
		ActualizarBD($query);
		
		//return $query.'<br /><br />';
		return 'Compras Comex '.$DatosComprasComexNoVacios[0]['MAWB_B_L'].' - '.$DatosComprasComexNoVacios[1]['Invoice'].' - '.$DatosComprasComexNoVacios[2]['PackingSlip'].' ingresado';
	}
	function InterpretarValidacionNoVacioComex($Resultado,$FilaExcel)
	{
		$Respuesta='';
		if($Resultado['Respuesta']=='nok')
		{
			switch ($Resultado['Campo_errado']) {
				default:
				$Respuesta= 'No se encontro un valor para el campo '.$Resultado['Campo_errado'].' en la fila '.$FilaExcel.'';
			}
		}
		return $Respuesta;
	}
	function getCampoSegunTipo($key,$Valor)
	{
		$Query="";
		switch ($key) {
					case 'FechaPagoDerechos':
						$Query="'".FechaCompletaFormateada(ms_escape_string($Valor))."',";
						break;
						
					case 'FechaG_D':
						$Query="'".FechaCompletaFormateada(ms_escape_string($Valor))."',";
						break;
						
					case 'FechaRetiroPuerto_Aeropuerto':
						$Query="'".FechaCompletaFormateada(ms_escape_string($Valor))."',";
						break;
					
					default:
					$Query="'".ms_escape_string($Valor)."',";
				}
		
		return $Query;
	}
?>