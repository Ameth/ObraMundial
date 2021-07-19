<?php 
if(!isset($_GET['id'])||$_GET['id']==""){exit();}
// include autoloader
require_once 'dompdf/autoload.inc.php';
require_once("includes/conexion.php");

PermitirAcceso(402);

//Congregacion
$SQL_Cong=Seleccionar('uvw_tbl_Congregaciones','*',"NumCong='".$_SESSION['NumCong']."'");
$row_Cong=sqlsrv_fetch_array($SQL_Cong);
$NombreCong=$row_Cong['NombreCongregacion']." (".$row_Cong['Ciudad'].", ".$row_Cong['Municipio'].")";

$Periodo=base64_decode($_GET['id']);

$SQL_Grp=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."'");

$SQL_Prd=Seleccionar('uvw_tbl_PeriodosInformes','*',"NumCong='".$_SESSION['NumCong']."' and IDPeriodo='".$Periodo."'");
$row_Prd=sqlsrv_fetch_array($SQL_Prd);

$ParamAsist=array(
	"'".$_SESSION['NumCong']."'",
	"'".$Periodo."'"
);
$SQL_Asist=EjecutarSP('usp_CalcularTotalesAsistencia',$ParamAsist);
$row_Asist=sqlsrv_fetch_array($SQL_Asist);

// reference the Dompdf namespace
use Dompdf\Dompdf;
$dompdf = new Dompdf();
define("DOMPDF_ENABLE_PHP", true);
$Cabecera='<html>
<head>
<title>Informe de servicio de la congregación</title>
<style>
 	@page { margin: 120px 50px; }
	body{
		font-family: "open sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size:10px;
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
		font-size:16px;
		text-align: center;
		font-weight: bold;
		max-width: 100%;
		margin-bottom: 1rem;
		background-color: transparent;
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
		margin-bottom: 1rem;
		background-color: transparent;
		
	}
	.table > thead > tr > th {
		border: 1px solid #BABABA;
		background-color: #F5F5F6;
		padding: 4px;
		line-height: 1.42857;
	}
	.table > thead > tr > td {
		border: 1px solid #BABABA;
		padding: 4px;
		line-height: 1.42857;
	}
	.table > tbody > tr > td {
		border: 1px solid #BABABA;
		padding: 4px;
		line-height: 1.42857;
	}
</style>
</head>
<body>
	<div id="footer">
		<p><strong>'.$NombreCong.'</strong></p>
    	<p class="page">Página </p>
  	</div>
	<table id="header" class="table-title">
		<tr>
			<td>INFORME DE SERVICIO DE LA CONGREGACIÓN</td>
		</tr>
		<tr>
			<td>PERIODO: '.$row_Prd['CodigoPeriodo'].'</td>
		</tr>
	</table>
	<table class="table-subtitle">
		<tr>
			<td>RESUMEN POR GRUPO</td>
		</tr>
	</table>';

//Resumen por grupo
$DatosGrupos="";
while($row_Grp=sqlsrv_fetch_array($SQL_Grp)){
	$SQL=EjecutarSP('usp_InformeServicioCongGrupos',$Periodo);
	$TotalCant=0;
	$TotalPub=0;
	$TotalVideos=0;
	$TotalHoras=0;
	$TotalRev=0;
	$TotalCursos=0;
	$DatosGrupos.='
	<table class="table">
		<thead>
			<tr>
				<td colspan="7"><strong>GRUPO: </strong>'.$row_Grp['NombreGrupo'].'</td>
			</tr>
			<tr>
				<th align="center" width="28%">Tipo publicador</th>
				<th align="center" width="12%">Cuántos informan</th>
				<th align="center" width="12%">Publicaciones</th>
				<th align="center" width="12%">Presentaciones de video</th>
				<th align="center" width="12%">Horas</th>
				<th align="center" width="12%">Revisitas</th>
				<th align="center" width="12%">Cursos biblicos</th>
			</tr>
		</thead>
		<tbody>';
	while($row=sqlsrv_fetch_array($SQL)){
		if($row_Grp['IDGrupo']==$row['IDGrupo']){
			$DatosGrupos.="
			<tr>
			  <td>".$row['TipoPublicador']."</td>
			  <td align='center'>".$row['Cantidad']."</td>
			  <td align='center'>".$row['Publicaciones']."</td>
			  <td align='center'>".$row['Videos']."</td>
			  <td align='center'>".$row['Horas']."</td>
			  <td align='center'>".$row['Revisitas']."</td>
			  <td align='center'>".$row['Cursos']."</td>
			</tr>";
			$TotalCant=$TotalCant+$row['Cantidad'];
			$TotalPub=$TotalPub+$row['Publicaciones'];
			$TotalVideos=$TotalVideos+$row['Videos'];
			$TotalHoras=$TotalHoras+$row['Horas'];
			$TotalRev=$TotalRev+$row['Revisitas'];
			$TotalCursos=$TotalCursos+$row['Cursos'];
		}	
	}
	$DatosGrupos.="
	<tr style='font-weight: bold;'>
      <td align='center'>TOTAL</td>
	  <td align='center'>".$TotalCant."</td>
	  <td align='center'>".$TotalPub."</td>
	  <td align='center'>".$TotalVideos."</td>
	  <td align='center'>".$TotalHoras."</td>
	  <td align='center'>".$TotalRev."</td>
	  <td align='center'>".$TotalCursos."</td>
    </tr>
	<tbody>
	</table>";
}

