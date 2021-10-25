<?php include("../template/cabecera.php"); ?>

<?php

    $txtID = (isset($_POST['txt_id']))?$_POST['txt_id']:"";
    $txtNombre = (isset($_POST['txt_nombre']))?$_POST['txt_nombre']:"";
    $txtImagen = (isset($_FILES['txt_imagen']['name']))?$_FILES['txt_imagen']['name']:"";
    $accion = (isset($_POST['accion']))?$_POST['accion']:"";

    include("../config/bd.php");

    switch($accion){
        case "agregar":
            $sentencia = $conexion->prepare("INSERT INTO libros (nombre, imagen) VALUES (:nombre, :imagen)");
            $sentencia->bindParam(':nombre',$txtNombre);

            $fecha = new DateTime();
            $nombreArchivo = ($txtImagen != "")?$fecha->getTimestamp()."_".$_FILES["txt_imagen"]["name"]:"imagen.jpg";

            $tmpImagen = $_FILES["txt_imagen"]["tmp_name"];

            if($tmpImagen!=""){
                move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);
            }
            $sentencia->bindParam(':imagen',$nombreArchivo);
            $sentencia->execute();
            header("Location:productos.php");
            break;
        case "modificar":
            // echo "Presionado botón modificar";
            $sentencia = $conexion->prepare('UPDATE libros SET nombre=:nombre WHERE id=:id');
            $sentencia->bindParam(':nombre',$txtNombre);
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();

            if($txtImagen != ""){
            $fecha = new DateTime();
            $nombreArchivo = ($txtImagen != "")?$fecha->getTimestamp()."_".$_FILES["txt_imagen"]["name"]:"imagen.jpg";
            $tmpImagen = $_FILES["txt_imagen"]["tmp_name"];

            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);

            $sentencia = $conexion->prepare('SELECT imagen FROM libros WHERE id=:id');
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            $libro = $sentencia->fetch(PDO::FETCH_LAZY);

            if(isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg")){
                if(file_exists("../../img/".$libro["imagen"])){
                    unlink("../../img/".$libro["imagen"]);
                }
            }

            $sentencia = $conexion->prepare('UPDATE libros SET imagen=:imagen WHERE id=:id');
            $sentencia->bindParam(':imagen',$nombreArchivo);
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            }
            header("Location:productos.php");
            break;
        case "cancelar":
            // echo "Presionado botón cancelar";
            header("Location:productos.php");
            break;
        case "Seleccionar":
            // echo "Presionado botón seleccionar";
            $sentencia = $conexion->prepare('SELECT * FROM libros WHERE id=:id');
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            $libro = $sentencia->fetch(PDO::FETCH_LAZY);
            $txtNombre = $libro['nombre'];
            $txtImagen = $libro['imagen'];
            break;
        case "Borrar":

            $sentencia = $conexion->prepare('SELECT imagen FROM libros WHERE id=:id');
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            $libro = $sentencia->fetch(PDO::FETCH_LAZY);

            if(isset($libro["imagen"]) && ($libro["imagen"]!="imagen.jpg")){
                if(file_exists("../../img/".$libro["imagen"])){
                    unlink("../../img/".$libro["imagen"]);
                }
            }

            // echo "Presionado botón borrar";
            $sentencia = $conexion->prepare('DELETE FROM libros WHERE id=:id');
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            header("Location:productos.php");
            break;
    }

    $sentencia = $conexion->prepare('SELECT * FROM libros');
    $sentencia->execute();
    $listaLibros = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

    <div class="col-md-5">

        <div class="card">

            <div class="card-header">
                Datos del libro
            </div>

            <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class = "form-group">
                <label for="txt_id">ID:</label>
                <input type="text" class="form-control" value="<?php echo $txtID; ?>" id="txt_id" name="txt_id" placeholder="ID" required readonly>
                </div>

                <div class = "form-group mt-2">
                <label for="txt_nombre">Nombre:</label>
                <input type="text" class="form-control" value="<?php echo $txtNombre; ?>" id="txt_nombre" name="txt_nombre" placeholder="Nombre" required>
                </div>

                <div class = "form-group mt-2">
                <label for="txt_imagen">Imagen:</label>
                <br>
                <?php if($txtImagen != ""){?>
                    <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen; ?>" alt="" width="80">
                <?php } ?>
                <input type="file" class="form-control"  id="txt_imagen" name="txt_imagen">
                </div>

                <div class="btn-group mt-2" role="group" aria-label="">
                    <button type="submit" name="accion" <?php echo ($accion == "Seleccionar")? "disabled" : ""; ?> value="agregar" class="btn btn-success mx-2">Agregar</button>
                    <button type="submit" name="accion" <?php echo ($accion != "Seleccionar")? "disabled" : ""; ?> value="modificar" class="btn btn-warning mx-2">Modificar</button>
                    <button type="submit" name="accion" <?php echo ($accion != "Seleccionar")? "disabled" : ""; ?> value="cancelar" class="btn btn-info mx-2">Cancelar</button>
                </div>
            </form>
            </div>
        </div>
       
    </div>

    <div class="col-md-7">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($listaLibros as $libro) { ?>
                <tr>
                    <td><?php echo $libro['id']; ?></td>
                    <td><?php echo $libro['nombre']; ?></td>
                    <td>
                    <img class="img-thumbnail rounded" src="../../img/<?php echo $libro['imagen']; ?>" alt="" width="80">
                    </td>
                    <td>
                        <form method="POST">
                            <input hidden type="text" name="txt_id" id="txt_id" value="<?php echo $libro['id'];?>">
                            <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary">
                            <input type="submit" name="accion" value="Borrar" class="btn btn-danger">
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

<?php include("../template/pie.php"); ?>