<?php

/*--  Validar parametros --*/
function esNulo(array $parametros){
foreach($parametros as $parametro)
    if(strlen(trim(($parametro))<1)){
        return true;
    }
    return false;
}

/*--  Validar email --*/
function esEmail($email){
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        return true;
    }
return false;
}

/*--  Validar contraseña--*/
function validaPassword($password, $repassword){
    if(strcmp($password, $repassword) !==0){
        return false;
    }
return true;
}

/*--  Generar TOKEN --*/
function generarToken(){
    return md5(uniqid(mt_rand(), false));
}

/*--  Registrar al cliente --*/
function registraCliente(array $datos, $con){
    $sql = $con->prepare("INSERT INTO cliente (nombres, apellidos, email, telefono, dni, estatus, fecha_alta) VALUES (?, ?, ?, ?, ?, 1, now())");
    if($sql->execute($datos)){
        return $con->lastInsertId();
    }
    return 0;
}

/*--  Registrar su usuario --*/
function registraUsuario(array $datos, $con){
    $sql = $con->prepare("INSERT INTO usuarios (usuario, password, token, id_cliente) VALUES (?, ?, ?, ?)");
    try {
        if ($sql->execute($datos)) {
            return true;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    return false;
}

/*--  Verificar que el usurio ya existe --*/
function usuarioExiste($usuario, $con){
    $sql = $con->prepare("SELECT id FROM usuarios WHERE usuario LIKE ? LIMIT 1 ");
    $sql->execute([$usuario]);
    if($sql->fetchColumn()>0){
        return true;
    }
    return false;
}

/*--  Verificar que el email ya existe --*/
function emailExiste($email, $con){
    $sql = $con->prepare("SELECT id FROM cliente WHERE email LIKE ? LIMIT 1 ");
    $sql->execute([$email]);
    if($sql->fetchColumn()>0){
        return true;
    }
    return false;
}

/*--  Mostrar mensajes --*/
function mostrarMensajes(array $errors){
    if(count($errors) > 0 ){
        echo'<div class="alert alert-warning alert-dismissible fade show" role="alert"><ul>';
        foreach($errors as $error){
            echo '<li>'. $error. '</li>';
        }
        echo '</ul>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}
/*--  Usuario existe y contraseña es corecta --*/
function login($usuario, $password, $con, $proceso){
    $sql = $con->prepare("SELECT id, usuario, password, id_cliente FROM usuarios WHERE usuario LIKE ? limit 1");
    $sql->execute([$usuario]);
    if($row = $sql->fetch(PDO::FETCH_ASSOC)){
        if(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['usuario'];
            $_SESSION['user_cliente'] = $row['id_cliente'];
            if($proceso == 'pago'){
                header("Location: checkout.php");
            }else{
                header("Location: index.php");
            }
            exit;
        }else{
            return 'No es la misma contraseña.';
        }
    }

    return 'El usuario no existe.';
}

/*--  Solicitar password --*/
function solicitaPassword($user_id, $con){
    $token= generarToken();
    $sql = $con->prepare("UPDATE usuarios SET token_password=?, password_request=1 WHERE id=?");
    if($sql->execute($token, $user_id)){
        return $token;
    }
    return null;
}
?>
