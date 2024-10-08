<?php
// Incluir el archivo de conexión
include 'db.php';

// Suponiendo que has pasado el ID del empleado a través de GET
$employeeId = $_GET['employee_id'];

// Consulta a la vista EmployeeRequests
$sql = "
SELECT 
    'vacation' AS RequestType, 
    vr.RequestID AS RequestID, 
    vr.StartDate AS StartDate, 
    vr.EndDate AS EndDate, 
    vr.DaysRequested AS DaysRequested,
    vr.Reason, 
    vr.RequestDate AS RequestDate, 
    vr.IsApproved AS IsApproved, 
    e.FirstName AS FirstName, 
    e.LastName AS LastName, 
    a.Username AS ApprovedByName
FROM 
    VacationRequests vr 
JOIN 
    Employees e ON vr.EmployeeID = e.EmployeeID
LEFT JOIN 
    Admins a ON vr.ApprovedBy = a.AdminID

UNION ALL

SELECT 
    'leave' AS RequestType, 
    lr.LeaveID AS RequestID, 
    lr.DepartureDateTime AS StartDate, 
    lr.ReturnDateTime AS EndDate, 
    lr.HoursRequested AS DaysRequested, 
    lr.Reason,
    lr.RequestDate AS RequestDate, 
    lr.IsApproved AS IsApproved, 
    e.FirstName AS FirstName, 
    e.LastName AS LastName, 
    a.Username AS ApprovedByName
FROM 
    LeaveRequests lr 
JOIN 
    Employees e ON lr.EmployeeID = e.EmployeeID
LEFT JOIN 
    Admins a ON lr.ApprovedBy = a.AdminID
WHERE 
    e.EmployeeID = ?;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h2>Solicitudes del Empleado</h2>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Tipo de Solicitud</th>
            <th>ID de Solicitud</th>
            <th>Fecha de Inicio</th>
            <th>Fecha de Fin</th>
            <th>Duración</th>
            <th>Motivo</th>
            <th>Aprobado Por</th>
            <th>Fecha de Solicitud</th>
            <th>Estado de Aprobación</th>
        </tr>";
    // Salida de cada fila
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["RequestType"]) . "</td>
                <td>" . htmlspecialchars($row["RequestID"]) . "</td>
                <td>" . htmlspecialchars($row["StartDate"]) . "</td>
                <td>" . htmlspecialchars($row["EndDate"]) . "</td>
                <td>" . htmlspecialchars($row["DaysRequested"]) . "</td>
                <td>" . htmlspecialchars($row["Reason"]) . "</td>
                <td>" . htmlspecialchars($row["ApprovedByName"]) . "</td>
                <td>" . htmlspecialchars($row["RequestDate"]) . "</td>
                <td>" . ($row["IsApproved"] ? 'Aprobado' : 'No Aprobado') . "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<strong>No hay solicitudes para este empleado.</strong>";
}

// Cerrar la conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes del Empleado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<?php
// Incluir el código PHP para la tabla aquí
?>

</body>
</html>
