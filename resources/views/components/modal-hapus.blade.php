{{-- Satu modal dipakai bersama. Tombol hapus mengisi form-nya lewat data-action & data-nama. --}}
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow">
            @csrf @method('DELETE')
            <div class="modal-body text-center p-4">
                <div class="stat-ikon merah mx-auto mb-3"><i class="bi bi-trash3"></i></div>
                <h5 class="fw-bold">Hapus data ini?</h5>
                <p class="text-muted mb-0"><strong data-nama></strong> akan dihapus. Riwayat transaksi terkait tetap tersimpan.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
