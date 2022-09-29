<?php

if (file_exists("includes/conect_srv.php")) {
	require_once("includes/conect_srv.php");
} else {
	require_once("conect_srv.php");
}

//if(file_exists("includes/conect_odbc.php")){
//	require_once("includes/conect_odbc.php");
//}else{
//	require_once("conect_odbc.php");
//}

function PermitirAcceso($Permiso)
{ //Para evitar acceder a la pagina
	global $conexion;
	$PaginaError = "404.php";
	$Consulta = "Select 1 From uvw_tbl_PermisosPerfiles Where ID_Permiso='" . $Permiso . "' and IDPerfilUsuario='" . $_SESSION['Perfil'] . "'";
	$SQL = sqlsrv_query($conexion, $Consulta, array(), array("Scrollable" => 'Buffered'));
	$Num = sqlsrv_num_rows($SQL);
	if ($Num == 1) {
		return true;
	} else {
		header("Location:" . $PaginaError);
	}
}

function PermitirFuncion($Permiso)
{ //Para evitar acceder a una opcion en particular
	global $conexion;

	$return = false;

	if (is_array($Permiso)) {
		foreach ($Permiso as $Valor) {
			$Consulta = "Select 1 From uvw_tbl_PermisosPerfiles Where ID_Permiso='" . $Valor . "' and IDPerfilUsuario='" . $_SESSION['Perfil'] . "'";
			$SQL = sqlsrv_query($conexion, $Consulta, array(), array("Scrollable" => 'Buffered'));
			$Num = sqlsrv_num_rows($SQL);
			if ($Num == 1) {
				$return = true;
			}
		}
	} else {
		$Consulta = "Select 1 From uvw_tbl_PermisosPerfiles Where ID_Permiso='" . $Permiso . "' and IDPerfilUsuario='" . $_SESSION['Perfil'] . "'";
		$SQL = sqlsrv_query($conexion, $Consulta, array(), array("Scrollable" => 'Buffered'));
		$Num = sqlsrv_num_rows($SQL);
		if ($Num == 1) {
			$return = true;
		}
	}

	return $return;
}

function InsertarLog($Type, $Code, $Consulta)
{
	global $conexion;
	if ($Type == 1) {
		$Type = "Error";
	} else {
		$Type = "Success";
	}
	$Consulta = str_replace("'", "''", $Consulta);

	if (isset($_SESSION['CodUser'])) {
		$User = $_SESSION['CodUser'];
	} else {
		$User = "";
	}

	$Cons = "EXEC usp_tbl_Log '" . $User . "','" . $Type . "','" . $Code . "','" . $Consulta . "'";
	//$InsertLog="Insert Into tbl_Log Values (GETDATE(),'".$_SESSION['CodUser']."','".$Type."','".$Code."','".$Consulta."')";
	//echo $InsertLog;
	//exit();
	//sqlsrv_query($conexion,$InsertLog);
	if (!sqlsrv_query($conexion, $Cons)) {
		$Cons = "EXEC usp_tbl_Log '" . $User . "','" . $Type . "','" . $Code . "','" . utf8_encode($Consulta) . "'";
		if (!sqlsrv_query($conexion, $Cons)) {
			echo "Error al insertar Log " . $Code;
			echo "<br>";
			echo $Cons;
			exit();
		}
	}
}

function ConsultarPago($DocEntry, $CardCode)
{
	global $conexion;
	$Con = "EXEC usp_ConsultarPagoFactura '" . $DocEntry . "', '" . $CardCode . "'";
	$SQL = sqlsrv_query($conexion, $Con);
	$row = sqlsrv_fetch_array($SQL);
	return $row;
}

function DiasTranscurridos($FechaInicial, $FechaFinal)
{ //Calcular dias transcurridos entre dos fechas
	$Dias = (strtotime($FechaInicial) - strtotime($FechaFinal)) / 86400;
	$Result = array();
	//$Dias=abs($Dias); 
	$Dias = floor($Dias);
	if (($Dias >= -2) && ($Dias < 0)) { //Establecer clase de colores del texto dependiendo de los dias vencidos
		$Result[0] = "text-warning";
	} elseif ($Dias > 0) {
		$Result[0] = "text-danger";
	} else {
		$Result[0] = "text-primary";
	}
	$Result[1] = $Dias;

	return $Result;
}

function FormatoFecha($Fecha, $Hora = '')
{ //Dar formato a la fecha para insertar en BD
	$FechaResult = "";
	$F = explode(" ", $Fecha);

	$FechaResult = str_replace("-", "", $F[0]);
	$FechaResult = str_replace(" ", "", $FechaResult);

	if (isset($F[1]) && ($F[1] != "")) {
		$Hora = str_replace(" ", "", $F[1]);
	}
	if ($Hora != "") {
		$FechaResult = $FechaResult . " " . $Hora;
	}
	return $FechaResult;
}

function FormatoFechaToSAP($Fecha, $Hora = '')
{ //Dar formato a la fecha para insertar en BD
	$FechaResult = "";

	if ($Hora == "") {
		$Hora = "00:00:00";
	}

	$FechaResult = $Fecha . "T" . $Hora;

	return $FechaResult;
}

function SubComent($pComents, $Len = 100)
{ //Substraer solo la cantidad de caracteres del string
	$result = "";

	$result = (strlen($pComents) > $Len) ? substr($pComents, 1, $Len) . "..." : $pComents;

	return $result;
}

function ReturnCons($pVista, $pCampos, $pWhere = '', $pOrderBy = '', $pOrderType = '', $pType = 1)
{ //Devolver la consulta generada
	if ($pType == 1) { //Consulta a SQL SERVER
		$Consulta = "EXEC usp_ConsultarTablasSAP '" . $pVista . "', '" . $pCampos . "', '" . str_replace("'", "''", $pWhere) . "', '" . $pOrderBy . "', '" . $pOrderType . "'";
		return $Consulta;
	} elseif ($pType == 2) { // Consulta a SAP HANA
		$Consulta = "CALL " . $databaseHN . ".USP_NDG_CONSULTAR_TABLAS_SAP('NDG_ONE_" . $pVista . "','" . str_replace(']', '"', str_replace('[', '"', $pCampos)) . "','" . str_replace(']', '"', str_replace('[', '"', str_replace("'", "''", $pWhere))) . "','" . str_replace(']', '"', str_replace('[', '"', $pOrderBy)) . "','" . $pOrderType . "')";
		return $Consulta;
	}
}

