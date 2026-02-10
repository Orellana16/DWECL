const formulario = document.getElementById('miFormulario');
const tablaBody = document.getElementById('tablaUsuarios');

// Cargar tabla al iniciar
document.addEventListener('DOMContentLoaded', cargarUsuarios);

async function cargarUsuarios() {
    const res = await fetch('servidor.php');
    const usuarios = await res.json();
    tablaBody.innerHTML = usuarios.map(u => `
        <tr>
            <td>${u.id}</td>
            <td>${u.nombre}</td>
            <td>${u.correo}</td>
            <td>${u.movil}</td>
            <td>${u.edad}</td>
            <td>${u.idioma}</td>
            <td>
                <button onclick="editarUsuario(${JSON.stringify(u).replace(/"/g, '&quot;')})">Editar</button>
                <button onclick="borrarUsuario(${u.id})">Borrar</button>
            </td>
        </tr>
    `).join('');
}

formulario.addEventListener('submit', async (e) => {
    e.preventDefault();
    const datos = new FormData(formulario);
    await fetch('servidor.php', { method: 'POST', body: datos });
    formulario.reset();
    document.getElementById('userId').value = ""; // Limpiar ID oculto
    cargarUsuarios();
});

async function borrarUsuario(id) {
    if(confirm('¿Seguro?')) {
        await fetch(`servidor.php?id=${id}`, { method: 'DELETE' });
        cargarUsuarios();
    }
}

function editarUsuario(u) {
    document.getElementById('userId').value = u.id;
    document.getElementById('nombre').value = u.nombre;
    document.getElementById('correo').value = u.correo;
    document.getElementById('movil').value = u.movil;
    document.getElementById('edad').value = u.edad;
    document.getElementById('idioma').value = u.idioma;
}