<?php 
if(isset($_GET['exp'])&&$_GET['exp']!=""&&$_GET['Cons']!=""){
	require_once("includes/conexion.php");
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
//	ini_set('open_basedir', '/var/tmp');
	
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
	
	sqlsrv_close ($conexion);
}
