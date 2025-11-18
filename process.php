<?php

require_once('functions.php');

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : null);

$response = ['success' => false, 'message' => 'Ação não reconhecida'];

try {
    switch ($action) {
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método de requisição inválido');
            }

            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';

            if (empty(trim($title))) throw new Exception('O título da tarefa é obrigatório');
            if (strlen(trim($title)) < 3) throw new Exception('O título deve ter pelo menos 3 caracteres');
            if (strlen(trim($title)) > 100) throw new Exception('O título não pode ter mais de 100 caracteres');
            if (strlen(trim($description)) > 500) throw new Exception('A descrição não pode ter mais de 500 caracteres');

            if (addTask($title, $description)) {
                $response = ['success' => true, 'message' => 'Tarefa adicionada com sucesso!'];

                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    echo json_encode($response);
                    exit;
                }

                header('Location: index.php?success=added');
                exit;
            } else {
                throw new Exception('Erro ao salvar a tarefa');
            }
        break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método de requisição inválido');
            }

            $id = isset($_POST['id']) ? intval($_POST['id']) : null;
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';

            if (!$id) throw new Exception('ID da tarefa não fornecido');
            if (empty(trim($title))) throw new Exception('O título da tarefa é obrigatório');
            if (strlen(trim($title)) < 3) throw new Exception('O título deve ter pelo menos 3 caracteres');
            if (strlen(trim($title)) > 100) throw new Exception('O título não pode ter mais de 100 caracteres');
            if (strlen(trim($description)) > 500) throw new Exception('A descrição não pode ter mais de 500 caracteres');

            $task = getTaskById($id);
            if (!$task) throw new Exception('Tarefa não encontrada');

            if (updateTask($id, $title, $description)) {
                $response = ['success' => true, 'message' => 'Tarefa atualizada com sucesso!'];

                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    echo json_encode($response);
                    exit;
                }

                header('Location: index.php?success=updated');
                exit;
            } else {
                throw new Exception('Erro ao atualizar a tarefa');
            }
        break;

        case 'delete':
            $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);

            if (!$id) throw new Exception('ID da tarefa não fornecido');

            $task = getTaskById($id);
            if (!$task) throw new Exception('Tarefa não encontrada');

            if (deleteTask($id)) {
                $response = ['success' => true, 'message' => 'Tarefa deletada com sucesso!'];

                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                    echo json_encode($response);
                    exit;
                }

                header('Location: index.php?success=deleted');
                exit;
            } else {
                throw new Exception('Erro ao deletar a tarefa');
            }
        break;

        case 'toggle':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método de requisição inválido');
            }

            $id = isset($_POST['id']) ? intval($_POST['id']) : null;

            if (!$id) throw new Exception('ID da tarefa não fornecido');

            $task = getTaskById($id);
            if (!$task) throw new Exception('Tarefa não encontrada');

            if (toggleTaskStatus($id)) {
                $response = ['success' => true, 'message' => 'Status da tarefa alterado com sucesso!'];
                echo json_encode($response);
                exit;
            } else {
                throw new Exception('Erro ao alterar o status da tarefa');
            }
        break;

        default:
            throw new Exception('Ação não reconhecida: ' . htmlspecialchars($action));
    }

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    header('Location: index.php?error=' . urlencode($e->getMessage()));
    exit;
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode($response);
}

?>