function Seleccionar($pVista, $pCampos, $pWhere = '', $pOrderBy = '', $pOrderType = '', $pType = 1, $pDebugMode = 0)
{ //Seleccionar datos de una tabla
	if ($pType == 1) { //Consulta a SQL SERVER
		global $conexion;
		$Consulta = "EXEC usp_ConsultarTablasSAP '" . $pVista . "', '" . $pCampos . "', '" . str_replace("'", "''", $pWhere) . "', '" . $pOrderBy . "', '" . $pOrderType . "'";
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = sqlsrv_query($conexion, $Consulta, array(), array("Scrollable" => 'Buffered'));
		if (!$SQL) {
			$SQL = sqlsrv_query($conexion, $Consulta);
		}
		// InsertarLog(2, 0, $Consulta);
		return $SQL;
	} elseif ($pType == 2) { //Consulta a SAP HANA
		global $conexion_odbc;
		global $databaseHN;
		$Consulta = "CALL " . $databaseHN . ".USP_NDG_CONSULTAR_TABLAS_SAP('NDG_ONE_" . $pVista . "','" . str_replace(']', '"', str_replace('[', '"', $pCampos)) . "','" . str_replace(']', '"', str_replace('[', '"', str_replace("'", "''", $pWhere))) . "','" . str_replace(']', '"', str_replace('[', '"', $pOrderBy)) . "','" . $pOrderType . "')";
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = odbc_exec($conexion_odbc, $Consulta);
		return $SQL;
	} elseif ($pType == 3) { //Consulta a MySQL (MariaDB)
		global $conexion_mysql;
		$Consulta = "CALL usp_ConsultarTablas ('" . $pVista . "', '" . $pCampos . "', '" . str_replace("'", "''", $pWhere) . "', '" . $pOrderBy . "', '" . $pOrderType . "');";
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = mysqli_query($conexion_mysql, $Consulta);
		mysqli_next_result($conexion_mysql);
		return $SQL;
	}
}

function SeleccionarGroupBy($pVista, $pCampos, $pWhere = '', $pGroupBy = '', $pOrderBy = '', $pOrderType = '', $pType = 1, $pDebugMode = 0)
{ //Seleccionar datos de una tabla
	if ($pType == 1) { //Consulta a SQL SERVER
		global $conexion;
		$Consulta = "EXEC usp_ConsultarTablasSAPGroupby '" . $pVista . "', '" . $pCampos . "', '" . str_replace("'", "''", $pWhere) . "',  '" . $pGroupBy . "', '" . $pOrderBy . "', '" . $pOrderType . "'";
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = sqlsrv_query($conexion, $Consulta, array(), array("Scrollable" => 'Buffered'));
		return $SQL;
	} elseif ($pType == 2) { //Consulta a SAP HANA
		global $conexion_odbc;
		global $databaseHN;
		$Consulta = "CALL " . $databaseHN . ".USP_NDG_CONSULTAR_TABLAS_SAPGROUPBY('NDG_ONE_" . $pVista . "','" . str_replace(']', '"', str_replace('[', '"', $pCampos)) . "','" . str_replace(']', '"', str_replace('[', '"', str_replace("'", "''", $pWhere))) . "','" . str_replace(']', '"', str_replace('[', '"', str_replace("'", "''", $pGroupBy))) . "','" . str_replace(']', '"', str_replace('[', '"', $pOrderBy)) . "','" . $pOrderType . "')";
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = odbc_exec($conexion_odbc, $Consulta);
		return $SQL;
	} elseif ($pType == 3) { //Consulta a MySQL (MariaDB)
		global $conexion_mysql;
		$Consulta = "CALL usp_ConsultarTablas ('" . $pVista . "', '" . $pCampos . "', '" . str_replace("'", "''", $pWhere) . "', '" . $pOrderBy . "', '" . $pOrderType . "');";
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = mysqli_query($conexion_mysql, $Consulta);
		mysqli_next_result($conexion_mysql);
		return $SQL;
	}
}

function Eliminar($pVista, $pWhere = '', $pIdReg = 0, $pType = 1, $pDebugMode = 0)
{ //Eliminar datos de una tabla
	if ($pType == 1) { //Consulta a SQL SERVER
		global $conexion;
		$Consulta = "DELETE FROM " . $pVista;
		if ($pWhere != "") {
			$Consulta .= " WHERE " . $pWhere;
		}
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = sqlsrv_query($conexion, $Consulta, array(), array("Scrollable" => 'Buffered'));
		if ($SQL) {
			if ($pIdReg >= 0) {
				InsertarLog(2, $pIdReg, $Consulta);
			}
		} else {
			InsertarLog(1, $pIdReg, $Consulta);
		}
		return $SQL;
	} elseif ($pType == 2) { //Consulta a SAP HANA
		global $conexion_odbc;
		global $databaseHN;
		$Consulta = 'DELETE FROM "' . $databaseHN . '"."NDG_ONE_' . $pVista . '"';
		if ($pWhere != "") {
			$Consulta .= ' WHERE ' . str_replace(']', '"', str_replace('[', '"', $pWhere));
		}
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = odbc_exec($conexion_odbc, $Consulta);
		if ($SQL) {
			if ($pIdReg >= 0) {
				InsertarLog(2, $pIdReg, $Consulta);
			}
		} else {
			InsertarLog(1, $pIdReg, $Consulta);
		}
		return $SQL;
	} elseif ($pType == 3) { //Consulta a MySQL (MariaDB)
		global $conexion_mysql;
		$Consulta = "CALL usp_ConsultarTablas ('" . $pVista . "', '" . $pCampos . "', '" . str_replace("'", "''", $pWhere) . "', '" . $pOrderBy . "', '" . $pOrderType . "');";
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = mysqli_query($conexion_mysql, $Consulta);
		mysqli_next_result($conexion_mysql);
		return $SQL;
	}
}

function sql_fetch_array($pSQL, $pType = 1)
{ //fetch_array SQL or HANNA
	if ($pType == 1) { //Consulta a SQL SERVER
		global $conexion;
		$row = sqlsrv_fetch_array($pSQL);
		return $row;
	} elseif ($pType == 2) { // Consulta a SAP HANA
		global $conexion_odbc;
		global $databaseHN;
		$row = odbc_fetch_array($pSQL);
		return $row;
	}
}

function sql_num_rows($pSQL, $pType = 1)
{ //fetch_array SQL or HANNA
	if ($pType == 1) { //Consulta a SQL SERVER
		global $conexion;
		$Num = sqlsrv_num_rows($pSQL);
		return $Num;
	} elseif ($pType == 2) { // Consulta a SAP HANA
		global $conexion_odbc;
		global $databaseHN;
		$Num = odbc_num_rows($pSQL);
		return $Num;
	}
}

