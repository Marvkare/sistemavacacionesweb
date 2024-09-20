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

// Verificar si se ha pasado una solicitud de rechazo
if (isset($_GET['approve']) && isset($_GET['type'])) {
    $requestId = intval($_GET['approve']);
    $requestType = $_GET['type']; // Tipo de solicitud ('vacation' o 'leave')

    // Manejar solicitud de rechazo de vacaciones
    if ($requestType === 'vacation') {
        // Actualizar el estado de la solicitud a "rechazado" (2)
        $sql = "UPDATE VacationRequests 
                SET IsApproved = 2, ApprovedBy = $adminId 
                WHERE RequestID = $requestId";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['success_message'] = "La solicitud de vacaciones ha sido rechazada.";
        } else {
            $_SESSION['error_message'] = "Error al rechazar la solicitud de vacaciones: " . $conn->error;
        }

        header('Location: approve_vacation.php');
        exit;

    } else if ($requestType === 'leave') {
        // Manejar solicitud de rechazo de permisos
        $sql = "UPDATE LeaveRequests 
                SET IsApproved = 2, ApprovedBy = $adminId 
                WHERE LeaveID = $requestId";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['success_message'] = "La solicitud de permiso ha sido rechazada.";
        } else {
            $_SESSION['error_message'] = "Error al rechazar la solicitud de permiso: " . $conn->error;
        }

        header('Location: approve_vacation.php');
        exit;
    }
}

$conn->close();
?>
