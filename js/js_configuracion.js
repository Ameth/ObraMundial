// JavaScript Document
function ActualizarDatosPortal(id){
	//alert('Entro');
	var Validar=document.getElementById("Result_"+id);
	var Valor=document.getElementById("Dato_"+id).value;
	Validar.innerHTML="";
	//alert(id);
	$.ajax({
		type: "GET",
		url: "registro.php?P=14&ID="+id+"&Valor="+Valor,
		success: function(response){
			//alert(response);
			if(response=="E1"){//Error
				//alert(response);
				Validar.innerHTML="<p class='text-danger'>Error al actualizar</p>";
			}else if(response=="OK"){
				Validar.innerHTML="<p class='text-info'><i class='fa fa-thumbs-up'></i> Actualizado</p>";
			}
		}
	});	
}

function CargarLogoEmpresa(){
	//alert('Entro');
	self.name='opener';
	remote=open('cargar_imagen.php?id=1','remote','width=400,height=150,location=no,scrollbar=yes,menubars=no,toolbars=no,resizable=yes,fullscreen=no,status=yes');
	remote.focus();
}

function CargarLogoSlimEmpresa(){
	//alert('Entro');
	self.name='opener';
	remote=open('cargar_imagen.php?id=2','remote','width=400,height=150,location=no,scrollbar=yes,menubars=no,toolbars=no,resizable=yes,fullscreen=no,status=yes');
	remote.focus();
}

function CargarFavicon(){
	//alert('Entro');
	self.name='opener';
	remote=open('cargar_imagen.php?id=3','remote','width=400,height=150,location=no,scrollbar=yes,menubars=no,toolbars=no,resizable=yes,fullscreen=no,status=yes');
	remote.focus();
}

function CargarFondo(){
	//alert('Entro');
	self.name='opener';
	remote=open('cargar_imagen.php?id=4','remote','width=400,height=150,location=no,scrollbar=yes,menubars=no,toolbars=no,resizable=yes,fullscreen=no,status=yes');
	remote.focus();
}

function ActualizarLogo(id){
	//alert('Entro');
	var Validar=document.getElementById("Result_"+id);
	Validar.innerHTML="";
	//alert(id);
	$.ajax({
		type: "GET",
		url: "registro.php?P=15&ID="+id,
		success: function(response){
			//alert(response);
			if(response=="E1"){//Error
				//alert(response);
				Validar.innerHTML="<p class='text-danger'>Error al actualizar</p>";
			}else if(response=="OK"){
				Validar.innerHTML="<p class='text-info'><i class='fa fa-thumbs-up'></i> Actualizado</p>";
			}
		}
	});	
}