function EjecutarSP($pNameSP, $pParametros = "", $pIdReg = 0, $pType = 1, $pDebugMode = 0)
{ //Ejecutar un SP en la BD
	if ($pType == 1) { //Consulta a SQL SERVER
		global $conexion;
		$Param = "";
		if (is_array($pParametros)) {
			$Param = implode(',', $pParametros);
		} elseif ($pParametros != "") {
			$Param = "'" . $pParametros . "'";
		}
		$Consulta = "EXEC " . $pNameSP . " " . $Param;
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = sqlsrv_query($conexion, $Consulta, array(), array("Scrollable" => 'Buffered'));
		if ($SQL) {
			if ($pIdReg >= 0) {
				InsertarLog(2, $pIdReg, $Consulta);
			}
		} else {
			$SQL = sqlsrv_query($conexion, $Consulta);
			if ($SQL) {
				if ($pIdReg >= 0) {
					InsertarLog(2, $pIdReg, $Consulta);
				}
			} else {
				InsertarLog(1, $pIdReg, $Consulta);
			}
		}
		return $SQL;
	} elseif ($pType == 2) { // Consulta a SAP HANA
		global $conexion_odbc;
		global $databaseHN;
		$Param = "";
		if (is_array($pParametros)) {
			$Param = implode(',', $pParametros);
		} elseif ($pParametros != "") {
			$Param = "'" . $pParametros . "'";
		}
		if ($Param != "") {
			$Param = "(" . $Param . ")";
		}
		$Consulta = 'CALL "' . $databaseHN . '"."' . $pNameSP . '"' . $Param;
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = odbc_exec($conexion_odbc, $Consulta);
		if ($SQL) {
			if ($pIdReg >= 0) {
				InsertarLog(2, $pIdReg, $Consulta);
			}
		} else {
			InsertarLog(1, $pIdReg, $Consulta);
		}
		return $SQL;
	} elseif ($pType == 3) { //Consulta a MySQL (MariaDB)
		global $conexion_mysql;
		$Param = "";
		if (is_array($pParametros)) {
			$Param = implode(',', $pParametros);
		} elseif ($pParametros != "") {
			$Param = "'" . $pParametros . "'";
		}
		if ($Param != "") {
			$Param = "(" . $Param . ");";
		}
		$Consulta = 'CALL ' . $pNameSP . ' ' . $Param;
		if ($pDebugMode == 1) {
			echo $Consulta . "<br>";
			exit();
		}
		$SQL = mysqli_query($conexion_mysql, $Consulta);
		mysqli_next_result($conexion_mysql);
		if ($SQL) {
			if ($pIdReg >= 0) {
				InsertarLog(2, $pIdReg, $Consulta);
			}
		} else {
			InsertarLog(1, $pIdReg, $Consulta);
		}
		return $SQL;
	}
}

function ObtenerVariable($Variable)
{ //Obtener valor de variable global
	global $conexion;
	$SQL = Seleccionar('uvw_tbl_VariablesGlobales', 'Valor', "NombreVariable='" . $Variable . "'");
	$row = sqlsrv_fetch_array($SQL);
	//$Num=sqlsrv_num_rows($SQL);
	return $row['Valor'];
}

function ObtenerValorDefecto($TipoObjeto, $NombreCampo)
{ //Obtener valor por defecto configurado en el usuario dependiendo del documento
	global $conexion;
	$SQL = Seleccionar('uvw_tbl_CamposValoresDefecto_Detalle', 'ValorCampo', "TipoObjeto='" . $TipoObjeto . "' AND NombreCampo='" . $NombreCampo . "' AND ID_Usuario='" . $_SESSION['CodUser'] . "'");
	$row = sqlsrv_fetch_array($SQL);
	//$Num=sqlsrv_num_rows($SQL);
	return $row['ValorCampo'];
}

function EliminarArchivo($pRuta)
{ //Eliminar un archivo en una ruta
	foreach (glob($pRuta) as $archivo) {
		//echo $archivos_carpeta;
		if (!is_dir($archivo)) {
			unlink($archivo);
		}
	}
}

function EliminarTemporal($carpeta)
{ //Eliminar los archivos de la carpeta temporal
	foreach (glob($carpeta . "/*") as $archivos_carpeta) {
		//echo $archivos_carpeta;
		if (is_dir($archivos_carpeta)) {
			EliminarTemporal($archivos_carpeta);
		} else {
			unlink($archivos_carpeta);
		}
	}
	rmdir($carpeta);
}

function LimpiarDirTemp()
{ //Limpiar la carpeta temporal antes de cargar nuevos anexos
	$route = "download/" . $_SESSION['CodUser'] . "/";
	if (file_exists($route)) {
		EliminarTemporal($route);
		mkdir($route, 0777, true);
	} else {
		mkdir($route, 0777, true);
	}
}

function CrearObtenerDirTemp()
{ //Crear y retornar la carpeta de anexos temporales
	$temp = ObtenerVariable("CarpetaTmp");
	$route = $temp . "/" . $_SESSION['CodUser'] . "/";
	if (!file_exists($route)) {
		mkdir($route, 0777, true);
	}
	return $route;
}

function CrearObtenerDirAnx($pCarpetaAnexo)
{ //Crear y retornar la carpeta de anexos locales
	$carp_archivos = ObtenerVariable("RutaArchivos");
	$carp_anexos = $pCarpetaAnexo;
	$dir_new = $_SESSION['BD'] . "/" . $carp_archivos . "/" . $carp_anexos . "/";
	if (!file_exists($dir_new)) {
		mkdir($dir_new, 0777, true);
	}
	return $dir_new;
}

function CrearObtenerDirRuta($pRuta)
{ //Crear y retornar la carpeta de la ruta que se pe pase
	$carp_anexos = $pRuta;
	if (!file_exists($carp_anexos)) {
		mkdir($carp_anexos, 0777, true);
	}
	return $carp_anexos;
}

function LimpiarDirTempFirma()
{ //Limpiar la carpeta temporal antes de cargar nuevos anexos
	$temp = "tmp_sig";
	$route = $temp . "/" . $_SESSION['CodUser'] . "/";
	if (file_exists($route)) {
		EliminarTemporal($route);
		mkdir($route, 0777, true);
	} else {
		mkdir($route, 0777, true);
	}
}

function CrearObtenerDirTempFirma()
{ //Crear y retornar la carpeta de firmas temporales
	$temp = "tmp_sig";
	$route = $temp . "/" . $_SESSION['CodUser'] . "/";
	if (!file_exists($route)) {
		mkdir($route, 0777, true);
	}
	return $route;
}

function FormatoNombreAnexo($pAnexo, $pAddPrefijo = true)
{ //Cambia el nombre del anexo para guardarlo
	//Sacar la extension del archivo
	$NuevoNombre = array();
	$FileActual = $pAnexo;
	$exp = explode('.', $FileActual);
	$Ext = end($exp);
	//Sacar el nombre sin la extension
	$OnlyName = substr($FileActual, 0, strlen($FileActual) - (strlen($Ext) + 1));
	//Reemplazar espacios
	$OnlyName = str_replace(" ", "_", $OnlyName);
	if ($pAddPrefijo) {
		$Prefijo = substr(uniqid(rand()), 0, 3);
		$OnlyName = LSiqmlObs($OnlyName) . "_" . date('Ymd') . $Prefijo;
	}

	$NuevoNombre[0] = $OnlyName . "." . $Ext;
	$NuevoNombre[1] = $OnlyName;
	$NuevoNombre[2] = $Ext;

	return $NuevoNombre;
}

