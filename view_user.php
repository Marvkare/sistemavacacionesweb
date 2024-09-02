<?php
include 'db.php';
include 'header.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit;
}
if (isset($_GET['id'])) {
    $employeeId = $_GET['id'];
    $sql = "SELECT * FROM Employees WHERE EmployeeID = $employeeId";
    $result = $conn->query($sql);
    $employee = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ver Usuario</title>
</head>
<body>
    <h2>Información del Usuario</h2>

    <p><strong>Nombre:</strong> <?php echo $employee['FirstName']; ?></p>
    <p><strong>Apellido:</strong> <?php echo $employee['LastName']; ?></p>
    <p><strong>Fecha de Contratación:</strong> <?php echo $employee['HireDate']; ?></p>
</body>
</html>
