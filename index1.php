<?php require_once("includes/conexion.php"); 

//Consultar grupos de la congregacion
$ParamGroup=array(
	"'".$_SESSION['NumCong']."'"
);
$SQL_Grupos=EjecutarSP('usp_ConsultarGruposDasboard',$ParamGroup);
$Num_Grupos=sqlsrv_num_rows($SQL_Grupos);

//Consultar indicadores de la congregacion
$ParamCong=array(
	"'".$_SESSION['NumCong']."'"
);
$SQL_Cong=EjecutarSP('usp_ConsultarCantCongDasboard',$ParamCong);
$row_Cong=sqlsrv_fetch_array($SQL_Cong);

//Periodo mas reciente abierto
$ParamPed=array(
	"'".$_SESSION['NumCong']."'"
);
$SQL_Periodo=EjecutarSP('usp_ConsultarUltimoPeriodoCong',$ParamPed);
$row_Periodo=sqlsrv_fetch_array($SQL_Periodo);

if($row_Periodo['IDPeriodo']!=""){
	$TotalPub=0;
	$TotalPR=0;
	$TotalInf=0;
	$TotalFalt=0;

	if(PermitirFuncion(205)){
		$Param=array(
			"'".$_SESSION['NumCong']."'",
			"'".$row_Periodo['IDPeriodo']."'",
			"'".$_SESSION['Grupo']."'"
		);
	}else{
		$Param=array(
			"'".$_SESSION['NumCong']."'",
			"'".$row_Periodo['IDPeriodo']."'"
		);
	}

	$SQL=EjecutarSP('usp_ConsultarGruposInformes',$Param);
}



?>
<!DOCTYPE html>
<html><!-- InstanceBegin template="/Templates/PlantillaPrincipal.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>
<?php include_once("includes/cabecera.php"); ?>
<!-- InstanceBeginEditable name="doctitle" -->
<title>Inicio | <?php echo NOMBRE_PORTAL;?></title>
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="head" -->
<style>
	#animar{
		animation-duration: 1.5s;
  		animation-name: tada;
  		animation-iteration-count: infinite;
	}
	#animar2{
		animation-duration: 1s;
  		animation-name: swing;
  		animation-iteration-count: infinite;
	}
	#animar3{
		animation-duration: 3s;
  		animation-name: pulse;
  		animation-iteration-count: infinite;
	}
	.edit1 {/*Widget editado por aordonez*/
		border-radius: 0px !important; 
		padding: 15px 20px;
		margin-bottom: 10px;
		margin-top: 10px;
		height: 120px !important;
	}
	.modal-lg {
		width: 50% !important;
	}
</style>
<?php if(!isset($_SESSION['SetCookie'])||($_SESSION['SetCookie']=="")){?>
<script>
$(document).ready(function(){
	$('#myModal').modal("show");
});
</script>
<?php }?>
<!-- InstanceEndEditable -->
</head>

<body>

