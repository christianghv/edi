<?php 
	
	require_once($_SERVER['DOCUMENT_ROOT']."/funciones/fx_util.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/funciones/Funciones_carga.php');
	
	//Funciones
	function getComex()
	{
		$Comex=array(
							'MAWB_B_L'=>'',
							'Invoice'=>'',
							'PackingSlip'=>'',
							'FechaPagoDerechos'=>'',
							'1erPago_2doPago'=>'',
							'HoraPago'=>'',
							'FechaG_D'=>'',
							'HoraG_D'=>'',
							'G_DAduana'=>'',
							'FechaRetiroPuerto_Aeropuerto'=>'',
							'BoletoTransportes'=>'',
							'ObservacionesComex'=>'');
		return $Comex;
	}
	function ValidarDatosNoVaciosComex($ArregloComex)
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
	function IngresarDatosComex_Manual($Comex)
	{
		//Obtener datos que tienen datos
		$DatosComexNoVacios=getArregloDatosNoVacios($Comex);
		
		global $CONNECCION_UNICA_FUNCIONES_EDI;
		
		$query= "SELECT COUNT([MAWB_B_L]) as DatoRegistrado
					FROM [Comex]
					WHERE [MAWB_B_L]='".ms_escape_string($DatosComexNoVacios[0]['MAWB_B_L'])."'
					AND [Invoice]='".ms_escape_string($DatosComexNoVacios[1]['Invoice'])."'
					AND [PackingSlip]='".ms_escape_string($DatosComexNoVacios[2]['PackingSlip'])."';";
		
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
			$query="UPDATE [Comex] SET ";
			
			$QuerySet="";
			
			foreach($DatosComexNoVacios as $Item)
			{
				$key=key($Item);
				$QuerySet="$key=".getCampoSegunTipo($key,$Item[$key]);
			}
			$QuerySet=substr($QuerySet, 0, -1);
			
			
			$query.=$QuerySet." WHERE [MAWB_B_L]='".ms_escape_string($DatosComexNoVacios[0]['MAWB_B_L'])."'
					AND [Invoice]='".ms_escape_string($DatosComexNoVacios[1]['Invoice'])."'
					AND [PackingSlip]='".ms_escape_string($DatosComexNoVacios[2]['PackingSlip'])."';";
		}
		else
		{
			//Se debe hacer un INSERT
			$query="INSERT INTO [Comex](";
			
			$QueryCampos="";
			$QueryValues="";
			
			foreach($DatosComexNoVacios as $Item)
			{
				$key=key($Item);
				$QueryCampos.=$key.",";
				$QueryValues.=getCampoSegunTipo($key,$Item[$key]);
			}
			
			$QueryCampos=substr($QueryCampos, 0, -1);
			$QueryValues=substr($QueryValues, 0, -1);
			
			$query.=$QueryCampos.") VALUES (".$QueryValues.") ";
		}
		
		ActualizarBD($query);
		
		//return $query.'<br /><br />';
		return 'Comex '.$DatosComexNoVacios[0]['MAWB_B_L'].' - '.$DatosComexNoVacios[1]['Invoice'].' - '.$DatosComexNoVacios[2]['PackingSlip'].' ingresado';
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