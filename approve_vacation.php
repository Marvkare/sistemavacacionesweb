<?php
include 'header.php';
include 'db.php';

// Autenticación del administrador
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit;
}

$adminId = $_SESSION['admin_id'];

// **Paso 2:** Procesar la aprobación de solicitudes
if (isset($_GET['approve'])) {
    $requestId = intval($_GET['approve']);
    $requestType = $_GET['type']; // Tipo de solicitud ('vacation' o 'leave')

    if ($requestType === 'vacation') {
        // Obtener los detalles de la solicitud de vacaciones
        $sql = "SELECT EmployeeID, DaysRequested FROM VacationRequests WHERE RequestID = $requestId";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $requestData = $result->fetch_assoc();
            $employeeId = $requestData['EmployeeID'];
            $daysRequested = $requestData['DaysRequested'];

            // Verificar los días de vacaciones disponibles del empleado
            $sql = "SELECT CurrentEntitlement FROM Employees WHERE EmployeeID = $employeeId";
            $result = $conn->query($sql);
            $employeeData = $result->fetch_assoc();
            $currentEntitlement = $employeeData['CurrentEntitlement'];

            if ($daysRequested <= $currentEntitlement) {
                // Marcar la solicitud como aprobada y registrar al administrador que la aprobó
                $sql = "UPDATE VacationRequests 
                        SET IsApproved = TRUE, ApprovedBy = $adminId 
                        WHERE RequestID = $requestId";

                if ($conn->query($sql) === TRUE) {
                    // Actualizar los días de vacaciones del empleado
                    $newEntitlement = $currentEntitlement - $daysRequested;
                    $sql = "UPDATE Employees 
                            SET CurrentEntitlement = $newEntitlement 
                            WHERE EmployeeID = $employeeId";

                    if ($conn->query($sql) === TRUE) {
                        $_SESSION['success_message'] = "Solicitud de vacaciones aprobada y días de vacaciones actualizados exitosamente.";
                    } else {
                        $_SESSION['error_message'] = "Error al actualizar los días de vacaciones: " . $conn->error;
                    }

                    header('Location: approve_vacation.php'); // Redirige para evitar el reenvío del formulario
                    exit;
                } else {
                    $_SESSION['error_message'] = "Error al aprobar la solicitud de vacaciones: " . $conn->error;
                }
            } else {
                $_SESSION['error_message'] = "No tiene suficientes días de vacaciones disponibles para aprobar esta solicitud.";
            }
        } else {
            $_SESSION['error_message'] = "Solicitud de vacaciones no encontrada.";
        }
    } else if ($requestType === 'leave') {
    // Obtener los detalles de la solicitud de permiso
    $sql = "SELECT EmployeeID, HoursRequested FROM LeaveRequests WHERE LeaveID = $requestId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $requestData = $result->fetch_assoc();
        $employeeId = $requestData['EmployeeID'];
        $hoursRequested = $requestData['HoursRequested'];

        // Obtener las horas acumuladas y los días de vacaciones disponibles del empleado
        $sql = "SELECT CurrentEntitlement, HoursRequested FROM Employees WHERE EmployeeID = $employeeId";
        $result = $conn->query($sql);
        $employeeData = $result->fetch_assoc();
        $currentEntitlement = $employeeData['CurrentEntitlement'];
        $accumulatedHours = $employeeData['HoursRequested'];

        // Sumar las horas solicitadas a las horas acumuladas
        $newAccumulatedHours = $accumulatedHours + $hoursRequested;

        // Verificar si las horas acumuladas alcanzan 8 horas (un día completo)
        if ($newAccumulatedHours >= 8) {
            // Convertir horas en días
            $daysFromHours = intdiv($newAccumulatedHours, 8);
            $newAccumulatedHours = $newAccumulatedHours % 8; // Restar las horas convertidas

            // Verificar si hay suficientes días de vacaciones disponibles
            if ($daysFromHours <= $currentEntitlement) {
                // Restar los días convertidos de las vacaciones disponibles
                $newEntitlement = $currentEntitlement - $daysFromHours;

                // Marcar la solicitud como aprobada y registrar al administrador que la aprobó
                $sql = "UPDATE LeaveRequests 
                        SET IsApproved = TRUE, ApprovedBy = $adminId 
                        WHERE LeaveID = $requestId";

                if ($conn->query($sql) === TRUE) {
                    // Actualizar las horas acumuladas y los días de vacaciones del empleado
                    $sql = "UPDATE Employees 
                            SET CurrentEntitlement = $newEntitlement, HoursRequested = $newAccumulatedHours
                            WHERE EmployeeID = $employeeId";

                    if ($conn->query($sql) === TRUE) {
                        $_SESSION['success_message'] = "Solicitud de permiso aprobada. Horas acumuladas y días de vacaciones actualizados exitosamente.";
                    } else {
                        $_SESSION['error_message'] = "Error al actualizar las horas acumuladas o los días de vacaciones: " . $conn->error;
                    }

                    header('Location: approve_vacation.php'); // Redirige para evitar el reenvío del formulario
                    exit;
                } else {
                    $_SESSION['error_message'] = "Error al aprobar la solicitud de permiso: " . $conn->error;
                }
            } else {
                $_SESSION['error_message'] = "No tiene suficientes días de vacaciones disponibles para aprobar esta solicitud de permiso.";
            }
        } else {
            // Si no alcanza 8 horas, solo actualizamos las horas acumuladas
            $sql = "UPDATE Employees 
                    SET HoursRequested = $newAccumulatedHours 
                    WHERE EmployeeID = $employeeId";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['success_message'] = "Solicitud de permiso aprobada. Horas acumuladas actualizadas exitosamente.";
            } else {
                $_SESSION['error_message'] = "Error al actualizar las horas acumuladas: " . $conn->error;
            }

            header('Location: approve_vacation.php'); // Redirige para evitar el reenvío del formulario
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Solicitud de permiso no encontrada.";
    }
}

}

