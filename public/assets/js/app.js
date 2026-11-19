document.addEventListener('DOMContentLoaded', () => {
  // Notifikasi sukses/gagal
  document.querySelectorAll('.toast').forEach(el => new bootstrap.Toast(el, { delay: 6000 }).show());

  // Modal konfirmasi hapus: tombol membawa data-action (URL) dan data-nama
  const modal = document.getElementById('modalHapus');
  if (modal) {
    modal.addEventListener('show.bs.modal', (e) => {
      const tombol = e.relatedTarget;
      modal.querySelector('form').action = tombol.dataset.action;
      modal.querySelector('[data-nama]').textContent = tombol.dataset.nama;
    });
  }

  // Cegah klik ganda pada form simpan
  document.querySelectorAll('form[data-sekali]').forEach(form => {
    form.addEventListener('submit', () => {
      const tombol = form.querySelector('[type=submit]');
      if (tombol) setTimeout(() => { tombol.disabled = true; }, 0);
    });
  });
});
