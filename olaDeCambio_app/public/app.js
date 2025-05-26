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

  //  Verificar sesión activa
  $.getJSON('../backend/myapi/AUTH/session.php', function (data) {
    if (data.usuario) {
      $('#usuario-logueado').html(`
        <div class="usuario-info">
          <img src="img/user-icon.png" alt="Usuario" class="icono-usuario-img" />
          <span>${data.usuario}</span>
        </div>
      `);
      $('#nav-quejas').removeClass('oculto');
      $('#btn-logout').removeClass('oculto');
    }
  });

  //  CRUD de reportes
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

  //  Registro
  $('#form-registro').on('submit', function (e) {
    e.preventDefault();

    const usuario = $('#usuario').val();
    const contrasena = $('#contrasena').val();

    $.ajax({
      url: '../backend/registro',
      method: 'POST',
      data: JSON.stringify({ usuario, contrasena }),
      contentType: 'application/json',
      success: function (data) {
        if (data.success) {
          alert('Registro exitoso');
          $('#form-registro')[0].reset();
          openTab({ currentTarget: $('#login-tab')[0] }, 'login');
        } else {
          alert(data.message || 'Error al registrar');
        }
      },
      error: function (xhr) {
        alert('Error al registrar: ' + xhr.responseText);
      }
    });
  });

  //  Login
  $('#login form').on('submit', function (e) {
    e.preventDefault();

    const username = $('#username').val();
    const password = $('#password').val();

    $.ajax({
      url: '../backend/login',
      method: 'POST',
      data: JSON.stringify({ username, password }),
      contentType: 'application/json',
      success: function (data) {
        if (data.success) {
          $('#usuario-logueado').html(`
            <div class="usuario-info">
              <img src="img/user-icon.png" alt="Usuario" class="icono-usuario-img" />
              <span>${data.usuario}</span>
            </div>
          `);
          $('#nav-quejas').removeClass('oculto');
          $('#btn-logout').removeClass('oculto');
          alert('Inicio de sesión exitoso');
        } else {
          alert(data.message || 'Credenciales inválidas');
        }
      },
      error: function (xhr) {
        alert('Error al iniciar sesión: ' + xhr.responseText);
      }
    });
  });

  // Ir a registro
  $('#btn-ir-registro').on('click', function (e) {
    e.preventDefault();
    $('.tabcontent').removeClass('active');
    $('.tablink').removeClass('active');
    $('#registro').addClass('active');
  });

  // Volver a login
  $('#btn-ir-login').on('click', function (e) {
    e.preventDefault();
    $('.tabcontent').removeClass('active');
    $('#login').addClass('active');
  });

  // Cerrar sesión
  $('#btn-logout').on('click', function () {
    $.get('../backend/myapi/AUTH/logout.php', function () {
      location.reload();
    });
  });
});

// Dashboard
const tipoReporteEtiquetas = {
  contaminacion: "Contaminación marina",
  fauna: "Fauna en peligro",
  pesca: "Pesca ilegal",
  otro: "Otro"
};


$(document).ready(function () {
  if ($('#graficoTipos').length) {
   $.ajax({
  url: '../backend/api/reportes/resumen',
  method: 'GET',
  dataType: 'json',
  success: function (data) {
    $('#total-reportes').text(data.total);
    $('#tipo-mas-comun').text(tipoReporteEtiquetas[data.tipo_mas_comun] || data.tipo_mas_comun);
    $('#estado-top').text(data.estado_top);

    new Chart($('#graficoTipos'), {
      type: 'doughnut',
      data: {
        labels: data.por_tipo.labels.map(val => tipoReporteEtiquetas[val] || val),
        datasets: [{
          data: data.por_tipo.valores,
          backgroundColor: ['#2ecc71', '#e67e22', '#3498db', '#9b59b6']
        }]
      }
    });

    new Chart($('#graficoEstados'), {
      type: 'bar',
      data: {
        labels: data.por_estado.labels,
        datasets: [{
          label: 'Reportes por Estado',
          data: data.por_estado.valores,
          backgroundColor: '#9b59b6'
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  },
  error: function (xhr, status, error) {
    console.error("Error al cargar el dashboard:", error);
  }
});

  $.ajax({
  url: '../backend/api/reportes/por-mes',
  method: 'GET',
  dataType: 'json',
  success: function (data) {
    new Chart($('#graficoLineaMeses'), {
  type: 'line',
  data: {
    labels: data.labels,
    datasets: [{
      label: 'Reportes por mes',
      data: data.valores,
      borderColor: '#2980b9',
      backgroundColor: '#2980b9',
      fill: false,
      tension: 0.3,
      pointRadius: 4,
      pointHoverRadius: 6
    }]
  },
  options: {
    responsive: true,
    plugins: {
      title: {
        display: true,
        text: 'Cronograma de reportes',
        font: {
          size: 20,
          weight: 'bold'
        },
        padding: {
          top: 10,
          bottom: 20
        }
      },
      legend: {
        display: false
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1
        }
      },
      x: {
        ticks: {
          maxRotation: 45,
          minRotation: 45
        }
      }
    }
  }
});

  },
  error: function (xhr, status, error) {
    console.error("Error al cargar gráfico de línea:", error);
  }
});


  }
});

