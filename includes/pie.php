<script>
	/*function Exec(){
		var Res=0;
		$.ajax({
				url:"ajx_buscar_datos_json.php",
				data:{type:9,user:<?php //echo $_SESSION['CodigoSAP'];?>},
				dataType:'json',
				success: function(data){
					if(data.ID!=""){
						PNotify.desktop.permission();
						PNotify.removeAll();
						(new PNotify({
							title: 'Mensaje de PortalOne',
							text: 'Actividad nueva',
							type: 'info',
							desktop: {
								desktop: true
							}
						}));
					}
				}
			});
		setTimeout("Exec()",5000);
	}*/
	 $(document).ready(function(){
		 $(".alnk").on('click', function(){
			 $('.ibox-content').toggleClass('sk-loading');
		});
		 //setTimeout("Exec()",5000);
	});


	
</script>