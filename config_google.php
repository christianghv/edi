 <?php
 include_once("SegAjax.php");
 session_start();
 $base_url= filter_var('http://d-edi20.kcl.cl', FILTER_SANITIZE_URL);
 //$base_url= filter_var('https://d-edi20.kcl.cl', FILTER_SANITIZE_URL);
 //$base_url= filter_var('http://ediqa.kcl.cl', FILTER_SANITIZE_URL);
 define('CLIENT_ID','204433625544-0kssv7okerl9bf1vppaarrrfc8tsv5ri.apps.googleusercontent.com');
 define('CLIENT_SECRET','kpQ6t2oVkd4LVkCjnbtsYaRI');
 define('REDIRECT_URI','http://d-edi20.kcl.cl/index.php');
 //define('REDIRECT_URI','http://ediqa.kcl.cl/index.php');
 define('APPROVAL_PROMPT','auto');
 define('ACCESS_TYPE','online');


 ?>