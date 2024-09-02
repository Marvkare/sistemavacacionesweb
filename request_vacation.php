
<!DOCTYPE html>
<?php
include 'header.php';
include 'db.php';
include 'vacation_functions.php';  // Incluir el archivo con las funciones
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirige al login si no está autenticado
    exit;
}

$employeeId = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;
$vacationDaysAvailable = 0;
$employeeData = [];

if ($employeeId > 0) {
    $vacationDaysAvailable = getAvailableVacationDays($conn, $employeeId);

    if ($vacationDaysAvailable < 0) {
        echo "Error al calcular los días de vacaciones disponibles.";
        exit;
    }

    // **Paso 1:** Obtener datos del empleado
    $sql = "SELECT EmployeeID, FirstName, LastName, HireDate, CurrentEntitlement FROM Employees WHERE EmployeeID = $employeeId";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $employeeData = $result->fetch_assoc();
    } else {
        echo "Empleado no encontrado.";
        exit;
    }
} else {
    echo "No se ha proporcionado un ID de empleado válido.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Permiso o Vacaciones</title>
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
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        form {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="date"], input[type="datetime-local"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h2>Solicitar Permiso o Vacaciones</h2>

    <?php if (!empty($employeeData)): ?>
        <p><strong>ID del Empleado:</strong> <?= htmlspecialchars($employeeData['EmployeeID']) ?></p>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($employeeData['FirstName']) . ' ' . htmlspecialchars($employeeData['LastName']) ?></p>
        <p><strong>Fecha de Contratación:</strong> <?= htmlspecialchars($employeeData['HireDate']) ?></p>
        <p><strong>Días de Vacaciones Disponibles:</strong> <?= htmlspecialchars($employeeData['CurrentEntitlement']) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="employee_id" value="<?= htmlspecialchars($employeeId) ?>">

        <div class="form-group">
            <label for="request_type">Tipo de Solicitud:</label>
            <select id="request_type" name="request_type" required>
                <option value="vacation">Días de Vacaciones Completas</option>
                <option value="leave">Permiso Parcial</option>
            </select>
        </div>

        <!-- Campos para Vacaciones Completas -->
        <div id="vacation_fields" class="form-group">
            <label for="start_date">Fecha de Inicio:</label>
            <input type="date" id="start_date" name="start_date">

            <label for="end_date">Fecha de Fin:</label>
            <input type="date" id="end_date" name="end_date">
          
            <label for="motivo">Motivo</label>
            <input type="text" id="motivo" name="motivo">
        </div>

        <!-- Campos para Permiso Parcial -->
        <div id="leave_fields" class="form-group hidden">
            <label for="leave_date">Fecha Inicial del Permiso:</label>
            <input type="date" id="leave_date" name="leave_date" >

            <label for="departure_datetime">Fecha y Hora de Salida:</label>
            <input type="datetime-local" id="departure_datetime" name="departure_datetime" >

            <label for="return_datetime">Fecha y Hora de Regreso:</label>
            <input type="datetime-local" id="return_datetime" name="return_datetime" >
            
            <label for="motivo">Motivo</label>
            <input type="text" id="motivo" name="motivo" >
        </div>

        <input type="submit" value="Enviar Solicitud">
    </form>

    <script>
        // Script para mostrar/ocultar campos según el tipo de solicitud
        document.getElementById('request_type').addEventListener('change', function() {
            if (this.value === 'vacation') {
                document.getElementById('vacation_fields').style.display = 'block';
                document.getElementById('leave_fields').style.display = 'none';
            } else {
                document.getElementById('vacation_fields').style.display = 'none';
                document.getElementById('leave_fields').style.display = 'block';
            }
        });
    </script>
</body>
</html>


<?php
// **Paso 3:** Validar y procesar la solicitud según el tipo de solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestType = $_POST['request_type'];
    date_default_timezone_set('America/Mexico_City');

    if ($requestType === 'vacation') {
        // Validar campos de vacaciones completas
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $motivo =$_POST['motivo'];
        
        if (empty($startDate) || empty($endDate)) {
            echo "<script>alert('Por favor, complete las fechas de inicio y fin para la solicitud de vacaciones.');</script>";
        } else {
            // Procesar solicitud de vacaciones completas
            $date1 = new DateTime($startDate);
            $date2 = new DateTime($endDate);
            $daysRequested = $date1->diff($date2)->days + 1;

            // Verificar si tiene suficientes días disponibles
            if (!updateVacationDays($conn, $employeeId, $daysRequested)) {
                echo "<script>alert('No tienes suficientes días de vacaciones disponibles.');</script>";
            } else {
                $requestDate = date('Y-m-d H:i:s');
                $sql = "INSERT INTO VacationRequests (EmployeeID, StartDate, EndDate, DaysRequested, RequestDate, Reason)  VALUES ('$employeeId', '$startDate', '$endDate', '$daysRequested', '$requestDate','$motivo')";

                if ($conn->query($sql) === TRUE) {
                   echo "<script>alert('Solicitud de permiso parcial enviada con éxito.'); window.location.href = 'approve_vacation.php';</script>"; 
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
        }
    }  elseif ($requestType === 'leave') {
    // Validar campos de permiso parcial
    $leaveDate = $_POST['leave_date'];
    $departureDateTime = $_POST['departure_datetime'];
    $returnDateTime = $_POST['return_datetime'];

    // Convertir fechas a objetos DateTime para facilitar la manipulación
    $departureDateTimeObj = new DateTime($departureDateTime);
    $returnDateTimeObj = new DateTime($returnDateTime);
    $leaveDateObj = new DateTime($leaveDate);

    // Validar que las fechas y horas no sean mayores ni menores que la fecha inicial
    if ($departureDateTimeObj->format('Y-m-d') !== $leaveDate || $returnDateTimeObj->format('Y-m-d') !== $leaveDate) {
        echo "<script>alert('La fecha de salida y regreso deben coincidir con la fecha del permiso.');</script>";
    } else {
        // Calcular las horas solicitadas incluyendo minutos
        $interval = $departureDateTimeObj->diff($returnDateTimeObj);
        $hoursRequested = $interval->h + ($interval->i / 60); // Suma horas y minutos como fracción de hora
            echo "<script>alert('No tienes días de vacaciones disponibles para solicitar horas.);</script>";

        // Verificar si las horas solicitadas exceden la jornada laboral diaria (10 horas)
        if ($hoursRequested > 10) {
            echo "<script>alert('Las horas solicitadas exceden la jornada laboral diaria. Por favor, elige la opción de vacaciones completas.');</script>";
        } elseif ($hoursRequested <= 1) {
            // Verificar si el usuario tiene días de vacaciones disponibles
            echo "<script>alert('Tienes que solicitar un permiso con el tiempo mayor a una hora');</script>";
        } else {
            // Si pasa todas las validaciones, registrar la solicitud en LeaveRequests
            $requestDate = date('Y-m-d H:i:s');
            $sql = "INSERT INTO LeaveRequests (EmployeeID, LeaveDate, DepartureDateTime, ReturnDateTime, HoursRequested, RequestDate, Reason) 
                    VALUES ('$employeeId', '$leaveDate', '$departureDateTime', '$returnDateTime', '$hoursRequested', '$requestDate','$motivo')";

            if ($conn->query($sql) === TRUE) {

                   echo "<script>alert('Solicitud de permiso parcial enviada con éxito.'); window.location.href = 'approve_vacation.php';</script>"; 
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}
}

$conn->close();
?>
