<?php 
if(isset($_GET['type'])&&$_GET['type']!=""){
	require_once('conexion.php');
	if($_GET['type']==1){//Consultar si existe el usuario ha agregar
		$Cons="Select Usuario From tbl_Usuarios Where Usuario='".$_GET['Usuario']."' and NumCong='".$_SESSION['NumCong']."'";
		$SQL_Cons=sqlsrv_query($conexion,$Cons);
		$row_Cons=sqlsrv_fetch_array($SQL_Cons);
		if($row_Cons['Usuario']!=""){
			echo "<p class='text-danger'><i class='fa fa-times-circle-o'></i> No disponible</p>";
		}else{
			echo "<p class='text-info'><i class='fa fa-thumbs-up'></i> Disponible</p>";
		}
	}
	if($_GET['type']==2){//Activar o Inactivar Usuario
		$Cons="Select Estado From tbl_Usuarios Where IDUsuario='".$_GET['ID_Usuario']."'";
		$SQL_Cons=sqlsrv_query($conexion,$Cons);
		$row_Cons=sqlsrv_fetch_array($SQL_Cons);
		if($row_Cons['Estado']==1){
			$Upd="Update tbl_Usuarios Set Estado=2 Where IDUsuario='".$_GET['ID_Usuario']."'";
			$SQL_Upd=sqlsrv_query($conexion,$Upd);
			if($SQL_Upd){
				echo "2";
			}
		}else{
			$Upd="Update tbl_Usuarios Set Estado=1 Where IDUsuario='".$_GET['ID_Usuario']."'";
			$SQL_Upd=sqlsrv_query($conexion,$Upd);
			if($SQL_Upd){
				echo "1";
			}
		}
	}
	if($_GET['type']==3){//Consultar si existe la cong ha agregar
		$Cons="Select NumCong From tbl_Congregaciones Where NumCong='".$_GET['cong']."'";
		$SQL_Cons=sqlsrv_query($conexion,$Cons);
		$row_Cons=sqlsrv_fetch_array($SQL_Cons);
		if($row_Cons['NumCong']!=""){
			echo "<p class='text-danger'><i class='fa fa-times-circle-o'></i> No disponible</p>";
		}else{
			echo "<p class='text-info'><i class='fa fa-thumbs-up'></i> Disponible</p>";
		}
	}
	if($_GET['type']==4){//Consultar si existe la el periodo a crear en la congregacion
		if(isset($_GET['cong'])&&$_GET['cong']!=""){
			$Cong=$_GET['cong'];
		}else{
			$Cong=$_SESSION['NumCong'];
		}
		$Cons="Select CodigoPeriodo From uvw_tbl_PeriodosInformes Where CodigoPeriodo='".$_GET['ped']."' and NumCong='".$Cong."'";
		$SQL_Cons=sqlsrv_query($conexion,$Cons);
		$row_Cons=sqlsrv_fetch_array($SQL_Cons);
		if($row_Cons['CodigoPeriodo']!=""){
			echo "<p class='text-danger'><i class='fa fa-times-circle-o'></i> Ya existe este periodo</p>";
		}else{
			echo "<p class='text-info'><i class='fa fa-thumbs-up'></i> Nuevo periodo</p>";
		}
	}
	if($_GET['type']==5){//Activar o Inactivar Periodo
		$Cons="Select IDEstado From tbl_PeriodosInformes Where IDPeriodo='".$_GET['ID_Periodo']."'";
		$SQL_Cons=sqlsrv_query($conexion,$Cons);
		$row_Cons=sqlsrv_fetch_array($SQL_Cons);
		if($row_Cons['IDEstado']==1){
			$Upd="Update tbl_PeriodosInformes Set IDEstado=2 Where IDPeriodo='".$_GET['ID_Periodo']."'";
			$SQL_Upd=sqlsrv_query($conexion,$Upd);
			if($SQL_Upd){
				echo "2";
			}
		}else{
			$Upd="Update tbl_PeriodosInformes Set IDEstado=1 Where IDPeriodo='".$_GET['ID_Periodo']."'";
			$SQL_Upd=sqlsrv_query($conexion,$Upd);
			if($SQL_Upd){
				echo "1";
			}
		}
	}
	if($_GET['type']==6){//Consultar si el perfil del usuario tiene el permiso de solo ver su grupo
		if(isset($_GET['cong'])&&$_GET['cong']!=""){
			$Cong=$_GET['cong'];
		}else{
			$Cong=$_SESSION['NumCong'];
		}
		$Cons="Select IDPerfilUsuario From uvw_tbl_PermisosPerfiles Where IDPerfilUsuario='".$_GET['IDPerfil']."' and ID_Permiso=205 and NumCong='".$Cong."'";
		$SQL_Cons=sqlsrv_query($conexion,$Cons);
		$row_Cons=sqlsrv_fetch_array($SQL_Cons);
		if($row_Cons['IDPerfilUsuario']!=""){
			echo "1";
		}else{
			echo "2";
		}
	}
	
	sqlsrv_close($conexion);	
}
