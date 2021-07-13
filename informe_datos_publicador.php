<?php require_once("includes/conexion.php");
PermitirAcceso(404);

$sw=0;

//Filtros
$Filtro="";//Filtro
$Grupo="";

if(isset($_GET['Grupo'])){
	$Filtro.=" and IDGrupo='".$_GET['Grupo']."'";
	$Grupo=$_GET['Grupo'];
	$sw=1;
}

//Grupos de congregacion
if(PermitirFuncion(205)){
	$SQL_Grupos=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."' and IDGrupo='".$_SESSION['Grupo']."'",'NombreGrupo');
}else{		
	$SQL_Grupos=Seleccionar('uvw_tbl_Grupos','*',"NumCong='".$_SESSION['NumCong']."'",'NombreGrupo');
}

if($sw==1){
	$SQL=Seleccionar('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' and IdGrupo='".$Grupo."'",'NombrePublicador');
	$SQLCons=ReturnCons('uvw_tbl_Publicadores','*',"NumCong='".$_SESSION['NumCong']."' and IdGrupo='".$Grupo."'",'NombrePublicador');
}

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Datos de los publicadores | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
	$(document).ready(function() {//Cargar los combos dependiendo de otros
		
	});
</script>
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
                    <h2>Datos de los publicadores</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Reportes</a>
                        </li>
                        <li class="active">
                            <strong>Datos de los publicadores</strong>
                        </li>
                    </ol>
                </div>
               <?php  //echo $Cons;?>
            </div>
         <div class="wrapper wrapper-content">
             <div class="row">
				<div class="col-lg-12">
			    <div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
				  <form action="informe_datos_publicador.php" method="get" id="formBuscar" class="form-horizontal">
					   <div class="form-group">
							<label class="col-xs-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-filter"></i> Datos para filtrar</h3></label>
						</div>
					 	<div class="form-group">
							<label class="col-lg-1 control-label">Grupo</label>
							<div class="col-lg-3">
								<select name="Grupo" class="form-control m-b" id="Grupo">
									<?php if(!PermitirFuncion(205)){?><option value="">(Todos)</option><?php }?>
								  <?php while($row_Grupos=sqlsrv_fetch_array($SQL_Grupos)){?>
										<option value="<?php echo $row_Grupos['IDGrupo'];?>" <?php if((isset($_GET['Grupo']))&&(strcmp($row_Grupos['IDGrupo'],$_GET['Grupo'])==0)){ echo "selected=\"selected\"";}?>><?php echo $row_Grupos['NombreGrupo'];?></option>
								  <?php }?>
								</select>
							</div>
							<div class="col-lg-1">
								<button type="submit" class="btn btn-outline btn-success pull-right"><i class="fa fa-search"></i> Consultar</button>
							</div>							
						</div>
					  <input type="hidden" id="MM_Buscas" name="MM_Buscar" value="1">
				 </form>
			</div>
			</div>
		  </div>
         <br>
		<?php if($sw==1){?>
		 <div class="row">
           <div class="col-lg-12">
			    <div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
				<div class="row m-b-md">
					<div class="col-lg-12">
						<a href="rpt_datos_publicadores.php?id=<?php echo base64_encode($_SESSION['CodUser']);?>&grp=<?php echo base64_encode($Grupo);?>" target="_blank" class="btn btn-outline btn-danger"><i class="fa fa-file-pdf-o"></i> Descargar en PDF</a>
						<a href="exportar_excel.php?exp=13&Cons=<?php echo base64_encode($SQLCons);?>" class="btn btn-outline btn-primary"><i class="fa fa-file-excel-o"></i> Exportar a Excel</a>
					</div>
				</div>
				<div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example" >
                    <thead>
                    <tr>
						<th>Nombre</th>
						<th>Apellidos</th>
						<th>Genero</th>
						<th>Fecha nacimiento</th>
						<th>Fecha bautismo</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
						<th>Celular</th>
						<th>Privilegio</th>
						<th>Anciano/SM</th>
						<th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while($row=sqlsrv_fetch_array($SQL)){?>
						<tr>
							<td><?php echo $row['Nombre']." ".$row['SegundoNombre'];?></td>
							<td><?php echo $row['Apellido']." ".$row['SegundoApellido'];?></td>
							<td><?php echo $row['NombreGenero'];?></td>
							<td><?php echo ($row['FechaNac']!="") ? $row['FechaNac']->format('Y-m-d') : "";?></td>
							<td><?php echo ($row['FechaBaut']!="") ? $row['FechaBaut']->format('Y-m-d') : "";?></td>
							<td><?php echo $row['Direccion'];?></td>
							<td><?php echo $row['Telefono'];?></td>
							<td><?php echo $row['Celular'];?></td>
							<td><?php echo $row['TipoPublicador'];?></td>
							<td><?php echo $row['PrivilegioServicioAbr'];?></td>													
							<td><span <?php if($row['IDEstado']=='1'){echo "class='label label-info'";}else{echo "class='label label-danger'";}?>><?php echo $row['NombreEstado'];?></span></td>
						</tr>
					<?php }?>
                    </tbody>
                    </table>
              </div>
			</div>
			 </div> 
          </div>
		<?php }?>
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

			
			$(".select2").select2();
			
			$('.dataTables-example').DataTable({
                pageLength: 25,
                dom: '<"html5buttons"B>lTfgitp',
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