function ObtenerDirAttach()
{ //Obtener la ruta de la direccion de anexos de SAP B1, parcheada para Windows o Linux
	global $conexion;
	$Ruta = array();
	$Ruta[0] = "";

	//Selecciono los datos del archivo
	$SQLRutaAttachSAP = Seleccionar('uvw_Sap_tbl_Empresa', 'AttachPath');
	$RutaAttachSAP = sqlsrv_fetch_array($SQLRutaAttachSAP);

	if (SO == "Linux") {

		/******* LINUX *******/
		$RutaAttachSAP[0] = str_replace("//", "", preg_replace('/\\\/', '/', $RutaAttachSAP[0]));

		//Credenciales
		$Dominio = DOMINIO_WIN;
		$User = USER_WIN;
		$Pass = PASS_WIN;

		$Ruta[0] = "smb://" . $Dominio . $User . $Pass . $RutaAttachSAP[0];
	} else {

		/******* WINDOWS *******/
		$Ruta[0] = $RutaAttachSAP[0];
	}

	return $Ruta;
}

function EnviarWebServiceSAP($pNombreWS, $pParametros, $pJSON = false, $pAPI = false, $method = 'POST')
{

	if (!$pJSON) {
		$result = array();

		//PARA CONECTARSE A UN METODO EN SOAP XML(DEFAULT)
		if (file_exists("includes/conect_ws.php")) {
			require_once("includes/conect_ws.php");
		} else {
			require_once("conect_ws.php");
		}
		$Client->$pNombreWS($pParametros);
		$Respuesta = $Client->__getLastResponse();
		$Contenido = new SimpleXMLElement($Respuesta, 0, false, "s", true);
		$espaciosDeNombres = $Contenido->getNamespaces(true);
		$Nodos = $Contenido->children($espaciosDeNombres['s']);
		$Nodo =	$Nodos->children($espaciosDeNombres['']);
		$Nodo2 =	$Nodo->children($espaciosDeNombres['']);
		$Archivo = json_decode($Nodo2, true);
		$result['ID_Respuesta'] = $Archivo['ID_Respuesta'];
		$result['DE_Respuesta'] = $Archivo['DE_Respuesta'];
		return $result;
	} else {
		if (!$pAPI) {
			//PARA CONECTARSE A UN METODO RESTFUL QUE DEVUELVE UN OBJETO EN JSON
			$Url = ObtenerVariable('DireccionWSIntJSON');
			$apiUrl = $Url . $pNombreWS;
			$curl = curl_init();
			/*$data = array(
				'pMetodo' => 1
			);*/
			$payload = json_encode($pParametros);
			curl_setopt($curl, CURLOPT_URL, $apiUrl);
			if ($method != "POST") {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			} else {
				curl_setopt($curl, CURLOPT_POST, true);
			}
			curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($curl, CURLOPT_ENCODING, "");
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			//La respuesta la devuelve en JSON
			$json = curl_exec($curl);
			//Decodifico el JSON en la variable $result
			$result = json_decode($json);
			//Como el primer atributo del JSON en el nombre del parametro mas la palabra Result, los concateno para que devuelva solo los atributos necesarios
			$Objeto = $pNombreWS . 'Result';
			//Meto en result solo los atributos obtenidos al sacar el atributo padre
			$result = $result->$Objeto;
			curl_close($curl);
			//echo json_encode($json);
			//print($json);
			//$jsonnew=json_decode($json);
			//echo "Success: ".$jsonnew['Success'];
			//var_dump(json_decode($json, true));
			return $result;
		} else {
			//PARA CONECTARSE A UNA API QUE DEVUELVE UN OBJETO EN JSON
			$Url = ObtenerVariable('DireccionAPIJSON');
			$apiUrl = $Url . $pNombreWS;
			$payload = json_encode($pParametros);
			$JWT = "";

			if ((isset($_SESSION['JWT'])) && ($_SESSION['JWT'] != "")) {
				$JWT = "Authorization: Bearer " . $_SESSION['JWT'];
			}

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $apiUrl);
			if ($method != "POST") {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			} else {
				curl_setopt($curl, CURLOPT_POST, true);
			}
			curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($curl, CURLOPT_ENCODING, "");
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $JWT));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			//La respuesta la devuelve en JSON
			$json = curl_exec($curl);
			//echo "json: ".$json;
			$cod_http = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			//echo "Codigo HTTP:".$cod_http;
			if ($cod_http != 200) { //Ocurrio un error
				$result = json_decode($json);
				if (!isset($result->Success)) {
					$json_array = array(
						"Success" => 0,
						"Mensaje" => "Ha ocurrido un error en el servicio. Por favor verificar. Código de error: $cod_http"
					);
					$json = json_encode($json_array);
				}
			}
			//Decodifico el JSON en la variable $result
			$result = json_decode($json);
			curl_close($curl);
			return $result;
		}
	}
}

function DescargarFileAPI($pNombreWS, $method = 'GET')
{

	$Url = ObtenerVariable('DireccionAPIJSON');
	$apiUrl = $Url . $pNombreWS;
	$JWT = "";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $apiUrl);

	if ((isset($_SESSION['JWT'])) && ($_SESSION['JWT'] != "")) {
		$JWT = "Authorization: Bearer " . $_SESSION['JWT'];
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $JWT));
	}

	if ($method != "GET") {
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	}

	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($curl);
	//echo "json: ".$json;
	$cod_http = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$array_res = curl_getinfo($curl);
	//echo "Codigo HTTP:".$cod_http;
	if ($cod_http != 200) { //Ocurrio un error
		echo "Codigo " . $cod_http . ": (" . $method . ") " . $array_res['content_type'];
	}
	curl_close($curl);
	return $result;
}

function DescargarFile($pNombreWS, $method = 'GET')
{

	//$Url=ObtenerVariable('DireccionAPIJSON');
	$apiUrl = $pNombreWS;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $apiUrl);

	if ($method != "GET") {
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	}

	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($curl);
	//echo "json: ".$json;
	$cod_http = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$array_res = curl_getinfo($curl);
	//echo "Codigo HTTP:".$cod_http;
	if ($cod_http != 200) { //Ocurrio un error
		echo "Codigo " . $cod_http . ": (" . $method . ") " . $array_res['content_type'];
	}
	curl_close($curl);
	return $result;
}

