<?php
if (!isset($_GET['id']) || $_GET['id'] == "") {
	exit();
}
// include autoloader
require_once 'dompdf/autoload.inc.php';
require_once("includes/conexion.php");

PermitirAcceso(404);

$zip = (isset($_GET['zip']) && $_GET['zip'] == 1) ? 1 : 0;

//Periodos
if (isset($_GET['anio']) && $_GET['anio'] != "") {
	$Periodo = " and AnioServicio IN (" . base64_decode($_GET['anio']) . ")";
} else {
	$Periodo = "";
}

//Congregacion
$SQL_Cong = Seleccionar('uvw_tbl_Congregaciones', '*', "NumCong='" . $_SESSION['NumCong'] . "'");
$row_Cong = sqlsrv_fetch_array($SQL_Cong);
$NombreCong = $row_Cong['NombreCongregacion'] . " (" . $row_Cong['Ciudad'] . ", " . $row_Cong['Municipio'] . ")";

$Publicador = base64_decode($_GET['id']);

//Publicador
$SQL_Pub = Seleccionar('uvw_tbl_Publicadores', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "'");
$row_Pub = sqlsrv_fetch_array($SQL_Pub);
$NombrePub = $row_Pub['NombrePublicador'];

//Datos
if ($row_Pub['FechaNac'] != "") {
	$FechaNac = $row_Pub['FechaNac']->format('Y-m-d');
} else {
	$FechaNac = "";
}

if ($row_Pub['FechaBaut'] != "") {
	$FechaBaut = $row_Pub['FechaBaut']->format('Y-m-d');
} else {
	$FechaBaut = "";
}

if ($row_Pub['IDGenero'] == "H") {
	$GenHombre = 'checked="checked"';
	$GenMujer = '';
} else {
	$GenHombre = '';
	$GenMujer = 'checked="checked"';
}

if ($row_Pub['IDTipoEsperanza'] == 1) {
	$TipoEspOV = 'checked="checked"';
	$TipoEspUN = '';
} else {
	$TipoEspOV = '';
	$TipoEspUN = 'checked="checked"';
}

if ($row_Pub['IDPrivServicio'] == 1) {
	$PrivAnc = 'checked="checked"';
	$PrivSM = '';
} elseif ($row_Pub['IDPrivServicio'] == 2) {
	$PrivAnc = '';
	$PrivSM = 'checked="checked"';
} else {
	$PrivAnc = '';
	$PrivSM = '';
}

if ($row_Pub['IDTipoPublicador'] == 2) {
	$TipoPR = 'checked="checked"';
} else {
	$TipoPR = '';
}

/*
$SQL_Grp=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."'");

$SQL_Prd=Seleccionar('uvw_tbl_PeriodosInformes','*',"NumCong='".$_SESSION['NumCong']."' and IDPeriodo='".$Periodo."'");
$row_Prd=sqlsrv_fetch_array($SQL_Prd);
*/
// reference the Dompdf namespace
use Dompdf\Dompdf;

$dompdf = new Dompdf();

$Cabecera = '<html>
<head>
<title>Registro de publicador S-21</title>
<style>
 	@page { margin: 120px 50px 130px 50px; }
	body{
		font-family: "open sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size:11px;
	}
	#header{
		position: fixed; 
		left: 0px; 
		top: -110px; 
		right: 0px;
		height: 150px; 
	}
	#footer{
		position: fixed; 
		left: 0px; 
		bottom: -2px; 
		height: 2px;
	}
    #footer .page:after{
		content: counter(page);
	}
	.table-title{
		width: 100%;
		font-size:12px;
		max-width: 100%;
		margin-bottom: 1rem;
		background-color: transparent;
		//border-style: solid;
		//border-width: 0.5px;
	}
	.table-title > tbody > tr > td{
		padding: 0px;
		//border-style: solid;
		//border-width: 0.5px;
	}
	.table-title > tbody > tr > .titulo{
		font-size:16px;
		text-align: center;
		height: 43;
	}
	.table-subtitle{
		width: 100%;
		font-size:14px;
		text-align: center;
		font-weight: bold;
		max-width: 100%;
		margin-bottom: 1rem;
		background-color: transparent;
	}
	.table{
		border-collapse: collapse;
		border: 1px solid #BABABA;
		width: 100%;
		max-width: 100%;
		margin-top: 50px;
		margin-bottom: 1rem;
		background-color: transparent;
		
	}
	.table > thead > tr > th {
		border: 1px solid #000000;
		padding: 2px;
		line-height: 1.42857;
		font-weight: bold;
		text-align: center;
	}
	.table > thead > tr > td {
		border: 1px solid #000000;
		//padding: 2px;
		line-height: 1.42857;
	}
	.table > tbody > tr > td {
		border: 1px solid #000000;
		//padding: 2px;
		line-height: 1.42857;
	}
