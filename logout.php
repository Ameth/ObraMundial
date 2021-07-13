<?php 
if (isset($_SESSION)) {
  session_destroy();
}
$parametros_cookies = session_get_cookie_params(); 
setcookie(session_name(),0,1,$parametros_cookies["path"]);
//setcookie ("obraAC", "", time() - 3600);
if(isset($_GET['data'])&&$_GET['data']!=""){
?>
<!DOCTYPE html>
<html lang="es">
	<head>
	</head>
	<body onload="Enviar();">
		<form name="form" id="form" method="post" action="login.php">
			<input type="hidden" name="data" value="OK"> 
		</form>
		<script language="javascript">
		 function Enviar(){
			 //alert('Hola');
			document.getElementById('form').submit();
		}
		</script>  
	</body>
</html>
<?php
}else{
	header('Location:login.php');
}

?>