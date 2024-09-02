<?php
include 'db.php';

// Verificar si el script ya ha sido ejecutado
// Puedes usar una verificación basada en la existencia de una tabla, un campo, o cualquier otro mecanismo para asegurarte de que el script no se ejecute más de una vez.
$checkSql = "SELECT COUNT(*) AS count FROM Admins";
$checkResult = $conn->query($checkSql);
$checkRow = $checkResult->fetch_assoc();

if ($checkRow['count'] > 0) {
    die("Ya existe al menos un administrador en la base de datos.");
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
    <title>Instalar Administrador</title>
</head>
<body>
    <h2>Instalar Administrador Inicial</h2>
    <form method="post">
        <label for="username">Nombre de Usuario:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Agregar Administrador">
    </form>
</body>
</html>
