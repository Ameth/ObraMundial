<?php require_once("includes/conexion.php");
PermitirAcceso(202);

$sw=0;

//Estado
$SQL_Estado=Seleccionar('uvw_tbl_Estados','*');

//Grupos de congregacion
$SQL_Grupos=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."'",'NombreGrupo');

//Genero
$SQL_Genero=Seleccionar('uvw_tbl_Genero','*','','NombreGenero');

//Tipo publicador
$SQL_TipoPublicador=Seleccionar('uvw_tbl_TipoPublicador','*','','TipoPublicador');

//Tipo privilegio de servicio
$SQL_PrivServicio=Seleccionar('uvw_tbl_PrivilegioServicio','*','','PrivilegioServicio');

//Filtros
$Filtro="";//Filtro
if(isset($_GET['Grupo'])&&$_GET['Grupo']!=""){
	$Filtro.=" and IDGrupo='".$_GET['Grupo']."'";
	$sw=1;
}
if(isset($_GET['Genero'])&&$_GET['Genero']!=""){
	$Filtro.=" and IDGenero='".$_GET['Genero']."'";
	$sw=1;
}
if(isset($_GET['TipoPublicador'])&&$_GET['TipoPublicador']!=""){
	$Filtro.=" and IDTipoPublicador='".$_GET['TipoPublicador']."'";
	$sw=1;
}
if(isset($_GET['PrivServicio'])&&$_GET['PrivServicio']!=""){
	$Filtro.=" and IDPrivServicio='".$_GET['PrivServicio']."'";
	$sw=1;
}
if(isset($_GET['Estado'])&&$_GET['Estado']!=""){
	$Filtro.=" and IDEstado='".$_GET['Estado']."'";
	$sw=1;
}
if(isset($_GET['BuscarDato'])&&$_GET['BuscarDato']!=""){
	$Filtro.=" and (Nombre LIKE '%".$_GET['BuscarDato']."%' OR SegundoNombre LIKE '%".$_GET['BuscarDato']."%' OR Apellido LIKE '%".$_GET['BuscarDato']."%' OR SegundoApellido LIKE '%".$_GET['BuscarDato']."%' OR Direccion LIKE '%".$_GET['BuscarDato']."%' OR PersonaCont LIKE '%".$_GET['BuscarDato']."%')";
	$sw=1;
}
if(PermitirFuncion(205)){
	$Cons="Select * From uvw_tbl_Publicadores Where NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$_SESSION['Grupo']."' $Filtro";
}else{
	$Cons="Select * From uvw_tbl_Publicadores Where NumCong='".$_SESSION['NumCong']."' $Filtro";
}
$SQL=sqlsrv_query($conexion,$Cons);

//echo $Cons;

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Gestionar publicadores | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<?php 
if(isset($_GET['a'])&&($_GET['a']==base64_encode("OK_PubAdd"))){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'El publicador ha sido creado exitosamente.',
                icon: 'success'
            });
		});		
		</script>";
}
if(isset($_GET['a'])&&($_GET['a']==base64_encode("OK_PubUpd"))){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'El publicador ha sido actualizado exitosamente.',
                icon: 'success'
            });
		});		
		</script>";
}
?>
<!-- InstanceEndEditable -->
</head>

<body>

