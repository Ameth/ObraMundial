//Actualizar datos del servidor SMTP
$('#btnActualizarMail').click(function(){
	//alert("Entro aqui 2");
	$("#MsgError").hide("fast");
	$("#MsgOk").hide("fast");

	var Usuario=document.getElementById("Usuario").value;
	var Password=Base64.encode(document.getElementById("Password").value);
	var Servidor=document.getElementById("Servidor").value;
	var Puerto=document.getElementById("Puerto").value;
	var ReqAut=document.getElementById("ReqAut");
	var ReqAut_value=0;
	var TypeCon=document.getElementById("TypeCon").value;
	if(ReqAut.checked){
		ReqAut_value=1;
	}else{
		ReqAut_value=0;
	}
	$.ajax({
		type: "GET",
		url: "registro.php?P=10&Usuario="+Usuario+"&Password="+Password+"&Servidor="+Servidor+"&Puerto="+Puerto+"&ReqAut="+ReqAut_value+"&TypeCon="+TypeCon,
		success: function(response){
			if(response=="E1"){//Error
				//alert(response);
				$("#MsgError").show("slow");
			}else if(response=="OK"){
				$("#MsgOk").show("slow");
			}
		}
	});	
});

function simpleLoad(btn, state){
	var spinner=document.getElementById('spinner1');
	var boton=document.getElementById("Probar");
	var validar=document.getElementById("Validar");
		if (state) {
			btn.children().addClass('fa-spin');
			btn.contents().last().replaceWith(" Enviando...");
			boton.disabled = true;
			validar.innerHTML="";
			spinner.style.display='block';				
		} else {
			//setTimeout(function () {
				btn.children().removeClass('fa-spin');
				btn.contents().last().replaceWith(" Probar configuración");
				boton.disabled = false;
				//validar.innerHTML="<p class='text-info'><i class='fa fa-thumbs-up'></i> Exitoso</p>";
				spinner.style.display='none';
			//}, 2000);
		}
	}

//Probar datos del servidor SMTP
$('#Probar').click(function () {
	//alert('Hola');
	btn = $(this);
	simpleLoad(btn, true);
	var spinner=document.getElementById('spinner1');
	var boton=document.getElementById("Probar");
	var validar=document.getElementById("Validar");
	var Usuario=document.getElementById("Usuario").value;
	var Password=Base64.encode(document.getElementById("Password").value);
	var Servidor=document.getElementById("Servidor").value;
	var Puerto=document.getElementById("Puerto").value;
	var ReqAut=document.getElementById("ReqAut").value;
	var TypeCon=document.getElementById("TypeCon").value;
	
	$.ajax({
		type: "GET",
		url: "mail.php?MM_Mail=mail&Usuario="+Usuario+"&Password="+Password+"&Servidor="+Servidor+"&Puerto="+Puerto+"&ReqAut="+ReqAut+"&TypeCon="+TypeCon,
		success: function(response){
			if(response!="MOK"){//Error
				simpleLoad(btn, false);
				validar.innerHTML="<p class='text-danger'>"+response+"</p>";
			}else if(response=="MOK"){
				simpleLoad(btn, false);
				validar.innerHTML="<p class='text-info'><i class='fa fa-thumbs-up'></i> Exitoso</p>";
			}
		}
	});
});
	
	
	
	