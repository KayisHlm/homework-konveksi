<?php

namespace App\Support;

/**
 * Label & warna badge untuk semua kode status, agar istilah di UI konsisten dengan dokumen skripsi.
 */
class Status
{
    private const LABELS = [
        // Status produksi pesanan
        'pesanan_diterima' => 'Pesanan Diterima',
        'menunggu_bahan' => 'Menunggu Bahan',
        'proses_produksi' => 'Proses Produksi',
        'quality_control' => 'Quality Control',
        'selesai' => 'Selesai',
        // Status pembayaran pesanan
        'belum_bayar' => 'Belum Bayar',
        'sebagian' => 'Dibayar Sebagian',
        'lunas' => 'Lunas',
        // Invoice
        'belum_dibayar' => 'Belum Dibayar',
        'dibatalkan' => 'Dibatalkan',
        'uang_muka' => 'Uang Muka',
        'termin' => 'Termin',
        'pelunasan' => 'Pelunasan',
        'tunai' => 'Tunai',
        'transfer' => 'Transfer',
        // Pengadaan
        'dipesan' => 'Dipesan',
        'diterima' => 'Diterima',
        // Kategori pengeluaran
        'bahan_baku' => 'Bahan Baku',
        'sablon' => 'Sablon',
        'bordir' => 'Bordir',
        'jahit' => 'Jahit',
        'transportasi' => 'Transportasi',
        'operasional' => 'Operasional',
        'lainnya' => 'Lainnya',
        // Peran
        'pemilik' => 'Pemilik',
        'admin' => 'Admin',
    ];

    private const BADGES = [
        'selesai' => 'success', 'lunas' => 'success', 'diterima' => 'success',
        'sebagian' => 'info', 'proses_produksi' => 'info', 'quality_control' => 'info',
        'belum_bayar' => 'warning', 'belum_dibayar' => 'warning', 'dipesan' => 'warning', 'menunggu_bahan' => 'warning',
        'dibatalkan' => 'danger',
    ];

    public static function label(?string $code): string
    {
        return self::LABELS[$code] ?? ($code ? ucwords(str_replace('_', ' ', $code)) : '-');
    }

    public static function badge(?string $code): string
    {
        return self::BADGES[$code] ?? 'secondary';
    }

    public static function rupiah(float|int|string|null $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}
