function openModal(id) {
  fetch("model_raket.php?id=" + id)
    .then(res => res.text())
    .then(html => {
      document.getElementById("modalContent").innerHTML = html;

      let modal = new bootstrap.Modal(
        document.getElementById("modalRaket")
      );
      modal.show();
    });
}


function pesanWA(){
  let raket   = document.getElementById('nama_raket').value;
  let tanggal = document.getElementById('tanggal').value;
  let mulai   = document.getElementById('jam_mulai').value;
  let selesai = document.getElementById('jam_selesai').value;

  if(!tanggal){
    alert('Pilih tanggal dulu');
    return;
  }

  let pesan = `Halo Admin,%0A
Saya ingin booking raket:%0A
Raket: ${raket}%0A
Tanggal: ${tanggal}%0A
Jam: ${mulai} - ${selesai}`;

  window.open(`https://wa.me/6287847550402?text=${pesan}`);
}

