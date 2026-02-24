/**
 * Módulo de UI: Gestión del DOM y Experiencia de Usuario (UX).
 */
export const UI = {
    tabla: document.getElementById('tablaProductos'),
    form: document.getElementById('formProducto'),
    btnEnviar: document.getElementById('btnEnviar'),

    // Genera las filas de la tabla dinámicamente
    renderizar(productos, { onEdit, onDelete }) {
        this.tabla.innerHTML = ''; // Limpieza previa del contenedor

        productos.forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${p.id}</td>
                <td><strong>${p.codigo}</strong></td>
                <td>${p.nombre}</td>
                <td>${p.talla}</td>
                <td>${p.precio}€</td>
                <td>${p.email_creador}</td>
                <td>
                    <button class="btn-edit">Editar</button>
                    <button class="btn-delete">Borrar</button>
                </td>
            `;

            // Asignación de eventos delegada a funciones callback para mayor modularidad
            tr.querySelector('.btn-edit').onclick = () => onEdit(p);
            tr.querySelector('.btn-delete').onclick = () => onDelete(p.id);

            this.tabla.appendChild(tr);
        });
    },

    // Control de flujo para evitar duplicidad de peticiones (UX)
    setCargando(estado) {
        this.btnEnviar.disabled = estado;
        this.btnEnviar.textContent = estado ? "Enviando..." : "Guardar Producto";
    },

    // Reseteo de campos y estados ocultos
    limpiarFormulario() {
        this.form.reset();
        document.getElementById('prodId').value = '';
        this.btnEnviar.textContent = "Guardar Producto";
    }
};