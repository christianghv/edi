 <?php
 session_start();
 $base_url= filter_var('https://edides.kccdesarrollo.cab', FILTER_SANITIZE_URL);

 define('CLIENT_ID','8c241262-9b78-405a-9e21-c76061fec309');
 define('CLIENT_SECRET','E77395m3ys._5Vo-p.bfNOY20O0k~1qFSi');
 define('REDIRECT_URI','https://edides.kccdesarrollo.cab/index.php');
 define('APPROVAL_PROMPT','auto');
 
 ?>