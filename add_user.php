<?php
include 'header.php';
include 'db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $hireDate = $_POST['hire_date'];
    $rfc = $_POST['rfc'];
    $departament = $_POST['departament'];
    // **Paso 1:** Calcular los días de vacaciones disponibles según la fecha de contratación
    $hireDateObj = new DateTime($hireDate);
    $currentDate = new DateTime();

    // **Paso 2:** Calcular los años trabajados y días de vacaciones disponibles
    $yearsWorked = $hireDateObj->diff($currentDate)->y;

    $vacationDays = 0;
    if ($yearsWorked == 1) {
        $vacationDays = 12;
    } elseif ($yearsWorked == 2) {
        $vacationDays = 14;
    } elseif ($yearsWorked == 3) {
        $vacationDays = 16;
    } elseif ($yearsWorked == 4) {
        $vacationDays = 18;
    } elseif ($yearsWorked == 5) {
        $vacationDays = 20;
    } elseif ($yearsWorked >= 6 && $yearsWorked <= 10) {
        $vacationDays = 22;
    } elseif ($yearsWorked >= 11 && $yearsWorked <= 15) {
        $vacationDays = 24;
    } elseif ($yearsWorked >= 16 && $yearsWorked <= 20) {
        $vacationDays = 26;
    } elseif ($yearsWorked >= 21 && $yearsWorked <= 25) {
        $vacationDays = 28;
    } elseif ($yearsWorked >= 26 && $yearsWorked <= 30) {
        $vacationDays = 30;
    } elseif ($yearsWorked >= 31 && $yearsWorked <= 35) {
        $vacationDays = 32;
    }

    // **Paso 3:** Insertar el nuevo empleado en la base de datos
    $sql = "INSERT INTO Employees (FirstName, LastName, HireDate, RFC, CurrentEntitlement, Departament) 
            VALUES ('$firstName', '$lastName', '$hireDate', '$rfc', $vacationDays,'$departament')";

    if ($conn->query($sql) === TRUE) {
        echo "Nuevo usuario agregado exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <style>
        .form-container {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            flex-flow:column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        h2 {
            color: #4CAF50;
            text-align: center;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        @media (max-width: 480px) {
            form {
                padding: 15px;
            }

            input[type="text"],
            input[type="date"] {
                font-size: 14px;
            }

            input[type="submit"] {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
    <h2>Agregar Usuario</h2>
    <form method="post">
        <label for="first_name">Nombre:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Apellido:</label>
        <input type="text" id="last_name" name="last_name" required>
        
        <label for="departament">Departamento:</label>
        <input type="text" id="departament" name="departament" required>

        <label for="hire_date">Fecha de Contratación:</label>
        <input type="date" id="hire_date" name="hire_date" required>

        <label for="rfc">RFC:</label>
        <input type="text" id="rfc" name="rfc" required>

        <input type="submit" value="Agregar Usuario">
    </form>
    </div>
</body>
</html>

