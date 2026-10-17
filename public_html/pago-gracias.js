const params = new URLSearchParams(window.location.search);
const preapprovalId = params.get("preapproval_id");

if (!preapprovalId) {
  mostrarError();
} else {
  activarTienda(preapprovalId);
}

function activarTienda(preapprovalId) {

  fetch("backend/activar_tienda.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ preapproval_id: preapprovalId })
  })
    .then(r => r.json())
    .then(res => {

      if (res.ok && res.slug) {
        document.getElementById("estadoCargando").classList.add("d-none");
        document.getElementById("estadoOk").classList.remove("d-none");
        document.getElementById("btnIrTienda").href = "/" + res.slug;
      } else {
        mostrarError();
      }

    })
    .catch(() => mostrarError());
}

function mostrarError() {
  document.getElementById("estadoCargando").classList.add("d-none");
  document.getElementById("estadoError").classList.remove("d-none");
}
