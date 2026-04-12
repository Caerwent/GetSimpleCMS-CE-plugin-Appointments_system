document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success messages after 5 seconds
    const successMessages = document.querySelectorAll('.updated');
    successMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.transition = 'opacity 0.5s';
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 500);
        }, 5000);
    });

    // Confirmation for delete actions
    const deleteButtons = document.querySelectorAll('button[name*="delete"]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm(button.getAttribute('data-confirm') || 'Are you sure?')) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Table row highlighting
    const tableRows = document.querySelectorAll('.edittable tbody tr');
    tableRows.forEach(function(row) {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f0f8ff';
        });
        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('appointment-cancelled')) {
                this.style.backgroundColor = '';
            }
        });
    });
});
