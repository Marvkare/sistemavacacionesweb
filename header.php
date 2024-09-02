<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Aplicación</title>
    <link rel="stylesheet" href="styles.css"> <!-- Enlaza tu archivo CSS si tienes uno -->
    <link rel="stylesheet" href="./styles/header.css">
</head>
<body>
<header>
    <nav>
        <div class="logo">
            <a href="index.php">Mi Sitio</a>
        </div>
        <div class="menu-toggle" id="menu-toggle">
            &#9776;
        </div>
        <ul class="nav-links" id="nav-links">
            <li><a href="view_all_users.php">Ver Usuarios</a></li>
            <li><a href="add_user.php">Agregar nuevo usuario</a></li>
            <li><a href="view_vacation_requests.php">Peticion de vacaciones</a></li>
            <li><a href="approve_vacation.php">Aprobar vacaciones</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
</header>
<script>
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.getElementById('nav-links');

    menuToggle.addEventListener('click', () => {
        navLinks.classList.toggle('show');
    });
</script>

    <main>
