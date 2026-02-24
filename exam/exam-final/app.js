import { API } from './api.js';
import { UI } from './ui.js';

/**
 * Controlador Principal: Inicializa eventos y gestiona el flujo de datos.
 */
document.addEventListener('DOMContentLoaded', () => {
    actualizarVista();
    UI.form.onsubmit = manejarEnvio;
});

// Función central para refrescar datos y persistencia local
async function actualizarVista() {
    try {
        // 1. Intentamos la vía principal: Internet
        const productos = await API.obtenerProductos();

        // 2. Si funciona, actualizamos la "foto" en el almacén local
        localStorage.setItem('productos_cache', JSON.stringify(productos));

        console.log("Datos recibidos:", productos[0]);

        // 3. Pintamos los datos reales
        UI.renderizar(productos, {
            onEdit: (p) => llenarCamposForm(p),
            onDelete: (id) => eliminar(id)
        });

    } catch (err) {
        console.warn("Servidor offline. Cargando desde caché...");

        // Intentamos recuperar el texto guardado anteriormente
        const datosCache = localStorage.getItem('productos_cache');

        if (datosCache) {
            // Como es texto, lo convertimos de nuevo a un Array de objetos
            const productosRecuperados = JSON.parse(datosCache);

            // Pintamos la tabla con los datos viejos
            UI.renderizar(productosRecuperados, {
                onEdit: (p) => alert("No puedes editar productos sin conexión"),
                onDelete: (id) => alert("No puedes borrar productos sin conexión")
            });

            alert("⚠️ Trabajando en modo sin conexión (datos locales).");
        } else {
            alert("No hay conexión ni datos guardados.");
        }
    }
}

// Gestión de inserción/actualización con validación avanzada
async function manejarEnvio(e) {
    e.preventDefault(); // Evita la recarga síncrona del navegador

    // Validación de lógica de negocio (Longitud del código requerida por el examen)
    const codigo = document.getElementById('codigo').value;
    if (codigo.length !== 9) {
        alert("El código debe tener exactamente 9 caracteres.");
        return;
    }

    UI.setCargando(true); // Bloqueo de UI (UX avanzada)

    try {
        const datos = new FormData(UI.form);
        const respuesta = await API.guardarProducto(datos);

        if (respuesta.status === "ok") {
            UI.limpiarFormulario();
            await actualizarVista();
        }
    } catch (err) {
        alert("Error de red: " + err.message);
    } finally {
        // El bloque finally asegura que el botón se libere aunque la petición falle
        UI.setCargando(false);
    }
}

// Carga los datos de un producto en el formulario para su edición
function llenarCamposForm(p) {
    document.getElementById('prodId').value = p.id;
    document.getElementById('codigo').value = p.codigo;
    document.getElementById('nombre').value = p.nombre;
    document.getElementById('talla').value = p.talla;
    document.getElementById('precio').value = p.precio;
    document.getElementById('email_creador').value = p.email_creador;

    UI.btnEnviar.textContent = "Actualizar Registro";
    window.scrollTo(0, 0); // Mejora de UX: sube al formulario
}

// Gestión del borrado con confirmación
async function eliminar(id) {
    if (confirm("¿Estás seguro de eliminar este producto?")) {
        try {
            await API.eliminarProducto(id);
            await actualizarVista();
        } catch (err) {
            alert(err.message);
        }
    }
}