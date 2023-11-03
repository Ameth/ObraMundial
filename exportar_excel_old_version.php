<?php 
if(isset($_GET['exp'])&&$_GET['exp']!=""&&$_GET['Cons']!=""){
	require_once("includes/conexion.php");
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
//	ini_set('open_basedir', '/var/tmp');
	
	//Exportar Gestiones de cartera
	if($_GET['exp']==1){
		$Cons=base64_decode($_GET['Cons']);
		$SQL=sqlsrv_query($conexion,$Cons);
		//$Num=sqlsrv_has_rows($SQL);
		//echo $Cons;
		//exit();
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator(NOMBRE_PORTAL);
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('L1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('M1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('N1')->applyFromArray($EstiloTitulo);
			
			//Ancho automatico
			/*$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);*/
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Informe datos de publicadores');
			
			$objExcel->setActiveSheetIndex(0)
					 ->setCellValue('A1','Nombre publicador')
					 ->setCellValue('B1','Genero')
					 ->setCellValue('C1','Fecha nacimiento')
					 ->setCellValue('D1','Fecha bautismo')
					 ->setCellValue('E1','Dirección')
				     ->setCellValue('F1','Teléfono')
					 ->setCellValue('G1','Célular')
				     ->setCellValue('H1','Persona de contacto')
					 ->setCellValue('I1','Teléfono del contacto')
					 ->setCellValue('J1','Grupo')
					 ->setCellValue('K1','Tipo publicador')
					 ->setCellValue('L1','Privilegio de servicio')
					 ->setCellValue('M1','Tipo esperanza')
					 ->setCellValue('N1','Estado');
			
			$i=2;
			while($registros=sqlsrv_fetch_array($SQL)){				
				$objSheet->setCellValue('A'.$i,$registros['NombrePublicador']);
				$objSheet->setCellValue('B'.$i,$registros['NombreGenero']);
				if($registros['FechaNac']!=""){
					$objSheet->setCellValue('C'.$i,$registros['FechaNac']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('C'.$i,'');
				}
				if($registros['FechaBaut']!=""){
					$objSheet->setCellValue('D'.$i,$registros['FechaBaut']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('D'.$i,'');
				}
				$objSheet->setCellValue('E'.$i,$registros['Direccion']);
				$objSheet->setCellValue('F'.$i,$registros['Telefono']);
				$objSheet->setCellValue('G'.$i,$registros['Celular']);
				$objSheet->setCellValue('H'.$i,$registros['PersonaCont']);
				$objSheet->setCellValue('I'.$i,$registros['TelefonoCont']);				
				$objSheet->setCellValue('J'.$i,$registros['NombreGrupo']);
				$objSheet->setCellValue('K'.$i,$registros['TipoPublicador']);
				$objSheet->setCellValue('L'.$i,$registros['PrivilegioServicio']);
				$objSheet->setCellValue('M'.$i,$registros['TipoEsperanza']);
				$objSheet->setCellValue('N'.$i,$registros['NombreEstado']);				
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="InformeDatosPublicadores.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		
		$filePath = '' . rand(0, getrandmax()) . rand(0, getrandmax()) . ".tmp";
		$objWriter->save($filePath);
		readfile($filePath);
		unlink($filePath);
		
//		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar formularios de hallazgos
	if($_GET['exp']==2){
		$Cons=base64_decode($_GET['Cons']);
		$ParamCons=explode(",",$Cons);
		//Cambiar el parametro 8 para incluir las plagas
		$ParamReem=array(8=>"'1'");
		$NewParam=array_replace($ParamCons, $ParamReem);
		
		$SQL=EjecutarSP('sp_ConsultarHallazgos',$NewParam);
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("COPLA GROUP SAS");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('L1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('M1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('N1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('O1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('P1')->applyFromArray($EstiloTitulo);
			
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Panorama de riesgos');
			
			$objExcel->setActiveSheetIndex(0)
				->setCellValue('A1','ID')
				->setCellValue('B1','Tecnico')
				->setCellValue('C1','Tipo visita')
				->setCellValue('D1','Cliente')
				->setCellValue('E1','Sucursal')
				->setCellValue('F1','Zona')
				->setCellValue('G1','Area')
				->setCellValue('H1','Hallazgo')
				->setCellValue('I1','Recomendacion')
				->setCellValue('J1','Fecha creacion')
				->setCellValue('K1','Hora creacion')
				->setCellValue('L1','Fecha actualizacion')
				->setCellValue('M1','Estado criticidad')
				->setCellValue('N1','Estado')
				->setCellValue('O1','Plaga')
				->setCellValue('P1','Cantidad');
			
			$i=2;
			while($registros=sqlsrv_fetch_array($SQL)){				
				$objSheet->setCellValue('A'.$i,$registros['ID_Frm']);
				$objSheet->setCellValue('B'.$i,$registros['NombreEmpleado']);
				$objSheet->setCellValue('C'.$i,$registros['DeTipoVisita']);
				$objSheet->setCellValue('D'.$i,$registros['NombreCliente']);
				$objSheet->setCellValue('E'.$i,$registros['NombreSucursal']);
				$objSheet->setCellValue('F'.$i,$registros['Zona']);
				$objSheet->setCellValue('G'.$i,$registros['DeArea']);
				$objSheet->setCellValue('H'.$i,$registros['Hallazgo']);				
				$objSheet->setCellValueExplicit('I'.$i,$registros['Recomendaciones']);	
				if($registros['FechaCreacion']!=""){
					$objSheet->setCellValue('J'.$i,$registros['FechaCreacion']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('J'.$i,'');
				}
				if($registros['FechaCreacion']!=""){
					$objSheet->setCellValue('K'.$i,$registros['FechaCreacion']->format('H:i'));
				}else{
					$objSheet->setCellValue('K'.$i,'');
				}
				if($registros['FechaAct']!=""){
					$objSheet->setCellValue('L'.$i,$registros['FechaAct']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('L'.$i,'');
				}
				$objSheet->setCellValue('M'.$i,$registros['DeEstadoCriticidad']);	
				$objSheet->setCellValue('N'.$i,$registros['NombreEstado']);		
				$objSheet->setCellValue('O'.$i,$registros['NombrePlaga']);	
				$objSheet->setCellValue('P'.$i,$registros['Cantidad']);		
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="PanoramaRiesgos.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save(str_replace('.php', '.xls', __FILE__));
//		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar listado de clientes en proyectos (DIALNET)
	if($_GET['exp']==3){
		$Cons=base64_decode($_GET['Cons']);
		$ParamCons=explode(",",$Cons);
		$SQL=EjecutarSP('sp_InformeSNProyecto',$ParamCons,0,2);
		//$Num=sqlsrv_has_rows($SQL);
		//echo $Cons;
		//exit();
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("DIALNET");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('L1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('M1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('N1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('O1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('P1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('Q1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('R1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('S1')->applyFromArray($EstiloTitulo);
			
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Clientes por proyectos');
			
			$objExcel->setActiveSheetIndex(0)
					 ->setCellValue('A1','Codigo cliente')
					 ->setCellValue('B1','Nombre cliente')
					 ->setCellValue('C1','Cedula')
					 ->setCellValue('D1','Fecha creacion')
					 ->setCellValue('E1','Municipio')
				     ->setCellValue('F1','Departamento')
					 ->setCellValue('G1','Direccion')
					 ->setCellValue('H1','Barrio')
					 ->setCellValue('I1','Proyecto')
				     ->setCellValue('J1','Contrato')
					 ->setCellValue('K1','ID de Servicio')
					 ->setCellValue('L1','Llamada de servicio')
					 ->setCellValue('M1','Actividad')
					 ->setCellValue('N1','Instalado')
					 ->setCellValue('O1','Envio de correo')
					 ->setCellValue('P1','Anexos')					 
					 ->setCellValue('Q1','Vendedor')
					 ->setCellValue('R1','Latitud')
					 ->setCellValue('S1','Longitud');
			
			$i=2;
			while($registros=sql_fetch_array($SQL,2)){				
				$objSheet->setCellValue('A'.$i,$registros['CodigoCliente']);
				$objSheet->setCellValue('B'.$i,utf8_encode($registros['NombreCliente']));
				$objSheet->setCellValue('C'.$i,$registros['LicTradNum']);
				if($registros['FechaCreacion']!=""){
					$objSheet->setCellValue('D'.$i,$registros['FechaCreacion']." ".$registros['HoraCreacion']);
				}else{
					$objSheet->setCellValue('D'.$i,'');
				}
				$objSheet->setCellValue('E'.$i,utf8_encode($registros['Municipio']));
				$objSheet->setCellValue('F'.$i,utf8_encode($registros['Departamento']));
				$objSheet->setCellValue('G'.$i,utf8_encode($registros['Direccion']));
				$objSheet->setCellValue('H'.$i,utf8_encode($registros['Barrio']));
				$objSheet->setCellValue('I'.$i,$registros['DeProyecto']);
				$objSheet->setCellValue('J'.$i,$registros['ID_Contrato']);	
				$objSheet->setCellValue('K'.$i,$registros['ID_Servicio']);
				$objSheet->setCellValue('L'.$i,$registros['LlamadaServicio']);
				$objSheet->setCellValue('M'.$i,$registros['ID_Actividad']);
				$objSheet->setCellValue('N'.$i,$registros['Instalado']);
				$objSheet->setCellValue('O'.$i,$registros['EnvioCorreo']);		
				$objSheet->setCellValue('P'.$i,$registros['EstadoAnexos']);
				$objSheet->setCellValue('Q'.$i,utf8_encode($registros['DeVendedor']));
				$objSheet->setCellValue('R'.$i,$registros['Latitud']);
				$objSheet->setCellValue('S'.$i,$registros['Longitud']);
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ClientesProyectos.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar solicitudes de salida de inventario
	if($_GET['exp']==4){
		$Cons=base64_decode($_GET['Cons']);
		$SQL=sqlsrv_query($conexion,$Cons);
		//$Num=sqlsrv_has_rows($SQL);
		//echo $Cons;
		//exit();
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("COPLA");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Solicitud de salida');
			
			$objExcel->setActiveSheetIndex(0)
					 ->setCellValue('A1','Numero')
					 ->setCellValue('B1','Serie')
					 ->setCellValue('C1','Fecha solicitud')
					 ->setCellValue('D1','Solicitado para')
					 ->setCellValue('E1','Tipo entrega')
					 ->setCellValue('F1','Descontable')
				     ->setCellValue('G1','Documento destino')
					 ->setCellValue('H1','Firmado')
					 ->setCellValue('I1','Usuario creacion')
					 ->setCellValue('J1','Usuario actualizacion')
				     ->setCellValue('K1','Estado');
			
			$i=2;
			while($registros=sql_fetch_array($SQL)){				
				$objSheet->setCellValue('A'.$i,$registros['DocNum']);
				$objSheet->setCellValue('B'.$i,$registros['DeSeries']);
				$objSheet->setCellValue('C'.$i,$registros['DocDate']);
				$objSheet->setCellValue('D'.$i,$registros['NomEmpleado']);
				$objSheet->setCellValue('E'.$i,$registros['DeTipoEntrega']);
				$objSheet->setCellValue('F'.$i,$registros['Descontable']);
				$objSheet->setCellValue('G'.$i,$registros['DocDestinoDocNum']);
				$objSheet->setCellValue('H'.$i,$registros['DocFirmado']);
				$objSheet->setCellValue('I'.$i,$registros['UsuarioCreacion']);
				$objSheet->setCellValue('J'.$i,$registros['UsuarioActualizacion']);	
				$objSheet->setCellValue('K'.$i,$registros['NombreEstado']);
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="SolicitudSalida.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

	//Exportar informe de descuento de nomina epp (COPLA)
	if($_GET['exp']==5){
		$Cons=base64_decode($_GET['Cons']);
		$ParamCons=explode(",",$Cons);
		$SQL=EjecutarSP('usp_Inf_DescuentoNominaEPP',$ParamCons);
		//$Num=sqlsrv_has_rows($SQL);
		//echo $Cons;
		//exit();
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("COPLA");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('L1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('M1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('N1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('O1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('P1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('Q1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('R1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('S1')->applyFromArray($EstiloTitulo);
			
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Descuentos de EPP');
			
			$objExcel->setActiveSheetIndex(0)
					->setCellValue('A1','Numero')
					->setCellValue('B1','Serie')
					->setCellValue('C1','Fecha documento')
					->setCellValue('D1','Fecha vencimiento')
					->setCellValue('E1','Codigo articulo')
					->setCellValue('F1','Nombre articulo')
					->setCellValue('G1','Cantidad')
					->setCellValue('H1','Precio')
					->setCellValue('I1','Total')
					->setCellValue('J1','Total documento')
					->setCellValue('K1','Referencia')
					->setCellValue('L1','Comentarios')					
					->setCellValue('M1','Centro de costo')
					->setCellValue('N1','Area')
					->setCellValue('O1','Sucursal')
					->setCellValue('P1','Codigo empleado')					 
					->setCellValue('Q1','Nombre empleado')
					->setCellValue('R1','Cargo')
					->setCellValue('S1','Tipo entrega');
			
			$i=2;
			while($registros=sql_fetch_array($SQL)){				
				$objSheet->setCellValue('A'.$i,$registros['DocNum']);
				$objSheet->setCellValue('B'.$i,$registros['SeriesName']);
				if($registros['DocDate']!=""){
					$objSheet->setCellValue('C'.$i,$registros['DocDate']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('C'.$i,'');
				}
				if($registros['DocDueDate']!=""){
					$objSheet->setCellValue('D'.$i,$registros['DocDueDate']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('D'.$i,'');
				}
				$objSheet->setCellValue('E'.$i,$registros['ItemCode']);
				$objSheet->setCellValue('F'.$i,$registros['Dscription']);
				$objSheet->setCellValue('G'.$i,$registros['Quantity']);
				$objSheet->setCellValue('H'.$i,$registros['Price']);
				$objSheet->setCellValue('I'.$i,$registros['LineTotal']);
				$objSheet->setCellValue('J'.$i,$registros['DocTotal']);
				$objSheet->setCellValue('K'.$i,$registros['Ref2']);	
				$objSheet->setCellValue('L'.$i,$registros['Comments']);
				$objSheet->setCellValue('M'.$i,$registros['IdCenCosto1']);
				$objSheet->setCellValue('N'.$i,$registros['Area']);
				$objSheet->setCellValue('O'.$i,$registros['Sucursal']);
				$objSheet->setCellValue('P'.$i,$registros['U_NDG_CodEmpleado']);		
				$objSheet->setCellValue('Q'.$i,$registros['U_NDG_NomEmpleado']);
				$objSheet->setCellValue('R'.$i,$registros['EmpCargo']);
				$objSheet->setCellValue('S'.$i,$registros['DeTipoEntrega']);
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="DescuentoNominaEPP.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar listado de clientes en proyectos, modulo de interventoria (DIALNET)
	if($_GET['exp']==6){
		$Cons=base64_decode($_GET['Cons']);
		$ParamCons=explode(",",$Cons);
		//Cambiar el parametro 8 para incluir las plagas
		$ParamReem=array(3=>"'1'");
		$NewParam=array_replace($ParamCons, $ParamReem);
		
		$SQL=EjecutarSP('sp_InformeSNProyecto_ConsultarBaseDatos',$NewParam,0,2);
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("DIALNET");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('L1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('M1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('N1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('O1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('P1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('Q1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('R1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('S1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('T1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('U1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('V1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('W1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('X1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('Y1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('Z1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('AA1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('AB1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('AC1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('AD1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('AE1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('AF1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('AG1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('AH1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('AI1')->applyFromArray($EstiloTitulo);
			
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Clientes por proyectos');
			
			$objExcel->setActiveSheetIndex(0)
					->setCellValue('A1','Nombre')
					->setCellValue('B1','Apellido')
					->setCellValue('C1','Tipo de documento')
					->setCellValue('D1','Cedula')
					->setCellValue('E1','Telefono')
					->setCellValue('F1','Celular')
					->setCellValue('G1','Fase')
					->setCellValue('H1','Correo electronico')
					->setCellValue('I1','Genero')
					->setCellValue('J1','Direccion')
					->setCellValue('K1','Tipo de usuario')
					->setCellValue('L1','Estrato')
					->setCellValue('M1','Estado')
					->setCellValue('N1','Cuenta (ID)')
					->setCellValue('O1','ID de la cuenta')
					->setCellValue('P1','Fecha de instalacion')					 
					->setCellValue('Q1','Fecha de inicio operacion')
					->setCellValue('R1','Fecha de retiro')
					->setCellValue('S1','Cantidad de dias para sustituir')
					->setCellValue('T1','Region')
					->setCellValue('U1','Departamento')
					->setCellValue('V1','DANE Departamento')
					->setCellValue('W1','Municipio')
					->setCellValue('X1','DANE Municipio')
					->setCellValue('Y1','Contrato de aporte MINTIC')
					->setCellValue('Z1','Barrio')
					->setCellValue('AA1','Latitud')
					->setCellValue('AB1','Longitud')
					->setCellValue('AC1','Estado de verificacion documental')
					->setCellValue('AD1','Observaciones')
					->setCellValue('AE1','Validacion documental')
					->setCellValue('AF1','Estado de verifiacion retiro/traslado')
					->setCellValue('AG1','Nombre reemplazo')
					->setCellValue('AH1','Cedula reemplazo')
					->setCellValue('AI1','No Contrato');
			
			$i=2;
			while($registros=sql_fetch_array($SQL,2)){				
				$objSheet->setCellValue('A'.$i,utf8_encode($registros['Nombre']));
				$objSheet->setCellValue('B'.$i,utf8_encode($registros['Apellido']));
				$objSheet->setCellValue('C'.$i,$registros['TipoDocumento']);
				$objSheet->setCellValue('D'.$i,$registros['Cedula']);
				$objSheet->setCellValue('E'.$i,$registros['Telefono']);
				$objSheet->setCellValue('F'.$i,$registros['Celular']);
				$objSheet->setCellValue('G'.$i,$registros['Fase']);
				$objSheet->setCellValue('H'.$i,$registros['CorreoElectronico']);
				$objSheet->setCellValue('I'.$i,$registros['Genero']);
				$objSheet->setCellValue('J'.$i,utf8_encode($registros['Direccion']));
				$objSheet->setCellValue('K'.$i,$registros['TipoUsuario']);
				$objSheet->setCellValue('L'.$i,$registros['Estrato']);
				$objSheet->setCellValue('M'.$i,$registros['Estado']);
				$objSheet->setCellValue('N'.$i,$registros['Cuenta']);
				$objSheet->setCellValue('O'.$i,$registros['IDCuenta']);
				$objSheet->setCellValue('P'.$i,$registros['FechaInstalacion']);
				$objSheet->setCellValue('Q'.$i,$registros['FechaContrato']);
				$objSheet->setCellValue('R'.$i,$registros['FechaFinContrato']);
				$objSheet->setCellValue('S'.$i,$registros['CantidadDiasSustituir']);
				$objSheet->setCellValue('T'.$i,$registros['Region']);
				$objSheet->setCellValue('U'.$i,utf8_encode($registros['Departamento']));
				$objSheet->setCellValue('V'.$i,$registros['CodDepDANE']);
				$objSheet->setCellValue('W'.$i,utf8_encode($registros['Municipio']));
				$objSheet->setCellValue('X'.$i,$registros['CodMunDANE']);
				$objSheet->setCellValue('Y'.$i,$registros['ContratoMinTIC']);
				$objSheet->setCellValue('Z'.$i,utf8_encode($registros['Barrio']));
				$objSheet->setCellValue('AA'.$i,$registros['Latitud']);
				$objSheet->setCellValue('AB'.$i,$registros['Longitud']);
				$objSheet->setCellValue('AC'.$i,$registros['EstadoVerificacionDocumental']);
				$objSheet->setCellValue('AD'.$i,$registros['Observaciones']);	
				$objSheet->setCellValue('AE'.$i,$registros['ValidacionDocumental']);
				$objSheet->setCellValue('AF'.$i,$registros['EstadoVerifiacionRetiroTraslado']);
				$objSheet->setCellValue('AG'.$i,utf8_encode($registros['NombreReemplazo']));
				$objSheet->setCellValue('AH'.$i,$registros['CedulaReemplazo']);
				$objSheet->setCellValue('AI'.$i,$registros['NoContrato']);
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ClientesProyectos.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar traslados de inventarios
	if($_GET['exp']==7){
		$Cons=base64_decode($_GET['Cons']);
		$SQL=sqlsrv_query($conexion,$Cons);
		//$Num=sqlsrv_has_rows($SQL);
		//echo $Cons;
		//exit();
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("COPLA");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Traslados de salida');
			
			$objExcel->setActiveSheetIndex(0)
					 ->setCellValue('A1','Numero')
					 ->setCellValue('B1','Serie')
					 ->setCellValue('C1','Sucursal')
					 ->setCellValue('D1','Fecha solicitud')
					 ->setCellValue('E1','Solicitado para')
					 ->setCellValue('F1','Tipo entrega')
					 ->setCellValue('G1','Descontable')
					 ->setCellValue('H1','Documento base')
				     ->setCellValue('I1','Documento destino')
					 ->setCellValue('J1','Firmado')
					 ->setCellValue('K1','Usuario creacion');
			
			$i=2;
			while($registros=sql_fetch_array($SQL)){				
				$objSheet->setCellValue('A'.$i,$registros['DocNum']);
				$objSheet->setCellValue('B'.$i,$registros['DeSeries']);
				$objSheet->setCellValue('C'.$i,$registros['OcrName3']);				
				$objSheet->setCellValue('D'.$i,$registros['DocDate']);
				$objSheet->setCellValue('E'.$i,$registros['NomEmpleado']);
				$objSheet->setCellValue('F'.$i,$registros['DeTipoEntrega']);
				$objSheet->setCellValue('G'.$i,$registros['Descontable']);
				$objSheet->setCellValue('H'.$i,$registros['DocBaseDocNum']);
				$objSheet->setCellValue('I'.$i,$registros['DocDestinoDocNum']);
				$objSheet->setCellValue('J'.$i,$registros['DocFirmado']);
				$objSheet->setCellValue('K'.$i,$registros['UsuarioCreacion']);
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="TrasladosSalidas.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar listado de clientes en proyectos listos para generarles facturas (DIALNET)
	if($_GET['exp']==8){		
		$SQL=Seleccionar("tbl_CreacionFacturasProyectos","*","[Usuario]='".strtolower($_SESSION['User'])."'",'[FechaActividadLlamada], [DocNumLlamadaServicio]','',2);
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("DIALNET");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('L1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('M1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('N1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('O1')->applyFromArray($EstiloTitulo);
			
			
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Clientes para facturar');
			
			$objExcel->setActiveSheetIndex(0)
					->setCellValue('A1','Código cliente')
					->setCellValue('B1','Nombre cliente')
					->setCellValue('C1','Cédula')
					->setCellValue('D1','Fecha creación')
					->setCellValue('E1','Fecha instalación')
					->setCellValue('F1','Fecha factura')
					->setCellValue('G1','Municipio')
					->setCellValue('H1','Departamento')
					->setCellValue('I1','Proyecto')
					->setCellValue('J1','Contrato')
					->setCellValue('K1','Id Servicio')
					->setCellValue('L1','Llamada servicio')
					->setCellValue('M1','Instalado')
					->setCellValue('N1','Factura')
					->setCellValue('O1','Validación');
			
			$i=2;
			while($registros=sql_fetch_array($SQL,2)){				
				$objSheet->setCellValue('A'.$i,$registros['IdCliente']);
				$objSheet->setCellValue('B'.$i,utf8_encode($registros['DeCliente']));
				$objSheet->setCellValue('C'.$i,$registros['Cedula']);
				$objSheet->setCellValue('D'.$i,$registros['FechaCreacionLlamada']);
				$objSheet->setCellValue('E'.$i,$registros['FechaActividadLlamada']);
				$objSheet->setCellValue('F'.$i,$registros['FechaFactura']);
				$objSheet->setCellValue('G'.$i,utf8_encode($registros['DeMunicipio']));
				$objSheet->setCellValue('H'.$i,utf8_encode($registros['DeDepartamento']));
				$objSheet->setCellValue('I'.$i,$registros['DeProyecto']);
				$objSheet->setCellValue('J'.$i,$registros['IdContrato']);
				$objSheet->setCellValue('K'.$i,$registros['IdArticulo']);
				$objSheet->setCellValue('L'.$i,$registros['DocNumLlamadaServicio']);
				$objSheet->setCellValue('M'.$i,$registros['Instalado']);
				$objSheet->setCellValue('N'.$i,$registros['DocNumFactura']);
				$objSheet->setCellValue('O'.$i,$registros['Validacion']);
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ClientesFacturar.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar listado de la programacion de los clientes
	if($_GET['exp']==9){	
		$Where="";
		if(isset($_GET['Cliente'])&&$_GET['Cliente']!=""){
			$Where.=" and IdCliente='".base64_decode($_GET['Cliente'])."'";
		}

		if(isset($_GET['Sucursal'])&&$_GET['Sucursal']!=""){
			$Where.=" and IdSucursalCliente='".base64_decode($_GET['Sucursal'])."'";
		}

		if(isset($_GET['Sede'])&&$_GET['Sede']!=""){
			$Where.=" and Sede='".base64_decode($_GET['Sede'])."'";
		}

		if(isset($_GET['Validacion'])&&$_GET['Validacion']!=""){
			if(base64_decode($_GET['Validacion'])==1){
				$Where.=" and Validacion='SI Tiene OT'";
			}elseif(base64_decode($_GET['Validacion'])==2){
				$Where.=" and Validacion='NO Tiene OT'";
			}			
		}
		
		$SQL=Seleccionar("tbl_ProgramacionClientes","*","Usuario='".$_SESSION['CodUser']."'".$Where);
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("NEDUGA");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			
			
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Programacion de clientes');
			
			$objExcel->setActiveSheetIndex(0)
					->setCellValue('A1','Nombre cliente')
					->setCellValue('B1','Sucursal cliente')
					->setCellValue('C1','Código LMT')
					->setCellValue('D1','Nombre LMT')
					->setCellValue('E1','Periodo')
					->setCellValue('F1','Sede')
					->setCellValue('G1','OT')
					->setCellValue('H1','Servicio')
					->setCellValue('I1','Validacion');
			
			$i=2;
			while($registros=sql_fetch_array($SQL)){				
				$objSheet->setCellValue('A'.$i,$registros['DeCliente']);
				$objSheet->setCellValue('B'.$i,$registros['IdSucursalCliente']);
				$objSheet->setCellValue('C'.$i,$registros['IdArticuloLMT']);
				$objSheet->setCellValue('D'.$i,$registros['NombreArticuloLMT']);
				if($registros['Periodo']!=""){
					$objSheet->setCellValue('E'.$i,$registros['Periodo']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('E'.$i,'');
				}				
				$objSheet->setCellValue('F'.$i,$registros['Sede']);
				$objSheet->setCellValue('G'.$i,utf8_encode($registros['IdLlamadaServicio']));
				$objSheet->setCellValue('H'.$i,utf8_encode($registros['ServiciosLlamadas']));
				$objSheet->setCellValue('I'.$i,$registros['Validacion']);
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ProgramacionClientes.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar datos desde un SP
	if($_GET['exp']==10){
		$Cons=base64_decode($_GET['Cons']);
		$ParamCons=explode(",",$Cons);
		if(isset($_GET['hn'])&&($_GET['hn']==1)){
			$SQL=EjecutarSP(base64_decode($_GET['sp']),$ParamCons,0,2);
		}else{
			$SQL=EjecutarSP(base64_decode($_GET['sp']),$ParamCons);
		}
		
		//$Num=sqlsrv_has_rows($SQL);
		//echo $Cons;
		//exit();

		$rawdata = array();
		$abc=array();
		$i=0;
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator(NOMBRE_PORTAL);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Reporte');
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);

			if(isset($_GET['hn'])&&($_GET['hn']==1)){
				//while($row=sql_fetch_array($SQL,2)){
				while(odbc_fetch_into($SQL, $rawdata[$i])){
					//odbc_fetch_into($SQL, $rawdata[$i]);
					//$rawdata[$i] = $row;
					//print_r($rawdata);
					//echo "<br><br>";
					//exit();
					$i++;
				}
				//exit();
			}else{
				while($row=sql_fetch_array($SQL)){
					$rawdata[$i] = $row;
					$i++;
				}
			}
			

			$columnas = count($rawdata[0])/2;
			$filas = count($rawdata);
			
			$j=0;
			$letra=65; //A
			$segLetra=65; //A

			//Llenar array de las letras del abecedario
			for($j=0;$j<$columnas;$j++){
				if($j<=25){
					$Titulo=chr($letra);
					$letra++;
				}else{
					$letra=65;
					$Titulo=chr($letra).chr($segLetra);
					$segLetra++;
				}
				$abc[$j]=$Titulo;
			}

			
			for($j=0;$j<$columnas;$j++){
				
				//Colocar estilos
				$objExcel->getActiveSheet()->getStyle($abc[$j].'1')->applyFromArray($EstiloTitulo);

				//Ancho automatico
				$objExcel->getActiveSheet()->getColumnDimension($abc[$j])->setAutoSize(true);
			}
			
			//Titulos de las columnas
			$j=0;
			for($i=1;$i<count($rawdata[0]);$i=$i+2){
				next($rawdata[0]);
				$objSheet->setCellValue($abc[$j].'1',key($rawdata[0]));
				next($rawdata[0]);
				$j++;
			 }
			
			//Valores de las filas
			$f=2;//Posicion de la fila
			$letra=65;
//			echo "Filas: ".$filas;
//			echo "<br>";
//			echo "Columnas: ".$columnas;
//			echo "<br>";
			for($i=0;$i<$filas;$i++){
				for($j=0;$j<$columnas;$j++){
					if(isset($rawdata[$i][$j])){
//						echo $rawdata[$i][$j];
//						echo "<br>";
						if(is_object($rawdata[$i][$j])){
							$objSheet->setCellValue($abc[$j].$f,$rawdata[$i][$j]->format('Y-m-d'));
						}else{
							$objSheet->setCellValue($abc[$j].$f,$rawdata[$i][$j]);
						}
					}
					
					
				}
				$f++;
				$letra++;
			}
//			exit();
		}
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Reporte'.date('Ymd').'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$filePath = '' . rand(0, getrandmax()) . rand(0, getrandmax()) . ".tmp";
		$objWriter->save($filePath);
		readfile($filePath);
		unlink($filePath);
//		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar listado de clientes en impresion de facturas (DIALNET)
	if($_GET['exp']==11){
		$Cons=base64_decode($_GET['Cons']);
		$ParamCons=explode(",",$Cons);
		
		$SQL=EjecutarSP(base64_decode($_GET['sp']),$ParamCons,0,2);
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("DIALNET");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('L1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('M1')->applyFromArray($EstiloTitulo);
			
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Facturas de clientes');
			
			$objExcel->setActiveSheetIndex(0)
					->setCellValue('A1','Codigo cliente')
					->setCellValue('B1','Nombre cliente')
					->setCellValue('C1','Cedula')
					->setCellValue('D1','Municipio')
					->setCellValue('E1','Departamento')
					->setCellValue('F1','Proyecto')
					->setCellValue('G1','Fecha instalacion')
					->setCellValue('H1','Fecha factura')
					->setCellValue('I1','Serie factura')
					->setCellValue('J1','Numero factura')
					->setCellValue('K1','Llamada servicio')
					->setCellValue('L1','Comentarios')
					->setCellValue('M1','Archivo factura');
			
			$i=2;
			while($registros=sql_fetch_array($SQL,2)){				
				$objSheet->setCellValue('A'.$i,$registros['ID_CodigoCliente']);
				$objSheet->setCellValue('B'.$i,utf8_encode($registros['NombreCliente']));
				$objSheet->setCellValue('C'.$i,$registros['LicTradNum']);
				$objSheet->setCellValue('D'.$i,utf8_encode($registros['Municipio']));
				$objSheet->setCellValue('E'.$i,utf8_encode($registros['Departamento']));
				$objSheet->setCellValue('F'.$i,$registros['DeProyecto']);
				$objSheet->setCellValue('G'.$i,$registros['FechaInicioActividad']);
				$objSheet->setCellValue('H'.$i,$registros['FechaContabilizacion']);
				$objSheet->setCellValue('I'.$i,$registros['SeriesName']);
				$objSheet->setCellValue('J'.$i,$registros['NoDocumento']);
				$objSheet->setCellValue('K'.$i,$registros['DocNumLlamada']);
				$objSheet->setCellValue('L'.$i,utf8_encode($registros['Comentarios']));
				$objSheet->setCellValue('M'.$i,$registros['NombreArchivo']);
				$i++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="FacturasClientes.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar datos desde un SP agregando un parametro para los datos multiples que tienen link que vienen de la BD
	if($_GET['exp']==12){
		$Cons=base64_decode($_GET['Cons']);
		$ParamCons=explode(",",$Cons);
		array_push($ParamCons,"'1'");
		if(isset($_GET['hn'])&&($_GET['hn']==1)){
			$SQL=EjecutarSP(base64_decode($_GET['sp']),$ParamCons,0,2);
		}else{
			$SQL=EjecutarSP(base64_decode($_GET['sp']),$ParamCons);
		}
		
		//$Num=sqlsrv_has_rows($SQL);
		//echo $Cons;
		//exit();

		$rawdata = array();
		$abc=array();
		$i=0;
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator(NOMBRE_PORTAL);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Reporte');
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);

			if(isset($_GET['hn'])&&($_GET['hn']==1)){
				//while($row=sql_fetch_array($SQL,2)){
				while(odbc_fetch_into($SQL, $rawdata[$i])){
					//odbc_fetch_into($SQL, $rawdata[$i]);
					//$rawdata[$i] = $row;
					//print_r($rawdata);
					//echo "<br><br>";
					//exit();
					$i++;
				}
				//exit();
			}else{
				while($row=sql_fetch_array($SQL)){
					$rawdata[$i] = $row;
					$i++;
				}
			}
			

			$columnas = count($rawdata[0])/2;
			$filas = count($rawdata);
			
			$j=0;
			$letra=65; //A
			$segLetra=65; //A

			//Llenar array de las letras del abecedario
			for($j=0;$j<$columnas;$j++){
				if($j<=25){
					$Titulo=chr($letra);
					$letra++;
				}else{
					$letra=65;
					$Titulo=chr($letra).chr($segLetra);
					$segLetra++;
				}
				$abc[$j]=$Titulo;
			}

			
			for($j=0;$j<$columnas;$j++){
				
				//Colocar estilos
				$objExcel->getActiveSheet()->getStyle($abc[$j].'1')->applyFromArray($EstiloTitulo);

				//Ancho automatico
				$objExcel->getActiveSheet()->getColumnDimension($abc[$j])->setAutoSize(true);
			}
			
			//Titulos de las columnas
			$j=0;
			for($i=1;$i<count($rawdata[0]);$i=$i+2){
				next($rawdata[0]);
				$objSheet->setCellValue($abc[$j].'1',key($rawdata[0]));
				next($rawdata[0]);
				$j++;
			 }
			
			//Valores de las filas
			$f=2;//Posicion de la fila
			$letra=65;
//			echo "Filas: ".$filas;
//			echo "<br>";
//			echo "Columnas: ".$columnas;
//			echo "<br>";
			for($i=0;$i<$filas;$i++){
				for($j=0;$j<$columnas;$j++){
					if(isset($rawdata[$i][$j])){
//						echo $rawdata[$i][$j];
//						echo "<br>";
						if(is_object($rawdata[$i][$j])){
							$objSheet->setCellValue($abc[$j].$f,$rawdata[$i][$j]->format('Y-m-d'));
						}else{
							$objSheet->setCellValue($abc[$j].$f,$rawdata[$i][$j]);
						}
					}
					
					
				}
				$f++;
				$letra++;
			}
//			exit();
		}
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Reporte'.date('Ymd').'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar datos desde una consulta
	if($_GET['exp']==13){
		$Cons=base64_decode($_GET['Cons']);
		$SQL=sqlsrv_query($conexion,$Cons);
		
//		$Num=sqlsrv_has_rows($SQL);
//		echo $Num;
//		exit();

		$rawdata = array();
		$abc=array();
		$i=0;
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator(NOMBRE_PORTAL);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Reporte');
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);

			if(isset($_GET['hn'])&&($_GET['hn']==1)){
				//while($row=sql_fetch_array($SQL,2)){
				while(odbc_fetch_into($SQL, $rawdata[$i])){
					//odbc_fetch_into($SQL, $rawdata[$i]);
					//$rawdata[$i] = $row;
					//print_r($rawdata);
					//echo "<br><br>";
					//exit();
					$i++;
				}
				//exit();
			}else{
				while($row=sql_fetch_array($SQL)){
					$rawdata[$i] = $row;
					$i++;
				}
			}
			

			$columnas = count($rawdata[0])/2;
			$filas = count($rawdata);
			
			$j=0;
			$letra=65; //A
			$segLetra=65; //A

			//Llenar array de las letras del abecedario
			for($j=0;$j<$columnas;$j++){
				if($j<=25){
					$Titulo=chr($letra);
					$letra++;
				}else{
					$letra=65;
					$Titulo=chr($letra).chr($segLetra);
					$segLetra++;
				}
				$abc[$j]=$Titulo;
			}

			
			for($j=0;$j<$columnas;$j++){
				
				//Colocar estilos
				$objExcel->getActiveSheet()->getStyle($abc[$j].'1')->applyFromArray($EstiloTitulo);

				//Ancho automatico
				$objExcel->getActiveSheet()->getColumnDimension($abc[$j])->setAutoSize(true);
			}
			
			//Titulos de las columnas
			$j=0;
			for($i=1;$i<count($rawdata[0]);$i=$i+2){
				next($rawdata[0]);
				$objSheet->setCellValue($abc[$j].'1',key($rawdata[0]));
				next($rawdata[0]);
				$j++;
			 }
			
			//Valores de las filas
			$f=2;//Posicion de la fila
			$letra=65;
//			echo "Filas: ".$filas;
//			echo "<br>";
//			echo "Columnas: ".$columnas;
//			echo "<br>";
			for($i=0;$i<$filas;$i++){
				for($j=0;$j<$columnas;$j++){
					if(isset($rawdata[$i][$j])){
//						echo $rawdata[$i][$j];
//						echo "<br>";
						if(is_object($rawdata[$i][$j])){
							$objSheet->setCellValue($abc[$j].$f,$rawdata[$i][$j]->format('Y-m-d'));
						}else{
							$objSheet->setCellValue($abc[$j].$f,$rawdata[$i][$j]);
						}
					}
					
					
				}
				$f++;
				$letra++;
			}
//			exit();
		}
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Reporte'.date('Ymd').'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$filePath = '' . rand(0, getrandmax()) . rand(0, getrandmax()) . ".tmp";
		$objWriter->save($filePath);
		readfile($filePath);
		unlink($filePath);
//		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar Factura de proveedores (Portal de proveedores)
	if($_GET['exp']==14){
		$Cons=base64_decode($_GET['Cons']);
		$SQL=sqlsrv_query($conexion,$Cons);
		$Num=sqlsrv_has_rows($SQL);
		//echo $Cons;
		
		if($SQL){
			require_once('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator(NOMBRE_PORTAL);
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
					
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Factura proveedores');
			
			$objExcel->setActiveSheetIndex(0)
				 	 ->setCellValue('A1','Factura proveedor')
					 ->setCellValue('B1','Numero interno')
					 ->setCellValue('C1','Fecha factura')
					 ->setCellValue('D1','Fecha registro')
					 ->setCellValue('E1','Estado')
					 ->setCellValue('F1','Valor factura')
					 ->setCellValue('G1','Fecha pago')
				     ->setCellValue('H1','Valor pagado')
				     ->setCellValue('I1','Saldo pendiente');
			
			$i=2;
			while($registros=sqlsrv_fetch_array($SQL)){
				$dPago=ConsultarPago($registros['ID_FacturaCompra'], $registros['CardCode']);
				
				$objExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
					->setFormatCode('#,###');
				$objExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()
					->setFormatCode('#,###');
				$objExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
					->setFormatCode('#,###');
				
				$objSheet->setCellValue('A'.$i,$registros['NumAtCard']);
				$objSheet->setCellValue('B'.$i,$registros['DocNum']);
				$objSheet->setCellValue('C'.$i,$registros['DocDate']);
				$objSheet->setCellValue('D'.$i,$registros['TaxDate']);			
				if($dPago['DocNum']!=""){
					$objSheet->setCellValue('E'.$i,'Pagada');
				}else{
					$objSheet->setCellValue('E'.$i,'Pendiente');
				}
				$objSheet->setCellValue('F'.$i,$registros['DocTotal']);
				if($dPago['DocNum']!=""){
					$objSheet->setCellValue('G'.$i,$dPago['FechaPago']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('G'.$i,'');
				}
						
				$objSheet->setCellValue('H'.$i,$registros['ValorPago']);
				$objSheet->setCellValue('I'.$i,$registros['SaldoPendiente']);
				
				$i++;
				}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="FacturasProveedores.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	//Exportar Pagos efectuados (Portal de proveedores)
	if($_GET['exp']==15){
		$Cons=base64_decode($_GET['Cons']);
		$SQL=sqlsrv_query($conexion,$Cons);
		$Num=sqlsrv_has_rows($SQL);
		//echo $Cons;
		
		if($SQL){
			require('Classes/PHPExcel.php');
			$objExcel= new PHPExcel();
			$objSheet=$objExcel->setActiveSheetIndex(0);
			$objExcel->
			getProperties()
				->setCreator("AConstruir");
			
			$EstiloTitulo = array(
				'font' => array(
					'bold' => true,
				)
			);
			
			
			//Colocar estilos
			$objExcel->getActiveSheet()->getStyle('A1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('B1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('C1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('D1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('E1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('F1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('G1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('H1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('I1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('J1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('K1')->applyFromArray($EstiloTitulo);
			$objExcel->getActiveSheet()->getStyle('L1')->applyFromArray($EstiloTitulo);
					
			//Ancho automatico
			$objExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
			$objExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
			
			//Titulo de la hoja
			$objExcel->getActiveSheet()->setTitle('Pagos efectuados');
			
			$objExcel->setActiveSheetIndex(0)
				->setCellValue('A1','Factura proveedor')
				->setCellValue('B1','Numero interno')
				->setCellValue('C1','Fecha factura')
				->setCellValue('D1','Fecha vencimiento')
				->setCellValue('E1','Numero de pago')
				->setCellValue('F1','Valor factura')
				->setCellValue('G1','Valor pagado')
				->setCellValue('H1','Fecha pago')
				->setCellValue('I1','Efectivo')
				->setCellValue('J1','Tranferencia')
				->setCellValue('K1','Cheque')
				->setCellValue('L1','Num. Cheque');
			
			$i=2;
			while($registros=sqlsrv_fetch_array($SQL)){
				$objExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
					->setFormatCode('#,###');
				$objExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()
					->setFormatCode('#,###');
				$objExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
					->setFormatCode('#,###');
				$objExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
					->setFormatCode('#,###');
				$objExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
					->setFormatCode('#,###');
				
				$objSheet->setCellValue('A'.$i,$registros['FacturaProveedor']);
				$objSheet->setCellValue('B'.$i,$registros['DocNumFactura']);
				if($registros['FechaContFactura']!=""){
					$objSheet->setCellValue('C'.$i,$registros['FechaContFactura']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('C'.$i,'');
				}
				if($registros['FechaVencFactura']!=""){
					$objSheet->setCellValue('D'.$i,$registros['FechaVencFactura']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('D'.$i,'');
				}
				$objSheet->setCellValue('E'.$i,$registros['NumPagoEfectuado']);
				$objSheet->setCellValue('F'.$i,$registros['DocTotal']);
				$objSheet->setCellValue('G'.$i,$registros['ValorPago']);
				if($registros['FechaPago']!=""){
					$objSheet->setCellValue('H'.$i,$registros['FechaPago']->format('Y-m-d'));
				}else{
					$objSheet->setCellValue('H'.$i,'');
				}				
				$objSheet->setCellValue('I'.$i,$registros['CashSum']);
				$objSheet->setCellValue('J'.$i,$registros['TrsfrSum']);
				$objSheet->setCellValue('K'.$i,$registros['CheckSum']);
				$objSheet->setCellValue('L'.$i,$registros['CheckNum']);
				$i++;
				}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="PagosEfectuados.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter=PHPExcel_IOFactory::createWriter($objExcel,'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	sqlsrv_close ($conexion);
}