<div id="wrapper">

    <?php include_once("includes/menu.php"); ?>

    <div id="page-wrapper" class="gray-bg">
        <?php include_once("includes/menu_superior.php"); ?>
        <!-- InstanceBeginEditable name="Contenido" -->
        <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-6">
                    <h2>Bienvenido a <?php echo NOMBRE_PORTAL;?></h2>
                </div>
        </div>
        <?php 
		$Nombre_archivo="contrato_confidencialidad.txt";
		$Archivo=fopen($Nombre_archivo,"r");
		$Contenido = fread($Archivo, filesize($Nombre_archivo));
		?>
        <div class="modal inmodal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-show="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Acuerdo de confidencialidad</h4>
						<small>Por favor lea atentamente este contrato que contiene los T&eacute;rminos y Condiciones de uso de este sitio. Si continua usando este portal, consideramos que usted est&aacute; de acuerdo con ellos.</small>
					</div>
					<div class="modal-body">
						<?php echo $Contenido;?>
					</div>

					<div class="modal-footer">
						<button type="button" onClick="AceptarAcuerdo();" class="btn btn-primary" data-dismiss="modal">Acepto los t&eacute;rminos</button>
					</div>
				</div>
			</div>
		</div>
        <div class="page-wrapper wrapper-content animated fadeInRight">
			<?php if($Num_Grupos>0){?>
			<h3 class="bg-info p-xss b-r-xs"><i class="fa fa-bar-chart-o"></i> Cantidad de publicadores por grupo</h3>
				<div class="row">
				<?php while($row_Grupos=sqlsrv_fetch_array($SQL_Grupos)){?>
				<div class="col-lg-3">
					<div class="ibox ">
						<div class="ibox-title">
							<h5 class="text-success"><?php echo $row_Grupos['NombreGrupo'];?></h5>
						</div>
						<div class="ibox-content">
							<div class="row">
								<div class="col-lg-4">
									<i class="fa fa-user fa-3x"></i>
								</div>
								<div class="col-lg-8 text-right">
									<h1 class="no-margins" id="Grp_run<?php echo $row_Grupos['IDGrupo'];?>">0</h1>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php }?>	
				</div>
			<?php }?>
			<h3 class="bg-primary p-xss b-r-xs"><i class="fa fa-line-chart"></i> Indicadores de la congregación</h3>
				<div class="row">
					<div class="col-lg-3">
						<div class="ibox ">
							<div class="ibox-title">
								<h5 class="text-info">Publicadores</h5>
							</div>
							<div class="ibox-content">
								<div class="row">
									<div class="col-lg-4">
										<i class="fa fa-users fa-3x"></i>
									</div>
									<div class="col-lg-8 text-right">
										<h1 class="no-margins" id="CG_run1">0</h1>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="ibox ">
							<div class="ibox-title">
								<h5 class="text-info">Precursores regulares</h5>
							</div>
							<div class="ibox-content">
								<div class="row">
									<div class="col-lg-4">
										<i class="fa fa-suitcase fa-3x"></i>
									</div>
									<div class="col-lg-8 text-right">
										<h1 class="no-margins" id="CG_run2">0</h1>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="ibox ">
							<div class="ibox-title">
								<h5 class="text-info">Precursores especiales</h5>
							</div>
							<div class="ibox-content">
								<div class="row">
									<div class="col-lg-4">
										<i class="fa fa-suitcase fa-3x"></i>
									</div>
									<div class="col-lg-8 text-right">
										<h1 class="no-margins" id="CG_run3">0</h1>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="ibox ">
							<div class="ibox-title">
								<h5 class="text-info">Prec. Auxiliares el último mes</h5>
							</div>
							<div class="ibox-content">
								<div class="row">
									<div class="col-lg-4">
										<i class="fa fa-child fa-3x"></i>
									</div>
									<div class="col-lg-8 text-right">
										<h1 class="no-margins" id="CG_run4">0</h1>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-3">
						<div class="ibox ">
							<div class="ibox-title">
								<h5 class="text-info">Ancianos</h5>
							</div>
							<div class="ibox-content">
								<div class="row">
									<div class="col-lg-4">
										<i class="fa fa-address-book fa-3x"></i>
									</div>
									<div class="col-lg-8 text-right">
										<h1 class="no-margins" id="CG_run5">0</h1>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="ibox ">
							<div class="ibox-title">
								<h5 class="text-info">Siervos ministeriales</h5>
							</div>
							<div class="ibox-content">
								<div class="row">
									<div class="col-lg-4">
										<i class="fa fa-address-book fa-3x"></i>
									</div>
									<div class="col-lg-8 text-right">
										<h1 class="no-margins" id="CG_run6">0</h1>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		<?php if($row_Periodo['IDPeriodo']!=""){?>
			<div class="row">
				<div class="col-lg-12">
					<h3 class="bg-info p-xss b-r-xs"><i class="fa fa-pencil"></i> Informes del periodo: <?php echo $row_Periodo['CodigoPeriodo']." (".$row_Periodo['NombreMes']."/".$row_Periodo['AnioPeriodo'].")";?></h3>
					<div class="ibox-content">
						<div class="row">
							<div class="col-lg-12 col-md-12">
								<div class="ibox-content">
									<div class="table-responsive">
										<table class="table table-striped table table-hover table-responsive" >
											<thead>
											<tr>
												<th>Grupo</th>
												<th>Estado</th>
												<th>Completado</th>
												<th>Superintendente</th>
												<th>Auxiliar</th>  
												<th>Publicadores</th>  
												<th>Informes ingresados</th>
												<th>Informes faltantes</th>												
												<th>Acciones</th>
											</tr>
											</thead>
										<tbody>
										<?php while($row=sqlsrv_fetch_array($SQL)){
											$TotalPub=$TotalPub+$row['CantPub'];
											//$TotalPR=$TotalPR+$row['CantPR'];
											$TotalFalt=$TotalFalt+$row['CantFalt'];
											$TotalInf=$TotalInf+$row['TotalInf'];
											if($row['CantPub']>0){
												$PrcComp=number_format((($row['TotalInf']/$row['CantPub'])*100),1);
											}else{
												$PrcComp=0;
											}
											?>
											 <tr>
												<td><?php echo $row['NombreGrupo'];?></td>
												<td>
													<?php if($row['CantPub']==$row['CantFalt']){?>
														<span class="badge badge-danger">No empezado</span>
													<?php }elseif(($row['CantPub']>$row['CantFalt'])&&($row['CantFalt']>0)){?>
														<span class="badge badge-warning">Pendientes</span>
													<?php }else{?>
														<span class="badge badge-primary">Completado</span>
													<?php }?>
												</td>
												<td>
													<?php if($row['CantPub']==$row['CantFalt']){?>
														<span data-peity='{"fill": ["#ed5565", "#eeeeee"]}' class="pie"><?php echo $PrcComp."/100";?></span>
														<span class="text-danger"><?php echo "0%";?></span>
													<?php }elseif(($row['CantPub']>$row['CantFalt'])&&($row['CantFalt']>0)){?>
														<span data-peity='{"fill": ["#f8ac59", "#eeeeee"]}' class="pie"><?php echo $PrcComp."/100";?></span>
														<span class="text-warning"><?php echo $PrcComp."%";?></span>
													<?php }else{?>
														<span data-peity='{"fill": ["#1ab394", "#eeeeee"]}' class="pie"><?php echo $PrcComp."/100";?></span>
														<span class="text-navy"><?php echo $PrcComp."%";?></span>
													<?php }?>												
												</td>
												<td><?php echo $row['NombreSuperGrupo'];?></td>
												<td><?php if($row['NombreAuxGrupo']!=""){echo $row['NombreAuxGrupo'];}else{echo "(Ninguno)";};?></td>
												<td><?php echo $row['CantPub'];?></td>
												<td><?php echo $row['TotalInf'];?></td>
												<td <?php if($row['CantFalt']>0){echo "class='text-danger'";}?>><?php echo $row['CantFalt'];?></td>
												<td><a href="informes.php?id=<?php echo base64_encode($row['IDGrupo']);?>&idped=<?php echo base64_encode($row_Periodo['IDPeriodo']);?>&return=<?php echo base64_encode($_SERVER['QUERY_STRING']);?>&pag=<?php echo base64_encode('gestionar_informes.php');?>&tl=1" class="alkin btn btn-success btn-xs"><i class="fa fa-folder-open"></i> Abrir</a></td>
											</tr>
										<?php }?>
											<tr>
												<td colspan="5" class="text-center font-bold">TOTAL</td>
												<td class="font-bold"><?php echo $TotalPub;?></td>
												<td class="font-bold"><?php echo $TotalInf;?></td>
												<td class="font-bold <?php if($TotalFalt>0){echo "text-danger";}?>"><?php echo $TotalFalt;?></td>
												<td>&nbsp;</td>
											</tr>
										</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> 
			</div>
			<br>
			<div class="row">
				<div class="col-lg-12">
					<div class="ibox-content">
						<div class="row">
							<div class="col-lg-12 col-md-12">
								<div class="ibox-content">
									<div id="graph" class="table-responsive"></div>
								</div>
							</div>
						</div>
					</div>
				</div> 
			</div>
		<?php }?>
        </div>
        <!-- InstanceEndEditable -->
        <?php include_once("includes/footer.php"); ?>

    </div>
