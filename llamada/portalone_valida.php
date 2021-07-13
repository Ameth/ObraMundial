<?php 
if(isset($_GET['type'])&&($_GET['type']!="")){
  $type=0;
if($_GET['type']==1){//Realizar llamada
  $type=$_GET['type'];
  $ext=base64_decode($_GET['ext']);
  $dest=base64_decode($_GET['dest']);
  $etiq=date('Y/m/d/').base64_decode($_GET['etiq']);
  /*if(strlen($dest)==14){
    $dest2=substr($dest,6,8);
    }elseif(strlen($dest)==17){
      $dest2=substr($dest,7,10);
      }else{
        $dest2=$dest;
        }*/
$webcall=$ext.date("YmdHis");
$callfile="Channel:SIP/$ext
CallerId:Webcall $dest <$ext>
MaxRetries:0
WaitTime:30
Setvar: etiq=$etiq
Context:WebCallBoton
Extension:$dest
Priority:1";
$fh=fopen("/var/spool/asterisk/outgoing/".$webcall.".call",'w');
fwrite($fh,$callfile);
fclose($fh);
}
?>
<!doctype html>
<html>
<head>
<style type="text/css">
  .ibox-content{
    border:none;
    border-style: none;
    border-width: 0px;
    box-sizing: border-box;
    clear: both;
    background-color: #ffffff;
    color: inherit;
    padding: 15px 20px 20px 20px;
    border-color: #e7eaec;
  }
body{
  font-family: "open sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 13px;
}
h2{
  margin-top: 5px;
  font-size: 24px;
  font-weight: 100;
  margin-bottom: 10px;
  font-family: inherit;
}
.text-success{
  color: #2dae1e;
  font-weight: 600;
  font-family: inherit;
  line-height: 1.1;
  box-sizing: border-box;
}
.text-primary{
  color: #ed5565;
  font-family: inherit;
  font-weight: 100;
  line-height: 1.1;
  box-sizing: border-box;
}
</style>
</head>

<body>
  <p align="center">
    <div class="ibox-content">
      <?php if($type==1){?>
        <h2><span class="text-success">Mi extensi&oacute;n: </span><span class="text-primary"><?php echo $ext; ?></span></h2>
      <?php }?>
    </div>
  </p>
</body>
</html>
<?php } ?>