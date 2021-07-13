<?php require_once("includes/conexion.php");
PermitirAcceso(101);
$sw=0;//Verificar que hay datos
$And=0;//Agregar mas filtros a la busqueda
$Filtro="";
if(isset($_GET['Buscar'])&&$_GET['Buscar']!=""){
	$Filtro="Where (NombreCongregacion LIKE '%".$_GET['Buscar']."%' Or Ciudad LIKE '%".$_GET['Buscar']."%')";
	$And=1;
}

$Cons="Select * From uvw_tbl_Congregaciones $Filtro";
$SQL=sqlsrv_query($conexion,$Cons);

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include_once("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Gestionar congregación | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<?php 
if(isset($_GET['a'])&&($_GET['a']==base64_encode("OK_Cong"))){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'La congregación ha sido agregada exitosamente.',
                icon: 'success'
            });
		});		
		</script>";
}
if(isset($_GET['a'])&&($_GET['a']==base64_encode("OK_EditCong"))){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'La congregación ha sido editada exitosamente.',
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

    <?php include_once("includes/menu.php"); ?>

    <div id="page-wrapper" class="gray-bg">
        <?php include_once("includes/menu_superior.php"); ?>
        <!-- InstanceBeginEditable name="Contenido" -->
        <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-8">
                    <h2>Gestionar congregación</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Administraci&oacute;n</a>
                        </li>
                        <li class="active">
                            <strong>Gestionar congregación</strong>
                        </li>
                    </ol>
                </div>
                 <div class="col-sm-4">
                    <div class="title-action">
                        <a href="congregacion.php" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Agregar congregación</a>
                    </div>
                </div>
            </div>           
        <div class="wrapper wrapper-content">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox-content">
					 <?php include("includes/spinner.php"); ?>
					<form action="gestionar_congregacion.php" method="get" id="formBuscar" class="form-horizontal">
						 <div class="form-group">
							<label class="col-xs-12"><h3 class="bg-success p-xs b-r-sm"><i class="fa fa-filter"></i> Datos para filtrar</h3></label>
						</div>
						<div class="form-group">
							<div class="col-lg-1"></div>
							<div class="col-lg-4">
								<div class="input-group form-group has-success">
									<input name="Buscar" type="text" class="form-control" id="Buscar" placeholder="Buscar datos..." value="<?php if(isset($_GET['Buscar'])&&($_GET['Buscar']!="")){ echo $_GET['Buscar'];}?>"><span class="input-group-btn"><button type="submit" class="btn btn-primary">Buscar</button></span>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
         <br>
          <div class="row">
           <div class="col-lg-12">
			<div class="ibox-content">
				 <?php include("includes/spinner.php"); ?>
           <div class="table-responsive">
			 <table class="table table-striped table-bordered table-hover dataTables-example" >
				<thead>
				<tr>
					<th>Número</th>
					<th>Nombre congregación</th>
					<th>Ciudad</th>
					<th>Municipio</th>
					<th>Pais</th>
					<th>Dirección</th>
					<th>Correo JW</th>
					<th>Fecha creación</th>
					<th>Usuario creación</th>
					<th>Acciones</th>
				</tr>
				</thead>
				<tbody>
				<?php while($row=sqlsrv_fetch_array($SQL)){?>
				<tr>
					<td><?php echo $row['NumCong'];?></td>
					<td><?php echo $row['NombreCongregacion'];?></td>
					<td><?php echo $row['Ciudad'];?></td>
					<td><?php echo $row['Municipio'];?></td>
					<td><?php echo $row['Pais'];?></td>
					<td><?php echo $row['Direccion'];?></td>
					<td><?php echo $row['CorreoJW'];?></td>
					<td><?php echo $row['FechaCreacion']->format('Y-m-d');?></td>
					<td><?php echo $row['NombreUsuario'];?></td>
                    <td><a href="congregacion.php?id=<?php echo base64_encode($row['NumCong']);?>&tl=1" class="alkin btn btn-success btn-xs" title="Editar"><i class="fa fa-edit"></i> Editar</a></td>
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
        <?php include_once("includes/footer.php"); ?>

    </div>
</div>
<?php include_once("includes/pie.php"); ?>
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
				order: [[ 0, "desc" ]],
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