//Resumen total
$DatosTotales="";
$SQL_Total=EjecutarSP('usp_InformeServicioCongTotal',$Periodo);
$DatosTotales='
<table class="table-subtitle">
	<tr>
		<td>RESUMEN GENERAL DE LA CONGREGACIÓN</td>
	</tr>
</table>
<table class="table">
	<thead>
		<tr>
			<th align="center" width="28%">Tipo publicador</th>
			<th align="center" width="12%">Cuántos informan</th>
			<th align="center" width="12%">Publicaciones</th>
			<th align="center" width="12%">Presentaciones de video</th>
			<th align="center" width="12%">Horas</th>
			<th align="center" width="12%">Revisitas</th>
			<th align="center" width="12%">Cursos biblicos</th>
		</tr>
	</thead>
	<tbody>';
$TotalCant=0;
$TotalPub=0;
$TotalVideos=0;
$TotalHoras=0;
$TotalRev=0;
$TotalCursos=0;
while($row_Total=sqlsrv_fetch_array($SQL_Total)){
	$DatosTotales.="
	<tr>
	  <td>".$row_Total['TipoPublicador']."</td>
	  <td align='center'>".$row_Total['Cantidad']."</td>
	  <td align='center'>".$row_Total['Publicaciones']."</td>
	  <td align='center'>".$row_Total['Videos']."</td>
	  <td align='center'>".$row_Total['Horas']."</td>
	  <td align='center'>".$row_Total['Revisitas']."</td>
	  <td align='center'>".$row_Total['Cursos']."</td>
	</tr>";
	$TotalCant=$TotalCant+$row_Total['Cantidad'];
	$TotalPub=$TotalPub+$row_Total['Publicaciones'];
	$TotalVideos=$TotalVideos+$row_Total['Videos'];
	$TotalHoras=$TotalHoras+$row_Total['Horas'];
	$TotalRev=$TotalRev+$row_Total['Revisitas'];
	$TotalCursos=$TotalCursos+$row_Total['Cursos'];
}
$DatosTotales.="
	<tr style='font-weight: bold;'>
      <td align='center'>TOTAL</td>
	  <td align='center'>".$TotalCant."</td>
	  <td align='center'>".$TotalPub."</td>
	  <td align='center'>".$TotalVideos."</td>
	  <td align='center'>".$TotalHoras."</td>
	  <td align='center'>".$TotalRev."</td>
	  <td align='center'>".$TotalCursos."</td>
    </tr>
	<tbody>
	</table>";

//Asistencia
$DatosAsistencia="";
$DatosAsistencia='
<table class="table">
	<thead>
		<tr>
			<th align="center" width="28%">Asistencia</th>
			<th align="center" width="12%">Total</th>
			<th align="center" width="12%">Promedio</th>
		</tr>
	</thead>
	<tbody>';
$DatosAsistencia.="
	<tr>
	  <td>Reunión de entre semana</td>
	  <td align='center'>".$row_Asist['TotalSemana']."</td>
	  <td align='center'>".$row_Asist['PromSemana']."</td>
	</tr>
	<tr>
	  <td>Reunión del fin de semana</td>
	  <td align='center'>".$row_Asist['TotalFinSemana']."</td>
	  <td align='center'>".$row_Asist['PromFinSemana']."</td>
	</tr>
	<tbody>
</table>";


$Cierre='</body>
</html>';
//InsertarLog("Descarga de entrada");
//sqlsrv_close($conexion);
//echo $HTML1.$Datos.$HTML2;/*
// instantiate and use the dompdf class
$dompdf->loadHtml($Cabecera.$DatosGrupos.$DatosTotales.$DatosAsistencia.$Cierre);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('letter', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("Informe_Servicio_Congregacion.pdf",array("Attachment" => false));
exit(0);
?>