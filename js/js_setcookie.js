// JavaScript Document
function AceptarAcuerdo(){
	$.ajax({
		type: "GET",
		url: "registro.php?P=19",
		success: function(response){
			//alert(response);
			if(response=="E1"){//Error
				alert("Ha ocurrido un error al aceptar el acuerdo");
			}
		}
	});	
}