<?php 
if(!isset($conexion_odbc)){
	$usuario='SYSTEM';
	$databaseHN='DIALNETGOLIVE';
	$password='Asdf1234$';
	$servidor='192.168.5.195:30015';
	$dsn='/usr/sap/hdbclient/libodbcHDB.so';//HDBODBC32
	$cadConect='DRIVER='.$dsn.';SERVERNODE='.$servidor.';DATABASE='.$databaseHN;	
	$conexion_odbc=odbc_pconnect($cadConect,$usuario,$password,SQL_CUR_USE_ODBC);
	if(!$conexion_odbc){
		echo "No es posible conectarse al servidor HANA.</br>";
		$rs=error_get_last();
		echo $rs['message']."<br>";
		print_r(odbc_errormsg($conexion_odbc));		
		exit();
	}
}

?>