<?php

return [
    // Identitas usaha (tampil di nota, bukti bayar, laporan, dan pesan WhatsApp)
    'nama' => env('LAUNDRY_NAMA', 'Yuhuu Laundry'),
    'alamat' => env('LAUNDRY_ALAMAT', 'Jl. Raya Kresek, Merak, Kec. Sukamulya, Kabupaten Tangerang, Banten 15621'),
    'telepon' => env('LAUNDRY_TELEPON', '0822-3586-2808'),
    'jam_operasional' => env('LAUNDRY_JAM', 'Senin - Sabtu, 08.00 - 21.00 WIB (Minggu Tutup)'),

    // Info tambahan di bagian bawah nota (kosongkan untuk menyembunyikan)
    'info_nota' => env('LAUNDRY_INFO_NOTA', 'Penjemputan di bawah 5 km GRATIS. Minimum berat laundry kiloan 3 kg.'),

    // Driver WhatsApp: saat ini hanya 'link' (tombol wa.me). Driver API dapat ditambah di WhatsAppService.
    'wa_driver' => env('LAUNDRY_WA_DRIVER', 'link'),

    // Template pesan per status. Placeholder: {nama} {kode} {layanan} {total} {sisa} {estimasi} {laundry} {alamat} {jam}
    'pesan' => [
        'menunggu' => "Halo {nama} 👋\nLaundry Anda sudah kami terima.\nKode: *{kode}*\nLayanan: {layanan}\nTotal: Rp {total}\nEstimasi selesai: {estimasi}\nTerima kasih telah memilih {laundry} 🙏",
        'dicuci' => "Halo {nama} 👋\nLaundry *{kode}* saat ini sedang *DICUCI*.\n— {laundry}",
        'dikeringkan' => "Halo {nama} 👋\nLaundry *{kode}* saat ini sedang *DIKERINGKAN*.\n— {laundry}",
        'disetrika' => "Halo {nama} 👋\nLaundry *{kode}* saat ini sedang *DISETRIKA*.\n— {laundry}",
        'siap_diambil' => "Halo {nama} 👋\nLaundry Anda dengan kode {kode} sudah *SIAP DIAMBIL*.\nSisa tagihan: Rp {sisa}\nSilakan datang ke {laundry}, {alamat}.\nJam operasional: {jam}.\nTerima kasih 🙏",
        'sudah_diambil' => "Halo {nama}, terima kasih! Laundry {kode} sudah diambil.\nSampai jumpa lagi di {laundry} 🙏",
    ],
];
