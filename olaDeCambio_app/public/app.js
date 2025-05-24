function openTab(evt, tabName) {
  $('.tabcontent').removeClass('active');
  $('.tablink').removeClass('active');
  $('#' + tabName).addClass('active');
  $(evt.currentTarget).addClass('active');
}

$(document).ready(function () {
  const $form = $('#form-reporte');
  const $tablaBody = $('#tabla-reportes tbody');
  const $botonSubmit = $('#btn-submit');
  const $botonCancelar = $('#btn-cancelar');

  const baseApi = '/Proyecto_TecnologiasWeb/olaDeCambio_app/backend/api/reportes';

  $form.on('submit', function (e) {
    e.preventDefault();

    const datos = Object.fromEntries(new FormData(this).entries());
    const isUpdate = datos.id && datos.id.trim() !== '';
    const url = isUpdate ? `${baseApi}/${datos.id}` : baseApi;
    const metodo = isUpdate ? 'PUT' : 'POST';

    if (!isUpdate) {
      delete datos.id;
    }

    $.ajax({
      url: url,
      method: metodo,
      contentType: 'application/json',
      data: JSON.stringify(datos),
      success: function (res) {
        if (res.status === 'ok') {
          alert('Reporte guardado correctamente');
          $form[0].reset();
          $form.find('[name=id]').val('');
          $botonSubmit.text("Enviar Reporte");
          $botonCancelar.hide();
          cargarReportes();
        } else {
          alert('Error al guardar el reporte');
        }
      },
      error: function () {
        alert('Error al comunicarse con el servidor');
      }
    });
  });

  function cargarReportes() {
    $.getJSON(baseApi, function (reportes) {
      $tablaBody.empty();

      const tipoMap = {
        contaminacion: 'Contaminación marina',
        fauna: 'Avistamiento de fauna en peligro',
        pesca: 'Pesca ilegal',
        otro: 'Otro'
      };

      reportes.forEach(r => {
        const $fila = $(`
          <tr>
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
          </tr>
        `);
        $tablaBody.append($fila);
      });

      $('.editar').click(function () {
        const id = $(this).data('id');
        editarReporte(id, reportes);
      });

      $('.eliminar').click(function () {
        const id = $(this).data('id');
        eliminarReporte(id);
      });
    });
  }

  function editarReporte(id, datos) {
    const reporte = datos.find(r => r.id == id);
    if (!reporte) return;

    $form.find('[name=id]').val(reporte.id);
    $form.find('[name=nombre_completo]').val(reporte.nombre_completo);
    $form.find('[name=correo_electronico]').val(reporte.correo_electronico);
    $form.find('[name=tipo_reporte]').val(reporte.tipo_reporte);
    $form.find('[name=ubicacion]').val(reporte.ubicacion);
    $form.find('[name=descripcion_detallada]').val(reporte.descripcion_detallada);
    $form.find('[name=fecha_incidente]').val(reporte.fecha_incidente);

    $botonSubmit.text("Actualizar Reporte");
    $botonCancelar.show();
    $('html, body').animate({ scrollTop: $form.offset().top }, 600);
  }

  function eliminarReporte(id) {
    if (!confirm('¿Eliminar este reporte?')) return;

    $.ajax({
      url: `${baseApi}/${id}`,
      method: 'DELETE',
      success: function (res) {
        if (res.status === 'ok') {
          alert('Reporte eliminado');
          cargarReportes();
        } else {
          alert('Error al eliminar');
        }
      },
      error: function () {
        alert('Error de red al eliminar');
      }
    });
  }

  $botonCancelar.click(function () {
    $form[0].reset();
    $form.find('[name=id]').val('');
    $botonSubmit.text("Enviar Reporte");
    $botonCancelar.hide();
  });

  cargarReportes();
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
