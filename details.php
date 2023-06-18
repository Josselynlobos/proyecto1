<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if($id == '' | $token == ''){
    echo 'Eror al procesar la peticion que desea';
    exit;
}else{
    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    if($token == $token_tmp){

        $sql = $db->con->prepare("SELECT count(id) FROM producto WHERE id=? AND activo = 1");
        $sql->execute([$id]);
        if($sql->fetchColumn()> 0){
            $sql = $db->con->prepare("SELECT nombre, descripcion, precio, kilometraje FROM producto WHERE id=? AND activo = 1
            LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $nombre =$row ['nombre'];
            $descripcion =$row ['descripcion'];
            $precio =$row ['precio'];
            $kilometraje=$row ['kilometraje'];
            $dir_images= 'imagenes/productos/'.$id.'/';

            $rutaImg =$dir_images . '1.jpg';

            if (!file_exists($rutaImg)) {
                $rutaImg = 'imagenes/no-photo.jpg';
            }
            
            $imagenes = array();
            if(file_exists($dir_images)) {
                $dir = dir($dir_images);
                
                while (($archivo = $dir->read()) !== false) {
                    if ($archivo != '1.jpg' && (strpos($archivo, 'jpg') !== false || strpos($archivo, 'jpeg') !== false)) {
                        $imagenes[] = $dir_images . $archivo;
                    }
                }
                
                $dir->close();
            }
        }

    }else{
        echo 'Eror al procesar la peticion que desea';
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
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
            <div class="row">
                <div class="col-md-6 order-md-1">
                    <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="<?php echo $rutaImg; ?>" width="500" height="auto" class="d-block w-100">
                            </div>
                            <?php foreach($imagenes as $img ) {?>
                                <div class="carousel-item">
                                    <img src="<?php echo $img; ?>" width="500" height="auto" class="d-block w-100">
                                </div>
                            <?php } ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 order-md-2">
                    <h2><?php echo $nombre;?></h2>
                    <h2><?php echo MONEDA . number_format($precio, 2, '.', ',');?></h2>
                    <h2>El kilometraje es de: <?php echo $kilometraje ?></h2>
                    <p class="lead">
                        <?php echo $descripcion ?>
                    </p>
                    <div class="d-grid gap-3 col-10 mx-auto">
                        <button class="btn btn-primary" type="button">Comprar ahora</button>
                        <button class="btn btn-outline-primary" type="button"onclick="addProducto(<?php echo $id;?>,'<?php echo $token_tmp?>')">Agregar al carrito</button>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" 
    crossorigin="anonymous"></script>
    <script>
        function addProducto(id, token){
            let url = 'clases/carrito.php'
            let formData = new FormData()
            formData.append('id', id)
            formData.append('token', token)

            fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json())
            .then(data=>{
                if(data.ok){
                    let elemento = document.getElementById("num_cart")
                    elemento.innerHTML = data.numero
                }
            })

        }
    </script>
</body>
</html>