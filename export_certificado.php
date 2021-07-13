 <?php
if(!isset($_POST['MM_Cert'])||$_POST['MM_Cert']==""){exit();}
require_once("includes/conexion.php");
//formatos a las fechas
$date = date_create($_POST['FechaInicial']);
$FInicial=date_format($date, 'Y-m-d');

$date = date_create($_POST['FechaFinal']);
$FFinal=date_format($date, 'Y-m-d');

//Generar certificado
$ConsCert="EXEC usp_GenerarCertificado '".str_replace('-','',$FInicial)."', '".str_replace('-','',$FFinal)."', '".$_SESSION['NIT']."', '".$_SESSION['CodigoSAPProv']."'";
//echo $ConsCert;
//exit();

$SQLCert=sqlsrv_query($conexion,$ConsCert);
//var_dump($SQLCert);
//exit;
$rowCert=sqlsrv_fetch_array($SQLCert);



require_once('fpdf181/fpdf.php');


class PDF extends FPDF
{
	
// Cabecera de página
function Header()
{
	global $rowCert;
	global $FInicial;
	global $FFinal;
	
    // Logo
    $this->Image('css/header.jpg',10,8,80);
    // Salto de línea
	$this->SetLineWidth(.3);
	$this->Line(11,40,200,40);
	$this->Line(11,41,200,41);
	$this->SetFont('Arial','B',10);
	$this->Ln(35);
	
	//Titulo del certificado
	$this->Cell(0,10,'CERTIFICADO DE RETENCIONES',0,1,'C');
	$this->Ln(8);
	
	//Información del certificado
	$this->SetFont('Arial','B',8);
	$this->Cell(40,5,utf8_decode('Año gravable:'),0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(10,5,utf8_decode($_POST['AGravable']),0,0,'L');
	$this->Ln();
	
	$this->SetFont('Arial','B',8);
	$this->Cell(40,5,utf8_decode('Fecha inicial:'),0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(20,5,utf8_decode($FInicial),0,0,'L');
	$this->SetFont('Arial','B',8);
	$this->Cell(30,5,utf8_decode('Fecha final:'),0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(20,5,utf8_decode($FFinal),0,0,'L');
	$this->SetFont('Arial','B',8);
	$this->Cell(35,5,utf8_decode('Fecha expedición:'),0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(20,5,date('Y-m-d h:i:s a'),0,0,'L');
	$this->Ln();
	
	$this->SetFont('Arial','B',8);
	$this->Cell(40,5,utf8_decode('Retención efectuada a:'),0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(0,5,utf8_decode($rowCert['U_CardName']),0,0,'L');
	$this->Ln();
	
	$this->SetFont('Arial','B',8);
	$this->Cell(40,5,utf8_decode('NIT:'),0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(0,5,utf8_decode($rowCert['U_LicTradNum']),0,0,'L');
	$this->Ln();
	
	$this->SetFont('Arial','B',8);
	$this->Cell(40,5,utf8_decode('Dirección:'),0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(0,5,utf8_decode($rowCert['Street']),0,0,'L');
	$this->Ln();
	
	$this->SetFont('Arial','B',8);
	$this->Cell(40,5,utf8_decode('Ciudad:'),0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(0,5,utf8_decode($rowCert['nomMunicipio']),0,0,'L');
	$this->Ln();
	
	$this->SetFont('Arial','B',8);
	$this->Cell(40,5,utf8_decode('Área de valoración:'),0,0,'L');
	$this->SetFont('Arial','',8);
	$this->Cell(0,5,utf8_decode('Todos'),0,0,'L');
	$this->Ln();
	
	$this->Line(11,100,200,100);
	$this->Line(11,101,200,101);
	
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','',8);
    // Número de página
    $this->Cell(0,5,utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'R');
}
		
}


// Creación del objeto de la clase heredada
$pdf = new PDF('P','mm','Letter');
$pdf->SetTitle('Certificado de retenciones');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Ln(7);

//Datos
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,5,utf8_decode('Certificamos que hemos realizado las siguientes retenciones:'),0,0,'L');
$pdf->Ln(10);


$pdf->SetLineWidth(.1);
// Anchuras de las columnas
$w = array(30, 70, 30, 30, 30);

//Titulos de la tabla
$pdf->SetFont('Arial','B',8);
$pdf->Cell($w[0],5,utf8_decode('Código impuesto'),'TB',0,'L');
$pdf->Cell($w[1],5,utf8_decode('Concepto'),'TB',0,'L');
$pdf->Cell($w[2],5,utf8_decode('Tarifa (%)'),'TB',0,'R');
$pdf->Cell($w[3],5,utf8_decode('Base impuesto'),'TB',0,'R');
$pdf->Cell($w[4],5,utf8_decode('Valor impuesto'),'TB',0,'R');
$pdf->Ln();

$pdf->SetFont('Arial','',8);
//$SumBase=0;
//$SumValor=0;

$SQLCert=sqlsrv_query($conexion,$ConsCert);
while($rowCert=sqlsrv_fetch_array($SQLCert)){
	$pdf->Cell($w[0],5,$rowCert['U_HBT_Impuesto'],0,0,'L');
	$pdf->Cell($w[1],5,substr($rowCert['WTName'],0,50),0,0,'L');
	$pdf->Cell($w[2],5,number_format($rowCert['PrctBsAmnt'],2)."%",0,0,'R');
	$pdf->Cell($w[3],5,number_format($rowCert['BASEIMPUESTO'],2),0,0,'R');
	$pdf->Cell($w[4],5,number_format($rowCert['ValorImpuesto'],2),0,0,'R');
	$pdf->Ln();
	//$SumBase=$SumBase+$rowCert['BASEIMPUESTO'];
	//$SumValor=$SumValor+$rowCert['ValorImpuesto'];
}
//Grosor de linea y negrita para totales
//$pdf->SetLineWidth(.5);
//$pdf->SetFont('','B');
//$pdf->Ln();

//*** Totales ***//
//$pdf->Cell(130,5,'Total',0,0,'R');
//$pdf->Cell(30,5,number_format($SumBase,2),'T',0,'R');
//$pdf->Cell(30,5,number_format($SumValor,2),'T',0,'R');

$pdf->SetFont('');
$pdf->Ln(25);
$pdf->Cell(0,5,utf8_decode('Ciudad donde se consignó la retención: BARRANQUILLA'),0,1,'L');
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode('Este certificado no requiere de firma autógrafa de acuerdo con el artículo 7 del decreto reglamentario 0380 del 27 de febrero de 1996.'),0,1,'L');
$pdf->MultiCell(0,5,utf8_decode('Las personas jurídicas podrán entregar los certificados de retención en la fuente en forma continua, impresa por computador, sin necesidad de firma autófrafa. Artículo 10 Decreto 0836 de 1991.'),0,'L');
$pdf->Ln(5);
$pdf->MultiCell(0,5,utf8_decode($_POST['Comentarios']),0,'L');
//InsertarLog("Descarga de certificado de retenciones");
sqlsrv_close($conexion);

//$pdf->Ln();
$pdf->Output('I','CertificadoRetenciones.pdf');
?>