function AuthJWT($pUser, $pPassword, $pEndpoint = 'Login', $pParametros = '')
{

	$Url = ObtenerVariable('DireccionAPIJSON');
	$apiUrl = $Url . $pEndpoint;
	if ($pParametros == "") {
		$pParametros = array(
			'usuario' => $pUser,
			'password' => base64_encode($pPassword),
			'app' => "PortalOne",
			'version_app' => "2.0"
		);
	}

	$payload = json_encode($pParametros);
	$return = array();

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $apiUrl);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$json = curl_exec($curl);
	$cod_http = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	$result = json_decode($json);
	if ($cod_http != 200) { //Ocurrio un error
		if (!isset($result->Success)) {
			$Success = 0;
			$token = "Ha ocurrido un error en el servicio. Por favor verificar. Código de error: $cod_http";
		} else {
			$Success = 0;
			$token = $result->Mensaje;
		}
	} else {
		if (isset($result->Objeto->token) && ($result->Objeto->token != "")) {
			$Success = 1;
			$token = $result->Objeto->token;
		} else {
			$Success = 0;
			$token = "";
		}
	}
	$return['Success'] = $Success;
	$return['Token'] = $token;
	return $return;
}

function UltimoDiaMes($pMes, $pAnio = "")
{ //Obtener el ultimo dia del mes
	if ($pAnio == "") {
		$pAnio = date('Y');
	}
	$month = $pMes; //date('m');
	$year = $pAnio; //date('Y');
	$day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

	return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
}

function PrimerDiaMes($pMes, $pAnio = "")
{ //Obtener el primer dia del mes
	if ($pAnio == "") {
		$pAnio = date('Y');
	}
	$month = $pMes; //date('m');
	$year = $pAnio; //date('Y');
	return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
}

function IconAttach($TypeFile, $Version = 1)
{ //Colocar un icono en los archivos anexos dependiendo de la extension
	if ($Version == 1) {
		switch (strtolower($TypeFile)) {
			case "pdf":
				$Icon = "fa fa-file-pdf-o";
				break;
			case "png":
				$Icon = "fa fa-file-image-o";
				break;
			case "jpg":
				$Icon = "fa fa-file-image-o";
				break;
			case "xls":
				$Icon = "fa fa-file-excel-o";
				break;
			case "xlsx":
				$Icon = "fa fa-file-excel-o";
				break;
			case "doc":
				$Icon = "fa fa-file-word-o";
				break;
			case "docx":
				$Icon = "fa fa-file-word-o";
				break;
			case "zip":
				$Icon = "fa fa-file-zip-o";
				break;
			case "rar":
				$Icon = "fa fa-file-zip-o";
				break;
			case "txt":
				$Icon = "fa fa-file-text-o";
				break;
			default:
				$Icon = "fa fa-file-o";
		}
	} elseif ($Version == 2) {
		switch (strtolower($TypeFile)) {
			case "pdf":
				$Icon = "fas fa-file-pdf";
				break;
			case "png":
				$Icon = "fas fa-file-image";
				break;
			case "jpg":
				$Icon = "fas fa-file-image";
				break;
			case "xls":
				$Icon = "fas fa-file-excel";
				break;
			case "xlsx":
				$Icon = "fas fa-file-excel";
				break;
			case "doc":
				$Icon = "fas fa-file-word";
				break;
			case "docx":
				$Icon = "fas fa-file-word";
				break;
			case "zip":
				$Icon = "fas fa-file-archive";
				break;
			case "rar":
				$Icon = "fas fa-file-archive";
				break;
			case "txt":
				$Icon = "fas fa-file-alt";
				break;
			default:
				$Icon = "fas fa-file-alt";
		}
	}


	return $Icon;
}

function ContarSucursalesCliente($CardCode, $ID_Usuario)
{ //Contar cuantas sucursales tiene asignados el usuario
	global $conexion;
	$Con = "Select Count(ID_Usuario) as Cant From uvw_tbl_SucursalesClienteUsuario Where CardCode='" . $CardCode . "' And ID_Usuario='" . $ID_Usuario . "'";
	$SQL = sqlsrv_query($conexion, $Con);
	$row = sqlsrv_fetch_array($SQL);
	return $row['Cant'];
}

function ConsultarNotasActividad($ID_Actividad)
{ //Consultar si la actividad tiene notas o no
	global $conexion;
	$Con = "Select NotasActividad From uvw_Sap_tbl_Actividades Where ID_Actividad='" . $ID_Actividad . "'";
	$SQL = sqlsrv_query($conexion, $Con);
	$row = sqlsrv_fetch_array($SQL);
	if ($row['NotasActividad'] == "") {
		return "NO";
	} else {
		return "SI";
	}
}

function ConsultarDescargaArchivo($ID_Archivo)
{ //Verificar si un archivo ya fue descargado por el usuario actual
	global $conexion;
	$ConsDown = "EXEC usp_tbl_DescargaArchivos '" . $_SESSION['CodUser'] . "','" . $ID_Archivo . "',1";
	$SQLDown = sqlsrv_query($conexion, $ConsDown);
	$rowDown = sqlsrv_fetch_array($SQLDown);
	return $rowDown['Result'];
}

function ContarClienteUsuario($ID_Usuario)
{ //Contar cuantas sucursales tiene asignados el usuario
	global $conexion;
	$Con = "Select Count(ID_Usuario) as Cant From uvw_tbl_ClienteUsuario Where ID_Usuario='" . $ID_Usuario . "'";
	$SQL = sqlsrv_query($conexion, $Con);
	$row = sqlsrv_fetch_array($SQL);
	return $row['Cant'];
}

function SumarFacturasPendientes($CodigoCliente)
{ //Sumar el valor total de las facturas pendientes de un cliente
	global $conexion;
	$Con = "Select SUM(SaldoDocumento) AS Total From uvw_Sap_tbl_FacturasPendientes Where ID_CodigoCliente='" . $CodigoCliente . "'";
	$SQL = sqlsrv_query($conexion, $Con);
	$row = sqlsrv_fetch_array($SQL);
	return $row['Total'];
}

function SumarTotalLotesEntregar($ItemCode, $LineNum, $WhsCode, $CardCode, $Objtype, $Sentido, $Usuario)
{ //Sumar el total que se está entregando en los lotes de SAP B1
	global $conexion;
	$Parametros = array(
		"'" . $ItemCode . "'",
		"'" . $LineNum . "'",
		"'" . $WhsCode . "'",
		"'" . $CardCode . "'",
		"'" . $Objtype . "'",
		"'" . $Sentido . "'",
		"'" . $Usuario . "'"
	);
	$SQL = EjecutarSP('usp_ConsultarLotesTotalEntregar', $Parametros);
	$row = sqlsrv_fetch_array($SQL);
	return number_format($row['Total'], 0);
}