</div>
<?php include_once("includes/pie.php"); ?>
<!-- InstanceBeginEditable name="EditRegion4" -->
<script>	
	 $(document).ready(function(){
		 $('.navy-bg').each(function() {
                animationHover(this, 'pulse');
            });
		  $('.yellow-bg').each(function() {
                animationHover(this, 'pulse');
            });
		 $('.lazur-bg').each(function() {
                animationHover(this, 'pulse');
            });
		 $(".truncate").dotdotdot({
            watch: 'window'
		  });
		 $("span.pie").peity("pie");
	});
</script>
<script>
<?php if($Num_Grupos>0){ 
	$SQL_Grupos=EjecutarSP('usp_ConsultarGruposDasboard',$ParamGroup);
	while($row_Grupos=sqlsrv_fetch_array($SQL_Grupos)){?>
	var amount=<?php echo $row_Grupos['CantPub'];?>;
		$({c:0}).animate({c:amount},{
			step: function(now){
				$("#Grp_run<?php echo $row_Grupos['IDGrupo'];?>").html(Math.round(now))
			},
			duration:2000,
			easing:"linear"
		});
<?php }
}?>
var amount=<?php echo $row_Cong['CantPub'];?>;
	$({c:0}).animate({c:amount},{
		step: function(now){
			$("#CG_run1").html(Math.round(now))
		},
		duration:2000,
		easing:"linear"
	});
