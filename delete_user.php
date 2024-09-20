<?php
// Incluir el archivo de conexión a la base de datos
include 'db.php';

// Verificar si se ha pasado el parámetro employee_id
if (isset($_GET['employee_id'])) {
    $employeeId = $_GET['employee_id'];

    // Preparar la consulta para actualizar el estado del empleado
    $sql = "UPDATE Employees SET IsActive = 0 WHERE EmployeeID = ?";

    // Preparar la declaración
    if ($stmt = $conn->prepare($sql)) {
        // Vincular el parámetro
        $stmt->bind_param("i", $employeeId);

        // Ejecutar la declaración
        if ($stmt->execute()) {
            echo "El estado del empleado con ID $employeeId ha sido actualizado a inactivo.";
        } else {
            echo "Error al actualizar el estado del empleado: " . $stmt->error;
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }
} else {
    echo "No se proporcionó un ID de empleado válido.";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
