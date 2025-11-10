<?php
/**
 * Arquivo de Funções Auxiliares
 * Contém todas as funções para manipulação de tarefas
 */

// Caminho do arquivo de dados
define('DATA_FILE', __DIR__ . '/data/tasks.json');

/**
 * Carrega todas as tarefas do arquivo JSON
 * 
 * @return array Array de tarefas
 */
function loadTasks() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    
    $json = file_get_contents(DATA_FILE);
    $tasks = json_decode($json, true);
    
    return is_array($tasks) ? $tasks : [];
}

/**
 * Salva as tarefas no arquivo JSON
 * 
 * @param array $tasks Array de tarefas a salvar
 * @return bool True se salvou com sucesso, False caso contrário
 */
function saveTasks($tasks) {
    $json = json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(DATA_FILE, $json) !== false;
}

/**
 * Adiciona uma nova tarefa
 * 
 * @param string $title Título da tarefa
 * @param string $description Descrição da tarefa
 * @return bool True se adicionou com sucesso
 */
function addTask($title, $description) {
    // Validação
    if (empty(trim($title))) {
        return false;
    }
    
    $tasks = loadTasks();
    
    // Criar nova tarefa
    $newTask = [
        'id' => time(), // Usar timestamp como ID único
        'title' => sanitize($title),
        'description' => sanitize($description),
        'completed' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $tasks[] = $newTask;
    
    return saveTasks($tasks);
}

/**
 * Obtém uma tarefa específica pelo ID
 * 
 * @param int $id ID da tarefa
 * @return array|null Array da tarefa ou null se não encontrada
 */
function getTaskById($id) {
    $tasks = loadTasks();
    
    foreach ($tasks as $task) {
        if ($task['id'] == $id) {
            return $task;
        }
    }
    
    return null;
}

/**
 * Atualiza uma tarefa existente
 * 
 * @param int $id ID da tarefa
 * @param string $title Novo título
 * @param string $description Nova descrição
 * @return bool True se atualizou com sucesso
 */
function updateTask($id, $title, $description) {
    if (empty(trim($title))) {
        return false;
    }
    
    $tasks = loadTasks();
    
    foreach ($tasks as &$task) {
        if ($task['id'] == $id) {
            $task['title'] = sanitize($title);
            $task['description'] = sanitize($description);
            $task['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    
    return saveTasks($tasks);
}

/**
 * Deleta uma tarefa
 * 
 * @param int $id ID da tarefa a deletar
 * @return bool True se deletou com sucesso
 */
function deleteTask($id) {
    $tasks = loadTasks();
    
    foreach ($tasks as $key => $task) {
        if ($task['id'] == $id) {
            unset($tasks[$key]);
            // Reindexar o array
            $tasks = array_values($tasks);
            return saveTasks($tasks);
        }
    }
    
    return false;
}

/**
 * Alterna o status de uma tarefa (Pendente/Concluída)
 * 
 * @param int $id ID da tarefa
 * @return bool True se atualizou com sucesso
 */
function toggleTaskStatus($id) {
    $tasks = loadTasks();
    
    foreach ($tasks as &$task) {
        if ($task['id'] == $id) {
            $task['completed'] = !$task['completed'];
            $task['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    
    return saveTasks($tasks);
}

/**
 * Sanitiza uma string para evitar XSS
 * 
 * @param string $input String a sanitizar
 * @return string String sanitizada
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Formata uma data para exibição
 * 
 * @param string $date Data no formato Y-m-d H:i:s
 * @return string Data formatada
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Retorna o número total de tarefas
 * 
 * @return int Total de tarefas
 */
function getTotalTasks() {
    $tasks = loadTasks();
    return count($tasks);
}

/**
 * Retorna o número de tarefas concluídas
 * 
 * @return int Total de tarefas concluídas
 */
function getCompletedTasks() {
    $tasks = loadTasks();
    $completed = 0;
    
    foreach ($tasks as $task) {
        if ($task['completed']) {
            $completed++;
        }
    }
    
    return $completed;
}

/**
 * Retorna o número de tarefas pendentes
 * 
 * @return int Total de tarefas pendentes
 */
function getPendingTasks() {
    return getTotalTasks() - getCompletedTasks();
}
?>
