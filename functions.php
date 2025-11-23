<?php

require_once('db_connect.php');

function loadTasks() {
    global $conn;
    $tasks = [];
    
   
    $sql = "SELECT id, title, description, is_completed, created_at, updated_at FROM tasks ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            
            $row = array_change_key_case($row, CASE_LOWER); 
            
            $row['completed'] = (bool)$row['is_completed'];
            unset($row['is_completed']);
            $tasks[] = $row;
        }
    }
    
    return $tasks;
}

function addTask($title, $description) {
    global $conn;

    if (empty(trim($title))) {
        return false;
    }

    $sql = "INSERT INTO tasks (title, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("ss", $title, $description);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function getTaskById($id) {
    global $conn;
    
    
    $sql = "SELECT id, title, description, is_completed, created_at, updated_at FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        return null;
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
    $stmt->close();
    
    if ($task) {
        
        $task = array_change_key_case($task, CASE_LOWER);
        
        $task['completed'] = (bool)$task['is_completed'];
        unset($task['is_completed']);
    }
    
    return $task;
}

function updateTask($id, $title, $description) {
    global $conn;

    if (empty(trim($title))) {
        return false;
    }

    $sql = "UPDATE tasks SET title = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("ssi", $title, $description, $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function deleteTask($id) {
    global $conn;

    $sql = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function toggleTaskStatus($id) {
    global $conn;

    $task = getTaskById($id);
    if (!$task) {
        return false;
    }
    
    $newStatus = $task['completed'] ? 0 : 1;

    $sql = "UPDATE tasks SET is_completed = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $stmt->bind_param("ii", $newStatus, $id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function getTotalTasks() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM tasks");
    if ($result) {
        return (int)$result->fetch_assoc()['total'];
    }
    return 0;
}

function getCompletedTasks() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM tasks WHERE is_completed = 1");
    if ($result) {
        return (int)$result->fetch_assoc()['total'];
    }
    return 0;
}

function getPendingTasks() {
    return getTotalTasks() - getCompletedTasks();
}

?>