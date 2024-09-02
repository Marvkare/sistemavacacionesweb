<?php
include 'header.php';
include 'db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit;
}

$employee = null; 
if (isset($_GET['employee_id'])) {
    $employeeId = $_GET['employee_id'];
    $sql = "SELECT * FROM Employees WHERE EmployeeID = $employeeId";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        echo "Empleado no encontrado.";
        exit;
    }
}
 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = $_POST['employee_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $hireDate = $_POST['hire_date'];
    $departament = $_POST['departament'];
    $sql = "UPDATE Employees SET FirstName='$firstName', LastName='$lastName', HireDate='$hireDate', Departament ='$departament' WHERE EmployeeID=$employeeId";

    if ($conn->query($sql) === TRUE) {
        echo "Usuario actualizado exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Usuario</title>
    <style>
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
        }

        form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-family: Arial, sans-serif;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Editar Usuario</h2>
    <form method="post">
        <input type="hidden" name="employee_id" value="<?php echo $employee['EmployeeID']; ?>">

        <label for="first_name">Nombre:</label><br>
        <input type="text" id="first_name" name="first_name" value="<?php echo $employee['FirstName']; ?>" required><br><br>

        <label for="last_name">Apellido:</label><br>
        <input type="text" id="last_name" name="last_name" value="<?php echo $employee['LastName']; ?>" required><br><br>

        <label for="hire_date">Fecha de Contratación:</label><br>
        <input type="date" id="hire_date" name="hire_date" value="<?php echo $employee['HireDate']; ?>" required><br><br>

        <label for="departament">Departamento</label>
        <input type="text" id="departament" name="departament" value="<?php echo $employee['Departament'];?>" required>
        <input type="submit" value="Actualizar Usuario">
    </form>
</body>
</html>