</style>
</head>
<body>
	<div id="footer">
		<p><strong>' . $NombreCong . '</strong></p>
    	<p class="page">Página </p>
  	</div>
<table id="header" class="table-title">
  <tbody>
    <tr>
      <td colspan="13" class="titulo"><strong>REGISTRO DE PUBLICADOR DE LA CONGREGACIÓN</strong></td>
    </tr>
    <tr>
      <td width="10%"><strong>Nombre:</strong></td>
      <td colspan="12">' . $row_Pub['Nombre'] . ' ' . $row_Pub['SegundoNombre'] . ' ' . $row_Pub['Apellido'] . ' ' . $row_Pub['SegundoApellido'] . '</td>
    </tr>
    <tr>
      <td height="10" colspan="2"><strong>Fecha de nacimiento:</strong></td>
      <td colspan="7">' . $FechaNac . '</td>
      <td width="3%"><input name="checkbox" type="checkbox" id="checkbox" ' . $GenHombre . '></td>
      <td width="11%"><strong>Hombre</strong></td>
      <td width="3%"><input name="checkbox" type="checkbox" id="checkbox" ' . $GenMujer . '></td>
      <td width="12%"><strong>Mujer</strong></td>
    </tr>
    <tr>
      <td height="10" colspan="2"><strong>Fecha de bautismo:</strong></td>
      <td colspan="7">' . $FechaBaut . '</td>
      <td><input name="checkbox" type="checkbox" id="checkbox" ' . $TipoEspOV . '></td>
      <td><strong>Otras ovejas</strong></td>
      <td><input name="checkbox" type="checkbox" id="checkbox" ' . $TipoEspUN . '></td>
      <td><strong>Ungido</strong></td>
    </tr>
    <tr>
      <td height="10">&nbsp;</td>
      <td width="9%">&nbsp;</td>
      <td width="19%">&nbsp;</td>
      <td width="3%"><input name="checkbox" type="checkbox" id="checkbox" ' . $PrivAnc . '></td>
      <td width="12%"><strong>Anciano</strong></td>
      <td width="3%"><input name="checkbox" type="checkbox" id="checkbox" ' . $PrivSM . '></td>
      <td width="16%"><strong>Siervo ministerial</strong></td>
      <td width="3%"><input name="checkbox" type="checkbox" id="checkbox" ' . $TipoPR . '></td>
      <td colspan="5"><strong>Precursor regular</strong></td>
    </tr>
  </tbody>
</table>';