function SumarTotalSerialesEntregar($ItemCode, $LineNum, $WhsCode, $CardCode, $Objtype, $Usuario)
{ //Sumar el total que se está entregando en los lotes de SAP B1
	global $conexion;
	$Parametros = array(
		"'" . $ItemCode . "'",
		"'" . $LineNum . "'",
		"'" . $WhsCode . "'",
		"'" . $CardCode . "'",
		"'" . $Objtype . "'",
		"'" . $Usuario . "'"
	);
	$SQL = EjecutarSP('usp_ConsultarSerialesTotalEntregar', $Parametros);
	$row = sqlsrv_fetch_array($SQL);
	return number_format($row['Total'], 0);
}

function FormatoNombreArchivo($NombreArchivo)
{ //Darle formato al nombre del archivo, quitando los "_"
	//$NombreArchivo=utf8_decode($NombreArchivo);
	//Sacar la extension del archivo
	$FileActual = $NombreArchivo;
	$exp = explode('.', $FileActual);
	$Ext = end($exp);
	//	$Ext = end(explode('.',$NombreArchivo));    
	//Sacar el nombre sin la extension
	$OnlyName = substr($NombreArchivo, 0, strlen($NombreArchivo) - (strlen($Ext) + 1));
	$NuevoNombre = substr(str_replace("_", " ", $OnlyName), 0, -12) . "." . $Ext;
	return $NuevoNombre;
}

function NormalizarNombreArchivo($NombreArchivo)
{
	$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	$modificadas = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	$NombreArchivo = utf8_decode($NombreArchivo);
	$NombreArchivo = strtr($NombreArchivo, utf8_decode($originales), $modificadas);
	//$NombreArchivo = strtolower($NombreArchivo);
	return utf8_encode($NombreArchivo);
}

function ValidarEstadoArchivoCargue($NombreCliente, $NombreCategoria, $Sucursal, $Archivo)
{ //Validar la información de cargue
	global $conexion;
	$Error = array();
	if ($NombreCliente == "") {
		$Error[0][0] = 1;
		$Error[0][1] = "No existe el cliente";
	} elseif ($Sucursal == "") {
		$Error[0][0] = 2;
		$Error[0][1] = "No existe la sucursal";
	} elseif ($NombreCategoria == "") {
		$Error[0][0] = 3;
		$Error[0][1] = "No existe la categoria";
	} elseif ($Archivo == "") {
		$Error[0][0] = 4;
		$Error[0][1] = "No se digitó el archivo";
	} elseif (!file_exists("cargue/" . $Archivo)) {
		$Error[0][0] = 5;
		$Error[0][1] = "No existe el archivo en la ruta de cargue: " . $Archivo;
	} else {
		$Error[0][0] = 0;
		$Error[0][1] = "";
	}
	return $Error;
}

function ValidarEstadoProductosCargue($ItemName, $Categoria, $Archivo)
{ //Validar la información de cargue
	global $conexion;
	$Error = array();
	if ($ItemName == "") {
		$Error[0][0] = 1;
		$Error[0][1] = "No existe el Item";
	} elseif ($Categoria == "") {
		$Error[0][0] = 2;
		$Error[0][1] = "No existe la categoria";
	} elseif ($Archivo == "") {
		$Error[0][0] = 3;
		$Error[0][1] = "No se digitó el archivo";
	} elseif (!file_exists("cargue/" . $Archivo)) {
		$Error[0][0] = 4;
		$Error[0][1] = "No existe el archivo en la ruta de cargue: " . $Archivo;
	} else {
		$Error[0][0] = 0;
		$Error[0][1] = "";
	}
	return $Error;
}

function ConsultarFechaDescarga($ID_Archivo)
{ //Consultar la ultima fecha de descarga de un archivo
	global $conexion;
	$Con = "SELECT MAX(FechaHora) AS Fecha FROM uvw_tbl_DescargaArchivos WHERE ID_Archivo='" . $ID_Archivo . "'";
	$SQL = sqlsrv_query($conexion, $Con);
	$row = sqlsrv_fetch_array($SQL);
	return $row['Fecha']->format('Y-m-d H:i:s');
}

function ConsultarUsuarioCargue($ID_Archivo)
{ //Consultar que usuario cargo el archivo para que solo el pueda eliminarlo
	global $conexion;
	$Con = "SELECT Usuario FROM uvw_tbl_Archivos WHERE ID_Archivo='" . $ID_Archivo . "'";
	$SQL = sqlsrv_query($conexion, $Con);
	$row = sqlsrv_fetch_array($SQL);
	if ($row['Usuario'] == $_SESSION['CodUser']) {
		return true;
	} else {
		return false;
	}
}

function ConsultarUsuarioCargueProd($ID_Producto)
{ //Consultar que usuario cargo el archivo para que solo el pueda eliminarlo
	global $conexion;
	$Con = "SELECT Usuario FROM uvw_tbl_Productos WHERE ID_Producto='" . $ID_Producto . "'";
	$SQL = sqlsrv_query($conexion, $Con);
	$row = sqlsrv_fetch_array($SQL);
	if ($row['Usuario'] == $_SESSION['CodUser']) {
		return true;
	} else {
		return false;
	}
}

function FormatUnitBytes($bytes)
{ //Dar formato a los tamaños de archivos
	if ($bytes >= 1073741824) {
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	} elseif ($bytes >= 1048576) {
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	} elseif ($bytes >= 1024) {
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	} elseif ($bytes > 1) {
		$bytes = $bytes . ' bytes';
	} elseif ($bytes == 1) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}

	return $bytes;
}

function CalcularCuotasAcuerdo($Fecha, $Cuotas, $Valor)
{ //Calcular las fechas de los acuerdos de pago en gestionar cartera
	$ArrCuotas = array();
	$FechaActual = strtotime($Fecha);
	$FechaActual = strtotime('+30 day', $FechaActual);
	$j = 1;
	$ValorCuota = $Valor / $Cuotas;
	for ($i = 0; $i < $Cuotas; $i++) {
		$FechaMostrar = date('Y-m-d', $FechaActual);

		$ArrCuotas[$j][0] = $j;
		$ArrCuotas[$j][1] = $FechaMostrar;
		$ArrCuotas[$j][2] = ($j / $Cuotas) * 100;
		$ArrCuotas[$j][3] = $ValorCuota;

		$nuevafecha = strtotime('+30 day', $FechaActual);
		$nuevafecha = date('Y-m-d', $nuevafecha);
		$FechaActual = strtotime($nuevafecha);
		$j++;
	}

	return $ArrCuotas;
}

function ObtenerHostURL()
{ //Obtiene solamente la ruta del servidor, sin el archivo .php
	$host = $_SERVER["HTTP_HOST"];
	$url = $_SERVER["REQUEST_URI"];
	$path = "https://" . $host . $url;
	$result = dirname($path) . "/";

	return $result;
}

