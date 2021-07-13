// JavaScript Document
function ActualizarPlantillaEmail(id){
	var TipoNot=document.getElementById("TipoNotificacion_"+id).value;
	var Asunto=document.getElementById("Asunto_"+id).value;
	var Mensaje=document.getElementById("Mensaje_"+id).value;
	var Estado=document.getElementById("Estado_"+id);
	var EstadoChk=2;
	if(Estado.checked){
		EstadoChk=1;
	}else{
		EstadoChk=2;
	}
	$("#MsgOkEmail_"+id).hide("fast");
	//alert(Mensaje);
	$.ajax({
		type: "GET",
		url: "registro.php?P=12&ID="+id+"&Estado="+EstadoChk+"&TipoNot="+TipoNot+"&Asunto="+Asunto+"&Mensaje="+Mensaje,
		success: function(response){
			// Bind normal buttons
			if(response=="E1"){//Error
				//alert(response);
				//Validar.innerHTML="<p class='text-danger'>Error al actualizar</p>";
			}else if(response=="OK"){
				$("#MsgOkEmail_"+id).show("slow");
			}
		}
	});	
}