// **Paso 1:** Obtener todas las solicitudes pendientes (vacaciones y permisos)
$sqlVacation = "
    SELECT vr.RequestID, e.EmployeeID, e.FirstName, e.LastName, vr.StartDate, vr.EndDate, vr.DaysRequested, vr.RequestDate
    FROM VacationRequests vr
    JOIN Employees e ON vr.EmployeeID = e.EmployeeID
    WHERE vr.IsApproved = FALSE
";
$vacationResult = $conn->query($sqlVacation);

$sqlLeave = "
    SELECT lr.LeaveID AS RequestID, e.EmployeeID, e.FirstName, e.LastName, lr.LeaveDate AS StartDate, lr.LeaveDate AS EndDate, lr.HoursRequested AS DaysRequested, lr.RequestDate
    FROM LeaveRequests lr
    JOIN Employees e ON lr.EmployeeID = e.EmployeeID
    WHERE lr.IsApproved = FALSE
";
$leaveResult = $conn->query($sqlLeave);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobar Solicitudes</title>
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
            margin-bottom: 20px;
        }

        p {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }

        .success-message {
            color: #4CAF50;
            background-color: #d4edda;
        }

        .error-message {
            color: #d9534f;
            background-color: #f8d7da;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        table {
            width: 100%;
            max-width: 800px;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .approve-button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            display: inline-block;
            text-align: center;
        }

        .approve-button:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            th, td {
                padding: 8px;
                font-size: 14px;
            }

            .approve-button {
                padding: 5px 10px;
                font-size: 14px;
            }
        }
    </style>
    <script>
        function toggleTables() {
            var vacationCheckbox = document.getElementById('showVacation');
            var leaveCheckbox = document.getElementById('showLeave');
            var vacationTable = document.getElementById('vacationTable');
            var leaveTable = document.getElementById('leaveTable');

            if (vacationCheckbox.checked) {
                vacationTable.style.display = 'table';
            } else {
                vacationTable.style.display = 'none';
            }

            if (leaveCheckbox.checked) {
                leaveTable.style.display = 'table';
            } else {
                leaveTable.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h2>Solicitudes Pendientes</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success-message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
    <?php endif; ?>

    <label>
        <input type="checkbox" id="showVacation" onclick="toggleTables()"> Mostrar Solicitudes de Vacaciones
    </label>
    <label>
        <input type="checkbox" id="showLeave" onclick="toggleTables()"> Mostrar Solicitudes de Permisos Parciales
    </label>

    <div class="table-container">
        <table id="vacationTable" style="display: none;">
            <tr>
                <th>ID Solicitud</th>
                <th>Empleado</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Fin</th>
                <th>Días Solicitados</th>
                <th>Fecha de Solicitud</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = $vacationResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['RequestID']) ?></td>
                <td><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                <td><?= htmlspecialchars($row['StartDate']) ?></td>
                <td><?= htmlspecialchars($row['EndDate']) ?></td>
                <td><?= htmlspecialchars($row['DaysRequested']) ?></td>
                <td><?= htmlspecialchars($row['RequestDate']) ?></td>
                <td>
                    <a href="approve_vacation.php?approve=<?= htmlspecialchars($row['RequestID']) ?>&type=vacation" class="approve-button">
                        Aprobar
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <table id="leaveTable" style="display: none;">
            <tr>
                <th>ID Solicitud</th>
                <th>Empleado</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Fin</th>
                <th>Horas Solicitadas</th>
                <th>Fecha de Solicitud</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = $leaveResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['RequestID']) ?></td>
                <td><?= htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) ?></td>
                <td><?= htmlspecialchars($row['StartDate']) ?></td>
                <td><?= htmlspecialchars($row['EndDate']) ?></td>
                <td><?= htmlspecialchars($row['DaysRequested']) ?> horas</td>
                <td><?= htmlspecialchars($row['RequestDate']) ?></td>
                <td>
                    <a href="approve_vacation.php?approve=<?= htmlspecialchars($row['RequestID']) ?>&type=leave" class="approve-button">
                        Aprobar
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>


<?php
$conn->close();
?>
