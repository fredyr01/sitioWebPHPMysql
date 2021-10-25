<?php
    session_start();

    if($_POST){
        if(($_POST['usuario'] == 'fredy') && ($_POST['contrasenia'] == "sistema")){
            $_SESSION['usuario'] = "ok";
            $_SESSION['nombreUsuario'] = "Fredy";
            header('Location:inicio.php');
        } else{
            $mensaje = "Error: El usuario o contraseña son incorrectos";
        }
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Administrador</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.0.2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">

  </head>
  <body>
    <div class="container">
        <div class="row">
            <div class="col-md-4">

            </div>
            <div class="col-md-4">
                <br><br><br>
                <div class="card">
                    <div class="card-header">
                        Login
                    </div>
                    <div class="card-body">
                        <?php if (isset($mensaje)){ ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $mensaje; ?>
                        </div>
                        <?php } ?>
                        <form method="POST">
                        <div class = "form-group">
                        <label>Usuario</label>
                        <input type="text" class="form-control" name="usuario" placeholder="Ingrese su usuario">

                        </div>
                        <div class="form-group mt-2">
                        <label>Contraseña:</label>
                        <input type="password" class="form-control" name="contrasenia" placeholder="Ingrese su contraseña">
                        </div>


                        <button type="submit" class="btn btn-primary mt-2">Entrar</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>