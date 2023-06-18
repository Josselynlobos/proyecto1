<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$db->conectar();
$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null; 

$lista_carrito = array();

if($productos !=null){
    foreach($productos as $clave => $cantidad){

        $sql = $db->con->prepare("SELECT id, nombre, precio,$cantidad AS cantidad FROM producto WHERE
        id=? AND activo = 1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
}else{
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" 
    crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'menu.php'; ?>  
    
    <!--contenido-->
    <main>
        <div class="container">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3"> 
                <div class="col-6">
                    <h4> Detalles de pago</h4>      
                    <div id="paypal-button-container"></div>
                </div>

                <div class="col-6"></div>
                <div class="table-responsive">
                    <table class=" table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Subtotal</th>
                                <th></th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if($lista_carrito == null){
                                echo '<tr><td colspan="5" class="text-center"><b>Lista vacia</b></td></tr>';
                            }else{
                                $total =0;
                                foreach($lista_carrito as $producto){
                                    $_id =  $producto['id'];
                                    $nombre =  $producto['nombre'];
                                    $precio =  $producto['precio'];
                                    $cantidad =  $producto['cantidad'];
                                    $subtotal =  $cantidad * $precio;
                                    $total +=  $subtotal;
    
                            ?>
                            <tr>
                                <td><?php echo $nombre;?></td>
                                <td>
                                    <div id="subtotal_<?php echo $_id;?>" name="subtotal[]"><?php echo MONEDA . number_format($subtotal,2,'.',',');?></div>
                                </td>
                            </tr>

                            <?php } ?>
                            <tr>
                                <td colspan="2">
                                    <p class="h3 text-end" id="total"><?php echo MONEDA . number_format($total,2,'.',',');?></p>
                                </td>
                            </tr>
                        </tbody>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" 
    crossorigin="anonymous"></script>

    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID;?>"></script>

    <script>
        paypal.Buttons({
            style:{
                color: 'blue',
                shape: 'pill',
                label: 'pay'
            },
            createOrder: function(data,actions){
                return actions.order.create({
                    purchase_units:[{
                        amount:{
                            value: <?php echo $total?>
                        }
                    }]
                });
            },
            onApprove: function(data, actions){
                let URL = 'clases/captura.php'
                actions.order.capture().then(function(detalles){
                    console.log(detalles)
                    let url='clases/captura.php'

                    return fetch(url, {
                        method: 'post',
                        headers: {
                            'content-type': 'application/json'
                        },
                        body: JSON.stringify({
                            detalles : detalles
                        })
                    })
                });
            },
            onCancel: function(data){
                alert("Pago cancelado")
                console.log(data);
            }
        }). render('#paypal-button-container')
    </script>

</body>
</html>