//Tablas
$SQL_AServ = Seleccionar('uvw_tbl_Informes_Old', 'Distinct AnioServicio', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' $Periodo");
$Datos = "";
while ($row_Pub = sqlsrv_fetch_array($SQL_AServ)) {
	$TotalPub = 0;
	$TotalVideos = 0;
	$TotalHoras = 0;
	$TotalRevisitas = 0;
	$TotalCursos = 0;

	$PromPub = 0;
	$PromVideos = 0;
	$PromHoras = 0;
	$PromRevisitas = 0;
	$PromCursos = 0;

	$Count = 0;

	$Datos .= '
	<table class="table">
		<thead>
			<tr>
			  <th width="15%">Año de servicio<br>' . $row_Pub['AnioServicio'] . '</th>
			  <th width="10%">Publicaciones</th>
			  <th width="10%">Presentaciones de videos</th>
			  <th width="10%">Horas</th>
			  <th width="10%">Revisitas</th>
			  <th width="10%">Cursos bíblicos</th>
			  <th width="35%">Notas</th>
			</tr>
		</thead>
	  <tbody>';

	//Septiembre
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='9'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}

	$Datos .= '<tr>
		  <td>Septiembre</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Octubre
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='10'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Octubre</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Noviembre
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='11'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Noviembre</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Diciembre
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='12'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Diciembre</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Enero
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='1'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Enero</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Febrero
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='2'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Febrero</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Marzo
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='3'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Marzo</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Abril
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='4'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Abril</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Mayo
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='5'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Mayo</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Junio
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='6'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Junio</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Julio
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='7'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Julio</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}

	//Agosto
	$SQL_InfPub = Seleccionar('uvw_tbl_Informes_Old', '*', "NumCong='" . $_SESSION['NumCong'] . "' and IDPublicador='" . $Publicador . "' and AnioServicio='" . $row_Pub['AnioServicio'] . "' and MesPeriodo='8'");
	$row_InfPub = sqlsrv_fetch_array($SQL_InfPub);
	if ($row_InfPub['PrecAuxiliar'] == 1) {
		$PrecAux = "Precursor auxiliar. ";
	} else {
		$PrecAux = "";
	}
	$Datos .= '<tr>
		  <td>Agosto</td>
		  <td align="center">' . $row_InfPub['Publicaciones'] . '</td>
		  <td align="center">' . $row_InfPub['Videos'] . '</td>
		  <td align="center">' . $row_InfPub['Horas'] . '</td>
		  <td align="center">' . $row_InfPub['Revisitas'] . '</td>
		  <td align="center">' . $row_InfPub['Cursos'] . '</td>
		  <td>' . $PrecAux . $row_InfPub['Notas'] . '</td>
		</tr>';
	if ($row_InfPub['Horas'] != "") {
		$TotalPub = $TotalPub + $row_InfPub['Publicaciones'];
		$TotalVideos = $TotalVideos + $row_InfPub['Videos'];
		$TotalHoras = $TotalHoras + $row_InfPub['Horas'];
		$TotalRevisitas = $TotalRevisitas + $row_InfPub['Revisitas'];
		$TotalCursos = $TotalCursos + $row_InfPub['Cursos'];
		$Count++;
	}
	$Datos .= '<tr style="font-weight: bold;">
		  <td>Total</td>
		  <td align="center">' . $TotalPub . '</td>
		  <td align="center">' . $TotalVideos . '</td>
		  <td align="center">' . $TotalHoras . '</td>
		  <td align="center">' . $TotalRevisitas . '</td>
		  <td align="center">' . $TotalCursos . '</td>
		  <td align="center">&nbsp;</td>
		</tr>';

	$PromPub = ($Count > 0) ? $TotalPub / $Count : 0;
	$PromVideos = ($Count > 0) ? $TotalVideos / $Count : 0;
	$PromHoras = ($Count > 0) ? $TotalHoras / $Count : 0;
	$PromRevisitas = ($Count > 0) ? $TotalRevisitas / $Count : 0;
	$PromCursos = ($Count > 0) ? $TotalCursos / $Count : 0;

	$Datos .= '<tr  style="font-weight: bold;">
		  <td>Promedio</td>
		  <td align="center">' . number_format($PromPub, 2) . '</td>
		  <td align="center">' . number_format($PromVideos, 2) . '</td>
		  <td align="center">' . number_format($PromHoras, 2) . '</td>
		  <td align="center">' . number_format($PromRevisitas, 2) . '</td>
		  <td align="center">' . number_format($PromCursos, 2) . '</td>
		  <td>&nbsp;</td>
		</tr>
	  </tbody>
	</table>';
}


$Cierre = '</body>
</html>';
//InsertarLog("Descarga de entrada");
//sqlsrv_close($conexion);
//echo $HTML1.$Datos.$HTML2;/*
// instantiate and use the dompdf class
$dompdf->loadHtml($Cabecera . $Datos . $Cierre);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('letter', 'portrait');

// Render the HTML as PDF
$dompdf->render();

if ($zip == 1) {
	$output = $dompdf->output();
	file_put_contents("download/" . $_SESSION['CodUser'] . "/" . $NombrePub . ".pdf", $output);
} else {
	// Output the generated PDF to Browser
	$dompdf->stream($NombrePub . ".pdf", array("Attachment" => false));
}
//exit(0);
