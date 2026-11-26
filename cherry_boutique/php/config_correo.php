<?php session_start();
    $user = $_SESSION['userData'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – MediChina</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./css/main.css">

</head>

<body>

    <?php include "./layouts/header.php" ?>

    <div class="container-fluid">
        <div class="row">
            <?php include "./layouts/sidebar.php" ?>

            <!-- CONTENIDO -->
            <div class="col-md-10 p-4">
                <h2 class="fw-bold mb-4">Configuración</h2>

                

            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/main.js"></script>
</body>

</html>