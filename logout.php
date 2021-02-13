<?php
error_reporting(0);
include_once("SegAjax.php");
session_start();
session_destroy();



require_once 'config.php';

unset($_SESSION['token']);


header('Location: '.$base_url.'?varE='.$_GET['error']);
?>