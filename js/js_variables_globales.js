// JavaScript Document
function ActualizarVariable(id){
	var Validar=document.getElementById("Validar_Var_"+id);
	var Valor=document.getElementById("VarGlobal_"+id).value;
	Validar.innerHTML="";
	//alert(id);
	$.ajax({
		type: "GET",
		url: "registro.php?P=11&ID="+id+"&Valor="+Valor,
		success: function(response){
			// Bind normal buttons
			if(response=="E1"){//Error
				//alert(response);
				Validar.innerHTML="<p class='text-danger'>Error al actualizar</p>";
			}else if(response=="OK"){
				Validar.innerHTML="<p class='text-info'><i class='fa fa-thumbs-up'></i> Actualizado</p>";
			}
		}
	});	
}