function QuitarParametrosURL($url, $keys = array())
{ //Elimina los parámetros suministrador mediante la array $keys de la URL $url
	$url_parts = parse_url($url);
	if (empty($url_parts['query'])) return $url;

	parse_str($url_parts['query'], $result_array);
	foreach ($keys as $key) {
		unset($result_array[$key]);
	}
	$url_parts['query'] = http_build_query($result_array);
	$url = (isset($url_parts["scheme"]) ? $url_parts["scheme"] . "://" : "") .
		(isset($url_parts["user"]) ? $url_parts["user"] . ":" : "") .
		(isset($url_parts["pass"]) ? $url_parts["pass"] . "@" : "") .
		(isset($url_parts["host"]) ? $url_parts["host"] : "") .
		(isset($url_parts["port"]) ? ":" . $url_parts["port"] : "") .
		(isset($url_parts["path"]) ? $url_parts["path"] : "") .
		(isset($url_parts["query"]) ? "?" . $url_parts["query"] : "") .
		(isset($url_parts["fragment"]) ? "#" . $url_parts["fragment"] : "");
	return $url;
}

function RedimensionarImagen(&$pNombreimg, $rutaimg, $xmax, $ymax)
{
	$nombreimg = $pNombreimg;
	$expl = explode('.', $nombreimg);
	$ext = end($expl);
	$ext = strtolower($ext);

	if ($ext == "jpg" || $ext == "jpeg")
		$imagen = imagecreatefromjpeg($rutaimg);
	elseif ($ext == "png")
		$imagen = imagecreatefrompng($rutaimg);
	elseif ($ext == "gif")
		$imagen = imagecreatefromgif($rutaimg);

	$x = imagesx($imagen);
	$y = imagesy($imagen);

	/*if($x <= $xmax && $y <= $ymax){
			//echo "<center>Esta imagen ya esta optimizada para los maximos que deseas.<center>";
			return $imagen;
		}*/

	if ($x >= $y) {
		$nuevax = $xmax;
		$nuevay = $nuevax * $y / $x;
	} else {
		$nuevay = $ymax;
		$nuevax = $x / $y * $nuevay;
	}

	//Agregar estampa de posición GPS
	/*if($Lat!=""&&$Long!=""){
			$estampa = imagecreatetruecolor($xmax, 70);
			imagestring($estampa, 5, 20, 20, 'Latitud: '.$Lat, 0xFFFF2B);
			imagestring($estampa, 5, 20, 40, 'Longitud: '.$Long,0xFFFF2B);
			$margen_dcho = 10;
			$margen_inf = 10;
			$sx = imagesx($estampa);
			$sy = imagesy($estampa);
		}*/

	$img2 = imagecreatetruecolor($nuevax, $nuevay);
	imagecopyresized($img2, $imagen, 0, 0, 0, 0, floor($nuevax), floor($nuevay), $x, $y);

	/*if($Lat!=""&&$Long!=""){
			imagecopymerge($img2, $estampa, imagesx($img2) - $sx - $margen_dcho, imagesy($img2) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa), 50);
		}*/

	imagejpeg($img2, $rutaimg);
	//unlink($archivos_carpeta);
	//echo "<center>La imagen se ha optimizado correctamente.</center>";
	//return $img2;
}

function AgregarDatosImagen($pNombreimg, $Lat = "", $Long = "")
{
	$im = imagecreatefromjpeg($pNombreimg);

	$x = imagesx($im);
	$y = imagesy($im);

	$estampa = imagecreatetruecolor($x, 70);

	imagestring($estampa, 5, 20, 20, 'Latitud: ' . $Lat, 0xFFFF2B);
	imagestring($estampa, 5, 20, 40, 'Longitud: ' . $Long, 0xFFFF2B);

	// Establecer los márgenes para la estampa y obtener el alto/ancho de la imagen de la estampa
	$margen_dcho = 10;
	$margen_inf = 10;
	$sx = imagesx($estampa);
	$sy = imagesy($estampa);

	// Fusionar la estampa con nuestra foto con una opacidad del 50%
	imagecopymerge($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa), 50);

	// Guardar la imagen en un archivo y liberar memoria
	imagepng($im, $pNombreimg);
	imagedestroy($im);
}

function ConvertirPNGtoJPG($NombreArchivo, $RutaArchivo, $DirOut)
{
	//Sacar la extension del archivo
	$NombreActual = explode('.', $NombreArchivo);
	$Ext = end($NombreActual);
	//Sacar el nombre sin la extension
	$OnlyName = substr($NombreArchivo, 0, strlen($NombreArchivo) - (strlen($Ext) + 1));

	$NuevoNombre = $OnlyName . ".jpg";

	$fuente = imagecreatefrompng($RutaArchivo);
	$ancho = imagesx($fuente);
	$alto = imagesy($fuente);
	$imagen = imagecreatetruecolor($ancho, $alto);
	$color = imagecolorallocatealpha($imagen, 255, 255, 255, 1);

	imagefill($imagen, 0, 0, $color);
	imagecopyresampled($imagen, $fuente, 0, 0, 0, 0, $ancho, $alto, $ancho, $alto);
	imageJpeg($imagen, $DirOut . $NuevoNombre, 100);
	unlink($RutaArchivo);

	return $NuevoNombre;
}

function GenerarColor()
{
	$letters = '0123456789ABCDEF';
	$arr = str_split($letters);
	$color = '#';
	for ($i = 0; $i < 6; $i++) {
		$color .= $arr[rand(0, 15)];
	}
	return $color;
}

function ValidarEmail($str)
{ //Validar que existe un email verificando los MX
	$result = (false !== filter_var($str, FILTER_VALIDATE_EMAIL));

	if ($result) {
		list($user, $domain) = explode('@', $str);

		$result = checkdnsrr($domain, 'MX');
	}

	return $result;
}

function EnviarCorreo($pID, $pTipoObjeto, $pIdPlantilla)
{ //Otra forma de enviar un correo, utilizando el SP de SQL
	$ParamEnviaMail = array(
		"'" . $pID . "'",
		"'" . $pTipoObjeto . "'",
		"'" . $pIdPlantilla . "'"
	);
	$SQL_EnviaMail = EjecutarSP('usp_CorreoEnvio', $ParamEnviaMail);
	if (!$SQL_EnviaMail) {
		return false;
	} else {
		return true;
	}
}

