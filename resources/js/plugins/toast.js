const Toast = {
  install(app) {
    app.config.globalProperties.$toast = {
      show(message, type = 'info', duration = 5000) {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
          <div class="toast-content">
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
          </div>
        `;
        
        const container = document.getElementById('toast-container') || this.createToastContainer();
        container.appendChild(toast);
        
        setTimeout(() => {
          if (toast.parentElement) {
            toast.remove();
          }
        }, duration);
      },
      
      success(message, duration) {
        this.show(message, 'success', duration);
      },
      
      error(message, duration) {
        this.show(message, 'error', duration);
      },
      
      warning(message, duration) {
        this.show(message, 'warning', duration);
      },
      
      info(message, duration) {
        this.show(message, 'info', duration);
      },
      
      createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
      }
    };
  }
};

export default Toast;