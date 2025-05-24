function openTab(evt, tabName) {
  const tabcontents = document.getElementsByClassName("tabcontent");
  for (let i = 0; i < tabcontents.length; i++) {
    tabcontents[i].classList.remove("active");
  }

  const tablinks = document.getElementsByClassName("tablink");
  for (let i = 0; i < tablinks.length; i++) {
    tablinks[i].classList.remove("active");
  }

  document.getElementById(tabName).classList.add("active");
  evt.currentTarget.classList.add("active");
}


document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#form-reporte');
  const tablaBody = document.querySelector('#tabla-reportes tbody');
  const botonSubmit = form.querySelector('#btn-submit');
  const botonCancelar = form.querySelector('#btn-cancelar');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const datos = Object.fromEntries(new FormData(form).entries());

    const url = datos.id
      ? `http://localhost/Proyecto_TecnologiasWeb/olaDeCambio_app/backend/api/reportes/${datos.id}`
      : `http://localhost/Proyecto_TecnologiasWeb/olaDeCambio_app/backend/api/reportes`;

    const metodo = datos.id ? 'PUT' : 'POST';

    if (!datos.id) delete datos.id;

    const response = await fetch(url, {
      method: metodo,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos)
    });

    const res = await response.json();

    if (res.status === 'ok') {
      alert('Reporte guardado correctamente');
      form.reset();
      botonSubmit.textContent = "Enviar Reporte";
      botonCancelar.style.display = "none";
      cargarReportes();
    } else {
      alert('Error al guardar el reporte');
    }
  });

  async function cargarReportes() {
    const response = await fetch('http://localhost/Proyecto_TecnologiasWeb/olaDeCambio_app/backend/api/reportes');
    const reportes = await response.json();

    tablaBody.innerHTML = '';
    reportes.forEach(r => {
      const fila = document.createElement('tr');

      const tipoMap = {
        contaminacion: 'Contaminación marina',
        fauna: 'Avistamiento de fauna en peligro',
        pesca: 'Pesca ilegal',
        otro: 'Otro'
      };

      fila.innerHTML = `
        <td style="display:none;">${r.id}</td>
        <td>${r.nombre_completo}</td>
        <td>${r.correo_electronico}</td>
        <td>${tipoMap[r.tipo_reporte] || r.tipo_reporte}</td>
        <td>${r.ubicacion}</td>
        <td>${r.fecha_incidente}</td>
        <td>
          <button class="editar" data-id="${r.id}">Editar</button>
          <button class="eliminar" data-id="${r.id}" style="background-color: #e53935; color: white;">Eliminar</button>
        </td>
      `;
      tablaBody.appendChild(fila);
    });

    document.querySelectorAll('.editar').forEach(btn =>
      btn.addEventListener('click', e => editarReporte(e.target.dataset.id, reportes)));

    document.querySelectorAll('.eliminar').forEach(btn =>
      btn.addEventListener('click', e => eliminarReporte(e.target.dataset.id)));
  }

  function editarReporte(id, datos) {
    const reporte = datos.find(r => r.id == id);
    if (!reporte) return;

    form.id.value = reporte.id;
    form.nombre_completo.value = reporte.nombre_completo;
    form.correo_electronico.value = reporte.correo_electronico;
    form.tipo_reporte.value = reporte.tipo_reporte;
    form.ubicacion.value = reporte.ubicacion;
    form.descripcion_detallada.value = reporte.descripcion_detallada;
    form.fecha_incidente.value = reporte.fecha_incidente;

    botonSubmit.textContent = "Actualizar Reporte";
    botonCancelar.style.display = "inline-block";
    form.scrollIntoView({ behavior: "smooth" });
  }

  async function eliminarReporte(id) {
    if (!confirm('¿Eliminar este reporte?')) return;

    const res = await fetch(`http://localhost/Proyecto_TecnologiasWeb/olaDeCambio_app/backend/api/reportes/${id}`, {
      method: 'DELETE'
    });

    const resultado = await res.json();
    if (resultado.status === 'ok') {
      alert('Reporte eliminado');
      cargarReportes();
    } else {
      alert('Error al eliminar');
    }
  }

  cargarReportes();

  botonCancelar.addEventListener('click', () => {
    form.reset();
    botonSubmit.textContent = "Enviar Reporte";
    botonCancelar.style.display = "none";
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.querySelector("#login form");
  const registroContainer = document.createElement("div");
  const userDisplay = document.createElement("div");

  userDisplay.id = "usuario-logueado";
  userDisplay.style.textAlign = "right";
  userDisplay.style.fontWeight = "bold";
  userDisplay.style.margin = "10px";

  document.body.insertBefore(userDisplay, document.body.firstChild);

  async function verificarSesion() {
    const res = await fetch("../backend/myapi/AUTH/session.php");
    const data = await res.json();
    if (data.usuario) {
      userDisplay.textContent = "Sesión iniciada como: " + data.usuario;
    }
  }

  verificarSesion();

  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const username = loginForm.username.value;
      const password = loginForm.password.value;

      const res = await fetch("../backend/myapi/AUTH/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      });

      const data = await res.json();
      if (data.success) {
        alert("Inicio de sesión exitoso");
        userDisplay.textContent = "Sesión iniciada como: " + data.usuario;
      } else {
        alert(data.message);
      }
    });
  }
});
