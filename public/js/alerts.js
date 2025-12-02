/**
 * SISTEMA DE ALERTAS ELEGANTES
 * 
 * Funciones utilitarias para mostrar notificaciones de error y éxito
 * con animaciones visuales mejores que los alert() estándar.
 */

/**
 * Muestra una alerta de error con popup elegante
 * 
 * @param {string} mensaje - Mensaje a mostrar
 * @param {number} duracion - Duración en ms (0 = sin auto-cierre)
 */
function showErrorAlert(mensaje, duracion = 5000) {
  showAlert(mensaje, 'error', duracion);
}

/**
 * Muestra una alerta de éxito con popup elegante
 * 
 * @param {string} mensaje - Mensaje a mostrar
 * @param {number} duracion - Duración en ms (0 = sin auto-cierre)
 */
function showSuccessAlert(mensaje, duracion = 5000) {
  showAlert(mensaje, 'ok', duracion);
}

/**
 * Muestra una alerta genérica con popup elegante
 * 
 * @param {string} mensaje - Mensaje a mostrar
 * @param {string} tipo - Tipo: 'error', 'ok', 'warning', 'info'
 * @param {number} duracion - Duración en ms (0 = sin auto-cierre)
 */
function showAlert(mensaje, tipo = 'info', duracion = 5000) {
  // Crear contenedor del popup si no existe
  let alertContainer = document.getElementById('alert-container');
  if (!alertContainer) {
    alertContainer = document.createElement('div');
    alertContainer.id = 'alert-container';
    alertContainer.style.cssText = `
      position: fixed;
      top: 70px;
      right: 20px;
      z-index: 9999;
      max-width: 400px;
    `;
    document.body.appendChild(alertContainer);
  }

  // Crear elemento de alerta
  const alertEl = document.createElement('div');
  alertEl.className = `alert alert-popup ${tipo}`;
  alertEl.style.cssText = `
    margin-bottom: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: center;
    animation: slideDownAlert 0.4s ease;
  `;

  // Contenido
  const contenido = document.createElement('span');
  contenido.textContent = mensaje;
  alertEl.appendChild(contenido);

  // Botón de cierre
  const closeBtn = document.createElement('button');
  closeBtn.textContent = '✕';
  closeBtn.style.cssText = `
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 18px;
    font-weight: bold;
    padding: 0;
    margin-left: 10px;
    opacity: 0.7;
    transition: opacity 0.2s;
  `;
  closeBtn.onmouseover = () => closeBtn.style.opacity = '1';
  closeBtn.onmouseout = () => closeBtn.style.opacity = '0.7';
  closeBtn.onclick = () => eliminarAlerta(alertEl);

  alertEl.appendChild(closeBtn);
  alertContainer.appendChild(alertEl);

  // Auto-cierre
  if (duracion > 0) {
    setTimeout(() => eliminarAlerta(alertEl), duracion);
  }
}

/**
 * Elimina una alerta con animación de salida
 * 
 * @param {HTMLElement} alertEl - Elemento de alerta a remover
 */
function eliminarAlerta(alertEl) {
  alertEl.style.animation = 'slideUpAlert 0.3s ease';
  setTimeout(() => {
    if (alertEl.parentNode) {
      alertEl.parentNode.removeChild(alertEl);
    }
  }, 300);
}

/**
 * Muestra una alerta de error de duplicado
 * 
 * @param {string} tipo - Tipo de registro: 'correo', 'comida', 'nutriente'
 * @param {string} valor - Valor duplicado (opcional)
 */
function showDuplicateAlert(tipo, valor = '') {
  let mensaje = '';
  
  switch (tipo) {
    case 'correo':
      mensaje = `⚠️ Este correo ya está registrado en el sistema`;
      break;
    case 'comida':
      mensaje = `⚠️ Ya existe una comida con ese nombre`;
      break;
    case 'nutriente':
      mensaje = `⚠️ Ya existe un nutriente con ese nombre`;
      break;
    default:
      mensaje = `⚠️ Este registro ya existe`;
  }
  
  showErrorAlert(mensaje, 6000);
}

// Agregar animación de salida si no existe
if (!document.querySelector('style[data-alerts-css]')) {
  const style = document.createElement('style');
  style.setAttribute('data-alerts-css', 'true');
  style.textContent = `
    @keyframes slideUpAlert {
      from {
        opacity: 1;
        transform: translateY(0);
      }
      to {
        opacity: 0;
        transform: translateY(-20px);
      }
    }
    
    .alert-popup {
      padding: 14px 16px;
      border-radius: 10px;
      font-weight: 500;
      font-size: 14px;
      border-left: 4px solid;
      background: white;
      animation: slideDownAlert 0.4s ease;
    }
    
    .alert-popup.error {
      background: #fee2e2;
      color: #991b1b;
      border-color: #dc2626;
    }
    
    .alert-popup.ok {
      background: #dcfce7;
      color: #065f46;
      border-color: #16a34a;
    }
    
    .alert-popup.warning {
      background: #fef3c7;
      color: #92400e;
      border-color: #f59e0b;
    }
    
    .alert-popup.info {
      background: #dbeafe;
      color: #1e40af;
      border-color: #3b82f6;
    }
  `;
  document.head.appendChild(style);
}
