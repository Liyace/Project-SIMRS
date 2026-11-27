@extends('layouts.app')
@php $halamanAktif = 'admin_statistik'; @endphp
@section('title', 'Statistik & Dashboard Eksekutif - Super Admin RS')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kelola.css') }}">
    <style>
        /* ========================================================
           STYLING TOMBOL HEADER ACTION
           ======================================================== */
        .btn-eksekutif-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 12px;
            background: #ffffff;
            color: #1e293b !important;
            border: 1.5px solid #cbd5e1;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            text-decoration: none;
        }
        .btn-eksekutif-print:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.06);
        }

        .btn-eksekutif-backup {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1C4D8D 0%, #0284c7 100%);
            color: #ffffff !important;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
            text-decoration: none !important;
        }
        .btn-eksekutif-backup:hover {
            background: linear-gradient(135deg, #163d70 0%, #0369a1 100%);
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.35);
        }

        /* ========================================================
           LAYOUT WEB DASHBOARD INTERAKTIF
           ======================================================== */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }
        .kpi-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
        }
        .kpi-icon-wrap {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .charts-grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .charts-grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .chart-box {
            background: #ffffff;
            border-radius: 18px;
            padding: 22px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }
        .chart-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .chart-box-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .chart-canvas-container {
            position: relative;
            height: 260px;
            width: 100%;
        }

        /* Dark Mode Overrides */
        body.dark-mode .kpi-card,
        body.dark-mode .chart-box {
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }
        body.dark-mode .chart-box-title {
            color: #f8fafc;
        }
        body.dark-mode .chart-box-header {
            border-bottom-color: #334155;
        }
        body.dark-mode .btn-eksekutif-print {
            background: #0f172a;
            border-color: #334155;
            color: #f1f5f9 !important;
        }

        @media (max-width: 900px) {
            .charts-grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')

<div class="kelola-container" style="max-width: 1300px; margin: 0 auto; padding: 24px 20px;">

    <!-- TOP HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-chart-line" style="color: #2563eb;"></i>
                Dashboard Statistik & Eksekutif RS Delta
            </h1>
            <p style="font-size: 13px; color: #64748b; margin: 0;">
                Pemantauan data klinis, performa kunjungan pasien, dan indikator pelayanan rumah sakit secara realtime.
            </p>
        </div>

        <!-- TOMBOL HEADER DENGAN STYLING RAPI & WARNA ELEGAN -->
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('admin.statistik.cetak') }}" target="_blank" class="btn-eksekutif-print" title="Buka dan Cetak Dokumen Laporan Eksekutif Resmi">
                <i class="fa-solid fa-print" style="color: #475569;"></i>
                <span>Cetak Laporan</span>
            </a>
            <a href="{{ route('admin.backup') }}" class="btn-eksekutif-backup" title="Pusat Cadangan & Backup Database">
                <i class="fa-solid fa-database"></i>
                <span>Backup Sistem</span>
            </a>
        </div>
    </div>

    <!-- KPI SUMMARY CARDS -->
    <div class="kpi-grid">
        <!-- KPI 1 -->
        <div class="kpi-card">
            <div class="kpi-icon-wrap" style="background: #eff6ff; color: #2563eb;">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 500; display: block;">Kunjungan Pasien Bulan Ini</span>
                <span style="font-size: 24px; font-weight: 800; color: #6e92e6ff;">{{ number_format($totalKunjunganBulanIni) }}</span>
                <span style="font-size: 11px; color: #16a34a; font-weight: 600; display: block; margin-top: 2px;">
                    <i class="fa-solid fa-calendar-day"></i> Hari ini: {{ $totalKunjunganHariIni }} pasien
                </span>
            </div>
        </div>

        <!-- KPI 2 -->
        <div class="kpi-card">
            <div class="kpi-icon-wrap" style="background: #f0fdf4; color: #16a34a;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 500; display: block;">Pendapatan Kasir Bulan Ini</span>
                <span style="font-size: 22px; font-weight: 800; color: #16a34a;">Rp {{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}</span>
                <span style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">Status Transaksi: Lunas</span>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="kpi-card">
            <div class="kpi-icon-wrap" style="background: #fdf4ff; color: #c026d3;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 500; display: block;">Pasien Baru Bulan Ini</span>
                <span style="font-size: 24px; font-weight: 800; color: #c026d3;">{{ number_format($pasienBaruBulanIni) }}</span>
                <span style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">Dari total {{ $totalPasienTerdaftar }} rekam medis</span>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="kpi-card">
            <div class="kpi-icon-wrap" style="background: #fffbeb; color: #d97706;">
                <i class="fa-solid fa-stethoscope"></i>
            </div>
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 500; display: block;">Poli Teraktif</span>
                @php
                    $maxPoliVal = !empty($poliValues) ? max($poliValues) : 0;
                    $maxPoliKey = !empty($poliValues) ? array_search($maxPoliVal, $poliValues) : null;
                    $topPoliNama = ($maxPoliKey !== null && isset($poliLabels[$maxPoliKey])) ? $poliLabels[$maxPoliKey] : 'Poli Umum';
                @endphp
                <span style="font-size: 18px; font-weight: 800; color: #d97706;">{{ $topPoliNama }}</span>
                <span style="font-size: 11px; color: #64748b; display: block; margin-top: 2px;">Kunjungan terbanyak</span>
            </div>
        </div>
    </div>

    <!-- CHARTS ROW 1: TREN HARIAN & DISTRIBUSI POLI -->
    <div class="charts-grid-2">
        <!-- Grafik 1: Tren Kunjungan Harian -->
        <div class="chart-box">
            <div class="chart-box-header">
                <div class="chart-box-title">
                    <i class="fa-solid fa-chart-area" style="color: #2563eb;"></i>
                    Tren Kunjungan Pasien (14 Hari Terakhir)
                </div>
                <span style="font-size: 11px; background: #eff6ff; color: #2563eb; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                    Harian
                </span>
            </div>
            <div class="chart-canvas-container">
                <canvas id="chartTrenHarian"></canvas>
            </div>
        </div>

        <!-- Grafik 2: Distribusi Pasien per Poliklinik -->
        <div class="chart-box">
            <div class="chart-box-header">
                <div class="chart-box-title">
                    <i class="fa-solid fa-chart-pie" style="color: #8b5cf6;"></i>
                    Distribusi per Poliklinik
                </div>
                <span style="font-size: 11px; background: #ede9fe; color: #7c3aed; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                    Bulan Ini
                </span>
            </div>
            <div class="chart-canvas-container">
                <canvas id="chartDistribusiPoli"></canvas>
            </div>
        </div>
    </div>

    <!-- CHARTS ROW 2: TOP DIAGNOSA, KOMPOSISI CARA BAYAR & TREN PENDAPATAN -->
    <div class="charts-grid-3">
        <!-- Grafik 3: Top Diagnosa ICD-10 -->
        <div class="chart-box">
            <div class="chart-box-header">
                <div class="chart-box-title">
                    <i class="fa-solid fa-viruses" style="color: #ef4444;"></i>
                    Top 8 Diagnosa Penyakit (ICD-10)
                </div>
                <span style="font-size: 11px; background: #fee2e2; color: #b91c1c; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                    Klinis
                </span>
            </div>
            <div class="chart-canvas-container">
                <canvas id="chartTopDiagnosa"></canvas>
            </div>
        </div>

        <!-- Grafik 4: Komposisi Cara Bayar -->
        <div class="chart-box">
            <div class="chart-box-header">
                <div class="chart-box-title">
                    <i class="fa-solid fa-id-card-clip" style="color: #059669;"></i>
                    Komposisi Penjamin Bayar
                </div>
                <span style="font-size: 11px; background: #d1fae5; color: #047857; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                    Rasio
                </span>
            </div>
            <div class="chart-canvas-container">
                <canvas id="chartCaraBayar"></canvas>
            </div>
        </div>

        <!-- Grafik 5: Tren Pendapatan 6 Bulan -->
        <div class="chart-box">
            <div class="chart-box-header">
                <div class="chart-box-title">
                    <i class="fa-solid fa-arrow-trend-up" style="color: #f59e0b;"></i>
                    Tren Pendapatan (6 Bulan)
                </div>
                <span style="font-size: 11px; background: #fef3c7; color: #b45309; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                    Keuangan
                </span>
            </div>
            <div class="chart-canvas-container">
                <canvas id="chartPendapatan"></canvas>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark-mode');
    const textColor = isDark ? '#cbd5e1' : '#64748b';
    const gridColor = isDark ? '#334155' : '#f1f5f9';

    // 1. CHART TREN HARIAN
    const ctxTrenHarian = document.getElementById('chartTrenHarian');
    if (ctxTrenHarian) {
        new Chart(ctxTrenHarian, {
            type: 'line',
            data: {
                labels: {!! json_encode($trenHariLabels) !!},
                datasets: [{
                    label: 'Jumlah Pasien',
                    data: {!! json_encode($trenHariValues) !!},
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 11 } } },
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, precision: 0 } }
                }
            }
        });
    }

    // 2. CHART DISTRIBUSI POLIKLINIK
    const ctxPoli = document.getElementById('chartDistribusiPoli');
    if (ctxPoli) {
        new Chart(ctxPoli, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($poliLabels) !!},
                datasets: [{
                    data: {!! json_encode($poliValues) !!},
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4'],
                    borderWidth: 2,
                    borderColor: isDark ? '#1e293b' : '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, boxWidth: 12, font: { size: 10 } } }
                }
            }
        });
    }

    // 3. CHART TOP DIAGNOSA (HORIZONTAL BAR)
    const ctxDiagnosa = document.getElementById('chartTopDiagnosa');
    if (ctxDiagnosa) {
        new Chart(ctxDiagnosa, {
            type: 'bar',
            data: {
                labels: {!! json_encode(!empty($diagnosaLabels) ? $diagnosaLabels : ['Belum ada data diagnosa']) !!},
                datasets: [{
                    label: 'Kasus',
                    data: {!! json_encode(!empty($diagnosaValues) ? $diagnosaValues : [0]) !!},
                    backgroundColor: '#ef4444',
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, precision: 0 } },
                    y: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } }
                }
            }
        });
    }

    // 4. CHART CARA BAYAR (PIE)
    const ctxCaraBayar = document.getElementById('chartCaraBayar');
    if (ctxCaraBayar) {
        new Chart(ctxCaraBayar, {
            type: 'pie',
            data: {
                labels: {!! json_encode($caraBayarLabels) !!},
                datasets: [{
                    data: {!! json_encode($caraBayarValues) !!},
                    backgroundColor: ['#0284c7', '#16a34a', '#f59e0b'],
                    borderWidth: 2,
                    borderColor: isDark ? '#1e293b' : '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, boxWidth: 12, font: { size: 11 } } }
                }
            }
        });
    }

    // 5. CHART PENDAPATAN 6 BULAN (BAR)
    const ctxPendapatan = document.getElementById('chartPendapatan');
    if (ctxPendapatan) {
        new Chart(ctxPendapatan, {
            type: 'bar',
            data: {
                labels: {!! json_encode($bulanLabels) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($pendapatanValues) !!},
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000) + ' Jt';
                                if (value >= 1000) return (value / 1000) + ' Rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
