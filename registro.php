<?php 
if(isset($_REQUEST['P'])&&$_REQUEST['P']!=""){
	require_once("includes/conexion.php");
	$P=$_REQUEST['P'];
	
if($P==1){//Agregar nueva categoria
	try{
		$Cons_InsCat="EXEC sp_tbl_Categorias NULL,'".$_POST['NombreCategoria']."',".$_POST['EstadoCategoria'].",".$_POST['IDPadre'].",'".$_POST['URL']."','".$_POST['MostrarDashboard']."',".$_POST['TipoCategoria'].",".$_SESSION['CodUser'].",1";
		//echo $Cons_InsCat;
		//exit();
		if(sqlsrv_query($conexion,$Cons_InsCat)){
			InsertarLog(2, 1, $Cons_InsCat);
			sqlsrv_close($conexion);
			header('Location:gestionar_categorias.php?a='.base64_encode("OK_Cat"));
		}else{
			InsertarLog(1, 1, $Cons_InsCat);
			throw new Exception('Ha ocurrido un error al insertar categoría');			
			sqlsrv_close($conexion);
			}
	}catch (Exception $e) {
		InsertarLog(1, 1, $Cons_InsCat);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==2){//Editar categoria
	try{
		$Cons_UptCat="EXEC sp_tbl_Categorias '".$_POST['ID']."','".$_POST['NombreCategoria']."',".$_POST['EstadoCategoria'].",".$_POST['IDPadre'].",'".$_POST['URL']."','".$_POST['MostrarDashboard']."',".$_POST['TipoCategoria'].",".$_SESSION['CodUser'].",2";
		//echo $Cons_UptCat;
		//exit();
		if(sqlsrv_query($conexion,$Cons_UptCat)){
			InsertarLog(2, 2, $Cons_UptCat);
			sqlsrv_close($conexion);
			header('Location:gestionar_categorias.php?a='.base64_encode("OK_Cat_edit"));
		}else{
			InsertarLog(1, 2, $Cons_UptCat);
			throw new Exception('Ha ocurrido un error al editar categoría');			
			sqlsrv_close($conexion);
			}
	}catch (Exception $e) {
		InsertarLog(1, 2, $Cons_UptCat);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==3){//Eliminar categoria
	try{
		$Cons_DelCat="EXEC sp_tbl_Categorias '".$_GET['id']."',NULL,NULL,NULL,NULL,NULL,NULL,".$_SESSION['CodUser'].",3";
		//echo Cons_DelCat;
		//exit();
		if(sqlsrv_query($conexion,$Cons_DelCat)){
			InsertarLog(2, 3, $Cons_DelCat);
			sqlsrv_close($conexion);
			header('Location:gestionar_categorias.php?a='.base64_encode("OK_Cat_delete"));
		}else{
			InsertarLog(1, 3, $Cons_DelCat);
			throw new Exception('Ha ocurrido un error al eliminar la categoría');			
			sqlsrv_close($conexion);
			}
	}catch (Exception $e) {
		InsertarLog(1, 3, $Cons_DelCat);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==4){//Agregar nuevo usuario
	try{
		$ParamInsUser=array(
			"NULL",
			"'".$_POST['Usuario']."'",
			"'".md5($_POST['Password'])."'",
			"'".$_POST['Nombre']."'",
			"'".$_POST['SegundoNombre']."'",
			"'".$_POST['Apellido']."'",
			"'".$_POST['SegundoApellido']."'",
			"'".$_POST['Email']."'",
			"'".$_POST['PerfilUsuario']."'",
			"'".$_POST['CambioPass']."'",
			"'".$_POST['TimeOut']."'",
			"'".$_POST['CodigoSAP']."'",
			"'".$_POST['Estado']."'",
			"'".$_POST['Proveedor']."'",
			"1"
		);
		$SQL_InsUser=EjecutarSP('sp_tbl_Usuarios',$ParamInsUser,4);
		if($SQL_InsUser){
			$row_InsUser=sqlsrv_fetch_array($SQL_InsUser);			
			$i=0;
			$CuentaCliente=count($_POST['Cliente']);
			//echo $Cuenta;
			while($i<$CuentaCliente){
				if($_POST['Cliente'][$i]!=""){
					
					//Consultar si ya existe el cliente
					$SQL_ConsCliente=Seleccionar('uvw_tbl_ClienteUsuario','CodigoCliente',"ID_Usuario='".$row_InsUser[0]."' and CodigoCliente='".$_POST['Cliente'][$i]."'");
					$row_ConsCliente=sqlsrv_fetch_array($SQL_ConsCliente);

					//Insertar el cliente
					if($row_ConsCliente['CodigoCliente']==""){
						$ParamInsertCliente=array(
							"'".$row_InsUser[0]."'",
							"'".$_POST['Cliente'][$i]."'",
							"1"
						);
						$SQL_InsertCliente=EjecutarSP('sp_InsertarClienteUsuario',$ParamInsertCliente,4);
						if(!$SQL_InsertCliente){
							throw new Exception('Ha ocurrido un error al insertar el cliente');
							sqlsrv_close($conexion);
							exit();
						}
					}

					//Insertar la sucursal
					$ParamInsertSucursal=array(
						"'".$row_InsUser[0]."'",
						"'".$_POST['Cliente'][$i]."'",
						"'".$_POST['Sucursal'][$i]."'",
						"'".$_SESSION['CodUser']."'",
						"1"
					);
					$SQL_InsertSucursal=EjecutarSP('sp_InsertarClienteSucursalUsuario',$ParamInsertSucursal,4);

					if(!$SQL_InsertSucursal){
						throw new Exception('Ha ocurrido un error al insertar la sucursal');
						sqlsrv_close($conexion);
						exit();
					}
				}
				$i++;
			}
			sqlsrv_close($conexion);
			header('Location:gestionar_usuarios.php?a='.base64_encode("OK_User"));
		}else{
			throw new Exception('Ha ocurrido un error al insertar el usuario');			
			sqlsrv_close($conexion);
			}
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==5){//Editar usuario
	try{
		if($_POST['Password']!=""){//Cambiar clave
			$ParamUpdClave=array(
				"'".$_POST['ID_Usuario']."'",
				"'".md5($_POST['Password'])."'",
				"'".$_POST['CambioPass']."'"
			);
			$SQL_Clave=EjecutarSP('sp_tbl_Usuarios_CambiarClave',$ParamUpdClave,5);
		}		
		$ParamInsUser=array(
			"'".$_POST['ID_Usuario']."'",
			"NULL",
			"NULL",
			"'".$_POST['Nombre']."'",
			"'".$_POST['SegundoNombre']."'",
			"'".$_POST['Apellido']."'",
			"'".$_POST['SegundoApellido']."'",
			"'".$_POST['Email']."'",
			"'".$_POST['PerfilUsuario']."'",
			"NULL",
			"'".$_POST['TimeOut']."'",
			"'".$_POST['CodigoSAP']."'",
			"'".$_POST['Estado']."'",
			"'".$_POST['Proveedor']."'",
			"2"
		);
		$SQL_InsUser=EjecutarSP('sp_tbl_Usuarios',$ParamInsUser,5);
		
		if($SQL_InsUser){
			
			$ParamDelete=array(
				"'".$_POST['ID_Usuario']."'",
				"'".$_SESSION['CodUser']."'",
				"1"
			);
			$SQL_Delete=EjecutarSP('sp_EliminarRelSucursalesClientesUsuario',$ParamDelete,5);
			
			$i=0;
			$CuentaCliente=count($_POST['Cliente']);
			//echo $Cuenta;
			while($i<$CuentaCliente){
				if($_POST['Cliente'][$i]!=""){
					
					//Consultar si ya existe el cliente
					$SQL_ConsCliente=Seleccionar('uvw_tbl_ClienteUsuario','CodigoCliente',"ID_Usuario='".$_POST['ID_Usuario']."' and CodigoCliente='".$_POST['Cliente'][$i]."'");
					$row_ConsCliente=sqlsrv_fetch_array($SQL_ConsCliente);

					//Insertar el cliente
					if($row_ConsCliente['CodigoCliente']==""){
						$ParamInsertCliente=array(
							"'".$_POST['ID_Usuario']."'",
							"'".$_POST['Cliente'][$i]."'",
							"1"
						);
						$SQL_InsertCliente=EjecutarSP('sp_InsertarClienteUsuario',$ParamInsertCliente,5);
						if(!$SQL_InsertCliente){
							throw new Exception('Ha ocurrido un error al insertar el cliente');
							sqlsrv_close($conexion);
							exit();
						}
					}

					//Insertar la sucursal
					$ParamInsertSucursal=array(
						"'".$_POST['ID_Usuario']."'",
						"'".$_POST['Cliente'][$i]."'",
						"'".$_POST['Sucursal'][$i]."'",
						"'".$_SESSION['CodUser']."'",
						"1"
					);
					$SQL_InsertSucursal=EjecutarSP('sp_InsertarClienteSucursalUsuario',$ParamInsertSucursal,5);

					if(!$SQL_InsertSucursal){
						throw new Exception('Ha ocurrido un error al insertar la sucursal');
						sqlsrv_close($conexion);
						exit();
					}
				}
				$i++;
			}
			$ParamDelete=array(
				"'".$_POST['ID_Usuario']."'",
				"'".$_SESSION['CodUser']."'",
				"2"
			);
			$SQL_Delete=EjecutarSP('sp_EliminarRelSucursalesClientesUsuario',$ParamDelete,5);
			sqlsrv_close($conexion);
			header('Location:gestionar_usuarios.php?a='.base64_encode("OK_EditUser"));
		}else{
			throw new Exception('Ha ocurrido un error al insertar el usuario');			
			sqlsrv_close($conexion);
			}
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==6){//Agregar nuevo perfil
	try{
		$Cons_InsPerfil="EXEC sp_tbl_PerfilesUsuarios NULL,'".$_POST['NombrePerfil']."',1";
		$SQL_InsPerfil=sqlsrv_query($conexion,$Cons_InsPerfil);
		if($SQL_InsPerfil){
			$row_InsPerfil=sqlsrv_fetch_array($SQL_InsPerfil);
			InsertarLog(2, 6, $Cons_InsPerfil);
			$i=0;
			$Cuenta=count($_POST['Permiso']);
			while($i<$Cuenta){
				$Cons_InsertPer="Insert Into tbl_PermisosPerfiles Values ('".$row_InsPerfil[0]."','".$_POST['Permiso'][$i]."')";
				$SQL_InsertPer=sqlsrv_query($conexion,$Cons_InsertPer);
				if($SQL_InsertPer){
					$i++;
				}else{
					throw new Exception('Error insertando permiso');
					InsertarLog(1, 6, $Cons_InsertPer);
					sqlsrv_close($conexion);
					exit();
				}			
			}
			sqlsrv_close($conexion);
			header('Location:gestionar_perfiles.php?a='.base64_encode("OK_Perfil"));	
		}else{
			InsertarLog(1, 6, $Cons_InsPerfil);
			throw new Exception('Ha ocurrido un error al insertar el nuevo perfil');			
			sqlsrv_close($conexion);
		}
	}catch (Exception $e) {
		InsertarLog(1, 6, $Cons_InsPerfil);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==7){//Eliminar perfil
	try{
		$Cons_DelPerfil="EXEC usp_tbl_PerfilesUsuarios '".$_GET['id']."',NULL,NULL,3";
		if(sqlsrv_query($conexion,$Cons_DelPerfil)){
			InsertarLog(2, 7, $Cons_DelPerfil);
			sqlsrv_close($conexion);
			header('Location:gestionar_perfiles.php?a='.base64_encode("OK_Perfil_delete"));
		}else{
			InsertarLog(1, 7, $Cons_DelPerfil);
			throw new Exception('Ha ocurrido un error al eliminar el perfil');			
			sqlsrv_close($conexion);
			}
	}catch (Exception $e) {
		InsertarLog(1, 7, $Cons_DelPerfil);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==8){//Editar perfil
	try{
		$Cons_EditPerfil="EXEC sp_tbl_PerfilesUsuarios '".$_POST['ID_PerfilUsuario']."','".$_POST['NombrePerfil']."',2";
		$SQL_EditPerfil=sqlsrv_query($conexion,$Cons_EditPerfil);
		if($SQL_EditPerfil){
			InsertarLog(2, 8, $Cons_EditPerfil);
			$Cons_Delete="Delete From tbl_PermisosPerfiles Where ID_PerfilUsuario='".$_POST['ID_PerfilUsuario']."'";
			$SQL_Delete=sqlsrv_query($conexion,$Cons_Delete);
			if($SQL_Delete){
				$i=0;
				$Cuenta=count($_POST['Permiso']);
				while($i<$Cuenta){
					$Cons_InsertPer="Insert Into tbl_PermisosPerfiles Values ('".$_POST['ID_PerfilUsuario']."','".$_POST['Permiso'][$i]."')";
					$SQL_InsertPer=sqlsrv_query($conexion,$Cons_InsertPer);
					if($SQL_InsertPer){
						$i++;
					}else{
						InsertarLog(1, 8, $Cons_InsertPer);
						throw new Exception('Error insertando permiso');						
						sqlsrv_close($conexion);
						exit();
					}			
				}
				sqlsrv_close($conexion);
				header('Location:gestionar_perfiles.php?a='.base64_encode("OK_EditPerfil"));	
			}else{
				throw new Exception('Ha ocurrido un error al eliminar los permisis del perfil');
				InsertarLog(1, 8, $Cons_Delete);
				sqlsrv_close($conexion);
				exit();
			}			
		}else{
			InsertarLog(1, 8, $Cons_EditPerfil);
			throw new Exception('Ha ocurrido un error al editar el perfil');			
			sqlsrv_close($conexion);
			exit();
		}		
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==9){//Insertar nuevos archivos en informes
	try{
		$i=0;//Archivos
		$j=0;//Cantidad de archivos
		//*** Carpeta de archivos ***
		$carp_archivos=ObtenerVariable("RutaArchivos");
		//*** Carpeta temporal ***
		$temp=ObtenerVariable("CarpetaTmp");
		$dir=$temp."/".$_SESSION['CodUser']."/";		   
		$route= opendir($dir);
		//$directorio = opendir("."); //ruta actual
		$DocFiles=array();
		while ($archivo = readdir($route)){ //obtenemos un archivo y luego otro sucesivamente
			if(($archivo == ".")||($archivo == "..")) continue;

			if (!is_dir($archivo)){//verificamos si es o no un directorio
				$DocFiles[$i]=$archivo;
				$i++;
				}
		}
		closedir($route);
		
		$CantFiles=$_POST['CantFiles'];
		
		while($j<$CantFiles){
			$CountSuc=count($_POST['Sucursal'.$j]);
			if($CountSuc>0){//Escogio sucursales
				$k=0;//Cantidad de sucursales
				while($k<$CountSuc){
					
					//Sacar la extension del archivo
					$Ext = end(explode('.',$DocFiles[$j]));
					//Sacar el nombre sin la extension
					$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
					$Prefijo = substr(uniqid(rand()),0,3);
					$NuevoNombre=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo.".".$Ext;
					
					//Insertar el registro en la BD
					$Cons_InsArchivo="EXEC sp_tbl_Archivos NULL,'".$_POST['CodigoCliente']."','".$_POST['Sucursal'.$j][$k]."','".$_POST['Categoria'.$j]."','".$_POST['Fecha'.$j]."','".LSiqmlObs($_POST['Comentarios'.$j])."','".$NuevoNombre."','".$_SESSION['CodUser']."',1";
					$SQL_InsArchivo=sqlsrv_query($conexion,$Cons_InsArchivo);
					
					if($SQL_InsArchivo){						
						//Mover archivo a la carpeta real
						$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$_POST['CodigoCliente']."/".$_POST['Categoria'.$j]."/";
						if(file_exists($dir_new)){
							copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
						}else{
							mkdir($dir_new,0777, true);
							copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
						}
						
						//Enviar email
						$Cons_DatosEmail="EXEC sp_ConsultarUsuariosSucursalesClientes '".$_POST['CodigoCliente']."', '".$_POST['Sucursal'.$j][$k]."'";
						$SQL_DatosEmail=sqlsrv_query($conexion,$Cons_DatosEmail);
						
						while($row_DatosEmail=sqlsrv_fetch_array($SQL_DatosEmail)){
							if($row_DatosEmail['Email']!=""){//Validar que exista el email
								EnviarMail($row_DatosEmail['Email'],$row_DatosEmail['NombreUsuario'],1,"","","","",$_POST['CodigoCliente'],$_POST['Sucursal'.$j][$k],$_POST['Categoria'.$j],LSiqmlObs($_POST['Comentarios'.$j]),$NuevoNombre);
							}
						}						
						//echo $Cons_DatosEmail;						
						$k++;
					}else{
						InsertarLog(1, 9, $Cons_InsArchivo);
						throw new Exception('Error insertando archivo');						
						sqlsrv_close($conexion);
						exit();
					}
				}
			}else{//No escogio sucursales
				//Buscar las sucursales asignadas
				if(PermitirFuncion(205)){
					$Where="CodigoCliente=''".$_POST['CodigoCliente']."''";
					$SQL_Sucursal=Seleccionar("uvw_Sap_tbl_Clientes_Sucursales","NombreSucursal",$Where);
				}else{
					$Where="CodigoCliente=''".$_POST['CodigoCliente']."'' and ID_Usuario = ".$_SESSION['CodUser'];
					$SQL_Sucursal=Seleccionar("uvw_tbl_SucursalesClienteUsuario","NombreSucursal",$Where);	
				}
				$ListSucursales=array();
				$t=0;//Cantidad de sucursales
				while($row_Sucursal=sqlsrv_fetch_array($SQL_Sucursal)){
					$ListSucursales[$t]=$row_Sucursal['NombreSucursal'];
					$t++;
				}
				$CountSuc=count($ListSucursales);
				$k=0;//Cantidad de sucursales
				while($k<$CountSuc){
					//Sacar la extension del archivo
					$Ext = end(explode('.',$DocFiles[$j]));    
					//Sacar el nombre sin la extension
					$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
					$Prefijo = substr(uniqid(rand()),0,3);
					$NuevoNombre=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo.".".$Ext;
					
					//Insertar el registro en la BD
					$Cons_InsArchivo="EXEC sp_tbl_Archivos NULL,'".$_POST['CodigoCliente']."','".$ListSucursales[$k]."','".$_POST['Categoria'.$j]."','".$_POST['Fecha'.$j]."','".LSiqmlObs($_POST['Comentarios'.$j])."','".$NuevoNombre."','".$_SESSION['CodUser']."',1";
					$SQL_InsArchivo=sqlsrv_query($conexion,$Cons_InsArchivo);

					if($SQL_InsArchivo){					
						//Mover archivo a la carpeta real
						$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$_POST['CodigoCliente']."/".$_POST['Categoria'.$j]."/";		
						if(file_exists($dir_new)){
							copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
						}else{
							mkdir($dir_new,0777, true);
							copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
						}	
						
						//Enviar email
						$Cons_DatosEmail="EXEC sp_ConsultarUsuariosSucursalesClientes '".$_POST['CodigoCliente']."', '".$ListSucursales[$k]."'";
						$SQL_DatosEmail=sqlsrv_query($conexion,$Cons_DatosEmail);
						
						while($row_DatosEmail=sqlsrv_fetch_array($SQL_DatosEmail)){
							if($row_DatosEmail['Email']!=""){//Validar que exista el email
								EnviarMail($row_DatosEmail['Email'],$row_DatosEmail['NombreUsuario'],1,"","","","",$_POST['CodigoCliente'],$ListSucursales[$k],$_POST['Categoria'.$j],LSiqmlObs($_POST['Comentarios'.$j]),$NuevoNombre);
							}
						}				
						//echo $Cons_DatosEmail;						
						$k++;
					}else{
						InsertarLog(1, 9, $Cons_InsArchivo);
						throw new Exception('Error insertando archivo');						
						sqlsrv_close($conexion);
						exit();
					}
				}
			}
		$j++;	
		}
		sqlsrv_close($conexion);
		header('Location:gestionar_informes.php?a='.base64_encode("OK_UpdFile"));					
	}catch (Exception $e) {
		InsertarLog(1, 9, $Cons_InsArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==10){//Actualizar servidor SMTP
	try{
		$Cons_Email="EXEC sp_tbl_EmailNotificaciones '".$_GET['Usuario']."','".$_GET['Password']."','".$_GET['Servidor']."','".$_GET['Puerto']."','".$_GET['ReqAut']."','".$_GET['TypeCon']."',1";
		if(sqlsrv_query($conexion,$Cons_Email)){
			InsertarLog(2, 10, $Cons_Email);
			sqlsrv_close($conexion);
			echo "OK";
		}else{
			InsertarLog(1, 10, $Cons_Email);
			throw new Exception('Ha ocurrido un error al actualizar el servidor SMTP');			
			sqlsrv_close($conexion);
			echo "E1";
			}
	}catch (Exception $e) {
		InsertarLog(1, 10, $Cons_Email);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==11){//Actualizar variables globales
	try{
		$Cons_Var="EXEC sp_tbl_VariablesGlobales '".$_GET['ID']."','".$_GET['Valor']."','".$_SESSION['CodUser']."',1";
		if(sqlsrv_query($conexion,$Cons_Var)){
			InsertarLog(2, 11, $Cons_Var);
			sqlsrv_close($conexion);
			echo "OK";
		}else{
			InsertarLog(1, 11, $Cons_Var);
			throw new Exception('Ha ocurrido un error al actualizar el valor de la variable');			
			sqlsrv_close($conexion);
			echo "E1";
			}
	}catch (Exception $e) {
		InsertarLog(1, 11, $Cons_Var);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==12){//Actualizar plantillas email
	try{
		$Cons_PlantEmail="EXEC sp_tbl_PlantillasEmail '".$_GET['ID']."','".$_GET['TipoNot']."','".$_GET['Asunto']."','".$_GET['Mensaje']."','".$_GET['Estado']."',2";
		if(sqlsrv_query($conexion,$Cons_PlantEmail)){
			//InsertarLog(2, 12, $Cons_PlantEmail);
			sqlsrv_close($conexion);
			echo "OK";
		}else{
			InsertarLog(1, 12, $Cons_PlantEmail);
			throw new Exception('Ha ocurrido un error al actualizar la plantilla de email');			
			sqlsrv_close($conexion);
			echo "E1";
			}
	}catch (Exception $e) {
		InsertarLog(1, 12, $Cons_PlantEmail);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==13){//Eliminar archivos
	try{
		//Consultar archivo para eliminarlo fisicamente
		$Con_BusArchivo="Select * From uvw_tbl_Archivos Where ID_Archivo='".$_GET['id']."'";
		$SQL_BusArchivo=sqlsrv_query($conexion,$Con_BusArchivo);
		$row_BusArchivo=sqlsrv_fetch_array($SQL_BusArchivo);
		
		//Consultar si el archivo esta en mas de una sucursal
		$ConsSuc="Select * From uvw_tbl_Archivos Where CardCode='".$row_BusArchivo['CardCode']."' and ID_Categoria='".$row_BusArchivo['ID_Categoria']."' and Archivo='".$row_BusArchivo['Archivo']."'";
		$SQLSuc=sqlsrv_query($conexion,$ConsSuc,array(),array( "Scrollable" => 'static' ));
		$NumSuc=sqlsrv_num_rows($SQLSuc);
		if($NumSuc==1){//Si solo esta en un, se elimina fisicamente. Sino, no se elimina fisicamente
			//echo $NumSuc;
			$carp_archivos=ObtenerVariable("RutaArchivos");
			$File=$_SESSION['BD']."/".$carp_archivos."/".$row_BusArchivo['CardCode']."/".$row_BusArchivo['ID_Categoria']."/".$row_BusArchivo['Archivo'];
			if(file_exists($File)){
				unlink($File);
			}
		}
		
		$Cons_DelArchivo="EXEC sp_tbl_Archivos '".$_GET['id']."',NULL,NULL,NULL,NULL,NULL,NULL,NULL,2";
		if(sqlsrv_query($conexion,$Cons_DelArchivo)){
			InsertarLog(2, 13, $Cons_DelArchivo);
			sqlsrv_close($conexion);
			if($_GET['type']==1){
				header('Location:gestionar_documentos.php?a='.base64_encode("OK_File_delete"));
			}elseif($_GET['type']==2){
				header('Location:gestionar_informes.php?a='.base64_encode("OK_File_delete"));
			}elseif($_GET['type']==4){
				header('Location:gestionar_calidad.php?a='.base64_encode("OK_File_delete"));
			}
			
		}else{
			InsertarLog(1, 13, $Cons_DelArchivo);
			throw new Exception('Ha ocurrido un error al eliminar el archivo');			
			sqlsrv_close($conexion);
			}
	}catch (Exception $e) {
		InsertarLog(1, 13, $Cons_DelArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==14){//Actualizar datos del portal
	try{
		$Cons_UpdDatos="EXEC sp_tbl_DatosPortal '".$_GET['Valor']."','".$_GET['ID']."'";
		if(sqlsrv_query($conexion,$Cons_UpdDatos)){
			InsertarLog(2, 14, $Cons_UpdDatos);
			sqlsrv_close($conexion);
			echo "OK";
		}else{
			InsertarLog(1, 14, $Cons_UpdDatos);
			throw new Exception('Ha ocurrido un error al actualizar los datos');			
			sqlsrv_close($conexion);
			echo "E1";
			}
	}catch (Exception $e) {
		InsertarLog(1, 14, $Cons_UpdDatos);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==15){//Mover imagenes de los logos del portal a la carpeta real
	try{
		if($_GET['ID']==3){//Logo de la empresa
			if(copy("img/img_tmp/img_logo.png","img/img_logo.png")){
				echo "OK";
			}else{
				echo "E1";
			}
		}elseif($_GET['ID']==4){//Logo slim de la empresa
			if(copy("img/img_tmp/img_logo_slim.png","img/img_logo_slim.png")){
				echo "OK";
			}else{
				echo "E1";
			}
		}elseif($_GET['ID']==5){//Favicon
			if(copy("img/img_tmp/favicon.png","css/favicon.png")){
				echo "OK";
			}else{
				echo "E1";
			}
		}elseif($_GET['ID']==8){//Fondo de la pantalla de inicio
			if(copy("img/img_tmp/img_background.jpg","img/img_background.jpg")){
				echo "OK";
			}else{
				echo "E1";
			}
		}	
		//throw new Exception('Ha ocurrido un error al mover el archivo');
	}catch (Exception $e) {
		InsertarLog(1, 15, $e->getMessage());
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==16){//Insertar nuevos archivos en documentos
	try{
		$i=0;//Archivos
		$j=0;//Cantidad de archivos
		//*** Carpeta de archivos ***
		$carp_archivos=ObtenerVariable("RutaArchivos");
		//*** Carpeta temporal ***
		$temp=ObtenerVariable("CarpetaTmp");
		$dir=$temp."/".$_SESSION['CodUser']."/";		   
		$route= opendir($dir);
		//$directorio = opendir("."); //ruta actual
		$DocFiles=array();
		while ($archivo = readdir($route)){ //obtenemos un archivo y luego otro sucesivamente
			if(($archivo == ".")||($archivo == "..")) continue;

			if (!is_dir($archivo)){//verificamos si es o no un directorio
				$DocFiles[$i]=$archivo;
				$i++;
				}
		}
		closedir($route);
		
		$CantFiles=$_POST['CantFiles'];
		
		while($j<$CantFiles){	
			//Sacar la extension del archivo
			$Ext = end(explode('.',$DocFiles[$j]));    
			//Sacar el nombre sin la extension
			$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
			$Prefijo = substr(uniqid(rand()),0,3);
			$NuevoNombre=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo.".".$Ext;

			//Insertar el registro en la BD
			$Cons_InsArchivo="EXEC sp_tbl_Archivos NULL,'".$_POST['CodigoCliente']."',NULL,'".$_POST['Categoria'.$j]."','".$_POST['Fecha'.$j]."','".LSiqmlObs($_POST['Comentarios'.$j])."','".$NuevoNombre."','".$_SESSION['CodUser']."',1";
			$SQL_InsArchivo=sqlsrv_query($conexion,$Cons_InsArchivo);

			if($SQL_InsArchivo){						
				//Mover archivo a la carpeta real
				$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$_POST['CodigoCliente']."/".$_POST['Categoria'.$j]."/";
				if(file_exists($dir_new)){
					copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
				}else{
					mkdir($dir_new,0777, true);
					copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
				}		

				$k++;
			}else{
				InsertarLog(1, 16, $Cons_InsArchivo);
				throw new Exception('Error insertando archivo');				
				sqlsrv_close($conexion);
				exit();
			}
			$j++;
		}
		sqlsrv_close($conexion);
		header('Location:gestionar_documentos.php?a='.base64_encode("OK_UpdFile"));					
	}catch (Exception $e) {
		InsertarLog(1, 16, $Cons_InsArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==17){//Insertar nuevos archivos en productos
	try{
		$i=0;//Archivos
		$j=0;//Cantidad de archivos
		//*** Carpeta de archivos ***
		$carp_archivos=ObtenerVariable("RutaArchivos");
		$carp_productos="productos";
		$RutaAttachSAP=ObtenerDirAttach();
		//*** Carpeta temporal ***
		$temp=ObtenerVariable("CarpetaTmp");
		$dir=$temp."/".$_SESSION['CodUser']."/";		   
		$route= opendir($dir);
		//$directorio = opendir("."); //ruta actual
		$DocFiles=array();
		while ($archivo = readdir($route)){ //obtenemos un archivo y luego otro sucesivamente
			if(($archivo == ".")||($archivo == "..")) continue;

			if (!is_dir($archivo)){//verificamos si es o no un directorio
				$DocFiles[$i]=$archivo;
				$i++;
				}
		}
		closedir($route);
		
		$CantFiles=$_POST['CantFiles'];
		
		while($j<$CantFiles){
			//Sacar la extension del archivo
			$Ext = end(explode('.',$DocFiles[$j]));    
			//Sacar el nombre sin la extension
			$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
			$Prefijo = substr(uniqid(rand()),0,3);
			$NuevoNombre=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo.".".$Ext;

			//Insertar el registro en la BD
			$Cons_InsArchivo="EXEC sp_tbl_Productos NULL,'".$_POST['ItemCode']."','".$_POST['Categoria'.$j]."','".$_POST['Fecha'.$j]."','".LSiqmlObs($_POST['Comentarios'.$j])."','".$NuevoNombre."','".$_SESSION['CodUser']."',1";
			$SQL_InsArchivo=sqlsrv_query($conexion,$Cons_InsArchivo);

			if($SQL_InsArchivo){						
				//Mover archivo a la carpeta real
				$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$carp_productos."/".$_POST['ItemCode']."/".$_POST['Categoria'.$j]."/";
				if(file_exists($dir_new)){
					copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
				}else{
					mkdir($dir_new,0777, true);
					copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
				}
				
				//Mover archivo a SAP B1
				if(file_exists($dir_new)){
					copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
					copy($dir_new.$NuevoNombre,$RutaAttachSAP[0].$NuevoNombre);

					//Registrar archivo en la BD
					$ParamInsAnex=array(
						"'Cola articulos Portal'",
						"'".date('Ymd')."'",
						"'AR'",
						"'".$_POST['ItemCode']."'",
						"'Insertando desde SAP B1'",
						"2"
					);
					$SQL_InsAnex=EjecutarSP('INTEGRA_SAPB1..usp_tbl_ColaIntegrador',$ParamInsAnex,17);
					if(!$SQL_InsAnex){
						throw new Exception('Error al insertar los anexos.');			
						sqlsrv_close($conexion);
					}
				}

				$k++;
			}else{
				InsertarLog(1, 17, $Cons_InsArchivo);
				throw new Exception('Error insertando archivo');				
				sqlsrv_close($conexion);
				exit();
			}
			$j++;
		}
		sqlsrv_close($conexion);
		header('Location:gestionar_productos.php?a='.base64_encode("OK_UpdFile"));					
	}catch (Exception $e) {
		InsertarLog(1, 17, $Cons_InsArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==18){//Eliminar productos
	try{
		//Consultar archivo para eliminarlo fisicamente
		$Con_BusArchivo="Select * From uvw_tbl_Productos Where ID_Producto='".$_GET['id']."'";
		$SQL_BusArchivo=sqlsrv_query($conexion,$Con_BusArchivo);
		$row_BusArchivo=sqlsrv_fetch_array($SQL_BusArchivo);
		
		$carp_archivos=ObtenerVariable("RutaArchivos");
		$carp_productos="productos";
		
		$File=$_SESSION['BD']."/".$carp_archivos."/".$carp_productos."/".$row_BusArchivo['ItemCode']."/".$row_BusArchivo['ID_CategoriaProductos']."/".$row_BusArchivo['Archivo'];
		if(file_exists($File)){
			unlink($File);
		}
				
		$Cons_DelArchivo="EXEC sp_tbl_Productos '".$_GET['id']."',NULL,NULL,NULL,NULL,NULL,NULL,2";
		if(sqlsrv_query($conexion,$Cons_DelArchivo)){
			//Registrar archivo en la BD
			$ParamInsAnex=array(
				"'Cola articulos Portal'",
				"'".date('Ymd')."'",
				"'AR'",
				"'".$row_BusArchivo['ItemCode']."'",
				"'Insertando desde SAP B1'",
				"2"
			);
			$SQL_InsAnex=EjecutarSP('INTEGRA_SAPB1..usp_tbl_ColaIntegrador',$ParamInsAnex,18);			
			if(!$SQL_InsAnex){
				throw new Exception('Error al insertar los anexos.');			
				sqlsrv_close($conexion);
			}
			
			InsertarLog(2, 18, $Cons_DelArchivo);
			sqlsrv_close($conexion);
			header('Location:gestionar_productos.php?a='.base64_encode("OK_File_delete"));
		}else{
			throw new Exception('Ha ocurrido un error al eliminar el archivo');
			InsertarLog(1, 18, $Cons_DelArchivo);
			sqlsrv_close($conexion);
			}
	}catch (Exception $e) {
		InsertarLog(1, 18, $Cons_DelArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==19){//Poner Cookie al aceptar acuerdo de confidencialidad
	try{
		$Cons_UpdCookie="Update tbl_Usuarios Set [SetCookie]='".base64_encode($_SESSION['CodUser'])."' Where IDUsuario='".$_SESSION['CodUser']."'";
		if(sqlsrv_query($conexion,$Cons_UpdCookie)){
			InsertarLog(2, 19, $Cons_UpdCookie);
			$_SESSION['SetCookie']=base64_encode($_SESSION['CodUser']);
			sqlsrv_close($conexion);
			echo "OK";
		}else{
			throw new Exception('Ha ocurrido un error al aceptar el acuerdo');
			InsertarLog(1, 19, $Cons_UpdCookie);
			sqlsrv_close($conexion);
			echo "E1";
			}
	}catch (Exception $e) {
		InsertarLog(1, 19, $Cons_UpdCookie);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==20){//Mover los archivos de cargue masivo a las carpetas correspondientes
	try{
		$ConArchivos="Select * From uvw_tbl_Archivos_Cargue";
		$SQL=sqlsrv_query($conexion,$ConArchivos);
		$carp_archivos=ObtenerVariable("RutaArchivos");
		$i=0;
		while($row=sqlsrv_fetch_array($SQL)){
			$Msg=ValidarEstadoArchivoCargue($row['NombreCliente'],$row['NombreCategoria'],$row['ID_Sucursal'],utf8_decode($row['Archivo']));
			if($Msg[0][0]==0){//No hay error en el registro
				//Sacar la extension del archivo
				$Ext = end(explode('.',utf8_decode($row['Archivo'])));
				//Sacar el nombre sin la extension
				$OnlyName = substr(utf8_decode($row['Archivo']),0,strlen(utf8_decode($row['Archivo']))-(strlen($Ext)+1));
				//Reemplazar espacios
				$OnlyName=str_replace(" ","_",$OnlyName);
				$Prefijo = substr(uniqid(rand()),0,3);
				$NuevoNombre=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo.".".$Ext;
				
				//Insertar el registro en la BD
				$Cons_InsArchivo="EXEC sp_tbl_Archivos NULL,'".$row['CardCode']."','".$row['ID_Sucursal']."','".$row['ID_Categoria']."','".$row['Fecha']->format('Y-m-d')."','".LSiqmlObs($row['Comentarios'])."','".$NuevoNombre."','".$_SESSION['CodUser']."',1";
				$SQL_InsArchivo=sqlsrv_query($conexion,$Cons_InsArchivo);
				
				if($SQL_InsArchivo){
					$DelFile="Delete From tbl_Archivos_Cargue Where ID_Archivo='".$row['ID_Archivo']."'";
					$SQL_DelFile=sqlsrv_query($conexion,$DelFile);
					//Mover archivo a la carpeta real
					$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$row['CardCode']."/".$row['ID_Categoria']."/";
					if(file_exists($dir_new)){
						copy("cargue/".$row['Archivo'],$dir_new.$NuevoNombre);
					}else{
						mkdir($dir_new,0777, true);
						copy("cargue/".$row['Archivo'],$dir_new.$NuevoNombre);
					}
					$i=$i+1;
				}else{
					InsertarLog(1, 20, $Cons_InsArchivo);
					throw new Exception('Ha ocurrido un error cargar los archivos de forma masiva.');					
					sqlsrv_close($conexion);
				}
			}
		 }
		sqlsrv_close($conexion);
		header('Location:cargue_masivo_archivos.php?a='.base64_encode($i));
	}catch (Exception $e) {
		InsertarLog(1, 20, $Cons_InsArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==21){//Eliminar registro archivo de cargue
	try{
		if($_GET['type']==1){
			$DelFile="Delete From tbl_Archivos_Cargue Where ID_Archivo='".$_GET['id']."'";
			if(sqlsrv_query($conexion,$DelFile)){
				sqlsrv_close($conexion);
				header('Location:cargue_masivo_archivos.php');			
			}else{
				throw new Exception('Ha ocurrido un error al eliminar el archivo');
				InsertarLog(1, 21, $DelFile);
				sqlsrv_close($conexion);
				}
		}else{
			$DelFile="Truncate Table tbl_Archivos_Cargue";
			if(sqlsrv_query($conexion,$DelFile)){
				sqlsrv_close($conexion);
				header('Location:cargue_masivo_archivos.php');			
			}else{
				throw new Exception('Ha ocurrido un error al borrar los datos');
				InsertarLog(1, 21, $DelFile);
				sqlsrv_close($conexion);
				}
		}

	}catch (Exception $e) {
		InsertarLog(1, 21, $DelFile);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==22){//Cargar archivo .csv
	try{	
		if($_FILES['FileArchivo']['tmp_name']!=""){
			if(is_uploaded_file($_FILES['FileArchivo']['tmp_name'])){
				$Nombre_ArchivoCSV=$_FILES['FileArchivo']['name'];
				$ruta=ObtenerVariable("CarpetaTmp");
				if(move_uploaded_file($_FILES['FileArchivo']['tmp_name'],$ruta."/csv/".$Nombre_ArchivoCSV)){
					$fila=1;
					if(($gestor=fopen($ruta."/csv/".$Nombre_ArchivoCSV,"r"))!==FALSE){
						$DelFile="Truncate Table tbl_Archivos_Cargue";
						if(!sqlsrv_query($conexion,$DelFile)){
							throw new Exception('No se pudo limpiar la tabla de cargue');
							sqlsrv_close($conexion);		
						}
						while(($datos=fgetcsv($gestor,1000,$_POST['Delimiter']))!==FALSE){
							if($fila!=1){
								//Insertar el registro en la BD
								$Cons_InsArchivo="EXEC sp_tbl_Archivos_Cargue NULL,'".$datos[0]."','".$datos[1]."','".$datos[2]."','".$datos[3]."','".$datos[4]."','".utf8_encode($datos[5])."','".$_SESSION['CodUser']."',1";
								if(!sqlsrv_query($conexion,$Cons_InsArchivo)){
									InsertarLog(1, 22, "Fila: ".$fila." - ".$Cons_InsArchivo);
									throw new Exception('Ha ocurrido un error al cargar la linea '.$fila);
									sqlsrv_close($conexion);
								}
							}
							$fila++;
						}
					}else{
						throw new Exception('No se pudo abrir el archivo. Compruebe la ruta');
						sqlsrv_close($conexion);
					}
				}else{
					throw new Exception('No se pudo mover el archivo');
					sqlsrv_close($conexion);
				}
				fclose($gestor);
				sqlsrv_close($conexion);
				header('Location:cargue_masivo_archivos.php?b='.base64_encode($fila-2));
			}
		}else{
			throw new Exception('No se pudo cargar el archivo');
			sqlsrv_close($conexion);
		}
	}catch (Exception $e) {
		//InsertarLog(1, 22, Cons_InsArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==23){//Cargar archivo de productos .csv
	try{	
		if($_FILES['FileArchivo']['tmp_name']!=""){
			if(is_uploaded_file($_FILES['FileArchivo']['tmp_name'])){
				$Nombre_ArchivoCSV=$_FILES['FileArchivo']['name'];
				$ruta=ObtenerVariable("CarpetaTmp");
				if(move_uploaded_file($_FILES['FileArchivo']['tmp_name'],$ruta."/csv/".$Nombre_ArchivoCSV)){
					$fila=1;
					if(($gestor=fopen($ruta."/csv/".$Nombre_ArchivoCSV,"r"))!==FALSE){
						$DelFile="Truncate Table tbl_Productos_Cargue";
						if(!sqlsrv_query($conexion,$DelFile)){
							throw new Exception('No se pudo limpiar la tabla de cargue');
							sqlsrv_close($conexion);		
						}
						while(($datos=fgetcsv($gestor,1000,$_POST['Delimiter']))!==FALSE){
							if($fila!=1){
								//Insertar el registro en la BD
								$Cons_InsArchivo="EXEC sp_tbl_Productos_Cargue NULL,'".$datos[0]."','".$datos[1]."','".$datos[2]."','".$datos[3]."','".utf8_encode($datos[4])."','".$_SESSION['CodUser']."',1";
								if(!sqlsrv_query($conexion,$Cons_InsArchivo)){
									InsertarLog(1, 23, "Fila: ".$fila." - ".$Cons_InsArchivo);
									throw new Exception('Ha ocurrido un error al cargar la linea '.$fila);
									sqlsrv_close($conexion);
								}
							}
							$fila++;
						}
					}else{
						throw new Exception('No se pudo abrir el archivo. Compruebe la ruta');
						sqlsrv_close($conexion);
					}
				}else{
					throw new Exception('No se pudo mover el archivo');
					sqlsrv_close($conexion);
				}
				fclose($gestor);
				sqlsrv_close($conexion);
				header('Location:cargue_masivo_productos.php?b='.base64_encode($fila-2));
			}
		}else{
			throw new Exception('No se pudo cargar el archivo');
			sqlsrv_close($conexion);
		}
	}catch (Exception $e) {
		//InsertarLog(1, 22, Cons_InsArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==24){//Mover los archivos de cargue de productos masivo a las carpetas correspondientes
	try{
		$ConArchivos="Select * From uvw_tbl_Productos_Cargue";
		$SQL=sqlsrv_query($conexion,$ConArchivos);
		$carp_archivos=ObtenerVariable("RutaArchivos");
		$carp_productos="productos";
		$i=0;
		while($row=sqlsrv_fetch_array($SQL)){
			$Msg=ValidarEstadoProductosCargue($row['ItemName'],$row['NombreCategoriaProductos'],utf8_decode($row['Archivo']));
			if($Msg[0][0]==0){//No hay error en el registro
				//Sacar la extension del archivo
				$Ext = end(explode('.',utf8_decode($row['Archivo'])));
				//Sacar el nombre sin la extension
				$OnlyName = substr(utf8_decode($row['Archivo']),0,strlen(utf8_decode($row['Archivo']))-(strlen($Ext)+1));
				//Reemplazar espacios
				$OnlyName=str_replace(" ","_",$OnlyName);
				$Prefijo = substr(uniqid(rand()),0,3);
				$NuevoNombre=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo.".".$Ext;
				
				//Insertar el registro en la BD
				$Cons_InsArchivo="EXEC sp_tbl_Productos NULL,'".$row['ItemCode']."','".$row['ID_CategoriaProductos']."','".$row['Fecha']->format('Y-m-d')."','".LSiqmlObs($row['Comentarios'])."','".$NuevoNombre."','".$_SESSION['CodUser']."',1";
				$SQL_InsArchivo=sqlsrv_query($conexion,$Cons_InsArchivo);
				
				if($SQL_InsArchivo){
					$DelFile="Delete From tbl_Productos_Cargue Where ID_Producto='".$row['ID_Producto']."'";
					$SQL_DelFile=sqlsrv_query($conexion,$DelFile);
					//Mover archivo a la carpeta real
					$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$carp_productos."/".$row['ItemCode']."/".$row['ID_CategoriaProductos']."/";
					if(file_exists($dir_new)){
						copy("cargue/".$row['Archivo'],$dir_new.$NuevoNombre);
					}else{
						mkdir($dir_new,0777, true);
						copy("cargue/".$row['Archivo'],$dir_new.$NuevoNombre);
					}
					$i=$i+1;
				}else{
					InsertarLog(1, 24, $Cons_InsArchivo);
					throw new Exception('Ha ocurrido un error cargar los productos de forma masiva.');					
					sqlsrv_close($conexion);
				}
			}
		 }
		sqlsrv_close($conexion);
		header('Location:cargue_masivo_productos.php?a='.base64_encode($i));
	}catch (Exception $e) {
		InsertarLog(1, 24, $Cons_InsArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==25){//Eliminar registro archivo de cargue de productos
	try{
		if($_GET['type']==1){
			$DelFile="Delete From tbl_Productos_Cargue Where ID_Producto='".$_GET['id']."'";
			if(sqlsrv_query($conexion,$DelFile)){
				sqlsrv_close($conexion);
				header('Location:cargue_masivo_productos.php');			
			}else{
				InsertarLog(1, 25, $DelFile);
				throw new Exception('Ha ocurrido un error al eliminar el archivo');
				sqlsrv_close($conexion);
				}
		}else{
			$DelFile="Truncate Table tbl_Productos_Cargue";
			if(sqlsrv_query($conexion,$DelFile)){
				sqlsrv_close($conexion);
				header('Location:cargue_masivo_productos.php');			
			}else{
				InsertarLog(1, 25, $DelFile);
				throw new Exception('Ha ocurrido un error al borrar los datos');
				sqlsrv_close($conexion);
				}
		}

	}catch (Exception $e) {
		InsertarLog(1, 25, $DelFile);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==26){//Insertar las nuevas alertas
	try{
		$Count=count($_POST['Categoria']);
		$i=0;
		$Delete="Delete From tbl_AlertasInformes Where CardCode='".base64_decode($_POST['CardCode'])."' and Anno='".base64_decode($_POST['Anno'])."'";
		if(sqlsrv_query($conexion,$Delete)){
			while($i<$Count){
				if(($_POST['Sucursal'][$i]!="")&&($_POST['Categoria'][$i]!="")){
					//Insertar el registro en la BD
					$Cons_InsAlerta="EXEC sp_tbl_AlertasInformes '".base64_decode($_POST['CardCode'])."','".$_POST['Sucursal'][$i]."','".$_POST['Categoria'][$i]."','".$_POST['Enero'][$i]."','".$_POST['Febrero'][$i]."','".$_POST['Marzo'][$i]."','".$_POST['Abril'][$i]."','".$_POST['Mayo'][$i]."','".$_POST['Junio'][$i]."','".$_POST['Julio'][$i]."','".$_POST['Agosto'][$i]."','".$_POST['Septiembre'][$i]."','".$_POST['Octubre'][$i]."','".$_POST['Noviembre'][$i]."','".$_POST['Diciembre'][$i]."','".base64_decode($_POST['Anno'])."',1,'".$_SESSION['CodUser']."',1";
					//echo $Cons_InsAlerta;
					//exit();
					$SQL_InsAlerta=sqlsrv_query($conexion,$Cons_InsAlerta);

					if(!$SQL_InsAlerta){
						InsertarLog(1, 26, $Cons_InsAlerta);
						throw new Exception('Ha ocurrido un error al insertar las alertas.');					
						sqlsrv_close($conexion);
					}
				}
				$i=$i+1;
			}
			sqlsrv_close($conexion);
			header('Location:gestionar_alertas.php?a='.base64_encode("OK_Alert").'&Cliente='.$_POST['CardCode'].'&Anno='.$_POST['Anno']);
		}else{
			InsertarLog(1, 26, $Delete);
			throw new Exception('Ha ocurrido un error al eliminar el registro');
			sqlsrv_close($conexion);	
		}		
	}catch (Exception $e) {
		InsertarLog(1, 26, $SQL_InsAlerta);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
}
	
if($P==27){//Insertar nueva actividad
	try{
		//*** Carpeta temporal ***
		$i=0;//Archivos
		$temp=ObtenerVariable("CarpetaTmp");
		$carp_archivos=ObtenerVariable("RutaArchivos");
		$carp_anexos="actividades";
		$NuevoNombre="";
		$RutaAttachSAP=ObtenerDirAttach();
		$dir=$temp."/".$_SESSION['CodUser']."/";		   
		$route= opendir($dir);
		//$directorio = opendir("."); //ruta actual
		$DocFiles=array();
		while ($archivo = readdir($route)){ //obtenemos un archivo y luego otro sucesivamente
			if(($archivo == ".")||($archivo == "..")) continue;

			if (!is_dir($archivo)){//verificamos si es o no un directorio
				$DocFiles[$i]=$archivo;
				$i++;
				}
		}
		closedir($route);
		$CantFiles=count($DocFiles);
		
		//Insertar el registro en la BD
		if($_POST['TipoTarea']=='Interna'){
			$ClienteActividad=base64_decode($_POST['ClienteActividadInterno']);
			$SucursalCliente=base64_decode($_POST['SucursalClienteInterno']);
			//Direccion
			$SQL_DirCliente=Seleccionar('uvw_Sap_tbl_Clientes_Sucursales','Direccion, Ciudad',"CodigoCliente='".$ClienteActividad."' and NombreSucursal='".$SucursalCliente."'");
			$row_DirCliente=sqlsrv_fetch_array($SQL_DirCliente);
			$DireccionActividad=$row_DirCliente['Direccion'];
			$CiudadActividad=$row_DirCliente['Ciudad'];
			//Contacto
			$SQL_ContCliente=Seleccionar('uvw_Sap_tbl_ClienteContactos','CodigoContacto',"CodigoCliente='".$ClienteActividad."'");
			$row_ContCliente=sqlsrv_fetch_array($SQL_ContCliente);
			$ContactoCliente=$row_ContCliente[0];
		}else{
			$ClienteActividad=$_POST['ClienteActividad'];
			$SucursalCliente=$_POST['SucursalCliente'];
			$DireccionActividad=$_POST['DireccionActividad'];
			$CiudadActividad=$_POST['NombreCiudad'];
			$ContactoCliente=$_POST['ContactoCliente'];
		}
		
		if(isset($_POST['chkTodoDia'])&&($_POST['chkTodoDia']==1)){
			$HoraInicio="00:00";
			$HoraFin="00:00";
			$chkTodoDia=1;
		}else{
			$HoraInicio=$_POST['HoraInicio'];
			$HoraFin=$_POST['HoraFin'];
			$chkTodoDia=0;
		}
		
		$ParamInsActividad=array(
			"NULL",
			"'".$_POST['OrdenServicioActividad']."'",
			"'".$_POST['TipoTarea']."'",
			"'".$_POST['TipoActividad']."'",
			"'".$_POST['AsuntoActividad']."'",
			"'".LSiqmlObs($_POST['TituloActividad'])."'",
			"'".$_POST['EmpleadoActividad']."'",
			"'".$_POST['EnRuta']."'",
			"'".$_POST['MotivoCierre']."'",
			"'".$ClienteActividad."'",
			"'".$ContactoCliente."'",
			"'".$_POST['TelefonoActividad']."'",
			"'".$_POST['CorreoActividad']."'",
			"'".$SucursalCliente."'",
			"'".$DireccionActividad."'",
			"'".$CiudadActividad."'",
			"'".$_POST['BarrioDireccionActividad']."'",
			"'".FormatoFecha($_POST['FechaInicio'],$HoraInicio)."'",
			"'".FormatoFecha($_POST['FechaFin'],$HoraFin)."'",
			"'".$chkTodoDia."'",
			"'".LSiqmlObs($_POST['Comentarios'])."'",
			"NULL",
			"'".$_POST['EstadoActividad']."'",
			"'".$_POST['TipoEstadoActividad']."'",
			"'".$_POST['OrdenServicioActividad']."'",
			"1",
			"'".$_SESSION['CodUser']."'",
			"1"
		);
		
		$SQL_InsActividad=EjecutarSP('sp_tbl_Actividades',$ParamInsActividad,27);		
		if($SQL_InsActividad){
			$row_NewIdActividad=sqlsrv_fetch_array($SQL_InsActividad);
			
			try{
				//Mover los anexos a la carpeta de archivos de SAP
				$j=0;
				while($j<$CantFiles){
					//Sacar la extension del archivo
					$Ext = end(explode('.',$DocFiles[$j]));
					//Sacar el nombre sin la extension
					$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
					//Reemplazar espacios
					$OnlyName=str_replace(" ","_",$OnlyName);
					$Prefijo = substr(uniqid(rand()),0,3);
					$OnlyName=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo;
					$NuevoNombre=$OnlyName.".".$Ext;

					$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$carp_anexos."/";
					if(!file_exists($dir_new)){
						mkdir($dir_new,0777, true);
					}
					if(file_exists($dir_new)){
						copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
						//move_uploaded_file($_FILES['FileArchivo']['tmp_name'],$dir_new.$NuevoNombre);
						copy($dir_new.$NuevoNombre,$RutaAttachSAP[0].$NuevoNombre);

						//Registrar archivo en la BD
						$ParamInsAnex=array(
							"'66'",
							"'".$row_NewIdActividad[0]."'",
							"'".$OnlyName."'",
							"'".$Ext."'",
							"1",
							"'".$_SESSION['CodUser']."'",
							"1"					
						);
						$SQL_InsAnex=EjecutarSP('sp_tbl_DocumentosSAP_Anexos',$ParamInsAnex,27);
						if(!$SQL_InsAnex){
							throw new Exception('Error al insertar los anexos.');			
							sqlsrv_close($conexion);
						}
					}
					$j++;
				}
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}	
			
			//Enviar datos al WebServices
			try{
				require_once("includes/conect_ws.php");
				$Parametros=array(
					'pIdActividad' => $row_NewIdActividad[0],
					'pLogin'=>$_SESSION['User']
				);
				$Client->CrearActividadPortal($Parametros);
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}			
			sqlsrv_close($conexion);
			if($_POST['d_LS']==1){
				header('Location:llamada_servicio.php?a='.base64_encode("OK_ActAdd")."&".base64_decode($_POST['return']));
			}else{
				header('Location:gestionar_actividades.php?a='.base64_encode("OK_ActAdd"));	
			}
		}else{
			throw new Exception('Error al crear la actividad');				
			sqlsrv_close($conexion);
			exit();
		}						
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==28){//Insertar nuevos archivos en calidad
	try{
		$i=0;//Archivos
		$j=0;//Cantidad de archivos
		//*** Carpeta de archivos ***
		$carp_archivos=ObtenerVariable("RutaArchivos");
		//*** Carpeta temporal ***
		$temp=ObtenerVariable("CarpetaTmp");
		$dir=$temp."/".$_SESSION['CodUser']."/";		   
		$route= opendir($dir);
		//$directorio = opendir("."); //ruta actual
		$DocFiles=array();
		while ($archivo = readdir($route)){ //obtenemos un archivo y luego otro sucesivamente
			if(($archivo == ".")||($archivo == "..")) continue;

			if (!is_dir($archivo)){//verificamos si es o no un directorio
				$DocFiles[$i]=$archivo;
				$i++;
				}
		}
		closedir($route);
		
		$CantFiles=$_POST['CantFiles'];
		
		while($j<$CantFiles){	
			//Sacar la extension del archivo
			$Ext = end(explode('.',$DocFiles[$j]));    
			//Sacar el nombre sin la extension
			$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
			$Prefijo = substr(uniqid(rand()),0,3);
			$NuevoNombre=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo.".".$Ext;

			//Insertar el registro en la BD
			$Cons_InsArchivo="EXEC sp_tbl_Archivos NULL,'".$_POST['CodigoCliente']."',NULL,'".$_POST['Categoria'.$j]."','".$_POST['Fecha'.$j]."','".LSiqmlObs($_POST['Comentarios'.$j])."','".$NuevoNombre."','".$_SESSION['CodUser']."',1";
			$SQL_InsArchivo=sqlsrv_query($conexion,$Cons_InsArchivo);

			if($SQL_InsArchivo){						
				//Mover archivo a la carpeta real
				$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$_POST['CodigoCliente']."/".$_POST['Categoria'.$j]."/";
				if(file_exists($dir_new)){
					copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
				}else{
					mkdir($dir_new,0777, true);
					copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
				}		

				$k++;
			}else{
				InsertarLog(1, 28, $Cons_InsArchivo);
				throw new Exception('Error insertando archivo');				
				sqlsrv_close($conexion);
				exit();
			}
			$j++;
		}
		sqlsrv_close($conexion);
		header('Location:gestionar_calidad.php?a='.base64_encode("OK_UpdFile"));					
	}catch (Exception $e) {
		InsertarLog(1, 28, $Cons_InsArchivo);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==29){//Actualizar actividad
	try{
		//*** Carpeta temporal ***
		$i=0;//Archivos
		$temp=ObtenerVariable("CarpetaTmp");
		$carp_archivos=ObtenerVariable("RutaArchivos");
		$carp_anexos="actividades";
		$NuevoNombre="";
		$RutaAttachSAP=ObtenerDirAttach();
		$dir=$temp."/".$_SESSION['CodUser']."/";		   
		$route= opendir($dir);
		//$directorio = opendir("."); //ruta actual
		$DocFiles=array();
		while ($archivo = readdir($route)){ //obtenemos un archivo y luego otro sucesivamente
			if(($archivo == ".")||($archivo == "..")) continue;

			if (!is_dir($archivo)){//verificamos si es o no un directorio
				$DocFiles[$i]=$archivo;
				$i++;
				}
		}
		closedir($route);
		$CantFiles=count($DocFiles);
		
		//Insertar el registro en la BD
		if($_POST['TipoTarea']=='Interna'){
			$ClienteActividad=base64_decode($_POST['ClienteActividadInterno']);
			$SucursalCliente=base64_decode($_POST['SucursalClienteInterno']);
			
			//Direccion
			$SQL_DirCliente=Seleccionar('uvw_Sap_tbl_Clientes_Sucursales','Direccion, Ciudad',"CodigoCliente='".$ClienteActividad."' and NombreSucursal='".$SucursalCliente."'");
			$row_DirCliente=sqlsrv_fetch_array($SQL_DirCliente);
			$DireccionActividad=$row_DirCliente['Direccion'];
			$CiudadActividad=$row_DirCliente['Ciudad'];
			
			//Contacto
			$SQL_ContCliente=Seleccionar('uvw_Sap_tbl_ClienteContactos','CodigoContacto',"CodigoCliente='".$ClienteActividad."'");
			$row_ContCliente=sqlsrv_fetch_array($SQL_ContCliente);
			$ContactoCliente=$row_ContCliente[0];
		}else{
			$ClienteActividad=$_POST['ClienteActividad'];
			$SucursalCliente=$_POST['SucursalCliente'];
			$DireccionActividad=$_POST['DireccionActividad'];
			$CiudadActividad=$_POST['NombreCiudad'];
			$ContactoCliente=$_POST['ContactoCliente'];
		}
		
		if(isset($_POST['chkTodoDia'])&&($_POST['chkTodoDia']==1)){
			$HoraInicio="00:00";
			$HoraFin="00:00";
			$TodoDia=1;
		}else{
			$HoraInicio=$_POST['HoraInicio'];
			$HoraFin=$_POST['HoraFin'];
			$TodoDia=0;
		}
		
		$Metodo=2;//Actualizar en el web services
		$Type=2;//Ejecutar actualizar en el SP
		if(base64_decode($_POST['IdActividadPortal'])==""){
			$Metodo=2;
			$Type=1;
		}
		
		//$dateInicial = date_create($_POST['FechaInicio']);
		//$FInicio=date_format($dateInicial, 'd/m/Y');
		
		//$dateFInal = date_create($_POST['FechaFin']);
		//$FFin=date_format($dateFInal, 'd/m/Y');
		
		$ParamUpdActividad=array(
			"'".base64_decode($_POST['IdActividadPortal'])."'",
			"'".base64_decode($_POST['ID'])."'",
			"'".$_POST['TipoTarea']."'",
			"'".$_POST['TipoActividad']."'",
			"'".$_POST['AsuntoActividad']."'",
			"'".LSiqmlObs($_POST['TituloActividad'])."'",
			"'".$_POST['EmpleadoActividad']."'",
			"'".$_POST['EnRuta']."'",
			"'".$_POST['MotivoCierre']."'",
			"'".$ClienteActividad."'",
			"'".$ContactoCliente."'",
			"'".$_POST['TelefonoActividad']."'",
			"'".$_POST['CorreoActividad']."'",
			"'".$SucursalCliente."'",
			"'".$DireccionActividad."'",
			"'".$CiudadActividad."'",
			"'".$_POST['BarrioDireccionActividad']."'",
			"'".FormatoFecha($_POST['FechaInicio'],$HoraInicio)."'",
			"'".FormatoFecha($_POST['FechaFin'],$HoraFin)."'",
			"'".$TodoDia."'",
			"'".LSiqmlObs($_POST['Comentarios'])."'",
			"'".LSiqmlObs($_POST['NotasActividad'])."'",
			"'".$_POST['EstadoActividad']."'",
			"'".$_POST['TipoEstadoActividad']."'",
			"'".$_POST['OrdenServicioActividad']."'",
			"$Metodo",
			"'".$_SESSION['CodUser']."'",
			"$Type"		
		);
		$SQL_UpdActividad=EjecutarSP('sp_tbl_Actividades',$ParamUpdActividad,29);
		if($SQL_UpdActividad){
			if(base64_decode($_POST['IdActividadPortal'])==""){
				$row_NewIdActividad=sqlsrv_fetch_array($SQL_UpdActividad);
				$IdActividad=$row_NewIdActividad[0];
			}else{
				$IdActividad=base64_decode($_POST['IdActividadPortal']);
			}
						
			try{
				//Mover los anexos a la carpeta de archivos de SAP
				$j=0;
				while($j<$CantFiles){
					//Sacar la extension del archivo
					$Ext = end(explode('.',$DocFiles[$j]));
					//Sacar el nombre sin la extension
					$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
					//Reemplazar espacios
					$OnlyName=str_replace(" ","_",$OnlyName);
					$Prefijo = substr(uniqid(rand()),0,3);
					$OnlyName=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo;
					$NuevoNombre=$OnlyName.".".$Ext;

					$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$carp_anexos."/";
					if(!file_exists($dir_new)){
						mkdir($dir_new,0777, true);
					}
					if(file_exists($dir_new)){
						copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
						//move_uploaded_file($_FILES['FileArchivo']['tmp_name'],$dir_new.$NuevoNombre);
						copy($dir_new.$NuevoNombre,$RutaAttachSAP[0].$NuevoNombre);

						//Registrar archivo en la BD
						$ParamInsAnex=array(
							"'66'",
							"'".$IdActividad."'",
							"'".$OnlyName."'",
							"'".$Ext."'",
							"1",
							"'".$_SESSION['CodUser']."'",
							"1"					
						);
						$SQL_InsAnex=EjecutarSP('sp_tbl_DocumentosSAP_Anexos',$ParamInsAnex,29);
						if(!$SQL_InsAnex){
							throw new Exception('Error al insertar los anexos.');			
							sqlsrv_close($conexion);
						}
					}
					$j++;
				}
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}	
			
			//Enviar datos al WebServices
			try{
				require_once("includes/conect_ws.php");
				$Parametros=array(
						'pIdActividad' => $IdActividad,
						'pLogin'=>$_SESSION['User']
					);
				$Client->ActualizarActividadPortal($Parametros);
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}
			sqlsrv_close($conexion);
			if($_POST['d_LS']==1){
				header('Location:llamada_servicio.php?a='.base64_encode("OK_UpdAdd")."&".base64_decode($_POST['return_param']));
			}else{
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_UpdAdd"));	
			}
			
		}else{
			throw new Exception('Error al actualizar la actividad');				
			sqlsrv_close($conexion);
			exit();
		}						
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

/*if($P==30){//Insertar notas en la actividad (deprecated)
	try{
		
		//Insertar el registro en la BD
		$Cons_InsNotaActividad="EXEC sp_tbl_Actividades '".base64_decode($_POST['ID'])."',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'".LSiqmlObs($_POST['NotasActividad'])."',NULL,NULL,NULL,NULL,3";
		$SQL_InsNotaActividad=sqlsrv_query($conexion,$Cons_InsNotaActividad);
		if($SQL_InsNotaActividad){
			InsertarLog(2, 30, $Cons_InsNotaActividad);
			sqlsrv_close($conexion);
			header('Location:actividad_edit.php?a='.base64_encode("OK_InsNotAct")."&".base64_decode($_POST['return']));
		}else{
			InsertarLog(1, 30, $Cons_InsNotaActividad);
			throw new Exception('Error al insertar las notas de la actividad');				
			sqlsrv_close($conexion);
			exit();
		}						
	}catch (Exception $e) {
		InsertarLog(1, 30, $Cons_InsNotaActividad);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}*/

if($P==31){//Actualizar el archivo de acuerdo de confidencialidad
	try{
		$Nombre_archivo="contrato_confidencialidad.txt";
		$Archivo=fopen($Nombre_archivo,"w+");
		if(fwrite($Archivo, $_POST['TextAcuerdo'])){
			fclose($Archivo);
			sqlsrv_close($conexion);
			header('Location:parametros_generales.php?t='.$_POST['t'].'&result='.base64_encode('MsgOkAcuerdoOK'));	
		}else{
			fclose($Archivo);
			InsertarLog(1, 31, "Error al insertar el Acuerdo de confidencialidad.");
			throw new Exception('Error al insertar el Acuerdo de confidencialidad.');			
			sqlsrv_close($conexion);
			header('Location:parametros_generales.php?t='.$_POST['t'].'&result='.base64_encode('MsgOkAcuerdoER'));	
			}
	}catch (Exception $e) {
		InsertarLog(1, 31, "Error al insertar el Acuerdo de confidencialidad.");
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==32){//Insertar nueva llamada de servicio
	try{
		//*** Carpeta temporal ***
		$i=0;//Archivos
		$temp=ObtenerVariable("CarpetaTmp");
		$carp_archivos=ObtenerVariable("RutaArchivos");
		$carp_anexos="llamadas";
		$NuevoNombre="";
		$RutaAttachSAP=ObtenerDirAttach();
		$dir=$temp."/".$_SESSION['CodUser']."/";		   
		$route= opendir($dir);
		//$directorio = opendir("."); //ruta actual
		$DocFiles=array();
		while ($archivo = readdir($route)){ //obtenemos un archivo y luego otro sucesivamente
			if(($archivo == ".")||($archivo == "..")) continue;

			if (!is_dir($archivo)){//verificamos si es o no un directorio
				$DocFiles[$i]=$archivo;
				$i++;
				}
		}
		closedir($route);
		$CantFiles=count($DocFiles);
		
		//Insertar el registro en la BD
		if($_POST['swTipo']==1){
			$ClienteLlamada=base64_decode($_POST['ClienteLlamadaInterno']);
			$SucursalLlamada=base64_decode($_POST['SucursalClienteInterno']);
			//Direccion
			$SQL_DirLlamada=Seleccionar('uvw_Sap_tbl_Clientes_Sucursales','Direccion, Ciudad',"CodigoCliente=''".$ClienteLlamada."'' and NombreSucursal=''".$SucursalLlamada."''");
			$row_DirLlamada=sqlsrv_fetch_array($SQL_DirLlamada);
			$DireccionLlamada=$row_DirLlamada['Direccion'];
			$CiudadLlamada=$row_DirCliente['Ciudad'];
			
			//Contacto
			$SQL_ContLlamada=Seleccionar('uvw_Sap_tbl_ClienteContactos','CodigoContacto',"CodigoCliente=''".$ClienteLlamada."''");
			$row_ContLlamada=sqlsrv_fetch_array($SQL_ContLlamada);
			$ContactoCliente=$row_ContLlamada[0];
		}else{
			$ClienteLlamada=$_POST['ClienteLlamada'];
			$SucursalLlamada=$_POST['SucursalCliente'];
			$DireccionLlamada=$_POST['DireccionLlamada'];
			$CiudadLlamada=$_POST['CiudadLlamada'];
			$ContactoCliente=$_POST['ContactoCliente'];
		}
		
		$ParamInsLlamada=array(
			"NULL",
			"NULL",
			"NULL",
			"'".$_POST['TipoTarea']."'",
			"'".$_POST['AsuntoLlamada']."'",
			"'".$_POST['Series']."'",
			"'".$_POST['EstadoLlamada']."'",
			"'".$_POST['TipoLlamada']."'",
			"'".$_POST['TipoProblema']."'",
			"'".$_POST['SubTipoProblema']."'",
			"'".$ClienteLlamada."'",
			"'".$ContactoCliente."'",
			"'".$_POST['TelefonoLlamada']."'",
			"'".$_POST['CorreoLlamada']."'",
			"'".$_POST['ArticuloLlamada']."'",
			"'".$SucursalLlamada."'",
			"'".$DireccionLlamada."'",
			"'".$CiudadLlamada."'",
			"'".$_POST['BarrioDireccionLlamada']."'",
			"'".$_POST['EmpleadoLlamada']."'",
			"'".LSiqmlObs($_POST['ComentarioLlamada'])."'",
			"'".LSiqmlObs($_POST['ResolucionLlamada'])."'",
			"'".$_POST['FechaCierre']." ".$_POST['HoraCierre']."'",
			"'".$_POST['TipoResolucion']."'",
			"'".$_POST['EstadoServicio']."'",
			"'".$_POST['CanceladoPor']."'",
			"'".$_POST['CategoriaOrigen']."'",
			"'".$_POST['Indisponibilidad']."'",
			"'".$_POST['Responsabilidad']."'",
			"'".$_POST['ColaLlamada']."'",
			"1",
			"'".$_SESSION['CodUser']."'",
			"'".$_SESSION['CodUser']."'",
			"1"
		);
		$SQL_InsLlamada=EjecutarSP('sp_tbl_LlamadaServicios',$ParamInsLlamada,32);		
		if($SQL_InsLlamada){
			$row_NewIdLlamada=sqlsrv_fetch_array($SQL_InsLlamada);
			
			try{
				//Mover los anexos a la carpeta de archivos de SAP
				$j=0;
				while($j<$CantFiles){
					//Sacar la extension del archivo
					$Ext = end(explode('.',$DocFiles[$j]));
					//Sacar el nombre sin la extension
					$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
					//Reemplazar espacios
					$OnlyName=str_replace(" ","_",$OnlyName);
					$Prefijo = substr(uniqid(rand()),0,3);
					$OnlyName=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo;
					$NuevoNombre=$OnlyName.".".$Ext;

					$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$carp_anexos."/";
					if(!file_exists($dir_new)){
						mkdir($dir_new,0777, true);
					}
					if(file_exists($dir_new)){
						copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
						//move_uploaded_file($_FILES['FileArchivo']['tmp_name'],$dir_new.$NuevoNombre);
						copy($dir_new.$NuevoNombre,$RutaAttachSAP[0].$NuevoNombre);

						//Registrar archivo en la BD
						$ParamInsAnex=array(
							"'191'",
							"'".$row_NewIdLlamada[0]."'",
							"'".$OnlyName."'",
							"'".$Ext."'",
							"1",
							"'".$_SESSION['CodUser']."'",
							"1"					
						);
						$SQL_InsAnex=EjecutarSP('sp_tbl_DocumentosSAP_Anexos',$ParamInsAnex,32);
						if(!$SQL_InsAnex){
							throw new Exception('Error al insertar los anexos.');			
							sqlsrv_close($conexion);
						}

					}
					$j++;
				}
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}			
			
			//Enviar datos al WebServices
			try{
				require_once("includes/conect_ws.php");
				$Parametros=array(
					'pIdLlamada' => $row_NewIdLlamada[0],
					'pLogin'=>$_SESSION['User']
				);
				$Client->InsertarLlamadaServicioPortal($Parametros);
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}
			sqlsrv_close($conexion);
			header('Location:gestionar_llamadas_servicios.php?a='.base64_encode("OK_LlamAdd"));	
		}else{
			throw new Exception('Error al crear la llamada de servicio');				
			sqlsrv_close($conexion);
			exit();
		}						
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==33){//Actualizar llamada de servicio
	try{
		///*** Carpeta temporal ***
		$i=0;//Archivos
		$temp=ObtenerVariable("CarpetaTmp");
		$carp_archivos=ObtenerVariable("RutaArchivos");
		$carp_anexos="llamadas";
		$NuevoNombre="";
		$RutaAttachSAP=ObtenerDirAttach();
		$dir=$temp."/".$_SESSION['CodUser']."/";		   
		$route= opendir($dir);
		//$directorio = opendir("."); //ruta actual
		$DocFiles=array();
		while ($archivo = readdir($route)){ //obtenemos un archivo y luego otro sucesivamente
			if(($archivo == ".")||($archivo == "..")) continue;

			if (!is_dir($archivo)){//verificamos si es o no un directorio
				$DocFiles[$i]=$archivo;
				$i++;
				}
		}
		closedir($route);
		$CantFiles=count($DocFiles);
		
		//Insertar el registro en la BD
		if($_POST['swTipo']==1){
			$ClienteLlamada=base64_decode($_POST['ClienteLlamadaInterno']);
			$SucursalLlamada=base64_decode($_POST['SucursalClienteInterno']);
			//Direccion
			$SQL_DirLlamada=Seleccionar('uvw_Sap_tbl_Clientes_Sucursales','Direccion, Ciudad',"CodigoCliente=''".$ClienteLlamada."'' and NombreSucursal=''".$SucursalLlamada."''");
			$row_DirLlamada=sqlsrv_fetch_array($SQL_DirLlamada);
			$DireccionLlamada=$row_DirLlamada['Direccion'];
			$CiudadLlamada=$row_DirCliente['Ciudad'];
			
			//Contacto
			$SQL_ContLlamada=Seleccionar('uvw_Sap_tbl_ClienteContactos','CodigoContacto',"CodigoCliente=''".$ClienteLlamada."''");
			$row_ContLlamada=sqlsrv_fetch_array($SQL_ContLlamada);
			$ContactoCliente=$row_ContLlamada[0];
		}else{
			$ClienteLlamada=$_POST['ClienteLlamada'];
			$SucursalLlamada=$_POST['SucursalCliente'];
			$DireccionLlamada=$_POST['DireccionLlamada'];
			$CiudadLlamada=$_POST['CiudadLlamada'];
			$ContactoCliente=$_POST['ContactoCliente'];
		}
		$Metodo=2;//Actualizar en el web services
		$Type=2;//Ejecutar actualizar en el SP
		if(base64_decode($_POST['IdLlamadaPortal'])==""){
			$Metodo=2;
			$Type=1;
		}
		
		$ParamUpdLlamada=array(
			"'".base64_decode($_POST['IdLlamadaPortal'])."'",
			"'".base64_decode($_POST['DocEntry'])."'",
			"'".base64_decode($_POST['DocNum'])."'",
			"'".$_POST['TipoTarea']."'",
			"'".$_POST['AsuntoLlamada']."'",
			"'".$_POST['Series']."'",
			"'".$_POST['EstadoLlamada']."'",
			"'".$_POST['TipoLlamada']."'",
			"'".$_POST['TipoProblema']."'",
			"'".$_POST['SubTipoProblema']."'",
			"'".$ClienteLlamada."'",
			"'".$ContactoCliente."'",
			"'".$_POST['TelefonoLlamada']."'",
			"'".$_POST['CorreoLlamada']."'",
			"'".$_POST['ArticuloLlamada']."'",
			"'".$SucursalLlamada."'",
			"'".$DireccionLlamada."'",
			"'".$CiudadLlamada."'",
			"'".$_POST['BarrioDireccionLlamada']."'",
			"'".$_POST['EmpleadoLlamada']."'",
			"'".LSiqmlObs($_POST['ComentarioLlamada'])."'",
			"'".LSiqmlObs($_POST['ResolucionLlamada'])."'",
			"'".$_POST['FechaCierre']." ".$_POST['HoraCierre']."'",
			"'".$_POST['TipoResolucion']."'",
			"'".$_POST['EstadoServicio']."'",
			"'".$_POST['CanceladoPor']."'",
			"'".$_POST['CategoriaOrigen']."'",
			"'".$_POST['Indisponibilidad']."'",
			"'".$_POST['Responsabilidad']."'",
			"'".$_POST['ColaLlamada']."'",
			"$Metodo",
			"'".$_SESSION['CodUser']."'",
			"'".$_SESSION['CodUser']."'",
			"$Type"
		);		
		$SQL_UpdLlamada=EjecutarSP('sp_tbl_LlamadaServicios',$ParamUpdLlamada,33);
		if($SQL_UpdLlamada){
			if(base64_decode($_POST['IdLlamadaPortal'])==""){
				$row_NewIdLlamada=sqlsrv_fetch_array($SQL_UpdLlamada);
				$IdLlamada=$row_NewIdLlamada[0];
			}else{
				$IdLlamada=base64_decode($_POST['IdLlamadaPortal']);
			}
			
			try{
				//Mover los anexos a la carpeta de archivos de SAP
				$j=0;
				while($j<$CantFiles){
					//Sacar la extension del archivo
					$Ext = end(explode('.',$DocFiles[$j]));
					//Sacar el nombre sin la extension
					$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
					//Reemplazar espacios
					$OnlyName=str_replace(" ","_",$OnlyName);
					$Prefijo = substr(uniqid(rand()),0,3);
					$OnlyName=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo;
					$NuevoNombre=$OnlyName.".".$Ext;

					$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$carp_anexos."/";
					if(!file_exists($dir_new)){
						mkdir($dir_new,0777, true);
					}
					if(file_exists($dir_new)){
						copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
						//move_uploaded_file($_FILES['FileArchivo']['tmp_name'],$dir_new.$NuevoNombre);
						copy($dir_new.$NuevoNombre,$RutaAttachSAP[0].$NuevoNombre);

						//Registrar archivo en la BD
						$ParamInsAnex=array(
							"'191'",
							"'".$IdLlamada."'",
							"'".$OnlyName."'",
							"'".$Ext."'",
							"1",
							"'".$_SESSION['CodUser']."'",
							"1"					
						);
						$SQL_InsAnex=EjecutarSP('sp_tbl_DocumentosSAP_Anexos',$ParamInsAnex,33);
						if(!$SQL_InsAnex){
							throw new Exception('Error al insertar los anexos.');			
							sqlsrv_close($conexion);
						}
					}
					$j++;
				}
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}		
			
			//Enviar datos al WebServices
			try{
				require_once("includes/conect_ws.php");
				$Parametros=array(
					'pIdLlamada' => $IdLlamada,
					'pLogin'=>$_SESSION['User']
				);
				$Client->InsertarLlamadaServicioPortal($Parametros);
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}
			sqlsrv_close($conexion);
			header('Location:gestionar_llamadas_servicios.php?a='.base64_encode("OK_UpdAdd"));	
		}else{
			throw new Exception('Error al actualizar la llamada de servicio');				
			sqlsrv_close($conexion);
			exit();
		}						
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==34){//Actualizar para cierre de llamada
	try{
		//*** Carpeta temporal ***
		$i=0;//Archivos
		$temp=ObtenerVariable("CarpetaTmp");
		$carp_archivos=ObtenerVariable("RutaArchivos");
		$carp_anexos="llamadas";
		$NuevoNombre="";
		$RutaAttachSAP=ObtenerDirAttach();
		$dir=$temp."/".$_SESSION['CodUser']."/";		   
		$route= opendir($dir);
		//$directorio = opendir("."); //ruta actual
		$DocFiles=array();
		while ($archivo = readdir($route)){ //obtenemos un archivo y luego otro sucesivamente
			if(($archivo == ".")||($archivo == "..")) continue;

			if (!is_dir($archivo)){//verificamos si es o no un directorio
				$DocFiles[$i]=$archivo;
				$i++;
				}
		}
		closedir($route);
		$CantFiles=count($DocFiles);
		
		//Insertar el registro en la BD
		if($_POST['swTipo']==1){
			$ClienteLlamada=base64_decode($_POST['ClienteLlamadaInterno']);
			$SucursalLlamada=base64_decode($_POST['SucursalClienteInterno']);
			//Direccion
			$SQL_DirLlamada=Seleccionar('uvw_Sap_tbl_Clientes_Sucursales','Direccion, Ciudad',"CodigoCliente=''".$ClienteLlamada."'' and NombreSucursal=''".$SucursalLlamada."''");
			$row_DirLlamada=sqlsrv_fetch_array($SQL_DirLlamada);
			$DireccionLlamada=$row_DirLlamada['Direccion'];
			$CiudadLlamada=$row_DirCliente['Ciudad'];
			
			//Contacto
			$SQL_ContLlamada=Seleccionar('uvw_Sap_tbl_ClienteContactos','CodigoContacto',"CodigoCliente=''".$ClienteLlamada."''");
			$row_ContLlamada=sqlsrv_fetch_array($SQL_ContLlamada);
			$ContactoCliente=$row_ContLlamada[0];
		}else{
			$ClienteLlamada=$_POST['ClienteLlamada'];
			$SucursalLlamada=$_POST['SucursalCliente'];
			$DireccionLlamada=$_POST['DireccionLlamada'];
			$CiudadLlamada=$_POST['CiudadLlamada'];
			$ContactoCliente=$_POST['ContactoCliente'];
		}
		$Metodo=3;//Cerrar en el web services
		$Type=3;//Ejecutar actualizar en el SP
		if(base64_decode($_POST['IdLlamadaPortal'])==""){
			$Metodo=3;
			$Type=1;
		}
		
		//Insertar el registro en la BD	
		$ParamUpdCierreLlamada=array(
			"'".base64_decode($_POST['IdLlamadaPortal'])."'",
			"'".base64_decode($_POST['DocEntry'])."'",
			"'".base64_decode($_POST['DocNum'])."'",
			"'".$_POST['TipoTarea']."'",
			"'".$_POST['AsuntoLlamada']."'",
			"'".$_POST['Series']."'",
			"'".$_POST['EstadoLlamada']."'",
			"'".$_POST['TipoLlamada']."'",
			"'".$_POST['TipoProblema']."'",
			"'".$_POST['SubTipoProblema']."'",
			"'".$ClienteLlamada."'",
			"'".$ContactoCliente."'",
			"'".$_POST['TelefonoLlamada']."'",
			"'".$_POST['CorreoLlamada']."'",
			"'".$_POST['ArticuloLlamada']."'",
			"'".$SucursalLlamada."'",
			"'".$DireccionLlamada."'",
			"'".$CiudadLlamada."'",
			"'".$_POST['BarrioDireccionLlamada']."'",
			"'".$_POST['EmpleadoLlamada']."'",
			"'".$_POST['ComentarioLlamada']."'",
			"'".$_POST['ResolucionLlamada']."'",
			"'".$_POST['FechaCierre']." ".$_POST['HoraCierre']."'",
			"'".$_POST['TipoResolucion']."'",
			"'".$_POST['EstadoServicio']."'",
			"'".$_POST['CanceladoPor']."'",
			"'".$_POST['CategoriaOrigen']."'",
			"'".$_POST['Indisponibilidad']."'",
			"'".$_POST['Responsabilidad']."'",
			"'".$_POST['ColaLlamada']."'",
			"$Metodo",
			"'".$_SESSION['CodUser']."'",
			"'".$_SESSION['CodUser']."'",
			"$Type"
		);
		$SQL_UpdCierreLlamada=EjecutarSP('sp_tbl_LlamadaServicios',$ParamUpdCierreLlamada,34);
		if($SQL_UpdCierreLlamada){
			if(base64_decode($_POST['IdLlamadaPortal'])==""){
				$row_NewIdLlamada=sqlsrv_fetch_array($SQL_UpdCierreLlamada);
				$IdLlamada=$row_NewIdLlamada[0];
			}else{
				$IdLlamada=base64_decode($_POST['IdLlamadaPortal']);
			}			
			
			try{
				//Mover los anexos a la carpeta de archivos de SAP
				$j=0;
				while($j<$CantFiles){
					//Sacar la extension del archivo
					$Ext = end(explode('.',$DocFiles[$j]));
					//Sacar el nombre sin la extension
					$OnlyName = substr($DocFiles[$j],0,strlen($DocFiles[$j])-(strlen($Ext)+1));
					//Reemplazar espacios
					$OnlyName=str_replace(" ","_",$OnlyName);
					$Prefijo = substr(uniqid(rand()),0,3);
					$OnlyName=LSiqmlObs($OnlyName)."_".date('Ymd').$Prefijo;
					$NuevoNombre=$OnlyName.".".$Ext;

					$dir_new=$_SESSION['BD']."/".$carp_archivos."/".$carp_anexos."/";
					if(!file_exists($dir_new)){
						mkdir($dir_new,0777, true);
					}
					if(file_exists($dir_new)){
						copy($dir.$DocFiles[$j],$dir_new.$NuevoNombre);
						//move_uploaded_file($_FILES['FileArchivo']['tmp_name'],$dir_new.$NuevoNombre);
						copy($dir_new.$NuevoNombre,$RutaAttachSAP[0].$NuevoNombre);

						//Registrar archivo en la BD
						$ParamInsAnex=array(
							"'191'",
							"'".$IdLlamada."'",
							"'".$OnlyName."'",
							"'".$Ext."'",
							"1",
							"'".$_SESSION['CodUser']."'",
							"1"					
						);
						$SQL_InsAnex=EjecutarSP('sp_tbl_DocumentosSAP_Anexos',$ParamInsAnex,34);
						if(!$SQL_InsAnex){
							throw new Exception('Error al insertar los anexos.');			
							sqlsrv_close($conexion);
						}
					}
					$j++;
				}
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}		
			
			//Enviar datos al WebServices
			try{
				require_once("includes/conect_ws.php");
				$Parametros=array(
					'pIdLlamada' => $IdLlamada,
					'pLogin'=>$_SESSION['User']
				);
				$Client->CerrarLlamadaServicioPortal($Parametros);
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}
			sqlsrv_close($conexion);
			header('Location:gestionar_llamadas_servicios.php?a='.base64_encode("OK_ClosLlam"));	
		}else{
			throw new Exception('Error cerrar la llamada de servicio');				
			sqlsrv_close($conexion);
			exit();
		}						
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==35){//Insertar articulos en el carrito
	try{
		if(isset($_POST['doctype'])){
			$type=$_POST['doctype'];
			$Item=$_POST['item'];
			$WhsCode=$_POST['whscode'];
			$CardCode=$_POST['cardcode'];
		}else{
			$type=$_GET['doctype'];
			$Item=$_GET['item'];
			$WhsCode=$_GET['whscode'];
			$CardCode=$_GET['cardcode'];
		}
		if($type==1){//Orden de venta
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$CardCode."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_OrdenVentaDetalleCarritoInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$WhsCode."'",
					"'".$CardCode."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_OrdenVentaDetalleCarritoInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=1');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
		if($type==2){//Orden de venta editar
			if(isset($_POST['id'])){
				$id=$_POST['id'];
				$evento=$_POST['evento'];
			}else{
				$id=$_GET['id'];
				$evento=$_GET['evento'];
			}
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$id."'",
				"'".$evento."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_OrdenVentaDetalleInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$id."'",
					"'".$evento."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_OrdenVentaDetalleInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=2');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
		if($type==3){//Oferta de venta
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$CardCode."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_OfertaVentaDetalleCarritoInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$WhsCode."'",
					"'".$CardCode."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_OfertaVentaDetalleCarritoInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=3');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
		if($type==4){//Oferta de venta editar
			if(isset($_POST['id'])){
				$id=$_POST['id'];
				$evento=$_POST['evento'];
			}else{
				$id=$_GET['id'];
				$evento=$_GET['evento'];
			}
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$id."'",
				"'".$evento."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_OfertaVentaDetalleInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$id."'",
					"'".$evento."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_OfertaVentaDetalleInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=4');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
		if($type==5){//Entrega de venta
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$CardCode."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_EntregaVentaDetalleCarritoInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$WhsCode."'",
					"'".$CardCode."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_EntregaVentaDetalleCarritoInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=5');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
		if($type==6){//Entrega de venta editar
			if(isset($_POST['id'])){
				$id=$_POST['id'];
				$evento=$_POST['evento'];
			}else{
				$id=$_GET['id'];
				$evento=$_GET['evento'];
			}
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$id."'",
				"'".$evento."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_EntregaVentaDetalleInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$id."'",
					"'".$evento."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_EntregaVentaDetalleInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=6');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
		if($type==7){//Solicitud de salida
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$CardCode."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_SolicitudSalidaDetalleCarritoInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$WhsCode."'",
					"'".$CardCode."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_SolicitudSalidaDetalleCarritoInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=7');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
		if($type==8){//Solicitud de salida editar
			if(isset($_POST['id'])){
				$id=$_POST['id'];
				$evento=$_POST['evento'];
			}else{
				$id=$_GET['id'];
				$evento=$_GET['evento'];
			}
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$id."'",
				"'".$evento."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_SolicitudSalidaDetalleInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$id."'",
					"'".$evento."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_SolicitudSalidaDetalleInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=8');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
		if($type==9){//Salida de inventario
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$CardCode."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_SalidaInventarioDetalleCarritoInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$WhsCode."'",
					"'".$CardCode."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_SalidaInventarioDetalleCarritoInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=9');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
		if($type==10){//Salida de inventario editar
			if(isset($_POST['id'])){
				$id=$_POST['id'];
				$evento=$_POST['evento'];
			}else{
				$id=$_GET['id'];
				$evento=$_GET['evento'];
			}
			//Insertar el registro en la BD
			$ParametrosInsert=array(
				"'".$Item."'",
				"'".$WhsCode."'",
				"'".$id."'",
				"'".$evento."'",
				"'".$_SESSION['CodUser']."'"
			);
			$SQL_Insert=EjecutarSP('sp_tbl_SalidaInventarioDetalleInsert',$ParametrosInsert,35);
			if($SQL_Insert){
				$ParametrosCount=array(
					"'".$id."'",
					"'".$evento."'"
				);
				$SQL_ConCount=EjecutarSP('sp_tbl_SalidaInventarioDetalleInsert_Count',$ParametrosCount,35);
				$row_ConCount=sqlsrv_fetch_array($SQL_ConCount);
				sqlsrv_close($conexion);
				echo $row_ConCount['Cuenta'];
			}else{
				throw new Exception('Error al agregar articulos DocType=10');				
				sqlsrv_close($conexion);
				exit();
			}		
		}
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==36){//Actualizar los datos del detalle de los documentos de SAP
	try{
		if($_GET['doctype']==1){//Orden de venta
			if($_GET['type']==1){//Actualiza campos en carrito
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".base64_decode($_GET['value'])."'",
					"'".$_GET['line']."'",
					"'".$_GET['cardcode']."'",
					"'".$_GET['whscode']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_OrdenVentaDetalleCarritoUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
			if($_GET['type']==2){//Actualiza campos en detalle editando
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".base64_decode($_GET['value'])."'",
					"'".$_GET['line']."'",
					"'".$_GET['id']."'",
					"'".$_GET['evento']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_OrdenVentaDetalleUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
		}
		if($_GET['doctype']==2){//Oferta de venta
			if($_GET['type']==1){//Actualiza campos en carrito
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".$_GET['value']."'",
					"'".$_GET['line']."'",
					"'".$_GET['cardcode']."'",
					"'".$_GET['whscode']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_OfertaVentaDetalleCarritoUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
			if($_GET['type']==2){//Actualiza campos en detalle editando
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".$_GET['value']."'",
					"'".$_GET['line']."'",
					"'".$_GET['id']."'",
					"'".$_GET['evento']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_OfertaVentaDetalleUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
		}
		if($_GET['doctype']==3){//Entrega de venta
			if($_GET['type']==1){//Actualiza campos en carrito
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".base64_decode($_GET['value'])."'",
					"'".$_GET['line']."'",
					"'".$_GET['cardcode']."'",
					"'".$_GET['whscode']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_EntregaVentaDetalleCarritoUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
			if($_GET['type']==2){//Actualiza campos en detalle editando
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".base64_decode($_GET['value'])."'",
					"'".$_GET['line']."'",
					"'".$_GET['id']."'",
					"'".$_GET['evento']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_EntregaVentaDetalleUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
		}
		if($_GET['doctype']==4){//Solicitud de salida
			if($_GET['type']==1){//Actualiza campos en carrito
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".base64_decode($_GET['value'])."'",
					"'".$_GET['line']."'",
					"'".$_GET['cardcode']."'",
					"'".$_GET['whscode']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_SolicitudSalidaDetalleCarritoUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
			if($_GET['type']==2){//Actualiza campos en detalle editando
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".base64_decode($_GET['value'])."'",
					"'".$_GET['line']."'",
					"'".$_GET['id']."'",
					"'".$_GET['evento']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_SolicitudSalidaDetalleUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
		}
		if($_GET['doctype']==5){//Salida de inventario
			if($_GET['type']==1){//Actualiza campos en carrito
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".base64_decode($_GET['value'])."'",
					"'".$_GET['line']."'",
					"'".$_GET['cardcode']."'",
					"'".$_GET['whscode']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_SalidaInventarioDetalleCarritoUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
			if($_GET['type']==2){//Actualiza campos en detalle editando
				$Parametros=array(
					"'".$_GET['name']."'",
					"'".base64_decode($_GET['value'])."'",
					"'".$_GET['line']."'",
					"'".$_GET['id']."'",
					"'".$_GET['evento']."'",
					"'".$_SESSION['CodUser']."'"
				);
				$SQL=EjecutarSP('sp_tbl_SalidaInventarioDetalleUpdCampos',$Parametros,36);
				if($SQL){
					sqlsrv_close($conexion);
					echo date('h:i:s a');
				}else{
					throw new Exception('Error al actualizar la cantidad');
					sqlsrv_close($conexion);
					exit();
				}
			}
		}
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==37){//Grabar Orden de venta
	try{
		$Cons_CabeceraOrdenVenta="EXEC sp_tbl_OrdenVenta NULL, NULL, NULL, NULL, '1','".$_POST['DocDate']."','".$_POST['DocDueDate']."','".$_POST['TaxDate']."','".$_POST['CardCode']."','".$_POST['ContactoCliente']."',NULL,'".$_POST['OrdenServicioCliente']."','".$_POST['Referencia']."','".$_SESSION['CodigoSAP']."','".LSiqmlObs($_POST['Comentarios'])."','".str_replace(',','',$_POST['SubTotal'])."','".str_replace(',','',$_POST['Descuentos'])."',NULL,'".str_replace(',','',$_POST['Impuestos'])."','".str_replace(',','',$_POST['TotalOrden'])."','".$_POST['NombreDireccionFacturacion']."','".$_POST['DireccionFacturacion']."','".$_POST['NombreDireccionDestino']."','".$_POST['DireccionDestino']."','".$_POST['CondicionPago']."','".$_POST['CentroCosto']."','".$_POST['UnidadNegocio']."',NULL,'".$_SESSION['CodUser']."','1'";
		//echo $Cons_CabeceraSolPed;
		$SQL_CabeceraOrdenVenta=sqlsrv_query($conexion,$Cons_CabeceraOrdenVenta);
		if($SQL_CabeceraOrdenVenta){
			InsertarLog(2, 37, $Cons_CabeceraOrdenVenta);
			$row_CabeceraOrdenVenta=sqlsrv_fetch_array($SQL_CabeceraOrdenVenta);
			//echo $row_CabeceraSolPed[0];
			$Cons_DetalleOrdenVenta="EXEC sp_tbl_OrdenVentaDetalle '".$row_CabeceraOrdenVenta[0]."', '".$_POST['CardCode']."', '".$_SESSION['CodUser']."'";
			//echo $Cons_DetalleSolPed;
			$SQL_DetalleOrdenVenta=sqlsrv_query($conexion,$Cons_DetalleOrdenVenta);
			if($SQL_DetalleOrdenVenta){
				sqlsrv_close($conexion);
				if($_POST['d_LS']==1){
					header('Location:llamada_edit.php?a='.base64_encode("OK_OVenAdd")."&".base64_decode($_POST['return']));
				}else{
					header('Location:orden_venta_add.php?data='.base64_encode($row_CabeceraOrdenVenta[0]));
				}				
			}else{
				InsertarLog(1, 37, $Cons_DetalleOrdenVenta);
				throw new Exception('Ha ocurrido un error al insertar las lineas de la orden de venta');
				sqlsrv_close($conexion);
				exit();
			}
		}else{
			InsertarLog(1, 37, $Cons_CabeceraOrdenVenta);
			throw new Exception('Ha ocurrido un error al crear la orden de venta');
			sqlsrv_close($conexion);
			exit();
		}
	}catch (Exception $e){
		InsertarLog(1, 37, $Cons_CabeceraOrdenVenta);
		InsertarLog(1, 37, $Cons_DetalleOrdenVenta);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==38){//Crear socios de negocios
	try{
		$Cons_SN="EXEC sp_tbl_SociosNegocios '".$_POST['CardCode']."','".$_POST['CardName']."','".$_POST['PNNombres']."','".$_POST['PNApellido1']."','".$_POST['PNApellido2']."','".$_POST['AliasName']."','".$_POST['CardType']."','".$_POST['TipoEntidad']."','".$_POST['TipoDocumento']."','".$_POST['LicTradNum']."','".$_POST['GroupCode']."','".$_POST['RegimenTributario']."','".$_POST['ID_MunicipioMM']."','".$_POST['GroupNum']."',1,'".$_SESSION['CodUser']."','1'";
		//echo $Cons_CabeceraSolPed;
		$SQL_SN=sqlsrv_query($conexion,$Cons_SN);
		if($SQL_SN){
			InsertarLog(2, 38, $SQL_SN);
			$row_NewIdSN=sqlsrv_fetch_array($SQL_SN);
			
			//Insertar Contactos
			$Count=count($_POST['NombreContacto']);
			$i=0;
			$Delete="Delete From tbl_SociosNegocios_Contactos Where CodigoCliente='".$_POST['CardCode']."'";
			if(sqlsrv_query($conexion,$Delete)){
				while($i<$Count){
					if($_POST['NombreContacto'][$i]!=""){
						//Insertar el registro en la BD
						$Cons_InsConct="EXEC sp_tbl_SociosNegocios_Contactos '".$row_NewIdSN[0]."','".$_POST['CardCode']."','".$_POST['CodigoContacto'][$i]."','".$_POST['NombreContacto'][$i]."','".$_POST['Telefono'][$i]."','".$_POST['TelefonoCelular'][$i]."','".$_POST['Email'][$i]."','".$_POST['ActEconomica'][$i]."','".$_POST['CedulaContacto'][$i]."','".$_POST['RepLegal'][$i]."',1,1";
						
						$SQL_InsConct=sqlsrv_query($conexion,$Cons_InsConct);

						if(!$SQL_InsConct){
							InsertarLog(1, 38, $Cons_InsConct);
							throw new Exception('Ha ocurrido un error al insertar los contactos.');					
							sqlsrv_close($conexion);
						}
					}
					$i=$i+1;
				}
				//sqlsrv_close($conexion);
				//header('Location:socios_negocios_add.php?a='.base64_encode("OK_SNAdd"));
			}else{
				InsertarLog(1, 38, $Delete);
				throw new Exception('Ha ocurrido un error al eliminar el registro');
				sqlsrv_close($conexion);	
			}
			//Insertar direcciones
			$Count=count($_POST['Address']);
			$i=0;
			$Delete="Delete From tbl_SociosNegocios_Direcciones Where CardCode='".$_POST['CardCode']."'";
			if(sqlsrv_query($conexion,$Delete)){
				while($i<$Count){
					if($_POST['Address'][$i]!=""){
						//Insertar el registro en la BD
						$Cons_InsDir="EXEC sp_tbl_SociosNegocios_Direcciones '".$row_NewIdSN[0]."','".$_POST['Address'][$i]."','".$_POST['CardCode']."','".$_POST['Street'][$i]."','".$_POST['Block'][$i]."','".$_POST['City'][$i]."','".$_POST['County'][$i]."','".$_POST['AdresType'][$i]."','".$_POST['LineNum'][$i]."',1,1";
						
						$SQL_InsDir=sqlsrv_query($conexion,$Cons_InsDir);

						if(!$SQL_InsDir){
							InsertarLog(1, 38, $Cons_InsDir);
							throw new Exception('Ha ocurrido un error al insertar las direcciones.');					
							sqlsrv_close($conexion);
						}
					}
					$i=$i+1;
				}
				sqlsrv_close($conexion);
				header('Location:socios_negocios_add.php?a='.base64_encode("OK_SNAdd"));
			}else{
				InsertarLog(1, 38, $Delete);
				throw new Exception('Ha ocurrido un error al eliminar el registro');
				sqlsrv_close($conexion);	
			}
		}else{
			
			InsertarLog(1, 38, $Cons_SN);
			throw new Exception('Ha ocurrido un error al crear el Socio de Negocio.');
			sqlsrv_close($conexion);
			exit();
		}
	}catch (Exception $e){
		InsertarLog(1, 38, $Cons_SN);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==39){//Editar Orden de venta
	try{
		$Cons_UpdCabeceraOrdenVenta="EXEC sp_tbl_OrdenVenta '".base64_decode($_POST['id'])."', NULL, NULL, NULL, '1','".$_POST['DocDate']."','".$_POST['DocDueDate']."','".$_POST['TaxDate']."','".$_POST['CardCode']."','".$_POST['ContactoCliente']."',NULL,'".$_POST['OrdenServicioCliente']."','".$_POST['Referencia']."','".$_SESSION['CodigoSAP']."','".LSiqmlObs($_POST['Comentarios'])."','".str_replace(',','',$_POST['SubTotal'])."','".str_replace(',','',$_POST['Descuentos'])."',NULL,'".str_replace(',','',$_POST['Impuestos'])."','".str_replace(',','',$_POST['TotalOrden'])."','".$_POST['NombreDireccionFacturacion']."','".$_POST['DireccionFacturacion']."','".$_POST['NombreDireccionDestino']."','".$_POST['DireccionDestino']."','".$_POST['CondicionPago']."','".$_POST['CentroCosto']."','".$_POST['UnidadNegocio']."',NULL,'".$_SESSION['CodUser']."','2'";
		//echo $Cons_CabeceraSolPed;
		$SQL_UpdCabeceraOrdenVenta=sqlsrv_query($conexion,$Cons_UpdCabeceraOrdenVenta);
		if($SQL_UpdCabeceraOrdenVenta){
			InsertarLog(2, 39, $Cons_UpdCabeceraOrdenVenta);
			//$row_CabeceraOrdenVenta=sqlsrv_fetch_array($SQL_CabeceraOrdenVenta);
			//echo $row_CabeceraSolPed[0];
			//$Cons_DetalleOrdenVenta="EXEC sp_tbl_OrdenVentaDetalle '".$row_CabeceraOrdenVenta[0]."', '".$_POST['CardCode']."', '".$_SESSION['CodUser']."'";
			//echo $Cons_DetalleSolPed;
			//$SQL_DetalleOrdenVenta=sqlsrv_query($conexion,$Cons_DetalleOrdenVenta);
			//if($SQL_DetalleOrdenVenta){
				sqlsrv_close($conexion);
				//if($_POST['d_LS']==1){
					//header('Location:llamada_edit.php?a='.base64_encode("OK_OVenAdd")."&".base64_decode($_POST['return']));
				//}else{
					header('Location:reportes_orden_venta.php?a='.base64_encode("OK_OVUPD"));
				//}				
			/*}else{
				InsertarLog(1, 39, $Cons_DetalleOrdenVenta);
				throw new Exception('Ha ocurrido un error al insertar las lineas de la orden de venta');
				sqlsrv_close($conexion);
				exit();
			}*/
		}else{
			InsertarLog(1, 39, $Cons_UpdCabeceraOrdenVenta);
			throw new Exception('Ha ocurrido un error al actualizar la orden de venta');
			sqlsrv_close($conexion);
			exit();
		}
	}catch (Exception $e){
		InsertarLog(1, 39, $Cons_UpdCabeceraOrdenVenta);
		//InsertarLog(1, 39, $Cons_DetalleOrdenVenta);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==40){//Reabrir llamada de servicio
	try{
		require_once("includes/conect_ws.php");
			$Parametros=array(
				'pIdLlamada' => base64_decode($_POST['DocEntry']),
				'pLogin'=>$_SESSION['User']
			);
			$Client->ReabrirLlamadaServicioPortal($Parametros);
			//InsertarLog(2, 40, $Cons_UpdCierreLlamada);
			sqlsrv_close($conexion);
			header('Location:llamada_edit.php?id='.$_POST['DocEntry'].'&a='.base64_encode("OK_OpenLlam"));
			//throw new Exception('Error cerrar la llamada de servicio');				
			//sqlsrv_close($conexion);
			//exit();
	}catch (Exception $e) {
		//InsertarLog(1, 40, $Cons_UpdCierreLlamada);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==41){//Eliminar la actividad
	try{
		require_once("includes/conect_ws.php");
			$Parametros=array(
				'pIdActividad' => base64_decode($_POST['ID']),
				'pLogin'=>$_SESSION['User']
			);
			$Client->EliminarActividadPortal($Parametros);
			//InsertarLog(2, 40, $Cons_UpdCierreLlamada);
			sqlsrv_close($conexion);
			if($_POST['d_LS']==1){
				header('Location:llamada_edit.php?a='.base64_encode("OK_DelAct")."&".base64_decode($_POST['return_param']));
			}else{
				header('Location:'.base64_decode($_POST['return']).'&a='.base64_encode("OK_DelAct"));	
			}
			//throw new Exception('Error cerrar la llamada de servicio');
			//sqlsrv_close($conexion);
			//exit();
	}catch (Exception $e) {
		//InsertarLog(1, 40, $Cons_UpdCierreLlamada);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==42){//Reabrir actividad
	try{
		require_once("includes/conect_ws.php");
			$Parametros=array(
				'pIdActividad' => base64_decode($_POST['ID']),
				'pLogin'=>$_SESSION['User']
			);
			$result = $Client->ReabrirActividadPortal($Parametros);
			if(is_soap_fault($result)){
				trigger_error("Fallo IntSAPB1: (Codigo: {$result->faultcode}, Mensaje: {$result->faultstring})", E_USER_ERROR);
			}
			//InsertarLog(2, 40, $Cons_UpdCierreLlamada);
			sqlsrv_close($conexion);
			header('Location:actividad_edit.php?id='.$_POST['ID'].'&a='.base64_encode("OK_OpenAct").'&return='.$_POST['return_param'].'&pag='.$_POST['pag_param']);
			//throw new Exception('Error cerrar la llamada de servicio');				
			//sqlsrv_close($conexion);
			//exit();
	}catch (Exception $e) {
		//InsertarLog(1, 40, $Cons_UpdCierreLlamada);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
}
	
if($P==43){//Registrar gestion de cartera
	try{
		if($_POST['FechaCompPago']!=""){
			$_POST['FechaCompPago']="'".$_POST['FechaCompPago']."'";
		}else{
			$_POST['FechaCompPago']="NULL";
		}
		
		//Si hay acuerdo de pago
		if(isset($_POST['chkRegAcuerdo'])&&($_POST['chkRegAcuerdo']==1)){
			$chkRegAcuerdo=1;
		}else{
			$chkRegAcuerdo=0;
		}
		
		//Si hay liquidacion de intereses
		if(isset($_POST['chkLiqIntereses'])&&($_POST['chkLiqIntereses']==1)){
			$chkLiqIntereses=1;
		}else{
			$chkLiqIntereses=0;
		}
		
		$ParametrosInsGestion=array(
			"NULL",
			"'".base64_decode($_POST['CardCode'])."'",
			"'".$_POST['TipoGestion']."'",
			"'".$_POST['Destino']."'",
			"'".$_POST['Evento']."'",
			"'".$_POST['Dirigido']."'",
			"'".$_POST['ResultadoGestion']."'",
			$_POST['FechaCompPago'],
			"'".LSiqmlObs($_POST['Comentarios'])."'",
			"'".$_POST['CausaNoPago']."'",
			"'".$chkLiqIntereses."'",
			"'".$chkRegAcuerdo."'",
			"'".base64_decode($_POST['cllName'])."'",
			"1",
			"'".$_SESSION['CodUser']."'",
			"1"			
		);
		$SQL_InsGestion=EjecutarSP('sp_tbl_Cartera_Gestion',$ParametrosInsGestion,43);
		if($SQL_InsGestion){
			$row_NewIdGestion=sqlsrv_fetch_array($SQL_InsGestion);
			
			//Si hay liquidacion de intereses
			if($chkLiqIntereses==1){
				$ParametrosInsLiq=array(
					"NULL",
					"'".$row_NewIdGestion[0]."'",
					"'".base64_decode($_POST['CardCode'])."'",
					"'".$_POST['FechaLiquidacion']."'",
					"'".LSiqmlValorDecimal($_POST['TotalSaldoLiqInt'])."'",
					"'".LSiqmlValorDecimal($_POST['InteresesMoraLiqInt'])."'",
					"'".LSiqmlValorDecimal($_POST['RetiroAnticipadoLiqInt'])."'",
					"'".LSiqmlValorDecimal($_POST['GastosCobranzaLiqInt'])."'",
					"'".LSiqmlValorDecimal($_POST['CobroPrejuridicoLiqInt'])."'",
					"'".LSiqmlValorDecimal($_POST['TotalLiquidadoLiqInt'])."'",
					"'".LSiqmlValorDecimal($_POST['DescuentoLiqInt'])."'",
					"'".LSiqmlValorDecimal($_POST['TotalPagarLiqInt'])."'",
					"1",
					"'".$_SESSION['CodUser']."'",
					"1"
				);
				$SQL_InsLiqInt=EjecutarSP('sp_tbl_Cartera_LiquidacionIntereses',$ParametrosInsLiq,43);
				if($SQL_InsLiqInt){
					$row_NewIdLiqInt=sqlsrv_fetch_array($SQL_InsLiqInt);
					
					//Enviar datos al WebServices - Liquidacion de intereses
					try{
						require_once("includes/conect_ws.php");
						$Parametros=array(
							'pIdLiqInteres' => $row_NewIdLiqInt[0],
							'pLogin'=>$_SESSION['User']
						);
						$Client->InsertarLiquidaInteresPortal($Parametros);
					}catch (Exception $e) {
						echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
					}
					
					//Insertar tabla de intereses de facturas
					if(isset($_POST['chkCobIntLiqInt'])&&($_POST['chkCobIntLiqInt']==1)){//Traer la tabla de facturas vencidas con o sin intereses
						if(isset($_POST['chkVerFactNoVencLiqInt'])&&($_POST['chkVerFactNoVencLiqInt']==1)){
							$Param=array("'".base64_decode($_POST['CardCode'])."'",1,1);
						}
						else{
							$Param=array("'".base64_decode($_POST['CardCode'])."'",1,0);
						}						
					}else{
						if(isset($_POST['chkVerFactNoVencLiqInt'])&&($_POST['chkVerFactNoVencLiqInt']==1)){
							$Param=array("'".base64_decode($_POST['CardCode'])."'",0,1);
						}else{
							$Param=array("'".base64_decode($_POST['CardCode'])."'",0,0);
						}
					}
					$SQL_FactPend=EjecutarSP('sp_CalcularIntMoraFactVencida',$Param);
					while($row_FactPend=sqlsrv_fetch_array($SQL_FactPend)){
						$ParametrosInsFacVenc=array(
							"NULL",
							"'".$row_NewIdLiqInt[0]."'",
							"'".$row_NewIdGestion[0]."'",
							"'".base64_decode($_POST['CardCode'])."'",
							"'".$row_FactPend['NoDocumento']."'",
							"'".$row_FactPend['FechaVencimiento']->format('Y-m-d')."'",
							"'".$row_FactPend['DiasVencidos']."'",
							"'".$row_FactPend['SaldoDocumento']."'",
							"'".$row_FactPend['InteresesMora']."'",
							"'".$row_FactPend['TotalPagar']."'",							
							"'".$_SESSION['CodUser']."'",
							"1"
						);
						$SQL_InsFacVenc=EjecutarSP('sp_tbl_Cartera_FactVencLiqIntereses',$ParametrosInsFacVenc,43);						
					}			
				}else{
					throw new Exception('Ha ocurrido un error al insertar la liquidacion de intereses');
					sqlsrv_close($conexion);
					exit();
				}
			}
			
			//Si hay acuerdo de pago
			if($chkRegAcuerdo==1){
				$ParametrosInsAcu=array(
					"NULL",
					"'".$row_NewIdGestion[0]."'",
					"'".base64_decode($_POST['CardCode'])."'",
					"'".$_POST['TipoConvenio']."'",
					"'".$_POST['FechaAcuerdo']."'",
					"'".LSiqmlValorDecimal($_POST['TotalSaldo'])."'",
					"'".LSiqmlValorDecimal($_POST['InteresesMora'])."'",
					"'".LSiqmlValorDecimal($_POST['RetiroAnticipado'])."'",					
					"'".LSiqmlValorDecimal($_POST['GastosCobranza'])."'",
					"'".LSiqmlValorDecimal($_POST['CobroPrejuridico'])."'",
					"'".LSiqmlValorDecimal($_POST['TotalLiquidado'])."'",
					"'".LSiqmlValorDecimal($_POST['Descuento'])."'",
					"'".LSiqmlValorDecimal($_POST['TotalPagar'])."'",
					"'".LSiqmlValorDecimal($_POST['AbonoInicial'])."'",
					"'".LSiqmlValorDecimal($_POST['SaldoDiferir'])."'",
					"'".$_POST['Cuotas']."'",
					"1",
					"'".$_SESSION['CodUser']."'",
					"1"
				);				
				$SQL_InsAcuerdo=EjecutarSP('sp_tbl_Cartera_AcuerdosDePago',$ParametrosInsAcu,43);
				if($SQL_InsAcuerdo){
					$row_NewIdAcuerdo=sqlsrv_fetch_array($SQL_InsAcuerdo);
					
					//Enviar datos al WebServices - Acuerdo de pago
					try{
						require_once("includes/conect_ws.php");
						$Parametros=array(
							'pIdAcpago' => $row_NewIdAcuerdo[0],
							'pLogin'=>$_SESSION['User']
						);
						$Client->InsertarAcuerdoPagoPortal($Parametros);
					}catch (Exception $e) {
						echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
					}
					
					//Insertar tabla de intereses de facturas
					if(isset($_POST['chkCobInt'])&&($_POST['chkCobInt']==1)){//Traer la tabla de facturas vencidas con o sin intereses
						if(isset($_POST['chkVerFactNoVenc'])&&($_POST['chkVerFactNoVenc']==1)){
							$Param=array("'".base64_decode($_POST['CardCode'])."'",1,1);
						}else{
							$Param=array("'".base64_decode($_POST['CardCode'])."'",1,0);
						}
					}else{
						if(isset($_POST['chkVerFactNoVenc'])&&($_POST['chkVerFactNoVenc']==1)){
							$Param=array("'".base64_decode($_POST['CardCode'])."'",0,1);
						}else{
							$Param=array("'".base64_decode($_POST['CardCode'])."'",0,0);
						}
					}
					$SQL_FactPend=EjecutarSP('sp_CalcularIntMoraFactVencida',$Param);
					while($row_FactPend=sqlsrv_fetch_array($SQL_FactPend)){
						$ParametrosInsFacVenc=array(
							"NULL",
							"'".$row_NewIdAcuerdo[0]."'",
							"'".$row_NewIdGestion[0]."'",
							"'".base64_decode($_POST['CardCode'])."'",
							"'".$row_FactPend['NoDocumento']."'",
							"'".$row_FactPend['FechaVencimiento']->format('Y-m-d')."'",
							"'".$row_FactPend['DiasVencidos']."'",
							"'".$row_FactPend['SaldoDocumento']."'",
							"'".$row_FactPend['InteresesMora']."'",
							"'".$row_FactPend['TotalPagar']."'",							
							"'".$_SESSION['CodUser']."'",
							"1"
						);
						$SQL_InsFacVenc=EjecutarSP('sp_tbl_Cartera_FactVencAcuerdos',$ParametrosInsFacVenc,43);						
					}
					
					//Enviar datos al WebServices - Intereses facturas
					try{
						require_once("includes/conect_ws.php");
						$Parametros=array(
							'pIdAcpago' => $row_NewIdAcuerdo[0],
							'pLogin'=>$_SESSION['User']
						);
						$Client->InsertarAcuerdoPagoFvaPortal($Parametros);
					}catch (Exception $e) {
						echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
					}
					
					//Si hay mas de una cuota
					if($_POST['Cuotas']>=1){
						$Array=CalcularCuotasAcuerdo($_POST['FechaAcuerdo'],$_POST['Cuotas'],LSiqmlValorDecimal($_POST['SaldoDiferir']));
						$j=1;
						for($i=0;$i<$_POST['Cuotas'];$i++){
							$ParametrosInsCuota=array(
								"NULL",
								"'".$row_NewIdAcuerdo[0]."'",
								"'".$row_NewIdGestion[0]."'",
								"'".base64_decode($_POST['CardCode'])."'",
								"'".$Array[$j][1]."'",
								"'".$Array[$j][2]."'",
								"'".$Array[$j][3]."'",
								"'".$_SESSION['CodUser']."'",
								"1"
							);	
							$SQL_InsCuota=EjecutarSP('sp_tbl_Cartera_CuotasAcuerdos',$ParametrosInsCuota,43);
							$j++;
						}
						//Enviar datos al WebServices - Cuotas acuerdos
						try{
							require_once("includes/conect_ws.php");
							$Parametros=array(
								'pIdAcpago' => $row_NewIdAcuerdo[0],
								'pLogin'=>$_SESSION['User']
							);
							$Client->InsertarAcuerdoPagoCuotasPortal($Parametros);
						}catch (Exception $e) {
							echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
						}
					}				
				}else{
					throw new Exception('Ha ocurrido un error al insertar el acuerdo de pago');
					sqlsrv_close($conexion);
					exit();
				}
			}
			
			//Enviar datos al WebServices
			try{
				require_once("includes/conect_ws.php");
				$Parametros=array(
					'pIdGestion' => $row_NewIdGestion[0],
					'pLogin'=>$_SESSION['User']
				);
				$Client->InsertarGescarPortal($Parametros);
			}catch (Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
			}
			sqlsrv_close($conexion);
			header('Location:gestionar_cartera.php?Clt='.$_POST['CardCode'].'&a='.base64_encode("OK_GtnCtr"));
		}else{
			throw new Exception('Error al insertar la gestion.');				
			sqlsrv_close($conexion);
			exit();
		}						
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==44){//Crear o actualizar campos de informes de SAP B1
	try{
		//Insertar datos WS
		$ParamInsWS=array(
			"'".base64_decode($_POST['id'])."'",
			"'".$_POST['NombreWS']."'",
			"'".$_SESSION['CodUser']."'",
			"1"
		);
		$SQL_InsWS=EjecutarSP('sp_tbl_ParamInfSAP_WebServices',$ParamInsWS,44);
		if($SQL_InsWS){
			//Insertar campos
			$Count=count($_POST['NombreParam']);
			$i=0;
			$Delete="Delete From tbl_ParamInfSAP_Campos Where ID_Categoria='".base64_decode($_POST['id'])."'";
			if(sqlsrv_query($conexion,$Delete)){
				while($i<$Count){
					if($_POST['NombreParam'][$i]!=""){
						//Insertar el registro en la BD
						$ParamInsDir=array(
							"'".base64_decode($_POST['id'])."'",
							"'".$_POST['NombreParam'][$i]."'",
							"'".$_POST['LabelCampo'][$i]."'",
							"'".$_POST['NombreCampo'][$i]."'",
							"'".$_POST['TipoCampo'][$i]."'",
							"'".$_POST['CampoOblig'][$i]."'",
							"'".$_POST['NombreCheckbox'][$i]."'",
							"'".$_POST['VistaList'][$i]."'",
							"'".$_POST['EtiqList'][$i]."'",
							"'".$_POST['ValorList'][$i]."'",
							"'".$_POST['TodosList'][$i]."'",
							"'".$_SESSION['CodUser']."'",
							"1"
						);
						$SQL_InsDir=EjecutarSP('sp_tbl_ParamInfSAP_Campos',$ParamInsDir,44);
						if(!$SQL_InsDir){
							throw new Exception('Ha ocurrido un error al insertar los parametros de SAP B1.');					
							sqlsrv_close($conexion);
							exit();
						}
					}
					$i=$i+1;
				}
				sqlsrv_close($conexion);
				header('Location:informes_sap_parametrizar.php?a='.base64_encode("OK_ParamInfSAP").'&id='.$_POST['id']);
			}else{
				InsertarLog(1, 44, $Delete);
				throw new Exception('Ha ocurrido un error al eliminar el registro');
				sqlsrv_close($conexion);	
			}				
		}		
	}catch (Exception $e) {
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==45){//Actualizar Socio de Negocios
	try{
		
		#Comprobar si el cliente ya esta guardado en la tabla de SN. Si no está guardado se ejecuta el INSERT con el Metodo de actualizar
		//$SQL_Dir=Seleccionar('tbl_SociosNegocios','CardCode',"CardCode='".$_POST['CardCode']."'");
		//$row_Dir=sqlsrv_fetch_array($SQL_Dir);
		
		$Metodo=2;//Actualizar en el web services
		$Type=2;//Ejecutar actualizar en el SP
		
		if($_POST['edit']==0){//Creando SN
			$Metodo=1;
		}
		
		if($_POST['ID_SN']==""){//Insertando en la tabla
			$Type=1;
		}
		
		$ParamSN=array(
			"'".$_POST['CardCode']."'",
			"'".$_POST['CardName']."'",
			"'".$_POST['PNNombres']."'",
			"'".$_POST['PNApellido1']."'",
			"'".$_POST['PNApellido2']."'",
			"'".$_POST['AliasName']."'",
			"'".$_POST['CardType']."'",
			"'".$_POST['TipoEntidad']."'",
			"'".$_POST['TipoDocumento']."'",
			"'".$_POST['LicTradNum']."'",
			"'".$_POST['GroupCode']."'",
			"'".$_POST['RegimenTributario']."'",
			"'".$_POST['ID_MunicipioMM']."'",
			"'".$_POST['GroupNum']."'",
			$Metodo,
			"'".$_SESSION['CodUser']."'",
			$Type
		);
		$SQL_SN=EjecutarSP('sp_tbl_SociosNegocios',$ParamSN,45);
		if($SQL_SN){			
			if(base64_decode($_POST['ID_SN'])==""){
				$row_NewIdSN=sqlsrv_fetch_array($SQL_SN);
				$IdSN=$row_NewIdSN[0];
			}else{
				$IdSN=base64_decode($_POST['ID_SN']);
			}			
			
			//Insertar Contactos
			$Count=count($_POST['NombreContacto']);
			$i=0;
			$Delete="Delete From tbl_SociosNegocios_Contactos Where CodigoCliente='".$_POST['CardCode']."'";
			if(sqlsrv_query($conexion,$Delete)){
				while($i<$Count){
					if($_POST['NombreContacto'][$i]!=""){
						//Insertar el registro en la BD
						$ParamInsConct=array(
							"'".$IdSN."'",
							"'".$_POST['CardCode']."'",
							"'".$_POST['CodigoContacto'][$i]."'",
							"'".$_POST['NombreContacto'][$i]."'",
							"'".$_POST['SegundoNombre'][$i]."'",
							"'".$_POST['Apellidos'][$i]."'",
							"'".$_POST['Telefono'][$i]."'",
							"'".$_POST['TelefonoCelular'][$i]."'",
							"'".$_POST['Posicion'][$i]."'",
							"'".$_POST['Email'][$i]."'",
							"'".$_POST['ActEconomica'][$i]."'",
							"'".$_POST['CedulaContacto'][$i]."'",
							"'".$_POST['RepLegal'][$i]."'",
							"'".$_POST['MetodoCtc'][$i]."'",
							"1"
						);
						
						$SQL_InsConct=EjecutarSP('sp_tbl_SociosNegocios_Contactos',$ParamInsConct,45);

						if(!$SQL_InsConct){
							throw new Exception('Ha ocurrido un error al insertar los contactos.');					
							sqlsrv_close($conexion);
						}
					}
					$i=$i+1;
				}
				//sqlsrv_close($conexion);
				//header('Location:socios_negocios_add.php?a='.base64_encode("OK_SNAdd"));
			}else{
				InsertarLog(1, 45, $Delete);
				throw new Exception('Ha ocurrido un error al eliminar el registro');
				sqlsrv_close($conexion);	
			}
			//Insertar direcciones
			$Count=count($_POST['Address']);
			$i=0;
			$Delete="Delete From tbl_SociosNegocios_Direcciones Where CardCode='".$_POST['CardCode']."'";
			if(sqlsrv_query($conexion,$Delete)){
				while($i<$Count){
					if($_POST['Address'][$i]!=""){
						//Insertar el registro en la BD
						$ParamInsDir=array(
							"'".$IdSN."'",
							"'".$_POST['Address'][$i]."'",
							"'".$_POST['CardCode']."'",
							"'".$_POST['Street'][$i]."'",
							"'".$_POST['Block'][$i]."'",
							"'".$_POST['City'][$i]."'",
							"'".$_POST['County'][$i]."'",
							"'".$_POST['AdresType'][$i]."'",
							"'".$_POST['LineNum'][$i]."'",
							"'".$_POST['Metodo'][$i]."'",
							"1"
						);
						
						$SQL_InsDir=EjecutarSP('sp_tbl_SociosNegocios_Direcciones',$ParamInsDir,45);

						if(!$SQL_InsDir){
							throw new Exception('Ha ocurrido un error al insertar las direcciones.');					
							sqlsrv_close($conexion);
						}
					}
					$i=$i+1;
				}
				
				//Enviar datos al WebServices
				try{
					require_once("includes/conect_ws.php");
					$Parametros=array(
						'pIdCliente' => $IdSN,
						'pLogin'=>$_SESSION['User']
					);
					$Client->InsertarClientePortal($Parametros);
				}catch (Exception $e) {
					echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
				}
				
				if($_POST['edit']==0){//Mensaje para devuelta
					$Msg=base64_encode("OK_SNAdd");
				}else{
					$Msg=base64_encode("OK_SNEdit");
				}
				sqlsrv_close($conexion);
				if($_POST['ext']==0){//Validar a donde debe ir la respuesta
					header('Location:socios_negocios.php?id='.base64_encode($_POST['CardCode']).'&ext='.$_POST['ext'].'&pag='.$_POST['pag'].'&return='.$_POST['return'].'&a='.$Msg.'&tl='.$_POST['edit']);
				}else{
					header('Location:socios_negocios.php?id='.base64_encode($_POST['CardCode']).'&ext='.$_POST['ext'].'&a='.$Msg.'&tl='.$_POST['edit']);
				}				
			}else{
				InsertarLog(1, 45, $Delete);
				throw new Exception('Ha ocurrido un error al eliminar el registro');
				sqlsrv_close($conexion);	
			}
		}else{
			throw new Exception('Ha ocurrido un error al crear el Socio de Negocio.');
			sqlsrv_close($conexion);
			exit();
		}
	}catch (Exception $e){
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}

if($P==46){//Grabar Oferta de venta
	try{
		$Cons_CabeceraOfertaVenta="EXEC sp_tbl_OfertaVenta NULL, NULL, NULL, NULL, '1','".$_POST['DocDate']."','".$_POST['DocDueDate']."','".$_POST['TaxDate']."','".$_POST['CardCode']."','".$_POST['ContactoCliente']."',NULL,'".$_POST['OrdenServicioCliente']."','".$_POST['Referencia']."','".$_SESSION['CodigoSAP']."','".LSiqmlObs($_POST['Comentarios'])."','".str_replace(',','',$_POST['SubTotal'])."','".str_replace(',','',$_POST['Descuentos'])."',NULL,'".str_replace(',','',$_POST['Impuestos'])."','".str_replace(',','',$_POST['TotalOferta'])."','".$_POST['NombreDireccionFacturacion']."','".$_POST['DireccionFacturacion']."','".$_POST['NombreDireccionDestino']."','".$_POST['DireccionDestino']."','".$_POST['CondicionPago']."','".$_POST['CentroCosto']."','".$_POST['UnidadNegocio']."',NULL,'".$_SESSION['CodUser']."','1'";
		//echo $Cons_CabeceraSolPed;
		$SQL_CabeceraOfertaVenta=sqlsrv_query($conexion,$Cons_CabeceraOfertaVenta);
		if($SQL_CabeceraOfertaVenta){
			InsertarLog(2, 46, $Cons_CabeceraOfertaVenta);
			$row_CabeceraOfertaVenta=sqlsrv_fetch_array($SQL_CabeceraOfertaVenta);
			//echo $row_CabeceraSolPed[0];
			$Cons_DetalleOfertaVenta="EXEC sp_tbl_OfertaVentaDetalle '".$row_CabeceraOfertaVenta[0]."', '".$_POST['CardCode']."', '".$_SESSION['CodUser']."'";
			//echo $Cons_DetalleSolPed;
			$SQL_DetalleOfertaVenta=sqlsrv_query($conexion,$Cons_DetalleOfertaVenta);
			if($SQL_DetalleOfertaVenta){
				sqlsrv_close($conexion);
				if($_POST['d_LS']==1){
					header('Location:llamada_edit.php?a='.base64_encode("OK_OFertAdd")."&".base64_decode($_POST['return']));
				}else{
					header('Location:oferta_venta_add.php?data='.base64_encode($row_CabeceraOfertaVenta[0]));
				}				
			}else{
				InsertarLog(1, 46, $Cons_DetalleOfertaVenta);
				throw new Exception('Ha ocurrido un error al insertar las lineas de la oferta de venta');
				sqlsrv_close($conexion);
				exit();
			}
		}else{
			InsertarLog(1, 46, $Cons_CabeceraOfertaVenta);
			throw new Exception('Ha ocurrido un error al crear la oferta de venta');
			sqlsrv_close($conexion);
			exit();
		}
	}catch (Exception $e){
		InsertarLog(1, 46, $Cons_CabeceraOfertaVenta);
		InsertarLog(1, 46, $Cons_DetalleOfertaVenta);
		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
	}
	
}
	
if($P==47){//Actualizar Oferta de venta	
}

if($P==48){//Insertar articulos SAP B1
}
	
if($P==49){//Entrega de ventas
}
	
if($P==50){//Solicitud de salida
}
	
if($P==51){//Salida de inventario
}

}
?>