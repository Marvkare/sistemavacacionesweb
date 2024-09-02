<?php
function getAvailableVacationDays($conn, $employeeId) {
    // Obtener la fecha de contratación
    $sql = "SELECT HireDate FROM Employees WHERE EmployeeID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    
    if (!$employee) {
        return 0; // Empleado no encontrado
    }

    $hireDate = new DateTime($employee['HireDate']);
    $currentDate = new DateTime();
    $yearsWorked = $hireDate->diff($currentDate)->y;

    // Calcular días de vacaciones según años trabajados
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

    // Obtener días de vacaciones utilizados
    $sql = "SELECT SUM(DaysRequested) AS DaysUsed FROM VacationRequests WHERE EmployeeID = ? AND IsApproved = TRUE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $daysUsed = $result->fetch_assoc()['DaysUsed'] ?? 0;

    return $vacationDays - $daysUsed;
}

function updateVacationDays($conn, $employeeId, $daysTaken) {
    // Obtener días disponibles actuales
    $availableDays = getAvailableVacationDays($conn, $employeeId);

    // Restar días tomados y actualizar en la base de datos
    $newAvailableDays = $availableDays - $daysTaken;
    if ($newAvailableDays < 0) {
        return false; // No suficientes días de vacaciones disponibles
    }

    // Aquí podrías actualizar el registro del empleado con los nuevos días disponibles si fuera necesario
    return true;
}

function updateDaysTaken($conn, $employeeId, $daysRequested) {
    // Aquí podrías sumar los días solicitados a un registro de días tomados
    // pero en este caso lo manejamos directamente con la solicitud
    // así que no necesitamos hacer nada aquí.
    return true;
}