<div id="wrapper">

    <?php include("includes/menu.php"); ?>

    <div id="page-wrapper" class="gray-bg">
        <?php include("includes/menu_superior.php"); ?>
        <!-- InstanceBeginEditable name="Contenido" -->
        <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-8">
                    <h2>Publicadores</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Congregación</a>
                        </li>
                        <li class="active">
                            <strong>Publicadores</strong>
                        </li>
                    </ol>
                </div>
			<?php if(PermitirFuncion(201)){?>
                <div class="col-sm-4">
                    <div class="title-action">
                        <a href="publicadores.php" class="alkin btn btn-primary"><i class="fa fa-plus-circle"></i> Crear nuevo publicador</a>
                    </div>
                </div>
			<?php }?>
               <?php  //echo $Cons;?>
            </div>
         <div class="wrapper wrapper-content">
             <div class="row">
				<div class="col-lg-12">
			    <div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
				  <form action="gestionar_publicadores.php" method="get" id="formBuscar" class="form-horizontal">
					  <div class="form-group">
							<label class="col-xs-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-filter"></i> Datos para filtrar</h3></label>
						</div>
					 	<div class="form-group">
							<label class="col-lg-1 control-label">Estado</label>
							<div class="col-lg-3">
								<select name="Estado" class="form-control" id="Estado">
										<option value="">(Todos)</option>
								  <?php while($row_Estado=sqlsrv_fetch_array($SQL_Estado)){?>
										<option value="<?php echo $row_Estado['IDEstado'];?>" <?php if((isset($_GET['Estado']))&&(strcmp($row_Estado['IDEstado'],$_GET['Estado'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Estado['NombreEstado'];?></option>
								  <?php }?>
								</select>
							</div>
							<?php if(!PermitirFuncion(205)){?>
							<label class="col-lg-1 control-label">Grupo</label>
							<div class="col-lg-3">
								<select name="Grupo" class="form-control" id="Grupo">
									<option value="">(Todos)</option>
								  <?php while($row_Grupos=sqlsrv_fetch_array($SQL_Grupos)){?>
										<option value="<?php echo $row_Grupos['IDGrupo'];?>" <?php if((isset($_GET['Grupo']))&&(strcmp($row_Grupos['IDGrupo'],$_GET['Grupo'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Grupos['NombreGrupo'];?></option>
								  <?php }?>
								</select>
							</div>
							<?php }?>
							<label class="col-lg-1 control-label">Genero</label>
							<div class="col-lg-3">
								<select name="Genero" class="form-control" id="Genero">
									<option value="">(Todos)</option>
								  <?php while($row_Genero=sqlsrv_fetch_array($SQL_Genero)){?>
										<option value="<?php echo $row_Genero['IDGenero'];?>" <?php if((isset($_GET['Genero']))&&(strcmp($row_Genero['IDGenero'],$_GET['Genero'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Genero['NombreGenero'];?></option>
								  <?php }?>
								</select>
							</div>
						</div>
					  	<div class="form-group">
							<label class="col-lg-1 control-label">Tipo publicador</label>
							<div class="col-lg-3">
								<select name="TipoPublicador" class="form-control" id="TipoPublicador">
									<option value="">(Todos)</option>
								  <?php while($row_TipoPublicador=sqlsrv_fetch_array($SQL_TipoPublicador)){?>
										<option value="<?php echo $row_TipoPublicador['IDTipoPublicador'];?>" <?php if((isset($_GET['TipoPublicador']))&&(strcmp($row_TipoPublicador['IDTipoPublicador'],$_GET['TipoPublicador'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_TipoPublicador['TipoPublicador'];?></option>
								  <?php }?>
								</select>
							</div>
							<label class="col-lg-1 control-label">Anciano / SM</label>
							<div class="col-lg-3">
								<select name="PrivServicio" class="form-control" id="PrivServicio">
									<option value="">(Todos)</option>
								  <?php while($row_PrivServicio=sqlsrv_fetch_array($SQL_PrivServicio)){?>
										<option value="<?php echo $row_PrivServicio['IDPrivServicio'];?>" <?php if((isset($_GET['PrivServicio']))&&(strcmp($row_PrivServicio['IDPrivServicio'],$_GET['PrivServicio'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_PrivServicio['PrivilegioServicio'];?></option>
								  <?php }?>
								</select>
							</div>
							<label class="col-lg-1 control-label">Buscar dato</label>
							<div class="col-lg-3">
								<input name="BuscarDato" type="text" class="form-control" id="BuscarDato" maxlength="100" value="<?php if(isset($_GET['BuscarDato'])&&($_GET['BuscarDato']!="")){ echo $_GET['BuscarDato'];}?>">
							</div>
					  	</div>
					  	<div class="form-group">
							<div class="col-lg-10">
								<a href="exportar_excel.php?exp=13&Cons=<?php echo base64_encode($Cons);?>">
									<img src="css/exp_excel.png" width="50" height="30" alt="Exportar a Excel" title="Exportar a Excel"/>
								</a>
							</div>
							<div class="col-lg-2">
								<button type="submit" class="btn btn-outline btn-success pull-right"><i class="fa fa-search"></i> Buscar</button>
							</div>
					  	</div>
				 </form>
			</div>
			</div>
		  </div>
         <br>
			 <?php //echo $Cons;?>
          <div class="row">
           <div class="col-lg-12">
			    <div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
			<div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example" >
                    <thead>
                    <tr>
						<th>Nombre</th>
						<th>Apellidos</th>
						<th>Genero</th>  
						<th>Grupo</th>  
						<th>Fecha nacimiento</th>  
						<th>Fecha bautismo</th>  
                        <th>Dirección</th>
                        <th>Teléfono</th>
						<th>Celular</th>
						<th>Privilegio</th>
						<th>Anciano/SM</th>
						<th>Estado</th>
						<th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while($row=sqlsrv_fetch_array($SQL)){?>
						 <tr class="gradeX tooltip-demo">
							<td><?php echo $row['Nombre']." ".$row['SegundoNombre'];?></td>
							<td><?php echo $row['Apellido']." ".$row['SegundoApellido'];?></td>
							<td><?php echo $row['NombreGenero'];?></td>						
							<td><?php echo $row['NombreGrupo'];?></td>
							<td><?php echo ($row['FechaNac']!="") ? $row['FechaNac']->format('Y-m-d') : "";?></td>
							<td><?php echo ($row['FechaBaut']!="") ? $row['FechaBaut']->format('Y-m-d') : "";?></td>
							<td><?php echo $row['Direccion'];?></td>
							<td><?php echo $row['Telefono'];?></td>
							<td><?php echo $row['Celular'];?></td>
							<td><?php echo $row['TipoPublicador'];?></td>
							<td><?php echo $row['PrivilegioServicioAbr'];?></td>							 
							<td><span <?php if($row['IDEstado']=='1'){echo "class='label label-info'";}else{echo "class='label label-danger'";}?>><?php echo $row['NombreEstado'];?></span></td>							 
							<td><a href="publicadores.php?id=<?php echo base64_encode($row['IDPublicador']);?>&return=<?php echo base64_encode($_SERVER['QUERY_STRING']);?>&pag=<?php echo base64_encode('gestionar_publicadores.php');?>&tl=1" class="alkin btn btn-success btn-xs"><i class="fa fa-edit"></i> Editar</a></td>
						</tr>
					<?php }?>
                    </tbody>
                    </table>
              </div>
			</div>
			 </div> 
          </div>
        </div>
        <!-- InstanceEndEditable -->
        <?php include("includes/footer.php"); ?>

    </div>
</div>
<?php include("includes/pie.php"); ?>
<!-- InstanceBeginEditable name="EditRegion4" -->
 <script>
        $(document).ready(function(){
			$("#formBuscar").validate({
			 submitHandler: function(form){
				 $('.ibox-content').toggleClass('sk-loading');
				 form.submit();
				}
			});
			 $(".alkin").on('click', function(){
					$('.ibox-content').toggleClass('sk-loading');
				});
			 $('#FechaInicial').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
				format: 'yyyy-mm-dd'
            });
			 $('#FechaFinal').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
				format: 'yyyy-mm-dd'
            }); 
			
			$('.chosen-select').chosen({width: "100%"});
			
            $('.dataTables-example').DataTable({
                pageLength: 25,
                dom: '<"html5buttons"B>lTfgitp',
				//order: [[ 0, "desc" ]],
				language: {
					"decimal":        "",
					"emptyTable":     "No se encontraron resultados.",
					"info":           "Mostrando _START_ - _END_ de _TOTAL_ registros",
					"infoEmpty":      "Mostrando 0 - 0 de 0 registros",
					"infoFiltered":   "(filtrando de _MAX_ registros)",
					"infoPostFix":    "",
					"thousands":      ",",
					"lengthMenu":     "Mostrar _MENU_ registros",
					"loadingRecords": "Cargando...",
					"processing":     "Procesando...",
					"search":         "Filtrar:",
					"zeroRecords":    "Ningún registro encontrado",
					"paginate": {
						"first":      "Primero",
						"last":       "Último",
						"next":       "Siguiente",
						"previous":   "Anterior"
					},
					"aria": {
						"sortAscending":  ": Activar para ordenar la columna ascendente",
						"sortDescending": ": Activar para ordenar la columna descendente"
					}
				},
                buttons: []

            });

        });

    </script>
<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd --></html>
<?php sqlsrv_close($conexion);?>