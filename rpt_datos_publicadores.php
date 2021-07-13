<?php 
if(!isset($_GET['id'])||$_GET['id']==""){exit();}
// include autoloader
require_once 'dompdf/autoload.inc.php';
require_once("includes/conexion.php");

PermitirAcceso(405);

$FiltGrupo="";
$Grupo="";

if(isset($_GET['grp'])&&($_GET['grp']!="")){
	$Grupo=base64_decode($_GET['grp']);
	$FiltGrupo="and IDGrupo='".$Grupo."'";
	$SQL_Grp=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$Grupo."'");
	$row_Grp=sqlsrv_fetch_array($SQL_Grp);
}

//Congregacion
$SQL_Cong=Seleccionar('uvw_tbl_Congregaciones','*',"NumCong='".$_SESSION['NumCong']."'");
$row_Cong=sqlsrv_fetch_array($SQL_Cong);
$NombreCong=$row_Cong['NombreCongregacion']." (".$row_Cong['Ciudad'].", ".$row_Cong['Municipio'].")";

$SQL=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' $FiltGrupo",'NombrePublicador');

$Datos="";
$i=1;
while($row=sqlsrv_fetch_array($SQL)){
	$FechaNac=($row['FechaNac']!="") ? $row['FechaNac']->format('Y-m-d') : "";
	$FechaBaut=($row['FechaBaut']!="") ? $row['FechaBaut']->format('Y-m-d') : "";
	$Datos.="<tr>
      <td align='center'>".$i."</td>
      <td>".$row['NombrePublicador']."</td>
	  <td align='center'>".$row['NombreGenero']."</td>
	  <td>".$FechaNac."</td>
	  <td>".$FechaBaut."</td>
	  <td>".$row['Direccion']."</td>
	  <td align='center'>".$row['Telefono']."</td>
	  <td align='center'>".$row['Celular']."</td>
	  <td align='center'>".$row['TipoPublicadorAbr']."</td>
	  <td align='center'>".$row['PrivilegioServicioAbr']."</td>
	  <td>".$row['PersonaCont']."</td>
	  <td align='center'>".$row['TelefonoCont']."</td>
    </tr>";
	$i++;
}

// reference the Dompdf namespace
use Dompdf\Dompdf;
$dompdf = new Dompdf();
define("DOMPDF_ENABLE_PHP", true);
$HTML1='<html>
<head>
<title>Información de los publicadores</title>
<style>
 	@page { margin: 120px 50px; }
	body{
		font-family: "open sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size:9px;
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
			<td>INFORMACIÓN DE LOS PUBLICADORES</td>
		</tr>';
if($Grupo!=""){
	$HTML1.='<tr>
			<td>GRUPO: '.$row_Grp['NombreGrupo'].'</td>
		</tr>';
}
$HTML1.='
	</table>
	<table class="table">
		<thead>
			<tr>
				<th align="center" width="5%">#</th>
				<th align="center" width="15%">Nombre publicador</th>
				<th align="center" width="7%">Genero</th>
				<th align="center" width="7%">Fecha Nac.</th>
				<th align="center" width="7%">Fecha Baut.</th>
				<th align="center" width="11%">Dirección</th>
				<th align="center" width="7%">Telefono</th>
				<th align="center" width="7%">Celular</th>
				<th align="center" width="7%">Tipo publicador</th>
				<th align="center" width="7%">Privilegio</th>
				<th align="center" width="13%">Contacto emergencia</th>
				<th align="center" width="7%">Telefono</th>
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
$dompdf->stream("Informe_Datos_Publicadores.pdf",array("Attachment" => false));
exit(0);
?>