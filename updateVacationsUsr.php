<?php

include "db.php";
function actualizarDiasDeVacaciones($employeeId, $conn) {
    // Paso 1: Obtener la fecha actual
    $currentDate = new DateTime();

    // Paso 2: Consultar los datos del empleado desde la base de datos
    $sql = "SELECT HireDate, LastVacationUpdate, CurrentEntitlement FROM Employees WHERE EmployeeID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Paso 3: Obtener los datos del empleado
        $row = $result->fetch_assoc();
        $hireDate = $row['HireDate'];
        $lastVacationUpdateDate = $row['LastVacationUpdate'];
        $currentEntitlement = $row['CurrentEntitlement'];

        // Paso 4: Convertir las fechas de contratación y última actualización a objetos DateTime
        $hireDateObj = new DateTime($hireDate);
        $lastUpdateDateObj = new DateTime($lastVacationUpdateDate);

        // Paso 5: Calcular los años desde la fecha de contratación y la última actualización
        $yearsWorked = $hireDateObj->diff($currentDate)->y;
        $yearsSinceLastUpdate = $lastUpdateDateObj->diff($currentDate)->y;

        // Paso 6: Verificar si ha pasado un año o más desde la última actualización
        if ($yearsSinceLastUpdate >= 1) {
            // Paso 7: Calcular los días de vacaciones basados en los años trabajados
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

            // Paso 8: Acumular los días de vacaciones disponibles
            $newEntitlement = $currentEntitlement + $vacationDays;

            // Paso 9: Actualizar los días de vacaciones en la base de datos
            $sqlUpdate = "UPDATE Employees SET CurrentEntitlement = ?, LastVacationUpdate = ? WHERE EmployeeID = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $formattedDate = $currentDate->format('Y-m-d');  // Formato de fecha para MySQL
            $stmtUpdate->bind_param("isi", $newEntitlement, $formattedDate, $employeeId);
            $stmtUpdate->execute();

            // Verificar si la actualización fue exitosa
            if ($stmtUpdate->affected_rows > 0) {
                echo "<script>alert('Los días de vacaciones han sido actualizados correctamente para el empleado con ID: $employeeId.');</script>";

                header('Location: view_all_users.php');
            } else {

                echo "<script>alert('No se pudo actualizar los días de vacaciones.');</script>";

                header('Location: view_all_users.php');
            }

            // Cerrar la declaración
            $stmtUpdate->close();
        } else {

            echo "<script>alert('No ha pasado un año desde la última actualización de vacaciones.');</script>";

            header('Location: view_all_users.php');
        }
    } else {
        echo "<script>alert('No se encontró al empleado con ID: $employeeId.');</script>";

        header('Location: view_all_users.php');
    }

    // Cerrar la declaración inicial
    $stmt->close();
}

if (isset($_GET['employee_id'])) {
    $employeeId = $_GET['employee_id'];

    actualizarDiasDeVacaciones($employeeId,$conn);

}
?>