<?php

require_once('functions.php');

$tasks = loadTasks();

$totalTasks = getTotalTasks();
$completedTasks = getCompletedTasks();
$pendingTasks = getPendingTasks();
$completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

$successMessage = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $successMessage = 'Tarefa adicionada com sucesso!';
            break;
        case 'updated':
            $successMessage = 'Tarefa atualizada com sucesso!';
            break;
        case 'deleted':
            $successMessage = 'Tarefa deletada com sucesso!';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Tarefas</title>
    <link rel="stylesheet" href="/todo-list/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1 style="color: white;"> 📋 Meu Gerenciador de Tarefas</h1>
            <p style="color: #ffffffff; margin-bottom: 20px;">Organize suas tarefas de forma simples e eficiente</p>
        </header>

        <nav>
            <a href="index.php" class="btn btn-primary">📊 Minhas Tarefas</a>
            <a href="add-task.php" class="btn btn-success">➕ Nova Tarefa</a>
        </nav>
        <div class="content">

        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                ✓ <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalTasks; ?></div>
                <div class="stat-label">Total de Tarefas</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #2ecc71, #27ae60);">
                <div class="stat-number"><?php echo $completedTasks; ?></div>
                <div class="stat-label">Concluídas</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                <div class="stat-number"><?php echo $pendingTasks; ?></div>
                <div class="stat-label">Pendentes</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #9b59b6, #8e44ad);">
                <div class="stat-number"><?php echo $completionPercentage; ?>%</div>
                <div class="stat-label">Progresso</div>
            </div>
        </div>

        <h2>Tarefas Cadastradas</h2>

        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h2>Nenhuma tarefa cadastrada</h2>
                <p>Comece criando sua primeira tarefa clicando no botão "Nova Tarefa"</p>
                <a href="add-task.php" class="btn btn-primary">Criar Primeira Tarefa</a>
            </div>
        <?php else: ?>
            <div class="tasks-container">
                <?php foreach ($tasks as $task): ?>
                    <div class="task-card <?php echo $task['completed'] ? 'completed' : ''; ?>">
                        <div class="task-header">
                            <h3 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h3>
                            <span class="task-status <?php echo $task['completed'] ? 'completed' : 'pending'; ?>">
                                <?php echo $task['completed'] ? 'Concluída' : 'Pendente'; ?>
                            </span>
                        </div>

                        <?php if (!empty($task['description'])): ?>
                            <p class="task-description"><?php echo htmlspecialchars($task['description']); ?></p>
                        <?php endif; ?>

                        <div class="task-dates">
                            <div class="task-date-item">
                                <strong>Criada:</strong> <?php echo formatDate($task['created_at']); ?>
                            </div>
                            <div class="task-date-item">
                                <strong>Atualizada:</strong> <?php echo formatDate($task['updated_at']); ?>
                            </div>
                        </div>

                        <div class="task-actions">
                            <button class="btn btn-secondary btn-small" onclick="toggleTaskStatus(<?php echo $task['id']; ?>, this)">
                                <?php echo $task['completed'] ? '↩️ Reabrir' : '✓ Concluir'; ?>
                            </button>
                            <a href="edit-task.php?id=<?php echo $task['id']; ?>" class="btn btn-warning btn-small">
                                ✏️ Editar
                            </a>
                            <a href="process.php?action=delete&id=<?php echo $task['id']; ?>" class="btn btn-danger btn-small" onclick="return confirmDelete(<?php echo $task['id']; ?>, '<?php echo htmlspecialchars($task['title']); ?>')">
                                🗑️ Deletar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
                        </div>

        <footer>
            <p>Sistema de Gerenciamento de Tarefas © 2025 | Desenvolvido com PHP puro</p>
        </footer>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
