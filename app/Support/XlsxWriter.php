<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

/**
 * Pembuat file .xlsx sederhana tanpa paket tambahan (memakai ekstensi zip bawaan PHP).
 * Cukup untuk laporan tabel: judul, baris data, dan baris ringkasan.
 */
class XlsxWriter
{
    /**
     * @param  array<int, array<int, string|int|float|null>>  $baris  seluruh baris sheet
     * @return string path file sementara
     */
    public static function simpan(array $baris, int $jumlahKolom = 8): string
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuat file Excel.');
        }

        $head = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $zip->addFromString('[Content_Types].xml', $head . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', $head . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', $head . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Laporan" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', $head . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');

        $xml = $head . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols><col min="1" max="' . max(1, $jumlahKolom) . '" width="22" customWidth="1"/></cols><sheetData>';
        foreach (array_values($baris) as $i => $row) {
            $xml .= '<row r="' . ($i + 1) . '">';
            foreach (array_values($row) as $j => $nilai) {
                $ref = self::kolom($j) . ($i + 1);
                if ($nilai === null || $nilai === '') {
                    continue;
                }
                if (is_int($nilai) || is_float($nilai)) {
                    $xml .= '<c r="' . $ref . '"><v>' . $nilai . '</v></c>';
                } else {
                    $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t>'
                        . htmlspecialchars((string) $nilai, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</t></is></c>';
                }
            }
            $xml .= '</row>';
        }
        $xml .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $xml);
        $zip->close();

        return $path;
    }

    /** 0 => A, 25 => Z, 26 => AA */
    public static function kolom(int $indeks): string
    {
        $huruf = '';
        for ($n = $indeks; $n >= 0; $n = intdiv($n, 26) - 1) {
            $huruf = chr(65 + $n % 26) . $huruf;
        }

        return $huruf;
    }
}
