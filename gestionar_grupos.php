<?php require_once("includes/conexion.php");
PermitirAcceso(204);

$sw=0;

//Filtros
$Filtro="";//Filtro
if(isset($_GET['BuscarDato'])&&$_GET['BuscarDato']!=""){
	$Filtro.=" and (NombreGrupo LIKE '%".$_GET['BuscarDato']."%' OR Direccion LIKE '%".$_GET['BuscarDato']."%' OR NombreSuperGrupo LIKE '%".$_GET['BuscarDato']."%' OR NombreAuxGrupo LIKE '%".$_GET['BuscarDato']."%')";
	$sw=1;
}
$Cons="Select * From uvw_tbl_Grupos Where NumCong='".$_SESSION['NumCong']."' $Filtro Order by NombreGrupo ASC";
$SQL=sqlsrv_query($conexion,$Cons);
if($sw==1){
	
}
//echo $Cons;

?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Gestionar grupos | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<?php 
if(isset($_GET['a'])&&($_GET['a']==base64_encode("OK_GrpAdd"))){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'El grupo ha sido creado exitosamente.',
                icon: 'success'
            });
		});		
		</script>";
}
if(isset($_GET['a'])&&($_GET['a']==base64_encode("OK_GrpUpd"))){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'El grupo ha sido actualizado exitosamente.',
                icon: 'success'
            });
		});		
		</script>";
}
if(isset($_GET['a'])&&($_GET['a']==base64_encode("OK_GrpDel"))){
	echo "<script>
		$(document).ready(function() {
			Swal.fire({
                title: '¡Listo!',
                text: 'El grupo ha sido eliminado exitosamente.',
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
                    <h2>Grupos</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index1.php">Inicio</a>
                        </li>
                        <li>
                            <a href="#">Congregación</a>
                        </li>
                        <li class="active">
                            <strong>Grupos de predicación</strong>
                        </li>
                    </ol>
                </div>
			<?php if(PermitirFuncion(203)){?>
                <div class="col-sm-4">
                    <div class="title-action">
                        <a href="grupos.php" class="alkin btn btn-primary"><i class="fa fa-plus-circle"></i> Crear nuevo grupo</a>
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
							<label class="col-lg-1 control-label">Buscar dato</label>
							<div class="col-lg-3">
								<input name="BuscarDato" type="text" class="form-control" id="BuscarDato" maxlength="100" value="<?php if(isset($_GET['BuscarDato'])&&($_GET['BuscarDato']!="")){ echo $_GET['BuscarDato'];}?>">
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
						<th>Nombre grupo</th>
						<th>Superintendente</th>  
						<th>Auxiliar</th>  
                        <th>Dirección</th>
                        <th>Creado por</th>
						<th>Fecha creación</th>
						<th>Actualizado por</th>
						<th>Última actualización</th>
						<th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while($row=sqlsrv_fetch_array($SQL)){?>
						 <tr class="gradeX tooltip-demo">
							<td><?php echo $row['NombreGrupo'];?></td>
							<td><?php echo $row['NombreSuperGrupo'];?></td>
							<td><?php if($row['NombreAuxGrupo']!=""){echo $row['NombreAuxGrupo'];}else{echo "(Ninguno)";};?></td>						
							<td><?php echo $row['Direccion'];?></td>
							<td><?php echo $row['NombreUsuarioCreacion'];?></td>
							<td><?php echo $row['FechaCreacion']->format('Y-m-d');?></td>
							<td><?php echo $row['NombreUsuarioActualizacion'];?></td>
							<td><?php echo $row['FechaAct']->format('Y-m-d');?></td>
							<td><a href="grupos.php?id=<?php echo base64_encode($row['IDGrupo']);?>&return=<?php echo base64_encode($_SERVER['QUERY_STRING']);?>&pag=<?php echo base64_encode('gestionar_grupos.php');?>&tl=1" class="alkin btn btn-success btn-xs"><i class="fa fa-edit"></i> Editar</a></td>
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