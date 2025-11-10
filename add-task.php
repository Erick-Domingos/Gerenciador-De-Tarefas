<?php
/**
 * Página para Adicionar Nova Tarefa
 */

require_once ('functions.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Nova Tarefa - Sistema de Gerenciamento de Tarefas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header>
            <h1>➕ Adicionar Nova Tarefa</h1>
            <p style="color: #666; margin-bottom: 20px;">Preencha os dados abaixo para criar uma nova tarefa</p>
        </header>

        <!-- NAVEGAÇÃO -->
        <nav>
            <a href="index.php" class="btn btn-primary">📊 Minhas Tarefas</a>
            <a href="add-task.php" class="btn btn-success">➕ Nova Tarefa</a>
        </nav>

        <!-- FORMULÁRIO -->
        <form method="POST" action="process.php" id="taskForm">
            <input type="hidden" name="action" value="add">

            <!-- CAMPO TÍTULO -->
            <div class="form-group">
                <label for="title">Título da Tarefa *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    placeholder="Digite o título da tarefa (mínimo 3 caracteres)"
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
                ></textarea>
                <small style="color: #999; display: block; margin-top: 5px;">
                    Máximo: 500 caracteres
                </small>
            </div>

            <!-- BOTÕES -->
            <div class="btn-group">
                <button type="submit" class="btn btn-success">
                    ✓ Criar Tarefa
                </button>
                <button type="reset" class="btn btn-secondary">
                    🔄 Limpar Formulário
                </button>
                <a href="index.php" class="btn btn-primary">
                    ← Voltar
                </a>
            </div>
        </form>

        <!-- DICAS -->
        <div style="background: #e8f4f8; padding: 20px; border-radius: 8px; margin-top: 30px; border-left: 4px solid #3498db;">
            <h3 style="color: #2c3e50; margin-top: 0;">💡 Dicas para Criar Boas Tarefas</h3>
            <ul style="color: #555; line-height: 1.8;">
                <li><strong>Seja específico:</strong> Use títulos claros e descritivos</li>
                <li><strong>Quebre em partes:</strong> Divida tarefas grandes em tarefas menores</li>
                <li><strong>Use a descrição:</strong> Adicione detalhes importantes na descrição</li>
                <li><strong>Priorize:</strong> Crie tarefas por ordem de importância</li>
                <li><strong>Revise regularmente:</strong> Atualize o status das suas tarefas</li>
            </ul>
        </div>

        <!-- FOOTER -->
        <footer>
            <p>Sistema de Gerenciamento de Tarefas © 2025 | Desenvolvido com PHP puro</p>
        </footer>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
