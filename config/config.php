<?php
/*--  definicion de variables a utilizar--*/
define("CLIENT_ID", "ATIWT66uzNGb-EPu1w6YA-ozoGCB-ZplPOvCJxiZMv3J0JETqudmCL62-rOlgH6g6Xr1chKp_j7Rlzuw");
define("KEY_TOKEN", "APR.wqc-354*");
define("MONEDA", "$");

/*--  Inicio de sesion--*/
session_start();

/*--  conteo de carrito --*/
$num_cart =0;
if(isset($_SESSION['carrito']['productos'])){
    $num_cart = count($_SESSION['carrito']['productos']);
}
?>