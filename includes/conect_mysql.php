<?php 
if(!isset($conexion_mysql)){
	$usuario='neduga';
	$database='radius';
	$password='Asdf1234$';
	$servidor='10.186.1.70';
	$conexion_mysql=mysqli_connect($servidor,$usuario,$password,$database) or die("No se ha podido conectar a la BD MySQL");
	if (!$conexion_mysql) {
		echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
		echo "Código de depuración: " . mysqli_connect_errno() . PHP_EOL;
		echo "Error de depuración: " . mysqli_connect_error() . PHP_EOL;
		exit();
	}
}

?>