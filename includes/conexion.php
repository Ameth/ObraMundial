<?php 
if (!isset($_SESSION)) {
  session_start();
}
if (!isset($_SESSION['User'])||$_SESSION['User']==""||$_SESSION['Perfil']=="") {
	if(file_exists('logout.php')){
		header('Location:logout.php');		
		}else{
			header('Location:../logout.php');
		}
	exit();
}

if(file_exists("includes/conect_srv.php")){
	require_once("includes/conect_srv.php");
}else{
	require_once("conect_srv.php");
}

//$onload_body="onLoad='Reloj();' onkeyup='ResetC();' onclick='ResetC();' onMouseOver='ResetC();' onMouseOut='ResetC();'";
//Funciones
if(file_exists("includes/funciones.php")){
	include_once("includes/funciones.php");
	include_once("includes/LSiqml.php");
}else{
	include_once("funciones.php");
	include_once("LSiqml.php");
}
//Declaraciones
if(file_exists("includes/definicion.php")){
	include_once("includes/definicion.php");
}else{
	include_once("definicion.php");
}
?>