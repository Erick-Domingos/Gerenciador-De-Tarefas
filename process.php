<?php
/**
 * Arquivo de Processamento de Ações
 * Processa as operações CRUD (Create, Read, Update, Delete)
 */

require_once ('functions.php');

// Definir header para JSON se for AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
}

// Obter ação
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : null);

// Variável para armazenar resposta JSON
$response = ['success' => false, 'message' => 'Ação não reconhecida'];

try {
    switch ($action) {
        // ============================================
        // ADICIONAR TAREFA
        // ============================================
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método de requisição inválido');
            }

            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';

            // Validações
            if (empty(trim($title))) {
                throw new Exception('O título da tarefa é obrigatório');
            }

            if (strlen(trim($title)) < 3) {
                throw new Exception('O título deve ter pelo menos 3 caracteres');
            }

            if (strlen(trim($title)) > 100) {
                throw new Exception('O título não pode ter mais de 100 caracteres');
            }

            if (strlen(trim($description)) > 500) {
                throw new Exception('A descrição não pode ter mais de 500 caracteres');
            }

            // Adicionar tarefa
            if (addTask($title, $description)) {
                $response = [
                    'success' => true,
                    'message' => 'Tarefa adicionada com sucesso!'
                ];

                // Se for AJAX, retornar JSON
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    echo json_encode($response);
                    exit;
                }

                // Caso contrário, redirecionar
                header('Location: index.php?success=added');
                exit;
            } else {
                throw new Exception('Erro ao salvar a tarefa');
            }
            break;

        // ============================================
        // ATUALIZAR TAREFA
        // ============================================
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método de requisição inválido');
            }

            $id = isset($_POST['id']) ? intval($_POST['id']) : null;
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';

            // Validações
            if (!$id) {
                throw new Exception('ID da tarefa não fornecido');
            }

            if (empty(trim($title))) {
                throw new Exception('O título da tarefa é obrigatório');
            }

            if (strlen(trim($title)) < 3) {
                throw new Exception('O título deve ter pelo menos 3 caracteres');
            }

            if (strlen(trim($title)) > 100) {
                throw new Exception('O título não pode ter mais de 100 caracteres');
            }

            if (strlen(trim($description)) > 500) {
                throw new Exception('A descrição não pode ter mais de 500 caracteres');
            }

            // Verificar se tarefa existe
            $task = getTaskById($id);
            if (!$task) {
                throw new Exception('Tarefa não encontrada');
            }

            // Atualizar tarefa
            if (updateTask($id, $title, $description)) {
                $response = [
                    'success' => true,
                    'message' => 'Tarefa atualizada com sucesso!'
                ];

                // Se for AJAX, retornar JSON
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    echo json_encode($response);
                    exit;
                }

                // Caso contrário, redirecionar
                header('Location: index.php?success=updated');
                exit;
            } else {
                throw new Exception('Erro ao atualizar a tarefa');
            }
            break;

        // ============================================
        // DELETAR TAREFA
        // ============================================
        case 'delete':
            $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);

            // Validações
            if (!$id) {
                throw new Exception('ID da tarefa não fornecido');
            }

            // Verificar se tarefa existe
            $task = getTaskById($id);
            if (!$task) {
                throw new Exception('Tarefa não encontrada');
            }

            // Deletar tarefa
            if (deleteTask($id)) {
                $response = [
                    'success' => true,
                    'message' => 'Tarefa deletada com sucesso!'
                ];

                // Se for AJAX, retornar JSON
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    echo json_encode($response);
                    exit;
                }

                // Caso contrário, redirecionar
                header('Location: index.php?success=deleted');
                exit;
            } else {
                throw new Exception('Erro ao deletar a tarefa');
            }
            break;

        // ============================================
        // ALTERNAR STATUS (TOGGLE)
        // ============================================
        case 'toggle':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método de requisição inválido');
            }

            $id = isset($_POST['id']) ? intval($_POST['id']) : null;

            // Validações
            if (!$id) {
                throw new Exception('ID da tarefa não fornecido');
            }

            // Verificar se tarefa existe
            $task = getTaskById($id);
            if (!$task) {
                throw new Exception('Tarefa não encontrada');
            }

            // Alternar status
            if (toggleTaskStatus($id)) {
                $response = [
                    'success' => true,
                    'message' => 'Status da tarefa alterado com sucesso!'
                ];

                // Retornar JSON (sempre para toggle)
                echo json_encode($response);
                exit;
            } else {
                throw new Exception('Erro ao alterar o status da tarefa');
            }
            break;

        // ============================================
        // AÇÃO NÃO RECONHECIDA
        // ============================================
        default:
            throw new Exception('Ação não reconhecida: ' . htmlspecialchars($action));
    }

} catch (Exception $e) {
    // Tratamento de erros
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];

    // Se for AJAX, retornar JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    // Caso contrário, redirecionar com erro
    header('Location: index.php?error=' . urlencode($e->getMessage()));
    exit;
}

// Se chegou aqui, retornar resposta JSON padrão
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode($response);
}
?>
