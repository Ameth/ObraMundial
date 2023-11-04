<?php 
if(!isset($_GET['type'])||($_GET['type']=="")){//Saber que combo voy a consultar
	exit();
}else{
	require_once("includes/conect_srv.php");
    include_once("includes/funciones.php");
	include_once("includes/LSiqml.php");

	if($_GET['type']==3){//Publicadores, dependiendo del grupo y la congregación
		if(!isset($_GET['id'])||($_GET['id']=="")){
			echo "<option value=''>(Seleccione)</option>";
		}else{
			$SQL=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_GET['c']."' and IDGrupo='".$_GET['id']."' and IDEstado=1",'Nombre');
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