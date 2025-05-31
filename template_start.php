<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    // Redireciona para a página de login
    header('Location: login.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gerenciamento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- DataTables CSS (com tema Bootstrap 5) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

    <!-- jQuery (carregado antes do DataTables e plugins que dependem dele) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <!-- Bootstrap JS Bundle (inclui Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS (primeiro o core, depois a integração com Bootstrap 5) -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f4f6f8;
        }
        .sidebar {
            width: 250px;
            background-color: #0d6efd;
            min-height: 100vh;
            padding-top: 30px;
            padding-left: 10px;
            position: fixed;
            transition: width 0.3s;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 15px;
        }
        .sidebar .nav-link:hover {
            background-color: #0b5ed7;
        }
        .sidebar .bi {
            margin-right: 10px;
        }
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: margin-left 0.3s;
        }
        .main-content.collapsed {
            margin-left: 70px;
        }
        footer {
            background-color: #ecf0f1;
            text-align: center;
            padding: 15px;
            color: #555;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar position-fixed d-flex flex-column justify-content-between">
    <ul class="nav flex-column pt-3">
        <li class="nav-item">
            <a class="nav-link" href="index.php"><i class="bi bi-house-door-fill"></i><span> Início</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="pizza.php"><i class="bi bi-basket3-fill"></i><span> Pizza</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="pedido.php"><i class="bi bi-card-checklist"></i><span> Pedido</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="clientes.php"><i class="bi bi-people"></i><span> Clientes</span></a>
        </li>
    </ul>
    <ul class="nav flex-column pt-3 mb-2">
        <li class="nav-item">
            <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i><span> Logout</span></a>
        </li>
    </ul>

</div>


<!-- Conteúdo principal -->
<main id="mainContent" class="main-content">
    <!-- Botão de toggle no topo -->
    <div class="d-flex align-items-center mb-2"> <!-- tirou o mb-4 -->
        <button class="btn btn-outline-primary me-3 p-2" id="toggleSidebar" style="line-height:1;">
            <i class="bi bi-list"></i>
        </button>
    </div>


