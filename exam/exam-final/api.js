/**
 * Módulo de API: Gestión de peticiones HTTP.
 * Encapsula la lógica asíncrona para que el resto de la app no dependa del backend.
 */
export const API = {
    url: 'servidor.php',

    // Obtener todos los productos (Petición GET)
    async obtenerProductos() {
        // fetch devuelve una promesa. Usamos await para esperar el objeto Response.
        const res = await fetch(this.url);
        
        // El bloque catch no detecta errores 404/500, por lo que validamos res.ok
        if (!res.ok) throw new Error("No se pudo conectar con el servidor.");
        
        // El método .json() también es asíncrono y requiere await.
        return await res.json();
    },

    // Crear o actualizar (Petición POST)
    async guardarProducto(formData) {
        const res = await fetch(this.url, {
            method: 'POST',
            body: formData 
            // NOTA: Al usar FormData, el navegador añade automáticamente el Boundary 
            // del Content-Type. No se debe añadir manualmente en los headers.
        });
        
        if (!res.ok) throw new Error("Error en la operación de guardado.");
        return await res.json();
    },

    // Eliminar registro (Petición DELETE)
    async eliminarProducto(id) {
        // Pasamos el ID por la URL (Query String)
        const res = await fetch(`${this.url}?id=${id}`, { method: 'DELETE' });
        if (!res.ok) throw new Error("Error al intentar eliminar el registro.");
        return await res.json();
    }
};