function EnviarMail($email_destino, $nombre_destino = "", $tipo_email = 0, $asunto = "", $mensaje = "", $concopia = "", $nombre_cc = "", $cliente = "", $sucursal = "", $categoria = "", $comentarios = "", $archivo = "")
{
	global $conexion;
	if (file_exists('../mailer/PHPMailerAutoload.php')) {
		require_once('../mailer/PHPMailerAutoload.php');
	} else {
		require_once('mailer/PHPMailerAutoload.php');
	}

	$Cons_Mail = "Select * From tbl_EmailNotificaciones";
	$SQL_Mail = sqlsrv_query($conexion, $Cons_Mail);
	$row_Mail = sqlsrv_fetch_array($SQL_Mail);

	if (!isset($row_Mail['Usuario']) || ($row_Mail['Usuario'] == "")) {
		return;
	}

	//instancio un objeto de la clase PHPMailer
	$mail = new PHPMailer(); // defaults to using php "mail()"
	$mail->CharSet = "UTF-8";
	$mail->Encoding = "quoted-printable";
	//indico a la clase que use SMTP
	$mail->isSMTP();
	$mail->setLanguage('es');

	//permite modo debug para ver mensajes de las cosas que van ocurriendo
	//$mail->SMTPDebug = 2;
	//Debo de hacer autenticación SMTP
	if ($row_Mail['AutenticacionSMTP'] == 1) {
		$mail->SMTPAuth = true;
	} else {
		$mail->SMTPAuth = false;
	}
	$mail->SMTPSecure = $row_Mail['TipoConexion'];
	//indico el servidor de Gmail para SMTP
	$mail->Host = $row_Mail['ServidorSMTP'];
	//indico el puerto que usa Gmail
	$mail->Port = $row_Mail['PuertoSMTP'];
	//indico un usuario / clave de un usuario de gmail
	$mail->Username = $row_Mail['Usuario'];
	$mail->Password = base64_decode($row_Mail['Clave']);
	$mail->SetFrom($row_Mail['Usuario'], NOMBRE_PORTAL);
	$mail->AddReplyTo($row_Mail['Usuario'], NOMBRE_PORTAL);
	$mail->IsHTML(true);

	//Datos del mensaje
	if ($tipo_email == 1) { //Cargar archivos
		$Cons_Platilla = "Select ID_Plantilla, Asunto, Mensaje From uvw_tbl_PlantillaEmail Where ID_TipoNotificacion=1 and Estado=1";
		$SQL_Platilla = sqlsrv_query($conexion, $Cons_Platilla);
		$row_Platilla = sqlsrv_fetch_array($SQL_Platilla);
		if ($row_Platilla['ID_Plantilla'] == "") {
			return;
		}
		$asunto = $row_Platilla['Asunto'];
		$mensaje = $row_Platilla['Mensaje'];
	} elseif ($tipo_email == 2) { //Descargar archivos
		$Cons_Platilla = "Select ID_Plantilla, Asunto, Mensaje From uvw_tbl_PlantillaEmail Where ID_TipoNotificacion=2 and Estado=1";
		$SQL_Platilla = sqlsrv_query($conexion, $Cons_Platilla);
		$row_Platilla = sqlsrv_fetch_array($SQL_Platilla);
		if ($row_Platilla['ID_Plantilla'] == "") {
			return;
		}
		$asunto = $row_Platilla['Asunto'];
		$mensaje = $row_Platilla['Mensaje'];
	}

	//Verificar si la sucursal tiene habilitado el envío de correos
	if (($cliente != "") && ($sucursal != "")) {
		$ConsEnviaCorreo = "Select EnviaCorreo From uvw_Sap_tbl_Clientes_Sucursales Where CodigoCliente='" . $cliente . "' And NombreSucursal='" . $sucursal . "'";
		$SQL_EnviaCorreo = sqlsrv_query($conexion, $ConsEnviaCorreo);
		$row_EnviaCorreo = sqlsrv_fetch_array($SQL_EnviaCorreo);
		if ($row_EnviaCorreo['EnviaCorreo'] == "NO") {
			return;
		}
	}

	//Reemplazar variables en el mensaje

	//Cliente
	if ($cliente != "") { //[Nombre_Cliente]
		$Cons_Reemp = "Select NombreCliente From uvw_Sap_tbl_Clientes Where CodigoCliente='" . $cliente . "'";
		$SQL_Reemp = sqlsrv_query($conexion, $Cons_Reemp);
		$row_Reemp = sqlsrv_fetch_array($SQL_Reemp);
		$mensaje = str_replace("[Nombre_Cliente]", $row_Reemp['NombreCliente'], $mensaje);
	}
	//Sucursal
	if ($sucursal != "") { //[Nombre_Sucursal]
		$mensaje = str_replace("[Nombre_Sucursal]", $sucursal, $mensaje);
	}
	//Categoria
	if ($categoria != "") { //[Nombre_Categoria]
		$Cons_Reemp = "Select NombreCategoria From uvw_tbl_Categorias Where ID_Categoria='" . $categoria . "'";
		$SQL_Reemp = sqlsrv_query($conexion, $Cons_Reemp);
		$row_Reemp = sqlsrv_fetch_array($SQL_Reemp);
		$mensaje = str_replace("[Nombre_Categoria]", $row_Reemp['NombreCategoria'], $mensaje);
	}
	//Comentarios
	if ($comentarios != "") { //[Comentarios]
		$mensaje = str_replace("[Comentarios]", $comentarios, $mensaje);
	}
	//Archivo
	if ($archivo != "") { //[Nombre_Archivo]
		$mensaje = str_replace("[Nombre_Archivo]", $archivo, $mensaje);
	}

	//[Fecha]
	$mensaje = str_replace("[Fecha]", date('Y-m-d'), $mensaje);
	//[Hora]
	$mensaje = str_replace("[Hora]", date('H:i:s'), $mensaje);
	//[Nombre_Usuario]
	if (isset($_SESSION['NomUser'])) {
		$mensaje = str_replace("[Nombre_Usuario]", $_SESSION['NomUser'], $mensaje);
	}

	//Nombre portal
	/*if(NOMBRE_PORTAL!=""){//[Nombre_Portal]
		$mensaje=str_replace("[Nombre_Portal]",NOMBRE_PORTAL,$mensaje);
	}*/

	//Asignar variables del email
	$mail->Subject = $asunto;
	$mail->MsgHTML($mensaje);

	//indico destinatario
	$address = $email_destino;
	$mail->AddAddress($address, $nombre_destino);

	//Añadir con copia
	if ($concopia != "") {
		$mail->AddCC($concopia, $nombre_cc);
	}

	if (!$mail->Send()) {
		$InsertLog = "Insert Into tbl_Log Values ('" . date('Y-m-d H:i:s') . "','" . $_SESSION['CodUser'] . "','Error',50,'" . $mail->ErrorInfo . "')";
		sqlsrv_query($conexion, $InsertLog);
	}/*else{
		$InsertLog="Insert Into tbl_Log Values ('".date('Y-m-d H:i:s')."','".$_SESSION['CodUser']."','Success',50,'Send Email: ".$email_destino."')";
		sqlsrv_query($conexion,$InsertLog);
	}*/
}
