/**
 * Sistema de Gerenciamento de Tarefas
 * Script JavaScript para validações e interatividades
 */

// ============================================
// VALIDAÇÃO DE FORMULÁRIOS
// ============================================

/**
 * Valida o formulário de tarefa
 */
function validateTaskForm() {
    const title = document.getElementById('title');
    const description = document.getElementById('description');
    
    // Limpar mensagens de erro anteriores
    clearErrors();
    
    let isValid = true;
    
    // Validar título
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
    
    // Validar descrição
    if (description && description.value.trim().length > 500) {
        showError(description, 'A descrição não pode ter mais de 500 caracteres');
        isValid = false;
    }
    
    return isValid;
}

/**
 * Exibe mensagem de erro em um campo
 */
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

/**
 * Limpa todas as mensagens de erro
 */
function clearErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(msg => msg.remove());
    
    const errorFields = document.querySelectorAll('.error');
    errorFields.forEach(field => {
        field.classList.remove('error');
        field.style.borderColor = '';
    });
}

// ============================================
// CONFIRMAÇÃO DE AÇÕES
// ============================================

/**
 * Confirma a exclusão de uma tarefa
 */
function confirmDelete(taskId, taskTitle) {
    const message = `Tem certeza que deseja deletar a tarefa "${taskTitle}"?\n\nEsta ação não pode ser desfeita.`;
    return confirm(message);
}

/**
 * Confirma ações gerais
 */
function confirmAction(message) {
    return confirm(message);
}

// ============================================
// MANIPULAÇÃO DE TAREFAS VIA AJAX
// ============================================

/**
 * Alterna o status de uma tarefa sem recarregar a página
 */
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
            // Atualizar visualmente o card
            const taskCard = element.closest('.task-card');
            if (taskCard) {
                taskCard.classList.toggle('completed');
                
                // Atualizar status
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
            
            // Atualizar estatísticas
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

/**
 * Atualiza as estatísticas em tempo real
 */
function updateStats() {
    const totalCards = document.querySelectorAll('.task-card').length;
    const completedCards = document.querySelectorAll('.task-card.completed').length;
    const pendingCards = totalCards - completedCards;
    const completionPercentage = totalCards > 0 ? Math.round((completedCards / totalCards) * 100) : 0;
    
    // Atualizar os números nas estatísticas
    const statCards = document.querySelectorAll('.stat-card');
    if (statCards.length >= 4) {
        statCards[0].querySelector('.stat-number').textContent = totalCards;
        statCards[1].querySelector('.stat-number').textContent = completedCards;
        statCards[2].querySelector('.stat-number').textContent = pendingCards;
        statCards[3].querySelector('.stat-number').textContent = completionPercentage + '%';
    }
}

// ============================================
// NOTIFICAÇÕES
// ============================================

/**
 * Exibe uma notificação na tela
 */
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
    
    // Remover após 5 segundos
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// ============================================
// UTILITÁRIOS
// ============================================

/**
 * Formata uma data para exibição
 */
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

/**
 * Conta o número de tarefas
 */
function countTasks() {
    const taskCards = document.querySelectorAll('.task-card');
    return taskCards.length;
}

/**
 * Conta o número de tarefas concluídas
 */
function countCompletedTasks() {
    const completedCards = document.querySelectorAll('.task-card.completed');
    return completedCards.length;
}

// ============================================
// INICIALIZAÇÃO
// ============================================

/**
 * Inicializa os event listeners quando o DOM está pronto
 */
document.addEventListener('DOMContentLoaded', function() {
    // Validar formulário ao submeter
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateTaskForm()) {
                e.preventDefault();
            }
        });
    });
    
    // Limpar erros ao digitar
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

// ============================================
// FUNÇÕES AUXILIARES DE INTERFACE
// ============================================

/**
 * Alterna a visibilidade de um elemento
 */
function toggleElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = element.style.display === 'none' ? 'block' : 'none';
    }
}

/**
 * Limpa um formulário
 */
function clearForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        clearErrors();
    }
}

/**
 * Copia um texto para a área de transferência
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Texto copiado para a área de transferência!', 'success');
    }).catch(() => {
        showNotification('Erro ao copiar texto', 'error');
    });
}
