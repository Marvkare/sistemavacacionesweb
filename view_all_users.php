<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empleados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background-color: #f4f4f4;
        }

        .table-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 100%;
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            width: 90%;
            max-width: 1000px;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        td a {
            color: #4CAF50;
            text-decoration: none;
            margin-right: 10px;
        }

        td a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            table {
                width: 100%;
            }

            th, td {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<?php
include 'db.php';
include 'header.php'; // Incluye el header
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit;
}
$sql = "SELECT 
            EmployeeID, 
            FirstName, 
            LastName, 
            HireDate,
            HoursRequested, 
            RFC,
            CurrentEntitlement 
        FROM Employees";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='table-container'>";
    echo "<table>";
    echo "<tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>RFC</th>
            <th>Fecha de Contratación</th>
            <th>Horas tomadas</th>
            <th>Días de Vacaciones Disponibles</th>
            <th>Acciones</th>
          </tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["EmployeeID"] . "</td>";
        echo "<td>" . $row["FirstName"] . "</td>";
        echo "<td>" . $row["LastName"] . "</td>";
        echo "<td>" . $row["RFC"] . "</td>";
        echo "<td>" . $row["HireDate"] . "</td>";
        echo "<td>" . $row["HoursRequested"] . "</td>";
        echo "<td>" . $row["CurrentEntitlement"] . "</td>";
    
        echo "<td>
                <a href='request_vacation.php?employee_id=" . $row["EmployeeID"] . "'>Solicitar Vacaciones</a> |
                <a href='edit_user.php?employee_id=" . $row["EmployeeID"] . "'>Editar</a> |
                <a href='view_request_employee.php?employee_id=" . $row["EmployeeID"] . "'>Ver solicitudes</a> |
                <a href='delete_user.php?employee_id=" . $row["EmployeeID"] . "' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este usuario?\");'>Eliminar</a>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
} else {
    echo "No se encontraron empleados.";
}

include 'footer.php'; // Incluye el footer
?>

</body>
</html>
