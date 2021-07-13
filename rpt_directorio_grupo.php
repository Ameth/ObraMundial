<?php 
if(!isset($_GET['ID'])||$_GET['ID']==""){exit();}
// include autoloader
require_once 'dompdf/autoload.inc.php';
require_once("includes/conexion.php");

$SQL=Seleccionar('uvw_tbl_Publicadores','*');

$Datos="";
$i=1;
while($row=sqlsrv_fetch_array($SQL)){
	$Datos.="<tr>
      <td>".$i."</td>
      <td>".$row['NombrePublicador']."</td>
	  <td>".$row['Direccion']."</td>
	  <td>".$row['Telefono']."</td>
	  <td>".$row['Celular']."</td>
	  <td>".$row['TipoPublicadorAbr']."</td>
	  <td>".$row['PrivilegioServicioAbr']."</td>
    </tr>";
	$i++;
}
// reference the Dompdf namespace
use Dompdf\Dompdf;
$dompdf = new Dompdf();
define("DOMPDF_ENABLE_PHP", true);
$HTML1='<html>
<head>
<title>Directorio de grupo</title>
<style>
 	@page { margin: 120px 50px; }
	body{
		font-family: "open sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size:12px;
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
		float: right;
		height: 2px;
	}
    #footer .page:after{
		content: counter(page); 
	}
	.table-title{
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
		border: 1px solid #EBEBEB;
		width: 100%;
		max-width: 100%;
		margin-bottom: 1rem;
		background-color: transparent;
		
	}
	.table > thead > tr > th {
		border: 1px solid #e7e7e7;
		background-color: #F5F5F6;
		padding: 4px;
		line-height: 1.42857;
	}
	.table > tbody > tr > td {
		border: 1px solid #e7e7e7;
		padding: 4px;
		line-height: 1.42857;
	}
</style>
</head>
<body>
	<div id="footer">
    	<p class="page">Página </p>
  	</div>
	<table id="header" class="table-title">
		<tr>
			<td>DIRECTORIO DE GRUPO</td>
		</tr>
		<tr>
			<td>CONGREGACIÓN EL CARMEN, BARRANQUILLA (ATL)</td>
		</tr>
	</table>
	<table class="table">
		<thead>
			<tr>
				<th>#</th>
				<th>Nombre del publicador</th>
				<th>Dirección</th>
				<th>Teléfono</th>
				<th>Celular</th>
				<th>Tipo publicador</th>
				<th>Priv. Servicio</th>
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
$dompdf->setPaper('letter', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("Directorio_grupo.pdf",array("Attachment" => false));
exit(0);
?>