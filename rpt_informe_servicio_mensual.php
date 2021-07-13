<?php 
if(!isset($_GET['id'])||$_GET['id']==""){exit();}
// include autoloader
require_once 'dompdf/autoload.inc.php';
require_once("includes/conexion.php");

PermitirAcceso(401);

//Congregacion
$SQL_Cong=Seleccionar('uvw_tbl_Congregaciones','*',"NumCong='".$_SESSION['NumCong']."'");
$row_Cong=sqlsrv_fetch_array($SQL_Cong);
$NombreCong=$row_Cong['NombreCongregacion']." (".$row_Cong['Ciudad'].", ".$row_Cong['Municipio'].")";

$Filtro=base64_decode($_GET['id']);
$Grupo=base64_decode($_GET['grp']);
$Periodo=base64_decode($_GET['prd']);

$SQL_Grp=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$Grupo."'");
$row_Grp=sqlsrv_fetch_array($SQL_Grp);

$SQL_Prd=Seleccionar('uvw_tbl_PeriodosInformes','*',"NumCong='".$_SESSION['NumCong']."' and IDPeriodo='".$Periodo."'");
$row_Prd=sqlsrv_fetch_array($SQL_Prd);

$Cons="Select * From uvw_tbl_Informes Where NumCong='".$_SESSION['NumCong']."' $Filtro Order by NombrePublicador";
$SQL=sqlsrv_query($conexion,$Cons);

$Datos="";
$i=1;
$TotalPub=0;
$TotalVideos=0;
$TotalHoras=0;
$TotalRev=0;
$TotalCursos=0;
while($row=sqlsrv_fetch_array($SQL)){
	$Datos.="<tr>
      <td align='center'>".$i."</td>
      <td>".$row['NombrePublicador']."</td>
	  <td align='center'>".$row['TipoPublicadorAbr']."</td>
	  <td align='center'>".$row['DePrecAuxiliar']."</td>
	  <td align='center'>".$row['Publicaciones']."</td>
	  <td align='center'>".$row['Videos']."</td>
	  <td align='center'>".$row['Horas']."</td>
	  <td align='center'>".$row['Revisitas']."</td>
	  <td align='center'>".$row['Cursos']."</td>
	  <td>".$row['Notas']."</td>
    </tr>";
	$TotalPub=$TotalPub+$row['Publicaciones'];
	$TotalVideos=$TotalVideos+$row['Videos'];
	$TotalHoras=$TotalHoras+$row['Horas'];
	$TotalRev=$TotalRev+$row['Revisitas'];
	$TotalCursos=$TotalCursos+$row['Cursos'];
	$i++;
}
$Datos.="<tr style='font-weight: bold;'>
      <td colspan='4' align='center'>TOTAL</td>
	  <td align='center'>".$TotalPub."</td>
	  <td align='center'>".$TotalVideos."</td>
	  <td align='center'>".$TotalHoras."</td>
	  <td align='center'>".$TotalRev."</td>
	  <td align='center'>".$TotalCursos."</td>
	  <td></td>
    </tr>";
// reference the Dompdf namespace
use Dompdf\Dompdf;
$dompdf = new Dompdf();
define("DOMPDF_ENABLE_PHP", true);
$HTML1='<html>
<head>
<title>Informe de servicio mensual</title>
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
	.table > tbody > tr > td {
		border: 1px solid #BABABA;
		padding: 2px;
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
			<td>INFORME DE SERVICIO MENSUAL</td>
		</tr>';
if($row_Grp['NombreGrupo']!=""){
	$HTML1.='<tr>
			<td>GRUPO: '.$row_Grp['NombreGrupo'].'</td>
		</tr>';
}
$HTML1.='<tr>
			<td>PERIODO: '.$row_Prd['CodigoPeriodo'].'</td>
		</tr>
	</table>
	<table class="table">
		<thead>
			<tr>
				<th align="center" width="5%">#</th>
				<th align="center" width="19%">Nombre publicador</th>
				<th align="center" width="7%">Tipo publicador</th>
				<th align="center" width="7%">Prec. Auxiliar</th>
				<th align="center" width="7%">Publicaciones</th>
				<th align="center" width="7%">Presentaciones de video</th>
				<th align="center" width="7%">Horas</th>
				<th align="center" width="7%">Revisitas</th>
				<th align="center" width="7%">Cursos biblicos</th>
				<th align="center" width="27%">Comentarios</th>
			</tr>
		</thead>
		<tbody>';
$HTML2='</tbody>
	</table>
</body>
</html>';
//InsertarLog("Descarga de entrada");
//sqlsrv_close($conexion);
//echo $HTML1.$Datos.$HTML2;/*
// instantiate and use the dompdf class
$dompdf->loadHtml($HTML1.$Datos.$HTML2);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('letter', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("Informe_Servicio_Mensual.pdf",array("Attachment" => false));
exit(0);
?>