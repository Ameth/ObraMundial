<?php 
if(!isset($_GET['type'])||($_GET['type']=="")){//Saber que combo voy a consultar
	exit();
}else{
	require_once("includes/conexion.php");
	
	if($_GET['type']==1){//Publicadores, dependiendo de la congregación
		if(!isset($_GET['id'])||($_GET['id']=="")){
			echo "<option value=''>(Seleccione)</option>";
		}else{
			$SQL=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_GET['id']."' and IDEstado=1",'Nombre');
			$Num=sqlsrv_num_rows($SQL);
			echo "<option value=''>(Seleccione)</option>";
			if($Num){
				while($row=sqlsrv_fetch_array($SQL)){
					echo "<option value=\"".$row['IDPublicador']."\">".$row['NombrePublicador']."</option>";
				}
			}
		}
	}
	if($_GET['type']==2){//Publicadores, dependiendo del grupo
		if(!isset($_GET['id'])||($_GET['id']=="")){
			echo "<option value=''>(Seleccione)</option>";
		}else{
			$SQL=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$_GET['id']."'",'Nombre');
			$Num=sqlsrv_num_rows($SQL);
			echo "<option value=''>(Seleccione)</option>";
			if($Num){
				while($row=sqlsrv_fetch_array($SQL)){
					echo "<option value=\"".$row['IDPublicador']."\">".$row['NombrePublicador']."</option>";
				}
			}else{
				echo "<option value=''>(Seleccione)</option>";

			}
		}
	}
	sqlsrv_close($conexion);
}
?>