
function validateTaskForm() {
    const title = document.getElementById('title');
    const description = document.getElementById('description');
    
   
    clearErrors();
    
    let isValid = true;
    
    
    if (!title || title.value.trim() === '') {
        showError(title, 'O título da tarefa é obrigatório');
        isValid = false;
    } else if (title.value.trim().length < 3) {
        showError(title, 'O título deve ter pelo menos 3 caracteres');
        isValid = false;
    } else if (title.value.trim().length > 100) {
        showError(title, 'O título não pode ter mais de 100 caracteres');
        isValid = false;
    }
    
   
    if (description && description.value.trim().length > 500) {
        showError(description, 'A descrição não pode ter mais de 500 caracteres');
        isValid = false;
    }
    
    return isValid;
}


function showError(element, message) {
    if (!element) return;
    
    element.classList.add('error');
    element.style.borderColor = '#e74c3c';
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#e74c3c';
    errorDiv.style.fontSize = '0.85rem';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    
    element.parentNode.appendChild(errorDiv);
}


function clearErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(msg => msg.remove());
    
    const errorFields = document.querySelectorAll('.error');
    errorFields.forEach(field => {
        field.classList.remove('error');
        field.style.borderColor = '';
    });
}


function confirmDelete(taskId, taskTitle) {
    const message = `Tem certeza que deseja deletar a tarefa "${taskTitle}"?\n\nEsta ação não pode ser desfeita.`;
    return confirm(message);
}


function confirmAction(message) {
    return confirm(message);
}


function toggleTaskStatus(taskId, element) {
    const formData = new FormData();
    formData.append('action', 'toggle');
    formData.append('id', taskId);
    
    fetch('process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            
            const taskCard = element.closest('.task-card');
            if (taskCard) {
                taskCard.classList.toggle('completed');
                
                
                const statusBadge = taskCard.querySelector('.task-status');
                if (statusBadge) {
                    if (statusBadge.classList.contains('pending')) {
                        statusBadge.classList.remove('pending');
                        statusBadge.classList.add('completed');
                        statusBadge.textContent = 'Concluída';
                    } else {
                        statusBadge.classList.remove('completed');
                        statusBadge.classList.add('pending');
                        statusBadge.textContent = 'Pendente';
                    }
                }
            }
            
            
            updateStats();
            
            showNotification('Status da tarefa atualizado com sucesso!', 'success');
        } else {
            showNotification('Erro ao atualizar tarefa: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showNotification('Erro ao processar requisição', 'error');
    });
}


function updateStats() {
    const totalCards = document.querySelectorAll('.task-card').length;
    const completedCards = document.querySelectorAll('.task-card.completed').length;
    const pendingCards = totalCards - completedCards;
    const completionPercentage = totalCards > 0 ? Math.round((completedCards / totalCards) * 100) : 0;
    
    
    const statCards = document.querySelectorAll('.stat-card');
    if (statCards.length >= 4) {
        statCards[0].querySelector('.stat-number').textContent = totalCards;
        statCards[1].querySelector('.stat-number').textContent = completedCards;
        statCards[2].querySelector('.stat-number').textContent = pendingCards;
        statCards[3].querySelector('.stat-number').textContent = completionPercentage + '%';
    }
}


function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.maxWidth = '400px';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}


function formatDate(dateString) {
    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    return new Date(dateString).toLocaleDateString('pt-BR', options);
}


function countTasks() {
    const taskCards = document.querySelectorAll('.task-card');
    return taskCards.length;
}


function countCompletedTasks() {
    const completedCards = document.querySelectorAll('.task-card.completed');
    return completedCards.length;
}


document.addEventListener('DOMContentLoaded', function() {
    
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateTaskForm()) {
                e.preventDefault();
            }
        });
    });
    
    
    const inputs = document.querySelectorAll('input[type="text"], textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                this.style.borderColor = '';
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) errorMsg.remove();
            }
        });
    });
    
    console.log('Sistema de Tarefas inicializado com sucesso!');
});


 
function toggleElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = element.style.display === 'none' ? 'block' : 'none';
    }
}


function clearForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        clearErrors();
    }
}


function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Texto copiado para a área de transferência!', 'success');
    }).catch(() => {
        showNotification('Erro ao copiar texto', 'error');
    });
}
