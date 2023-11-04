<?php 
if((isset($_GET['type'])&&($_GET['type']!=""))||(isset($_POST['type'])&&($_POST['type']!=""))){
	
	require_once("includes/conect_srv.php");
    include_once("includes/funciones.php");
	include_once("includes/LSiqml.php");
	
	header('Content-Type: application/json');
	if(isset($_GET['type'])&&($_GET['type']!="")){
		$type=$_GET['type'];
	}else{
		$type=$_POST['type'];
	}
	   
	if($type==1){//Buscar la información del publicador
		$Consulta="Select * From uvw_tbl_Publicadores Where NumCong='".$_GET['c']."' And IDPublicador='".$_GET['id']."'";
		//echo $Consulta;
		$SQL=sqlsrv_query($conexion,$Consulta);
		$records=array();
		$row=sqlsrv_fetch_array($SQL);
		$records=array(
			'NombrePublicador' => $row['NombrePublicador'],
			'IDTipoPublicador' => $row['IDTipoPublicador'],
			'IDPrivServicio' => $row['IDPrivServicio']
		);
		echo json_encode($records);
	}	

	if($type==2){//Buscar los datos del grupo
		$Consulta="Select * From uvw_tbl_Grupos Where NumCong='".$_GET['c']."' And IDGrupo='".$_GET['id']."'";
		//echo $Consulta;
		$SQL=sqlsrv_query($conexion,$Consulta);
		$records=array();
		$row=sqlsrv_fetch_array($SQL);
		$records=array(
			'NombreGrupo' => $row['NombreGrupo']
		);
		echo json_encode($records);
	}
	
	sqlsrv_close($conexion);
}
?>