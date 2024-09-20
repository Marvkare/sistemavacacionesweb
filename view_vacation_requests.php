<?php
include 'header.php';
include 'db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit;
}
// Consulta para obtener todas las solicitudes de vacaciones con nombre del administrador
$sql = "
SELECT 'vacation' AS RequestType, vr.RequestID, vr.StartDate, vr.EndDate, vr.DaysRequested, vr.RequestDate, 
       vr.IsApproved, e.FirstName, e.LastName, a.Username AS ApprovedByName
FROM VacationRequests vr
JOIN Employees e ON vr.EmployeeID = e.EmployeeID
LEFT JOIN Admins a ON vr.ApprovedBy = a.AdminID

UNION ALL

SELECT 'leave' AS RequestType, lr.LeaveID AS RequestID, lr.DepartureDateTime AS StartDate, lr.ReturnDateTime AS EndDate, 
       lr.HoursRequested AS DaysRequested, lr.RequestDate, NULL AS IsApproved, e.FirstName, e.LastName, a.Username AS ApprovedByName
FROM LeaveRequests lr 
JOIN Employees e ON lr.EmployeeID = e.EmployeeID
LEFT JOIN Admins a ON lr.ApprovedBy = a.AdminID
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Vacaciones y Permisos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .table-container {
            width: 100%;
            max-width: 1000px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .view-button {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .view-button:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            .table-container {
                padding: 10px;
            }

            th, td {
                padding: 8px;
                font-size: 14px;
            }

            .view-button {
                padding: 6px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <h2>Lista de Solicitudes de Vacaciones y Permisos</h2>

    <div class="table-container">
        <table>
            <tr>
                <th>ID Solicitud</th>
                <th>Empleado</th>
                <th>Fecha de Inicio/Salida</th>
                <th>Fecha de Fin/Regreso</th>
                <th>Días/Horas Solicitados</th>
                <th>Fecha de Solicitud</th>
                <th>Aprobado Por</th>
                <th>Estado</th>
                <th>Acciones</th> <!-- Nueva columna para el botón -->
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['RequestID'] . "</td>";
                    echo "<td>" . $row['FirstName'] . " " . $row['LastName'] . "</td>";
                    echo "<td>" . $row['StartDate'] . "</td>";
                    echo "<td>" . $row['EndDate'] . "</td>";
                    echo "<td>" . ($row['RequestType'] == 'vacation' ? $row['DaysRequested'] . ' días' : $row['DaysRequested'] . ' horas') . "</td>";
                    echo "<td>" . $row['RequestDate'] . "</td>";
                    echo "<td>" . ($row['ApprovedByName'] ? $row['ApprovedByName'] : 'Pendiente') . "</td>";
                    echo "<td>" . ($row['IsApproved'] !== NULL ? ($row['IsApproved'] ? 'Aprobado' : 'Pendiente') : 'N/A') . "</td>";
                    echo "<td><a href='view_request.php?RequestID=" . $row['RequestID'] . "&type=" . $row['RequestType'] . "' class='view-button' target='_blank'>Ver Solicitud</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No se encontraron solicitudes</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