var amount=<?php echo $row_Cong['CantPR'];?>;
	$({c:0}).animate({c:amount},{
		step: function(now){
			$("#CG_run2").html(Math.round(now))
		},
		duration:2000,
		easing:"linear"
	});
var amount=<?php echo $row_Cong['CantPE'];?>;
	$({c:0}).animate({c:amount},{
		step: function(now){
			$("#CG_run3").html(Math.round(now))
		},
		duration:2000,
		easing:"linear"
	});
var amount=<?php echo $row_Cong['CantPAUlt'];?>;
	$({c:0}).animate({c:amount},{
		step: function(now){
			$("#CG_run4").html(Math.round(now))
		},
		duration:1300,
		easing:"linear"
	});
var amount=<?php echo $row_Cong['CantAnc'];?>;
	$({c:0}).animate({c:amount},{
		step: function(now){
			$("#CG_run5").html(Math.round(now))
		},
		duration:1300,
		easing:"linear"
	});
var amount=<?php echo $row_Cong['CantSM'];?>;
	$({c:0}).animate({c:amount},{
		step: function(now){
			$("#CG_run6").html(Math.round(now))
		},
		duration:1300,
		easing:"linear"
	});
</script>
<?php if(isset($_GET['dt'])&&$_GET['dt']==base64_encode("result")){?>
<script>
	$(document).ready(function(){
		toastr.options = {
			closeButton: true,
			progressBar: true,
			showMethod: 'slideDown',
			timeOut: 6000
		};
		toastr.success('¡Su contraseña ha sido modificada!', 'Felicidades');
	});
</script>
<?php }?>
<?php if($row_Periodo['IDPeriodo']!=""){
	$SQL=EjecutarSP('usp_ConsultarGruposInformes',$Param);?>
<script>
Highcharts.chart('graph', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Informes del periodo <?php echo $row_Periodo['CodigoPeriodo']." (".$row_Periodo['NombreMes']."/".$row_Periodo['AnioPeriodo'].")";?>'
    },
    subtitle: {
        text: 'Estado actual de los informes'
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Cantidad'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f}%'
            }
        }
    },

    tooltip: {
        //headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> informado<br/>'
    },

    series: [
        {
            name: "Grupos",
            colorByPoint: true,
			 data: [
			<?php while($row=sqlsrv_fetch_array($SQL)){?>
				  {
					name: "<?php echo $row['NombreGrupo'];?>",
					y: <?php if($row['CantPub']>0){echo number_format((($row['TotalInf']/$row['CantPub'])*100),1); }else{echo "0";} ?>
				  },
			<?php }?>
            ]
        }
    ]
});
</script>
<?php }?>
<script src="js/js_setcookie.js"></script>
<!-- InstanceEndEditable -->
</body>

<!-- InstanceEnd --></html>
<?php sqlsrv_close($conexion);?>