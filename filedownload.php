<?php
if (isset($_REQUEST['file']) && $_REQUEST['file'] != "") {
	require_once("includes/conexion.php");

	$Filtro = isset($_REQUEST['filtro']) ? base64_decode($_REQUEST['filtro']) : ""; //Filtro
	$AnioServicio = isset($_REQUEST['anio']) ? base64_decode($_REQUEST['anio']) : ""; //Año de servicio
	$NombreArchivo = "";
	$size = 0;
	$ZipMode = 0;

	if (isset($_REQUEST['zip']) && ($_REQUEST['zip']) == base64_encode('1')) {
		$ZipMode = 1;
	}

	$RutaLocal = "download/" . $_SESSION['CodUser'] . "/";


	if ($ZipMode == 1) { //Comprimir los archivos descargados
		ini_set('max_execution_time', '900');
		ini_set('memory_limit', '1024M');
		set_time_limit(0);

		if (PermitirFuncion(205)) {
			$SQL = Seleccionar('uvw_tbl_Publicadores', 'IDPublicador, NombrePublicador', "NumCong='" . $_SESSION['NumCong'] . "' and IDGrupo='" . $_SESSION['Grupo'] . "' $Filtro", 'NombrePublicador');
			$sw = 1;
		} else {
			$SQL = Seleccionar('uvw_tbl_Publicadores', 'IDPublicador, NombrePublicador', "NumCong='" . $_SESSION['NumCong'] . "' $Filtro", 'NombrePublicador');
			$sw = 1;
			//$row=sqlsrv_fetch_array($SQL);
		}

		$Files = array();
		$count_reg = 0;

		LimpiarDirTemp();

		while ($row = sqlsrv_fetch_array($SQL)) {
			$Files[$count_reg] = $row['NombrePublicador'] . ".pdf";
			$count_reg++;
			$_GET['id'] = base64_encode($row['IDPublicador']);
			$_GET['anio'] = base64_encode($AnioServicio);
			$_GET['zip'] = 1;
			include("rpt_informe_registro_publicador.php");
		}
		// echo count($Files)."\n";

		//Crear archivo ZIP e insertar los archivos
		$zip = new ZipArchive();
		$zipName = "Tarjetas_S21_" . $_SESSION['NumCong'] . "_" . date('YmdHi') . ".zip";
		$filezip = $RutaLocal . $zipName;
		//echo $filezip;
		//exit();

		// var_dump($Files);

		if ($zip->open($filezip, ZIPARCHIVE::CREATE) === TRUE) {
			$Count = count($Files);
			$count_file = 0;
			//$zip->close();
			echo $Count."\n";
			while ($count_file < $Count) {
				$zip->addFile($RutaLocal . $Files[$count_file], $Files[$count_file]);
				// echo "Se agregó: ".$RutaLocal . $Files[$count_file]."\n";
				$count_file++;
			}
			// exit();
			$zip->close();
			//$filename=$zipName;
		} else {
			exit("No se puede abrir el archivo $filezip\n");
		}
	}

	//BUSCAR ARCHIVO PARA DESCARGAR
	if ($ZipMode == 1) {
		$filename = $filezip;
		$NombreArchivo = $zipName;
	} else {
		$filename = $RutaAttachSAP[0] . $row['NombreArchivo'];
		$NombreArchivo = $row['NombreArchivo'];
	}

	$size = filesize($filename);


	header("Content-Transfer-Encoding: binary");
	header('Content-type: application/pdf', true);
	header("Content-Type: application/force-download");
	header('Content-Disposition: attachment; filename="' . $NombreArchivo . '"');
	header("Content-Length: $size");
	readfile($filename);

	//echo $filename;
}
