<?php
//include 'header.php';
include 'db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit;
}
$requestId = isset($_GET['RequestID']) ? intval($_GET['RequestID']) : 0;
$requestType = isset($_GET['type']) ? $_GET['type'] : '';

if ($requestId > 0 && ($requestType == 'vacation' || $requestType == 'leave')) {
    if ($requestType == 'vacation') {
        $sql = "SELECT vr.*, e.FirstName, e.LastName, e.Departament, a.Username AS ApprovedByName 
                FROM VacationRequests vr
                JOIN Employees e ON vr.EmployeeID = e.EmployeeID
                LEFT JOIN Admins a ON vr.ApprovedBy = a.AdminID
                WHERE vr.RequestID = ?";
    } else if ($requestType == 'leave') {
        $sql = "SELECT lr.*, e.FirstName, e.LastName, e.Departament, a.Username AS ApprovedByName 
                FROM LeaveRequests lr
                JOIN Employees e ON lr.EmployeeID = e.EmployeeID
                LEFT JOIN Admins a ON lr.ApprovedBy = a.AdminID
                WHERE lr.LeaveID = ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_assoc();
    $stmt->close();

    if ($details) {
        // Variables con los datos para usar en HTML
        $nombre = $details['FirstName'] . ' ' . $details['LastName'];
        $numTrab = $details['EmployeeID']; // Esto puede ser reemplazado con un campo real si lo tienes
        $fechaSolicitud = $details['RequestDate'];
        $aprobadoPor = $details['ApprovedByName'] ? $details['ApprovedByName'] : 'Pendiente';
        $estado = $details['IsApproved'] !== NULL ? ($details['IsApproved'] ? 'Aprobado' : 'Pendiente') : 'N/A';
        $departameto = $details['Departament'];
        $motivo = $details['Reason'];
        if ($requestType == 'vacation') {
            $fechaInicio = $details['StartDate'];
            $fechaFin = $details['EndDate'];
            $diasSolicitados = $details['DaysRequested'];
        } else if ($requestType == 'leave') {
            $fechaSalida = $details['DepartureDateTime'];
            $fechaRegreso = $details['ReturnDateTime'];
            $horasSolicitadas = $details['HoursRequested'];
        }
    } else {
        $error = "No se encontró la solicitud.";
    }
} else {
    $error = "ID de solicitud inválido o tipo de solicitud no especificado.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Solicitud</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            border: 2px solid black;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 100px;
        }
        .header h1 {
            margin: 0;
        }
        .header p {
            margin: 2px 0;
        }
        .form-group {
            margin: 20px 0;
        }
        .form-group label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 5px;
            font-size: 14px;
            border: 1px solid black;
            box-sizing: border-box;
        }
        .form-group input[type="text"], .form-group input[type="number"] {
            height: 30px;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
        }
        .signature div {
            text-align: center;
            width: 30%;
            border-top: 1px solid black;
            padding-top: 10px;
        }
        .footer {
            text-align: right;
            font-size: 12px;
            margin-top: 20px;
        }

        .box1{
            display: flex;
            justify-content:space-evenly;
        }
        .box2{
            display: flex;
            justify-content:space-around;
            
        }
        .motivo-box{
            display:flex;
            justify-content:space-around;
            
        }
        .motivo-textbox{
            display:flex;
            justify-content:flex-start;
            
            border: 1px solid black; 
            min-width: 70%;
        }
        .motivo-textbox>p{
            padding-left:20px;
            padding-right:20px;
        }
        .box3{
            display: flex;
            align-items:center;
            justify-content:space-evenly;
        }
        .box4{
            display: flex;
            align-items:center;
            justify-content:space-evenly;
        }
        .header_box1{
            display:flex;
            justify-content:space-evenly;
            align-items:center;    
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
    <img src="./img/Logo.png">
    <h1>TRANSPORTES HIDRO HIDALGENSES, 
    </h1>
    <h1>S.A DE C.V</h1>
     
    <h4>SAN MIGUEL VINDHO MUNICIPIO DE TULA DE ALLENDE HGO.</h4>
    <p><strong>RFC: </strong>THH891218DS0</p>
    <div class="header_box1">
    <h3>AVISO DE AUSENCIA</h3>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php else: ?>

    <p><strong>NO.</strong> <?php echo $requestId; ?></p>
    </div>
    
    </div><!--Fin del div header -->
        <div class="box1">
        <p><strong>No. Trab:</strong> <?php echo $numTrab; ?></p>
        <p><strong>Nombre:</strong> <?php echo $nombre; ?></p>
        <p><strong>Fecha de Solicitud:</strong> <?php echo $fechaSolicitud; ?></p>
        </div>

        <div class="box2">
       
        <p><strong>Motivo:</strong> </p>
        <p><strong>Departamento:</strong> <?php echo $departameto; ?></p>
        </div>
        <div class="motivo-box">
           <div class="motivo-textbox"><p><?php echo $motivo; ?></p></div> 
        </div>
        <?php if ($requestType == 'vacation'): ?>
            <div class="box3">
            <p><strong>Fecha de Inicio:</strong> <?php echo $fechaInicio; ?></p>
            <p><strong>Fecha de Fin:</strong> <?php echo $fechaFin; ?></p>
            <p><strong>Días Solicitados:</strong> <?php echo $diasSolicitados; ?></p>
            </div>
        <?php elseif ($requestType == 'leave'): ?>
            <div class="box3">
            <p><strong>Fecha de Salida:</strong> <?php echo $fechaSalida; ?></p>
            <p><strong>Fecha de Regreso:</strong> <?php echo $fechaRegreso; ?></p>
            <p><strong>Horas Solicitadas:</strong> <?php echo $horasSolicitadas; ?></p>
            </div>
        <?php endif; ?>
        <div class="box4">
        <p><strong>Aprobado por:</strong> <?php echo $aprobadoPor; ?></p>
        <p><strong>Estado:</strong> <?php echo $estado; ?></p>
        </div>
        <div class="box4">
            <p><strong>RECURSOS HUMANOS</strong> </p>
            <p><strong>ENC. DEPARTAMENTO</strong> </p>

            <p><strong>INTERESADO</strong> </p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
