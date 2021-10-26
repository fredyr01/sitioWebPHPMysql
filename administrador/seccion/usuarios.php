<?php include("../template/cabecera.php"); ?>

<?php

    $txtID = (isset($_POST['txt_id']))?$_POST['txt_id']:"";
    $txtDoc = (isset($_POST['txt_doc']))?$_POST['txt_doc']:"";
    $txtNom = (isset($_POST['txt_nombre']))?$_POST['txt_nombre']:"";
    $txtApe = (isset($_POST['txt_apellido']))?$_POST['txt_apellido']:"";
    $txtCel = (isset($_POST['txt_celular']))?$_POST['txt_celular']:"";
    $txtFec = (isset($_POST['txt_fechaNac']))?$_POST['txt_fechaNac']:"";
    $txtCargo = (isset($_POST['txt_cargo']))?$_POST['txt_cargo']:"";
    $txtImagen = (isset($_FILES['txt_imagen']['name']))?$_FILES['txt_imagen']['name']:"";
    $accion = (isset($_POST['accion']))?$_POST['accion']:"";

    include("../config/bd.php");

    switch($accion){

        case "agregar":
            $sentencia = $conexion->prepare("INSERT INTO usuarios (documento, nombres, apellidos, celular, fecha_nacimiento, cargo, foto) VALUES (:documento, :nombres, :apellidos, :celular, :fecha_nacimiento, :cargo, :foto)");
            $sentencia->bindParam(':documento',$txtDoc);
            $sentencia->bindParam(':nombres',$txtNom);
            $sentencia->bindParam(':apellidos',$txtApe);
            $sentencia->bindParam(':celular',$txtCel);
            $sentencia->bindParam(':fecha_nacimiento',$txtFec);
            $sentencia->bindParam(':cargo',$txtCargo);
            $fecha = new DateTime();
            $nombreArchivo = ($txtImagen != "")?$fecha->getTimestamp()."_".$_FILES["txt_imagen"]["name"]:"imagen.jpg";

            $tmpImagen = $_FILES["txt_imagen"]["tmp_name"];

            if($tmpImagen!=""){
                move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);
            }
            $sentencia->bindParam(':foto',$nombreArchivo);
            $sentencia->execute();
            header("Location:usuarios.php");
            break;
        case "modificar":
            $sentencia = $conexion->prepare('UPDATE usuarios SET nombres=:nombres, apellidos=:apellidos, celular=:celular, fecha_nacimiento=:fecha_nacimiento, cargo=:cargo WHERE id=:id');
            $sentencia->bindParam(':nombres',$txtNom);
            $sentencia->bindParam(':apellidos',$txtApe);
            $sentencia->bindParam(':celular',$txtCel);
            $sentencia->bindParam(':fecha_nacimiento',$txtFec);
            $sentencia->bindParam(':cargo',$txtCargo);
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();

            if($txtImagen != ""){
            $fecha = new DateTime();
            $nombreArchivo = ($txtImagen != "")?$fecha->getTimestamp()."_".$_FILES["txt_imagen"]["name"]:"imagen.jpg";
            $tmpImagen = $_FILES["txt_imagen"]["tmp_name"];

            move_uploaded_file($tmpImagen,"../../img/".$nombreArchivo);

            $sentencia = $conexion->prepare('SELECT foto FROM usuarios WHERE id=:id');
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            $usuario = $sentencia->fetch(PDO::FETCH_LAZY);

            if(isset($usuario["foto"]) && ($usuario["foto"]!="imagen.jpg")){
                if(file_exists("../../img/".$usuario["foto"])){
                    unlink("../../img/".$usuario["foto"]);
                }
            }

            $sentencia = $conexion->prepare('UPDATE usuarios SET foto=:foto WHERE id=:id');
            $sentencia->bindParam(':foto',$nombreArchivo);
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            }
            header("Location:usuarios.php");
            break;
            break;
        case "cancelar":
            header("Location:usuarios.php");
            break;
        case "Seleccionar":
            $sentencia = $conexion->prepare('SELECT * FROM usuarios WHERE id=:id');
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            $usuario = $sentencia->fetch(PDO::FETCH_LAZY);
            $txtDoc = $usuario['documento'];
            $txtNom = $usuario['nombres'];
            $txtApe = $usuario['apellidos'];
            $txtCel = $usuario['celular'];
            $txtFec = $usuario['fecha_nacimiento'];
            $txtCargo = $usuario['cargo'];
            $txtImagen = $usuario['foto'];
            break;
        case "Borrar":
            $sentencia = $conexion->prepare('SELECT foto FROM usuarios WHERE id=:id');
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            $usuario = $sentencia->fetch(PDO::FETCH_LAZY);

            if(isset($usuario["foto"]) && ($usuario["foto"]!="imagen.jpg")){
                if(file_exists("../../img/".$usuario["foto"])){
                    unlink("../../img/".$usuario["foto"]);
                }
            }
            $sentencia = $conexion->prepare('DELETE FROM usuarios WHERE id=:id');
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            header("Location:usuarios.php");
            break;
    }

    $sentencia = $conexion->prepare('SELECT * FROM usuarios');
    $sentencia->execute();
    $listaUsuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Datos del usuario
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class = "form-group">
                    <label for="txt_id">ID:</label>
                    <input type="text" class="form-control" value="<?php echo $txtID; ?>" id="txt_id" name="txt_id" placeholder="ID" required readonly>
                    </div>

                    <div class = "form-group mt-2">
                    <label for="txt_doc">Número documento:</label>
                    <input type="number" class="form-control" value="<?php echo $txtDoc; ?>" id="txt_doc" name="txt_doc" placeholder="Número de documento" required>
                    </div>

                    <div class = "form-group mt-2">
                    <label for="txt_nombre">Nombres:</label>
                    <input type="text" class="form-control" value="<?php echo $txtNom; ?>" id="txt_nombre" name="txt_nombre" placeholder="Nombres" required>
                    </div>

                    <div class = "form-group mt-2">
                    <label for="txt_apellido">Apellidos:</label>
                    <input type="text" class="form-control" value="<?php echo $txtApe; ?>" id="txt_apellido" name="txt_apellido" placeholder="Apellidos" required>
                    </div>

                    <div class = "form-group mt-2">
                    <label for="txt_celular">Celular:</label>
                    <input type="text" class="form-control" value="<?php echo $txtCel; ?>" id="txt_celular" name="txt_celular" placeholder="Celular" required>
                    </div>

                    <div class = "form-group mt-2">
                    <label for="txt_fechaNac">Fecha nacimiento:</label>
                    <input type="date" class="form-control" value="<?php echo $txtFec; ?>" id="txt_fechaNac" name="txt_fechaNac" placeholder="Fecha de nacimiento" required>
                    </div>

                    <div class = "form-group mt-2">
                    <label for="txt_cargo">Cargo:</label>
                    <input type="text" class="form-control" value="<?php echo $txtCargo; ?>" id="txt_cargo" name="txt_cargo" placeholder="Cargo" required>
                    </div>

                    <div class = "form-group mt-2">
                    <label for="txt_imagen">Foto:</label>
                    <br>
                    <?php //if($txtImagen != ""){?>
                        <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen; ?>" alt="" width="80">
                    <?php //} ?>
                    <input type="file" class="form-control"  id="txt_imagen" name="txt_imagen">
                    </div>

                    <div class="btn-group mt-2" role="group" aria-label="">
                        <button type="submit" name="accion" <?php echo ($accion == "Seleccionar")? "disabled" : ""; ?> value="agregar" class="btn btn-success mx-1">Agregar</button>
                        <button type="submit" name="accion" <?php echo ($accion != "Seleccionar")? "disabled" : ""; ?> value="modificar" class="btn btn-warning mx-1">Modificar</button>
                        <button type="submit" name="accion" <?php echo ($accion != "Seleccionar")? "disabled" : ""; ?> value="cancelar" class="btn btn-info mx-1">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Documento</th>
                    <th>Nombres</th>
                    <th>Celular</th>
                    <th>Fecha nacimiento</th>
                    <th>Cargo</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($listaUsuarios as $usuario) { ?>
                <tr>
                    <td><?php echo $usuario['id']; ?></td>
                    <td><?php echo $usuario['documento']; ?></td>
                    <td><?php echo $usuario['nombres'].' '.$usuario['apellidos']; ?></td>
                    <td><?php echo $usuario['celular']; ?></td>
                    <td><?php echo $usuario['fecha_nacimiento']; ?></td>
                    <td><?php echo $usuario['cargo']; ?></td>
                    <td>
                    <img class="img-thumbnail rounded" src="../../img/<?php echo $usuario['foto']; ?>" alt="" width="80">
                    </td>
                    <td>
                        <form method="POST">
                            <input hidden type="text" name="txt_id" id="txt_id" value="<?php echo $usuario['id'];?>">
                            <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary btn-sm">
                            <input type="submit" name="accion" value="Borrar" class="btn btn-danger btn-sm">
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        
    </div>


<?php include("../template/pie.php"); ?>