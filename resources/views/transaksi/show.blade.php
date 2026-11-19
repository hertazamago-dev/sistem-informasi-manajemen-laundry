@extends('layouts.app')
@section('judul', 'Detail Transaksi')

@section('content')
    @php
        $operasional = !auth()->user()->isAdmin(); // hanya Petugas yang mengubah status/pembayaran; Admin hanya melihat
        $lunas = $transaksi->status_pembayaran === \App\Enums\StatusPembayaran::Lunas;
        $indeksSekarang = array_search($transaksi->status_laundry, $alur, true);
        $tertahan = $berikutnya === \App\Enums\StatusLaundry::SudahDiambil && !$lunas; // belum lunas, belum boleh diambil
    @endphp

    {{-- Tombol WA muncul langsung setelah status diubah --}}
    @if ($operasional && session('wa_status') && in_array(session('wa_status'), array_map(fn($s) => $s->value, $statusWa), true))
        <div class="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-2 no-print">
            <span><i class="bi bi-whatsapp"></i> Beritahu <b>{{ $transaksi->pelanggan->nama }}</b> bahwa laundry kini
                <b>{{ \App\Enums\StatusLaundry::from(session('wa_status'))->label() }}</b>?</span>
            <form method="POST" action="{{ route('transaksi.wa', $transaksi) }}" target="_blank">
                @csrf <input type="hidden" name="status" value="{{ session('wa_status') }}">
                <button class="btn btn-success btn-sm"><i class="bi bi-whatsapp"></i> Kirim WA ke Pelanggan</button>
            </form>
        </div>
    @endif

    <div class="cardx mb-3">
        <div class="cardx-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="text-muted small">Kode transaksi</div>
                <h4 class="fw-bold mb-1">{{ $transaksi->kode }}</h4>
                <x-badge-status :status="$transaksi->status_laundry" /> <x-badge-status
                    :status="$transaksi->status_pembayaran" />
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('transaksi.nota', $transaksi) }}" target="_blank" class="btn btn-primary"><i
                        class="bi bi-printer"></i> Cetak Nota</a>
                <a href="{{ $operasional ? route('transaksi.index') : route('riwayat.index') }}"
                    class="btn btn-light">Kembali</a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            {{-- Proses laundry --}}
            <div class="cardx mb-3">
                <div class="cardx-head">
                    <h2><i class="bi bi-arrow-repeat text-primary"></i> Proses Laundry</h2>
                </div>
                <div class="cardx-body">
                    <div class="stepper mb-3">
                        @foreach ($alur as $i => $s)
                            <div class="step {{ $i < $indeksSekarang ? 'done' : ($i === $indeksSekarang ? 'now' : '') }}">
                                <div class="dot"><i class="bi {{ $i < $indeksSekarang ? 'bi-check-lg' : $s->ikon() }}"></i>
                                </div>{{ $s->label() }}
                            </div>
                        @endforeach
                    </div>
                    @if ($berikutnya && $operasional)
                        <form method="POST" action="{{ route('transaksi.status', $transaksi) }}" data-sekali
                            class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-7">
                                <label class="form-label small mb-1">Catatan (opsional)</label>
                                <input type="text" name="catatan" class="form-control" maxlength="255"
                                    placeholder="Contoh: noda membandel sudah dihilangkan" @disabled($tertahan)>
                            </div>
                            <div class="col-md-5 d-grid">
                                <button class="btn btn-primary" @disabled($tertahan)><i
                                        class="bi {{ $berikutnya->ikon() }}"></i> Lanjut: {{ $berikutnya->label() }}</button>
                            </div>
                        </form>
                        @if ($tertahan)
                            <div class="alert alert-danger py-2 mt-3 mb-0 small"><i class="bi bi-lock"></i> Pembayaran belum lunas
                                (sisa @rupiah($transaksi->sisa)). Selesaikan pembayaran agar laundry bisa ditandai <b>Sudah
                                    Diambil</b>.</div>
                        @endif
                    @elseif ($berikutnya)
                        <div class="small text-muted"><i class="bi bi-eye"></i> Tampilan hanya lihat. Perubahan status dilakukan
                            oleh petugas.</div>
                    @else
                        <div class="alert alert-success py-2 mb-0"><i class="bi bi-check2-all"></i> Laundry sudah diambil
                            pelanggan. Proses selesai.</div>
                    @endif
                </div>
            </div>

            {{-- Rincian --}}
            <div class="cardx mb-3">
                <div class="cardx-head">
                    <h2><i class="bi bi-list-ul text-primary"></i> Rincian Layanan</h2>
                </div>
                <div class="table-wrap">
                    <table class="table tabel">
                        <thead>
                            <tr>
                                <th>Layanan</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksi->details as $d)
                                <tr>
                                    <td>{{ $d->nama_layanan }}</td>
                                    <td class="text-end">{{ \App\Support\Format::jumlah($d->jumlah) }} {{ $d->satuan }}</td>
                                    <td class="text-end">@rupiah($d->harga_satuan)</td>
                                    <td class="text-end">@rupiah($d->subtotal)</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end text-primary">@rupiah($transaksi->total)</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="cardx-body border-top small">
                    <div class="row g-2">
                        <div class="col-sm-6"><span class="text-muted">Pelanggan:</span> <a
                                href="{{ route('pelanggan.show', $transaksi->pelanggan) }}"
                                class="fw-semibold text-decoration-none">{{ $transaksi->pelanggan->nama }}</a></div>
                        <div class="col-sm-6"><span class="text-muted">Petugas penerima:</span>
                            {{ $transaksi->petugas->name }}</div>
                        <div class="col-sm-6"><span class="text-muted">Tanggal masuk:</span>
                            {{ $transaksi->tanggal_masuk->format('d/m/Y H:i') }}</div>
                        <div class="col-sm-6"><span class="text-muted">Estimasi selesai:</span>
                            {{ $transaksi->estimasi_selesai->format('d/m/Y H:i') }}</div>
                        @if ($transaksi->catatan)
                        <div class="col-12"><span class="text-muted">Catatan:</span> {{ $transaksi->catatan }}</div>@endif
                    </div>
                </div>
            </div>

            {{-- Riwayat status (hanya lihat) --}}
            <div class="cardx">
                <div class="cardx-head">
                    <h2><i class="bi bi-clock-history text-primary"></i> Riwayat Perubahan Status</h2><span
                        class="small text-muted">Tidak dapat diubah</span>
                </div>
                <div class="cardx-body">
                    <ul class="timeline">
                        @foreach ($transaksi->riwayat->reverse() as $r)
                            <li>
                                <x-badge-status :status="$r->status" />
                                <div class="small text-muted mt-1">{{ $r->waktu->format('d/m/Y H:i') }} &middot; oleh
                                    {{ $r->petugas->name }}</div>
                                @if ($r->catatan)
                                <div class="small">{{ $r->catatan }}</div>@endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            {{-- Pembayaran --}}
            <div class="cardx mb-3">
                <div class="cardx-head">
                    <h2><i class="bi bi-cash-coin text-success"></i> Pembayaran</h2><x-badge-status
                        :status="$transaksi->status_pembayaran" />
                </div>
                <div class="cardx-body">
                    <div class="row text-center g-2 mb-3">
                        <div class="col-4">
                            <div class="small text-muted">Tagihan</div><b>@rupiah($transaksi->total)</b>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Dibayar</div><b
                                class="text-success">@rupiah($transaksi->total_dibayar)</b>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Sisa</div><b
                                class="{{ $transaksi->sisa > 0 ? 'text-danger' : 'text-success' }}">@rupiah($transaksi->sisa)</b>
                        </div>
                    </div>

                    @if ($operasional && $transaksi->sisa > 0)
                        <form method="POST" action="{{ route('pembayaran.store', $transaksi) }}" data-sekali class="mb-3">
                            @csrf
                            <label class="form-label small mb-1">Catat pembayaran tunai</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah_bayar" id="jumlahBayar" min="1" max="{{ $transaksi->sisa }}"
                                    value="{{ old('jumlah_bayar') }}"
                                    class="form-control @error('jumlah_bayar') is-invalid @enderror" placeholder="Jumlah bayar">
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="document.getElementById('jumlahBayar').value={{ $transaksi->sisa }}">Lunasi</button>
                                @error('jumlah_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <input type="text" name="catatan" class="form-control form-control-sm mb-2" maxlength="255"
                                placeholder="Catatan (opsional, mis. DP)">
                            <button class="btn btn-success w-100"><i class="bi bi-check2-circle"></i> Simpan Pembayaran</button>
                        </form>
                    @endif

                    <div class="small fw-semibold mb-2">Riwayat pembayaran</div>
                    @forelse ($transaksi->pembayaran as $b)
                        <div class="d-flex justify-content-between align-items-center py-2 border-top">
                            <div>
                                <div class="fw-semibold">@rupiah($b->jumlah_bayar) <span class="bdg bdg-abu ms-1">Tunai</span>
                                </div>
                                <div class="small text-muted">{{ $b->kode }} &middot;
                                    {{ $b->tanggal_bayar->format('d/m/Y H:i') }} &middot; {{ $b->petugas->name }}</div>
                            </div>
                            <a href="{{ route('pembayaran.bukti', $b) }}" target="_blank" class="btn btn-sm btn-light"
                                title="Cetak bukti"><i class="bi bi-printer"></i></a>
                        </div>
                    @empty
                        <div class="small text-muted">Belum ada pembayaran.</div>
                    @endforelse
                </div>
            </div>

            {{-- WhatsApp --}}
            <div class="cardx">
                <div class="cardx-head">
                    <h2><i class="bi bi-whatsapp text-success"></i>
                        {{ $operasional ? 'Beritahu Pelanggan' : 'Log Pesan WhatsApp' }}</h2>
                </div>
                <div class="cardx-body">
                    @if ($operasional)
                        <form method="POST" action="{{ route('transaksi.wa', $transaksi) }}" target="_blank" class="mb-3">
                            @csrf
                            <label class="form-label small mb-1">Kirim info status ke
                                {{ $transaksi->pelanggan->telepon_tampil }}</label>
                            <div class="input-group">
                                <select name="status" class="form-select">
                                    @foreach ($statusWa as $s)
                                        <option value="{{ $s->value }}" @selected($s === $transaksi->status_laundry)>{{ $s->label() }}
                                    </option>@endforeach
                                </select>
                                <button class="btn btn-success"><i class="bi bi-whatsapp"></i> Kirim WA</button>
                            </div>
                            <div class="form-text">WhatsApp terbuka dengan pesan siap kirim. Petugas tinggal menekan Kirim.
                            </div>
                        </form>
                    @endif

                    <div class="small fw-semibold mb-2">Log pesan</div>
                    @forelse ($transaksi->notifikasi as $n)
                        <div class="small py-2 border-top">
                            <x-badge-status :status="$n->status" />
                            <div class="text-muted mt-1">Disiapkan {{ $n->dibuat_pada->format('d/m/Y H:i') }} oleh
                                {{ $n->petugas->name }}</div>
                        </div>
                    @empty
                        <div class="small text-muted">Belum ada pesan yang disiapkan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection