@extends('layouts.app')
@section('judul', 'Transaksi Baru')

@section('content')
    @php
        $dataLayanan = $layananList->map(fn($l) => [
            'id' => $l->id,
            'nama' => $l->nama,
            'kategori' => $l->kategori ?: 'Lainnya',
            'harga' => $l->harga,
            'satuan' => $l->satuan,
            'minimum' => (float) $l->minimum,
            'jam' => $l->estimasi_jam,
        ])->values();
        $itemAwal = old('items', [['layanan_id' => '', 'jumlah' => '']]);
    @endphp
    <form method="POST" action="{{ route('transaksi.store') }}" data-sekali novalidate>
        @csrf
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="cardx mb-3">
                    <div class="cardx-head"><h2><i class="bi bi-person text-primary"></i> 1. Pelanggan</h2>
                        <a href="{{ route('pelanggan.create', ['dari_transaksi' => 1]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-person-plus"></i> Pelanggan baru</a></div>
                    <div class="cardx-body">
                        <select name="pelanggan_id" class="form-select @error('pelanggan_id') is-invalid @enderror">
                            <option value="">-- Pilih pelanggan --</option>
                            @foreach ($pelangganList as $p)
                                <option value="{{ $p->id }}" @selected(old('pelanggan_id', $pelangganDipilih) == $p->id)>{{ $p->nama }} - {{ $p->telepon_tampil }} ({{ $p->kode }})</option>
                            @endforeach
                        </select>
                        @error('pelanggan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="cardx mb-3">
                    <div class="cardx-head"><h2><i class="bi bi-basket3 text-primary"></i> 2. Layanan & Berat</h2>
                        <button type="button" class="btn btn-sm btn-primary" id="tambahBaris"><i class="bi bi-plus"></i> Tambah layanan</button></div>
                    <div class="cardx-body">
                        @error('items')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
                        <div id="daftarBaris" class="d-grid gap-3"></div>
                    </div>
                </div>

                <div class="cardx">
                    <div class="cardx-head"><h2><i class="bi bi-chat-left-text text-primary"></i> 3. Catatan (opsional)</h2></div>
                    <div class="cardx-body">
                        <textarea name="catatan" rows="2" class="form-control @error('catatan') is-invalid @enderror" placeholder="Contoh: pisahkan pakaian putih, ada noda di kemeja">{{ old('catatan') }}</textarea>
                        @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cardx position-sticky" style="top:80px">
                    <div class="cardx-head"><h2><i class="bi bi-calculator text-primary"></i> Ringkasan</h2></div>
                    <div class="cardx-body">
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total layanan</span><b id="jmlLayanan">0</b></div>
                        <div class="d-flex justify-content-between mb-2"><span class="text-muted">Estimasi selesai</span><b id="estimasi" class="text-end">-</b></div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Total biaya</span><span class="fs-4 fw-bold text-primary" id="total">Rp 0</span>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-check2-circle"></i> Simpan Transaksi</button>
                        <a href="{{ route('transaksi.index') }}" class="btn btn-light w-100 mt-2">Batal</a>
                        <div class="small text-muted mt-3">Kode transaksi dibuat otomatis. Pembayaran dicatat setelah transaksi disimpan.</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const layanan = {{ \Illuminate\Support\Js::from($dataLayanan) }};
        const itemAwal = {{ \Illuminate\Support\Js::from($itemAwal) }};
        const galat = {{ \Illuminate\Support\Js::from($errors->getMessages()) }};
        const wadah = document.getElementById('daftarBaris');
        let nomor = 0;

        const rp = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
        const esc = (t) => String(t ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        // Pilihan layanan dikelompokkan per kategori (optgroup) agar daftar panjang tetap mudah dicari
        function opsiHtml(terpilih) {
            const grup = {};
            layanan.forEach((l) => { (grup[l.kategori] = grup[l.kategori] || []).push(l); });
            return Object.keys(grup).map((k) =>
                `<optgroup label="${esc(k)}">` + grup[k].map((l) =>
                    `<option value="${l.id}" ${String(terpilih) === String(l.id) ? 'selected' : ''}>${esc(l.nama)} - ${rp(l.harga)}/${l.satuan}</option>`
                ).join('') + '</optgroup>'
            ).join('');
        }

        function buatBaris(item) {
            const i = nomor++;
            const div = document.createElement('div');
            div.className = 'row g-2 align-items-start baris';
            const eLay = (galat[`items.${i}.layanan_id`] || [''])[0];
            const eJml = (galat[`items.${i}.jumlah`] || [''])[0];
            div.innerHTML = `
                <div class="col-md-6">
                    <select name="items[${i}][layanan_id]" class="form-select pilih ${eLay ? 'is-invalid' : ''}">
                        <option value="">-- Pilih layanan --</option>${opsiHtml(item.layanan_id)}
                    </select>
                    <div class="invalid-feedback">${esc(eLay)}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="input-group">
                        <input type="number" step="0.1" min="0.1" name="items[${i}][jumlah]" value="${esc(item.jumlah)}" class="form-control jumlah ${eJml ? 'is-invalid' : ''}" placeholder="Jumlah">
                        <span class="input-group-text satuan">kg</span>
                    </div>
                    <div class="small petunjuk text-muted"></div>
                    <div class="text-danger small">${esc(eJml)}</div>
                </div>
                <div class="col-4 col-md-2 pt-2 text-end fw-semibold subtotal">Rp 0</div>
                <div class="col-2 col-md-1 text-end">
                    <button type="button" class="btn btn-light text-danger hapus" title="Hapus baris"><i class="bi bi-trash3"></i></button>
                </div>`;
            wadah.appendChild(div);
        }

        function hitung() {
            let total = 0, jam = 0, jml = 0;
            wadah.querySelectorAll('.baris').forEach((baris) => {
                const l = layanan.find((x) => String(x.id) === baris.querySelector('.pilih').value);
                const berat = parseFloat(baris.querySelector('.jumlah').value) || 0;
                const kurang = !!l && berat > 0 && berat < l.minimum;

                baris.querySelector('.satuan').textContent = l ? l.satuan : 'kg';
                baris.querySelector('.jumlah').step = l && l.satuan === 'pcs' ? '1' : '0.1';

                // Jumlah di bawah minimum ditagih sebesar minimum (mis. kiloan minimum 3 kg)
                const sub = l && berat > 0 ? Math.round(Math.max(berat, l.minimum) * l.harga) : 0;
                baris.querySelector('.subtotal').textContent = rp(sub);

                const info = baris.querySelector('.petunjuk');
                info.textContent = l && l.minimum > 0 ? (kurang ? `Dihitung minimum ${l.minimum} ${l.satuan}` : `Minimum ${l.minimum} ${l.satuan}`) : '';
                info.className = 'small petunjuk ' + (kurang ? 'text-warning fw-semibold' : 'text-muted');

                if (l && berat > 0) { total += sub; jam = Math.max(jam, l.jam); jml++; }
            });
            document.getElementById('total').textContent = rp(total);
            document.getElementById('jmlLayanan').textContent = jml;
            if (jml) {
                const e = new Date(Date.now() + jam * 3600 * 1000);
                document.getElementById('estimasi').textContent = e.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            } else {
                document.getElementById('estimasi').textContent = '-';
            }
        }

        wadah.addEventListener('input', hitung);
        wadah.addEventListener('change', hitung);
        wadah.addEventListener('click', (e) => {
            const tombol = e.target.closest('.hapus');
            if (!tombol) return;
            if (wadah.querySelectorAll('.baris').length > 1) { tombol.closest('.baris').remove(); hitung(); }
        });
        document.getElementById('tambahBaris').addEventListener('click', () => { buatBaris({ layanan_id: '', jumlah: '' }); hitung(); });

        Object.values(itemAwal).forEach(buatBaris);
        hitung();
    </script>
@endpush
