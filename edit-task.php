<?php
/**
 * Página para Editar Tarefa Existente
 */

require_once ('functions.php');

// Obter ID da tarefa
$taskId = isset($_GET['id']) ? intval($_GET['id']) : null;

// Se não houver ID, redirecionar
if (!$taskId) {
    header('Location: index.php');
    exit;
}

// Carregar a tarefa
$task = getTaskById($taskId);

// Se tarefa não existir, redirecionar
if (!$task) {
    header('Location: index.php?error=not_found');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarefa - Sistema de Gerenciamento de Tarefas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header>
            <h1>✏️ Editar Tarefa</h1>
            <p style="color: #666; margin-bottom: 20px;">Modifique os dados da tarefa abaixo</p>
        </header>

        <!-- NAVEGAÇÃO -->
        <nav>
            <a href="index.php" class="btn btn-primary">📊 Minhas Tarefas</a>
            <a href="add-task.php" class="btn btn-success">➕ Nova Tarefa</a>
        </nav>

        <!-- INFORMAÇÕES DA TAREFA -->
        <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #3498db;">
            <p style="margin: 0; color: #2c3e50;">
                <strong>Status:</strong> 
                <span class="task-status <?php echo $task['completed'] ? 'completed' : 'pending'; ?>">
                    <?php echo $task['completed'] ? 'Concluída' : 'Pendente'; ?>
                </span>
            </p>
            <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">
                <strong>Criada em:</strong> <?php echo formatDate($task['created_at']); ?>
            </p>
        </div>

        <!-- FORMULÁRIO -->
        <form method="POST" action="process.php" id="taskForm">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">

            <!-- CAMPO TÍTULO -->
            <div class="form-group">
                <label for="title">Título da Tarefa *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="<?php echo htmlspecialchars($task['title']); ?>"
                    placeholder="Digite o título da tarefa"
                    maxlength="100"
                    required
                >
                <small style="color: #999; display: block; margin-top: 5px;">
                    Mínimo: 3 caracteres | Máximo: 100 caracteres
                </small>
            </div>

            <!-- CAMPO DESCRIÇÃO -->
            <div class="form-group">
                <label for="description">Descrição da Tarefa</label>
                <textarea 
                    id="description" 
                    name="description" 
                    placeholder="Digite uma descrição detalhada da tarefa (opcional)"
                    maxlength="500"
                ><?php echo htmlspecialchars($task['description']); ?></textarea>
                <small style="color: #999; display: block; margin-top: 5px;">
                    Máximo: 500 caracteres
                </small>
            </div>

            <!-- BOTÕES -->
            <div class="btn-group">
                <button type="submit" class="btn btn-success">
                    ✓ Salvar Alterações
                </button>
                <a href="index.php" class="btn btn-primary">
                    ← Voltar
                </a>
                <a href="process.php?action=delete&id=<?php echo $task['id']; ?>" class="btn btn-danger" onclick="return confirmDelete(<?php echo $task['id']; ?>, '<?php echo htmlspecialchars($task['title']); ?>')">
                    🗑️ Deletar Tarefa
                </a>
            </div>
        </form>

        <!-- FOOTER -->
        <footer>
            <p>Sistema de Gerenciamento de Tarefas © 2025 | Desenvolvido com PHP puro</p>
        </footer>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
