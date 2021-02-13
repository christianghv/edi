<?php
include 'conect/conect.php';
$link = conectar_srvdev();


$src = "XCBL";
$dst = $src."/sinee";

                $files = scandir($src);

    
  
$sql  = "/****** Script for SelectTopNRows command from SSMS  ******/
                SELECT replace(replace(replace(replace(replace(InvoiceNumber,'E09',''),'-000',''),'-00',''),'-0',''),'-','')
                  FROM [InvoiceHeader]
                  where [InvoiceDate] >= convert(datetime,'01-01-2018',103)
                	and [InvoiceVendor] = '0000010001'
                	and InvoiceNumber IN (SELECT  [InvoiceNumber]
                      
                  FROM [InvoiceDetail]
                  where EntregaEntrante is null or EntregaEntrante = '' or EntregaEntrante = '0')";
              
         $c_fact = sqlsrv_query($sql,$link);
         
         while ($r_fact = sqlsrv_fetch_array($c_fact)) {
                   
                //echo $r_fact[0]."<br>";
                foreach ($files as $file)
                {
                    
                    $pos = strpos ($file, "-".$r_fact[0].".xml") ;
                    if ($file != "." && $file != ".." && $pos) 
                    {
                       $count++;
                       echo "copy(\"$src/$file\", \"$dst/$file\"); "."<br>";
                       copy("$src/$file", "$dst/$file"); 
                      }
                }
                //rcopy("$src/$file", "$dst/$file"); 
         }

         echo "Se copiaron $count archivos";

?>