<?php

namespace App\Http\Controllers;

use App\Enums\StatusLaundry;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Services\LaporanService;
use App\Support\XlsxWriter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    public function __construct(private LaporanService $service)
    {
    }

    /** Halaman laporan lengkap (Admin). */
    public function index(Request $request)
    {
        [$jenis, $awal, $akhir] = $this->parameter($request);
        $laporan = $this->service->susun($jenis, $awal, $akhir);

        return view('laporan.index', [
            'laporan' => $laporan,
            'daftarJenis' => LaporanService::JENIS,
            'jenis' => $jenis,
            'dari' => $awal->toDateString(),
            'sampai' => $akhir->toDateString(),
        ]);
    }

    /** Cetak / PDF / Excel memakai data yang sama dengan tampilan layar. */
    public function export(Request $request, string $format)
    {
        abort_unless(in_array($format, ['cetak', 'pdf', 'excel'], true), 404);

        [$jenis, $awal, $akhir] = $this->parameter($request);
        $laporan = $this->service->susun($jenis, $awal, $akhir);
        $nama = 'laporan-' . $jenis . '-' . now()->format('Ymd-His');

        if ($format === 'pdf') {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.dokumen', ['laporan' => $laporan, 'mode' => 'pdf'])
                ->setPaper('a4', count($laporan['kolom']) > 6 ? 'landscape' : 'portrait')
                ->download($nama . '.pdf');
        }

        if ($format === 'excel') {
            $baris = [[config('laundry.nama')], [$laporan['judul']], ['Periode: ' . $laporan['periode']], [], $laporan['kolom']];
            foreach ($laporan['baris'] as $b) {
                $baris[] = $b; // angka dibiarkan numerik agar bisa dihitung di Excel
            }
            $baris[] = [];
            foreach ($laporan['ringkasan'] as $label => $nilai) {
                $baris[] = [$label, is_array($nilai) ? $nilai[1] : $nilai];
            }
            $path = XlsxWriter::simpan($baris, count($laporan['kolom']));

            return response()->download($path, $nama . '.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        }

        return view('laporan.dokumen', ['laporan' => $laporan, 'mode' => 'cetak']);
    }

    /** "Rekap Harian Saya" untuk Petugas: hanya data miliknya hari ini, tanpa laporan pendapatan toko. */
    public function rekapSaya(Request $request)
    {
        $user = $request->user();

        $transaksi = Transaksi::with('pelanggan')->where('user_id', $user->id)
            ->whereDate('tanggal_masuk', today())->orderBy('tanggal_masuk')->get();

        $pembayaran = Pembayaran::with('transaksi.pelanggan')->where('user_id', $user->id)
            ->whereDate('tanggal_bayar', today())->orderBy('tanggal_bayar')->get();

        // Ringkasan status laundry: hanya jumlah, tanpa nominal
        $perStatus = Transaksi::selectRaw('status_laundry, count(*) as jml')->groupBy('status_laundry')->pluck('jml', 'status_laundry');
        $statusRingkas = collect(StatusLaundry::cases())->map(fn ($s) => ['status' => $s, 'jumlah' => (int) ($perStatus[$s->value] ?? 0)]);

        return view('laporan.rekap-saya', compact('transaksi', 'pembayaran', 'statusRingkas'));
    }

    private function parameter(Request $request): array
    {
        $data = $request->validate([
            'jenis' => ['nullable', Rule::in(array_keys(LaporanService::JENIS))],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ], ['sampai.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.']);

        $jenis = $data['jenis'] ?? 'harian';
        [$awal, $akhir] = $this->service->periode($jenis, $data['dari'] ?? null, $data['sampai'] ?? null);

        return [$jenis, $awal, $akhir];
    }
}
