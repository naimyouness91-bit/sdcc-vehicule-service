/**
 * Modern Toast Notification System
 * Production-ready notifications with smooth animations
 * Theme: NFS-inspired dark design with color-coded status
 */

class ToastNotification {
    constructor(options = {}) {
        this.message = options.message || '';
        this.title = options.title || '';
        this.type = options.type || 'info'; // success, error, warning, info
        this.duration = options.duration || 4500;
        this.dismissible = options.dismissible !== false;
        this.container = this.getContainer();
        this.element = null;
        this.timeout = null;
    }

    /**
     * Get or create toast container in DOM
     */
    getContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Get icon based on toast type
     */
    getIcon() {
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-times-circle',
            warning: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };
        return icons[this.type] || icons.info;
    }

    /**
     * Get color based on toast type
     */
    getColor() {
        const colors = {
            success: '#00d084',
            error: '#ff4757',
            warning: '#ffa500',
            info: '#0066ff'
        };
        return colors[this.type] || colors.info;
    }

    /**
     * Get title based on type if not provided
     */
    getTitle() {
        if (this.title) return this.title;
        const titles = {
            success: 'Succès',
            error: 'Erreur',
            warning: 'Attention',
            info: 'Information'
        };
        return titles[this.type] || 'Notification';
    }

    /**
     * Create and display the toast with smooth animation
     */
    show() {
        // Create toast element
        this.element = document.createElement('div');
        this.element.className = `toast toast-${this.type}`;
        
        const icon = this.getIcon();
        const title = this.getTitle();
        const color = this.getColor();
        
        this.element.innerHTML = `
            <div class="toast-icon" style="color: ${color};">
                <i class="${icon}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                ${this.message ? `<div class="toast-message">${this.message}</div>` : ''}
            </div>
            ${this.dismissible ? '<button type="button" class="toast-close" aria-label="Fermer"><i class="fas fa-times"></i></button>' : ''}
            <div class="toast-progress" style="background-color: ${color};"></div>
        `;

        // Add to container
        this.container.appendChild(this.element);
        
        // Trigger animation by forcing reflow
        this.element.offsetHeight;
        this.element.classList.add('show');

        // Add close button event listener
        if (this.dismissible) {
            const closeBtn = this.element.querySelector('.toast-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.dismiss());
            }
        }

        // Add hover pause functionality
        this.element.addEventListener('mouseenter', () => {
            if (this.timeout) clearTimeout(this.timeout);
        });
        this.element.addEventListener('mouseleave', () => {
            if (this.duration > 0) {
                this.timeout = setTimeout(() => this.dismiss(), this.duration);
            }
        });

        // Auto-dismiss after duration
        if (this.duration > 0) {
            this.timeout = setTimeout(() => this.dismiss(), this.duration);
        }

        return this.element;
    }

    /**
     * Dismiss and remove the toast with animation
     */
    dismiss() {
        if (!this.element) return;

        // Clear timeout if exists
        if (this.timeout) clearTimeout(this.timeout);

        // Add dismiss animation
        this.element.classList.remove('show');
        this.element.classList.add('hide');

        // Remove after animation completes
        setTimeout(() => {
            if (this.element && this.element.parentNode) {
                this.element.parentNode.removeChild(this.element);
            }
        }, 300);
    }
}

/**
 * Global helper functions for easy access
 * Usage: Toast.success('Message'), Toast.error('Error message'), etc.
 */
window.Toast = {
    /**
     * Show success toast
     */
    success: function(message, title = '') {
        return new ToastNotification({
            message,
            title: title || 'Succès',
            type: 'success',
            duration: 4500
        }).show();
    },

    /**
     * Show error toast
     */
    error: function(message, title = '') {
        return new ToastNotification({
            message,
            title: title || 'Erreur',
            type: 'error',
            duration: 5500
        }).show();
    },

    /**
     * Show warning toast
     */
    warning: function(message, title = '') {
        return new ToastNotification({
            message,
            title: title || 'Attention',
            type: 'warning',
            duration: 4500
        }).show();
    },

    /**
     * Show info toast
     */
    info: function(message, title = '') {
        return new ToastNotification({
            message,
            title: title || 'Information',
            type: 'info',
            duration: 4000
        }).show();
    },

    /**
     * Show custom toast with options
     */
    show: function(options) {
        return new ToastNotification(options).show();
    }
};

/**
 * Auto-display flash messages from Laravel as toasts
 * Called on page load to display any flash messages from session
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get flash message data from data attributes
    const successElements = document.querySelectorAll('[data-toast-success]');
    const errorElements = document.querySelectorAll('[data-toast-error]');
    const warningElements = document.querySelectorAll('[data-toast-warning]');
    const infoElements = document.querySelectorAll('[data-toast-info]');

    // Display success messages
    successElements.forEach(el => {
        const msg = el.getAttribute('data-toast-success');
        if (msg) Toast.success(msg);
    });

    // Display error messages
    errorElements.forEach(el => {
        const msg = el.getAttribute('data-toast-error');
        if (msg) Toast.error(msg);
    });

    // Display warning messages
    warningElements.forEach(el => {
        const msg = el.getAttribute('data-toast-warning');
        if (msg) Toast.warning(msg);
    });

    // Display info messages
    infoElements.forEach(el => {
        const msg = el.getAttribute('data-toast-info');
        if (msg) Toast.info(msg);
    });
});

/**
 * Function to display toasts from AJAX responses
 * Usage: displayToastFromResponse(response);
 */
function displayToastFromResponse(response) {
    if (!response) return;

    if (response.success !== undefined) {
        // Response has success boolean
        if (response.success && response.message) {
            Toast.success(response.message, response.title);
        } else if (!response.success && response.message) {
            Toast.error(response.message, response.title);
        }
    } else if (response.message) {
        // Response has message but no success boolean
        const type = response.type || 'info';
        Toast.show({
            message: response.message,
            title: response.title,
            type: type
        });
    }
}

/**
 * Display error toast from fetch/axios errors
 */
function displayErrorToast(error, defaultMessage = 'Une erreur est survenue') {
    if (error.response && error.response.data) {
        const data = error.response.data;
        if (data.message) {
            Toast.error(data.message);
        } else if (data.error) {
            Toast.error(data.error);
        } else {
            Toast.error(defaultMessage);
        }
    } else if (error.message) {
        Toast.error(error.message);
    } else {
        Toast.error(defaultMessage);
    }
}
