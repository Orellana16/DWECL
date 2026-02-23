const formulario = document.getElementById('miFormulario');
const mensajeDiv = document.getElementById('mensajeRespuesta');
const btnEnviar = document.getElementById('btnEnviar');
formulario.addEventListener('submit', async function (e) {
    e.preventDefault(); // 1. Evitamos que el navegador recargue la página
    // UX: Feedback visual (Deshabilitar botón)
    btnEnviar.disabled = true;
    btnEnviar.innerText = "Enviando...";
    mensajeDiv.innerText = "";
    // 2. Empaquetado automático de datos
    const datos = new FormData(formulario);
    try {
        // 3. Petición al servidor (La Promesa)
        const respuesta = await fetch('servidor.php', {
            method: 'POST',
            body: datos
        });
        // 4. Validación técnica (¿El servidor respondió un código 200?)
        if (!respuesta.ok) throw new Error("Error HTTP: " + respuesta.status);
        // 5. Desempaquetado (Leer JSON)
        const data = await respuesta.json();
        // 6. Mostrar resultado
        if (data.status === "ok") {
            mensajeDiv.style.color = "green";
            mensajeDiv.innerText = `Éxito: ${data.mensaje}`;
            formulario.reset(); // Limpiar campos
        } else {
            throw new Error(data.error || "Error desconocido");
        }
    } catch (error) {
        // Manejo de errores (Red caída o error lanzado manualmente)
        console.error("Hubo un problema:", error);
        mensajeDiv.style.color = "red";
        mensajeDiv.innerText = "Error: " + error.message;
    } finally {
        // UX: Restaurar botón siempre, pase lo que pase
        btnEnviar.disabled = false;
        btnEnviar.innerText = "Registrar";
    }
});