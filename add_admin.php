<?php
include 'db.php';

// Verificar si el usuario está autenticado y es admin
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash de la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Admins (Username, Password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $hashedPassword);

    if ($stmt->execute()) {
        echo "Administrador agregado exitosamente.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agregar Administrador</title>
</head>
<body>
    <h2>Agregar Nuevo Administrador</h2>
    <form method="post">
        <label for="username">Nombre de Usuario:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Agregar Administrador">
    </form>
</body>
</html>
