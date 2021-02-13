<?php
ini_set('memory_limit', '-1');
include_once("SegAjax.php");
//$prueba=tranformarEDI810_XML("archivos/ediPesado.bak");

//echo $prueba;
function tranformarEDI810_XML($RutaArchivoEDI)
{
$xml="";
$file = "$RutaArchivoEDI";

$lt               = chr(60);
$gt               = chr(62);
$i                = 0;
$j                = 0;
$indent           = 0;



$fp   = @file_get_contents($file);

if( !$fp ){
  printf("\nCould not open file $file\n");
  exit;
}
$fp.='ISA';
$segments      = explode("<",$fp);

$segment_lenth = sizeof($segments);

$xml= '<?xml version="1.0" encoding="UTF-8"?><ediroot>';
//Vueltas maximos de cada elemento
$isa=16;
$gs=9;
$st=3;
$big=8;
$n1BT=5;
$n1ST=5;
$n1RI=3;
$itd=8;
$fob=4;
$it1=14;
$pid=6;
$refSI=3;
$refER=3;
$refVN=3;
$refVS=3;
$tds=2;
$ita=8;
$ctt=2;

//XMLs
$xmlIsa='';
$xmlGs='';
$xmlSt='';
$xmlBig='';
$xmlN1BT='';
$xmlN1ST='';
$xmlN1RI='';
$xmlITD='';
$xmlFOB='';
$xmlit1='';
$xmlpid='';
$xmlRefSI='';
$xmlRefER='';
$xmlRefVN='';
$xmlRefVS='';
$xmlTds='';
$xmlIta='';
$xmlCtt='';


 $IsaPos= array(				
				array( atributo => "date",
					  valor=>"",
                      Posicion => 9
                    ),
				array( atributo => "Time",
					  valor=>"",
                      Posicion => 10
                    ),
				array( atributo => "StandardsId",
					  valor=>"",
                      Posicion => 11
                    ),
				array( atributo => "Version",
					  valor=>"",
                      Posicion => 12
                    ),
				array( atributo => "Control",
					  valor=>"",
                      Posicion => 13
                    ),
					//sender
					//-address
				array( atributo => "id",
					  valor=>"",
                      Posicion => 5
                    ),					
				array( atributo => "Qual",
					  valor=>"",
                      Posicion => 6
                    ),
					//-#address
					
					//receiver					
					//-receiver
				array( atributo => "id",
					  valor=>"",
                      Posicion => 7
                    ),					
				array( atributo => "Qual",
					  valor=>"",
                      Posicion => 8
                    )
					//-#receiver
             );  
	$GsPos= array(				
				array( atributo => "GroupType",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "ApplSender",
					  valor=>"",
                      Posicion => 2
                    ),
				array( atributo => "ApplReceiver",
					  valor=>"",
                      Posicion => 3
                    ),
				array( atributo => "Date",
					  valor=>"",
                      Posicion => 4
                    ),
				array( atributo => "Time",
					  valor=>"",
                      Posicion => 5
                    ),
				array( atributo => "Control",
					  valor=>"",
                      Posicion => 6
                    ),
				array( atributo => "StandardCode",
					  valor=>"",
                      Posicion => 7
                    ),
				array( atributo => "StandardVersion",
					  valor=>"",
                      Posicion => 8
                    )					
					);
	$StPos= array(				
				array( atributo => "DocType",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "Control",
					  valor=>"",
                      Posicion => 2
                    )
					);
	$StBig= array(				
				array( atributo => "BIG01",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "BIG02",
					  valor=>"",
                      Posicion => 2
                    ),
				array( atributo => "BIG04",
					  valor=>"",
                      Posicion => 4
                    ),
				array( atributo => "BIG07",
					  valor=>"",
                      Posicion => 7
                    )
					);
	$N1_BT= array(				
				array( atributo => "N101",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "N102",
					  valor=>"",
                      Posicion => 2
                    ),
				array( atributo => "N103",
					  valor=>"",
                      Posicion => 3
                    ),
				array( atributo => "N104",
					  valor=>"",
                      Posicion => 4
                    )
					);
	$N1_ST= array(				
				array( atributo => "N101",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "N102",
					  valor=>"",
                      Posicion => 2
                    ),
				array( atributo => "N103",
					  valor=>"",
                      Posicion => 3
                    ),
				array( atributo => "N104",
					  valor=>"",
                      Posicion => 4
                    )
					);
	$N1_RI= array(				
				array( atributo => "N101",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "N102",
					  valor=>"",
                      Posicion => 2
                    )
					);
	$ITD_A= array(				
				array( atributo => "ITD01",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "ITD02",
					  valor=>"",
                      Posicion => 2
                    ),
				array( atributo => "ITD07",
					  valor=>"",
                      Posicion => 7
                    )
					);
	$FOB_A= array(				
				array( atributo => "FOB01",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "FOB02",
					  valor=>"",
                      Posicion => 2
                    ),
				array( atributo => "FOB03",
					  valor=>"",
                      Posicion => 3
                    )
					);
	$It1_A= array(				
				array( atributo => "IT102",
					  valor=>"",
                      Posicion => 2
                    ),
				array( atributo => "IT103",
					  valor=>"",
                      Posicion => 3
                    ),
				array( atributo => "IT104",
					  valor=>"",
                      Posicion => 4
                    ),
				array( atributo => "IT106",
					  valor=>"",
                      Posicion => 6
                    ),
				array( atributo => "IT107",
					  valor=>"",
                      Posicion => 7
                    ),
				array( atributo => "IT108",
					  valor=>"",
                      Posicion => 8
                    ),
				array( atributo => "IT109",
					  valor=>"",
                      Posicion => 9
                    ),
				array( atributo => "IT110",
					  valor=>"",
                      Posicion => 10
                    ),
				array( atributo => "IT111",
					  valor=>"",
                      Posicion => 11
                    ),
				array( atributo => "IT112",
					  valor=>"",
                      Posicion => 12
                    ),
				array( atributo => "IT113",
					  valor=>"",
                      Posicion => 13
                    )
					);
					
	$PID_A= array(				
				array( atributo => "PID01",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "PID05",
					  valor=>"",
                      Posicion => 5
                    )
					);
	$RefSI_A= array(				
				array( atributo => "REF01",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "REF02",
					  valor=>"",
                      Posicion => 2
                    )
					);
	$RefER_A= array(				
				array( atributo => "REF01",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "REF02",
					  valor=>"",
                      Posicion => 2
                    )
					);
	$RefVN_A= array(
				array( atributo => "REF01",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "REF02",
					  valor=>"",
                      Posicion => 2
                    )
					);
	$RefVS_A= array(
				array( atributo => "REF01",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "REF02",
					  valor=>"",
                      Posicion => 2
                    )
					);
	$Tds_A= array(
				array( atributo => "TDS01",
					  valor=>"",
                      Posicion => 1
                    )
					);
	$Ita_A= array(
				array( atributo => "ITA01",
					  valor=>"",
                      Posicion => 1
                    ),
				array( atributo => "ITA03",
					  valor=>"",
                      Posicion => 3
                    ),
				array( atributo => "ITA04",
					  valor=>"",
                      Posicion => 4
                    ),
				array( atributo => "ITA07",
					  valor=>"",
                      Posicion => 7
                    )
					);
	$Ctt_A= array(
				array( atributo => "CTT01",
					  valor=>"",
                      Posicion => 1
                    )
					);

	$estadoXML;
	
	$isaVuelta=0;
	$isaId=0;
	$old_IdISA=0;
	$idTransaction=0;
	for($i=0; $i<$segment_lenth; $i++){
	  $elements      = explode("*",$segments[$i]);
	  $element_lenth = sizeof($elements);
	  $etiqueta   = strtolower(trim($elements[0],"\r\n\t\0"));


		  if($etiqueta=="isa")
		  {
			$isaId=$i+1;
			
			if($isaId==$idTransaction)
			{
				//Continue
			}
			else
			{
				if($idTransaction>0)
				{
				//echo 'FINAL DE ISA isaID: '.$isaId.' transaction: '.$idTransaction.' <br />';
				$xml.= $xmlIsa.$xmlGs.$xmlSt.'</group></interchange>';
				$xmlSt="";
				$xmlit1="";
				}
			}

			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$isa; $j++){
				  $f=0;
				  foreach($IsaPos as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$IsaPos[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";
			}
			$xmlIsa='<interchange Standard="ANSI X.12" Date="'.$IsaPos[0][valor].'" Time="'.$IsaPos[1][valor].'" StandardsId="'.$IsaPos[2][valor].'" Version="'.$IsaPos[3][valor].'" Control="'.$IsaPos[4][valor].'" >';
			$xmlIsa.='<sender><address Id="'.$IsaPos[6][valor].'" Qual="'.$IsaPos[5][valor].'"/></sender>';
			$xmlIsa.='<receiver><address Id="'.$IsaPos[8][valor].'" Qual="'.$IsaPos[7][valor].'"/></receiver>';
			
		  }
		  if($etiqueta=="gs")
		  {
			$gsActual=$i;
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$gs; $j++){
				  $f=0;
				  foreach($GsPos as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$GsPos[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";
			}
			$xmlGs='<group GroupType="'.$GsPos[0][valor].'" ApplSender="'.$GsPos[1][valor].'" ApplReceiver="'.$GsPos[2][valor].'" Date="'.$GsPos[3][valor].'" Time="'.$GsPos[4][valor].'" Control="'.$GsPos[5][valor].'" StandardCode="'.$GsPos[6][valor].'" StandardVersion="'.$GsPos[7][valor].'">';	
		  }
		  if($etiqueta=="st")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$st; $j++){
				  $f=0;
				  foreach($StPos as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$StPos[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";
			}
			$xmlSt.='<transaction DocType="'.$StPos[0][valor].'" Name="Invoice" Control="'.$StPos[1][valor].'">';			
		  }
		  if($etiqueta=="big")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$big; $j++){
				  $f=0;
				  foreach($StBig as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$StBig[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";
			}
			$xmlBig='<segment Id="BIG"><element Id="BIG01">'.$StBig[0][valor].'</element><element Id="BIG02">'.$StBig[1][valor].'</element><element Id="BIG04">'.$StBig[2][valor].'</element><element Id="BIG07">'.$StBig[3][valor].'</element></segment>';
			$xmlSt.=$xmlBig;
		  }
		  //Este n1 por ordern corresponde al N1 Tipo BT
		  if($etiqueta=="n1" && (string)$elements[1]=="BT")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$n1BT; $j++){
				  $f=0;
				  foreach($N1_BT as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$N1_BT[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";
			}
			$xmlN1BT='<loop Id="N1"><segment Id="N1"><element Id="N101">'.$N1_BT[0][valor].'</element><element Id="N102">'.$N1_BT[1][valor].'</element><element Id="N103">'.$N1_BT[2][valor].'</element><element Id="N104">'.$N1_BT[3][valor].'</element></segment></loop>';
			$xmlSt.=$xmlN1BT;
		  }
		  //Este n1 por ordern corresponde al N1 Tipo ST
		  if($etiqueta=="n1" && (string)$elements[1]=="ST")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$n1ST; $j++){
				  $f=0;
				  foreach($N1_ST as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$N1_ST[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";
			}
			$xmlN1ST='<loop Id="N1"><segment Id="N1"><element Id="N101">'.$N1_ST[0][valor].'</element><element Id="N102">'.$N1_ST[1][valor].'</element><element Id="N103">'.$N1_ST[2][valor].'</element><element Id="N104">'.$N1_ST[3][valor].'</element></segment></loop>';
			$xmlSt.=$xmlN1ST;
		 }
		  //Este n1 por ordern corresponde al N1 Tipo RI
		  if($etiqueta=="n1" && (string)$elements[1]=="RI")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$n1RI; $j++){
				  $f=0;
				  foreach($N1_RI as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$N1_RI[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlN1RI='<loop Id="N1"><segment Id="N1"><element Id="N101">'.$N1_RI[0][valor].'</element><element Id="N102">'.$N1_RI[1][valor].'</element></segment></loop>';
		    $xmlSt.=$xmlN1RI;
		  }
		  
		  if($etiqueta=="itd")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$itd; $j++){
				  $f=0;
				  foreach($ITD_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$ITD_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlITD.='<segment Id="ITD"><element Id="ITD01">'.$ITD_A[0][valor].'</element><element Id="ITD02">'.$ITD_A[1][valor].'</element><element Id="ITD07">'.$ITD_A[2][valor].'</element></segment>';
		    $xmlSt.=$xmlITD;
		  }
		  
		  if($etiqueta=="fob")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$fob; $j++){
				  $f=0;
				  foreach($FOB_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$FOB_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlFOB='<segment Id="FOB"><element Id="FOB01">'.$FOB_A[0][valor].'</element><element Id="FOB02">'.$FOB_A[1][valor].'</element><element Id="FOB03">'.$FOB_A[2][valor].'</element></segment>';
		    $xmlSt.=$xmlFOB.'';
		  }
		  
		  if($etiqueta=="it1")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$it1; $j++){
				  $f=0;
				  foreach($It1_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$It1_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlit1.='<loop Id="IT1"><segment Id="IT1"><element Id="IT102">'.$It1_A[0][valor].'</element><element Id="IT103">'.$It1_A[1][valor].'</element><element Id="IT104">'.$It1_A[2][valor].'</element><element Id="IT106">'.$It1_A[3][valor].'</element><element Id="IT107">'.$It1_A[4][valor].'</element><element Id="IT108">'.$It1_A[5][valor].'</element><element Id="IT109">'.$It1_A[6][valor].'</element><element Id="IT110">'.$It1_A[7][valor].'</element><element Id="IT111">'.$It1_A[8][valor].'</element><element Id="IT112">'.$It1_A[9][valor].'</element><element Id="IT113">'.$It1_A[10][valor].'</element></segment>';
		  }
		  if($etiqueta=="pid")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$pid; $j++){
				  $f=0;
				  foreach($PID_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$PID_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlpid='<loop Id="PID"><segment Id="PID"><element Id="PID01">'.$PID_A[0][valor].'</element><element Id="PID05">'.$PID_A[1][valor].'</element></segment></loop>';
			$xmlit1.=$xmlpid;
		  }
		  if($etiqueta=="ref" && (string)$elements[1]=="SI")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$refSI; $j++){
				  $f=0;
				  foreach($RefSI_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$RefSI_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlRefSI='<segment Id="REF"><element Id="REF01">'.$RefSI_A[0][valor].'</element><element Id="REF02">'.$RefSI_A[1][valor].'</element></segment>';
			$xmlit1.=$xmlRefSI;
		  }
		  if($etiqueta=="ref" && (string)$elements[1]=="ER")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$refER; $j++){
				  $f=0;
				  foreach($RefER_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$RefER_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlRefER='<segment Id="REF"><element Id="REF01">'.$RefER_A[0][valor].'</element><element Id="REF02">'.$RefER_A[1][valor].'</element></segment>';
			$xmlit1.=$xmlRefER;
		  }
		  
		  if($etiqueta=="ref" && (string)$elements[1]=="VN")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$refVN; $j++){
				  $f=0;
				  foreach($RefVN_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$RefVN_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlRefVN='<segment Id="REF"><element Id="REF01">'.$RefVN_A[0][valor].'</element><element Id="REF02">'.$RefVN_A[1][valor].'</element></segment>';
			$xmlit1.=$xmlRefVN;
		  }
		  
		  if($etiqueta=="ref" && (string)$elements[1]=="VS")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$refVS; $j++){
				  $f=0;
				  foreach($RefVS_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$RefVS_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlRefVS='<segment Id="REF"><element Id="REF01">'.$RefVS_A[0][valor].'</element><element Id="REF02">'.$RefVS_A[1][valor].'</element></segment>';
			$xmlit1.=$xmlRefVS.'</loop>';
			$xmlSt.=$xmlit1;
		  }
		  
		  if($etiqueta=="tds")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$tds; $j++){
				  $f=0;
				  foreach($Tds_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$Tds_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlTds='<segment Id="TDS"><element Id="TDS01">'.$Tds_A[0][valor].'</element></segment>';
			$xmlSt.=$xmlTds;
		  }
		  
		  if($etiqueta=="ita")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$ita; $j++){
				  $f=0;
				  foreach($Ita_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$Ita_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }				
			}
			$xmlIta='<segment Id="ITA"><element Id="ITA01">'.$Ita_A[0][valor].'</element><element Id="ITA03">'.$Ita_A[1][valor].'</element><element Id="ITA04">'.$Ita_A[2][valor].'</element><element Id="ITA07">'.$Ita_A[3][valor].'</element></segment>';
			$xmlSt.=$xmlIta;
		  }
		  
		  if($etiqueta=="ctt")
		  {
			//echo "".$etiqueta;
			//j es 1 porque 0 es la etiqueta
			for($j=1; $j<$ctt; $j++){
				  $f=0;
				  foreach($Ctt_A as $row)
					  {
						 if($row[Posicion]==$j)
						 {
							$Ctt_A[$f][valor]="".$elements[$j];
						 }
						 $f++;
					  }
				//echo "vuelta ".$j." valor: ".$elements[$j]."<br />";				
			}
			$xmlCtt='<segment Id="CTT"><element Id="CTT01">'.$Ctt_A[0][valor].'</element></segment>';
			$xmlSt.=$xmlCtt.'</transaction>';
			$idTransaction=$isaId;
			//$estadoXML="Completo";
		  }

	}
	

//echo "valor ".$IsaPos[1][valor];
$xml.= '</ediroot>';
return $xml;
}
?>