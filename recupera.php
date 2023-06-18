<?php

require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

/*--  Conexion BD--*/
$db = new Database();
$con = $db->conectar();

$errors = [];
if(!empty($_POST)){
    
    $email = trim($_POST['email']);
    
    /*--  Validaciones--*/

    if(esNulo([$email])){
        $errors[]="Debe llenar todos los campos";
    }
    if(!esEmail($email)){
        $errors[]="La direccion de correo no es validad";
    }
    if(count($errors)==0){
        if(emailExiste($email, $con)){
            $sql = $con->prepare("SELECT usuarios.id, cliente.nombres FROM usuarios
            INNER JOIN cliente ON usuarios.id_cliente=cliente.id
            WHERE cliente.email LIKE ? LIMIT 1");
            $sql->execute([$email]);
            $row =$sql->fetch(PDO::FETCH_ASSOC);
            $user_id = $row['id'];
            $nombres = $row['nombres'];

            $token= solicitaPassword($user_id, $con);

            if($token !==null){
                require 'clases/Mailer.php'; //faltaa
            }

        }
    }
    
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
    
</head>
<body>
    <header>
    
        <div class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand">
                    <strong>Tienda Online</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false"
                aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

            </div>
        </div>
    </header>   
    
    <!--contenido-->
    <main class="form-login pt-4 container text-center" style="max-width: 350px">
        <h3>Recuperar contraseña</h3>

        <?php mostrarMensajes($errors); ?>

        <form action="recupera.php" method="post"class="row g-3" autocomplete="off">
            <div class="form-floating">
                <input class="form-control" type="email" name="email" id="email" placeholder="Correo electronico" required>
                <label form="email">Correo electronico</label>
            </div>
            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Continuar</button>
            </div>
            <div class="col-12">
                ¿No tiene cuenta? <a href="registro.php">Registrate aqui</a>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" 
    crossorigin="anonymous">
    </script>

    
</body>
</html>