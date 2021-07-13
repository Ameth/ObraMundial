<?php 
if((isset($_GET['id'])&&$_GET['id']!="")&&(isset($_GET['code'])&&$_GET['code']!="")){
	require_once("includes/conect_srv.php");
	$Cons_Validar="Select * From uvw_tbl_Usuarios Where ID_Usuario='".base64_decode($_GET['id'])."' and ForgotPassword='".$_GET['code']."'";
	$SQL_Validar=sqlsrv_query($conexion,$Cons_Validar,array(),array( "Scrollable" => 'static' ));
	$Num_Validar=sqlsrv_num_rows($SQL_Validar);
	if($Num_Validar>=1){
		$row_Validar=sqlsrv_fetch_array($SQL_Validar);
		$Consulta="EXEC sp_ValidarUsuario '".$row_Validar['Usuario']."', '".$row_Validar['Password']."'";
		$SQL=sqlsrv_query($conexion,$Consulta,array(),array( "Scrollable" => 'Buffered' ));
		if($SQL){
			$Num=sqlsrv_num_rows($SQL);
			if($Num>0){
				$row=sqlsrv_fetch_array($SQL);
				session_start();
				$_SESSION['BD']=$database;//Del archivo conect
				$_SESSION['User']=strtoupper($row['Usuario']);
				$_SESSION['CodUser']=$row['ID_Usuario'];
				$_SESSION['NomUser']=$row['NombreUsuario'];
				$_SESSION['Perfil']=$row['ID_PerfilUsuario'];
				$_SESSION['NomPerfil']=$row['PerfilUsuario'];
				$_SESSION['CambioClave']=$row['CambioClave'];
				$_SESSION['TimeOut']=$row['TimeOut'];
				$ConsUpd="Update tbl_Usuarios Set ForgotPassword=NULL Where ID_Usuario='".base64_decode($_GET['id'])."'";
				if(sqlsrv_query($conexion,$ConsUpd)){
					header('Location:login_cambio_clave.php');
				}else{
					header('Location:logout.php');
				}
			}else{
				sqlsrv_close($conexion);
			}
		}		
	}else{
		header('Location:login.php');
	}
	sqlsrv_close($conexion);
}
?>