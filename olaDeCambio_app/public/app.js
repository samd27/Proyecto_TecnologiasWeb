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
  const form = document.querySelector('#reportes form');

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(form);
      const datos = Object.fromEntries(formData.entries());

      try {
        const response = await fetch('http://localhost/Proyecto_TecnologiasWeb/olaDeCambio_app/backend/api/reportes', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(datos)
        });

        if (!response.ok) {
          throw new Error("Error en la solicitud");
        }

        let resultado;
        try {
          resultado = await response.json();
        } catch (e) {
          alert("La respuesta del servidor no fue JSON válido");
          return;
        }

        if (resultado.status === 'ok') {
          alert('Reporte enviado exitosamente');
          form.reset();
        } else {
          alert('Ocurrió un error al enviar el reporte');
        }
      } catch (error) {
        alert('Ocurrió un error al enviar el reporte');
      }
    });
  }
});
