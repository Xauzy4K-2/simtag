<template>
    <div class="app">
        <!-- SIDEBAR -->
        <aside class="sidebar" :class="{ open: sidebarOpen }">
            <div class="logo">
                <div class="logo-icon">💧</div>

                <div>
                    <h2>SIMTAG</h2>
                    <span>Tirta Pinang</span>
                </div>
            </div>

            <nav class="menu">
                <div class="menu-title">MENU UTAMA</div>

                <a href="#" class="menu-item active">
                    <span>📊</span>
                    Dashboard
                </a>

                <a href="#" class="menu-item">
                    <span>👥</span>
                    Pelanggan
                </a>

                <a href="#" class="menu-item">
                    <span>📏</span>
                    Data Meter
                </a>

                <a href="#" class="menu-item">
                    <span>🧾</span>
                    Tagihan
                </a>

                <a href="#" class="menu-item">
                    <span>💳</span>
                    Pembayaran
                </a>

                <a href="#" class="menu-item">
                    <span>⚠️</span>
                    Tunggakan
                </a>

                <div class="menu-title">LAPORAN</div>

                <a href="#" class="menu-item">
                    <span>📈</span>
                    Laporan
                </a>

                <a href="#" class="menu-item">
                    <span>⚙️</span>
                    Pengaturan
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="user-mini">
                    <div class="avatar">A</div>

                    <div>
                        <strong>Admin</strong>
                        <small>Administrator</small>
                    </div>

                    <span>⋮</span>
                </div>
            </div>
        </aside>

        <!-- MAIN -->
        <main class="main">
            <!-- NAVBAR -->
            <header class="navbar">
                <button
                    class="mobile-button"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    ☰
                </button>

                <div>
                    <h1>Dashboard</h1>
                    <p>Selamat datang di Sistem Informasi Manajemen Tagihan</p>
                </div>

                <div class="navbar-right">
                    <button class="notification">
                        🔔
                        <span></span>
                    </button>

                    <div class="profile">
                        <div class="avatar">A</div>

                        <div>
                            <strong>Admin</strong>
                            <small>Administrator</small>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <section class="content">
                <!-- STATISTICS -->
                <div class="stats">
                    <div class="card stat-card">
                        <div class="stat-icon blue">👥</div>

                        <div>
                            <p>Total Pelanggan</p>
                            <h2>12.458</h2>
                            <small class="up">↑ 4,8% bulan ini</small>
                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-icon orange">🧾</div>

                        <div>
                            <p>Tagihan Bulan Ini</p>
                            <h2>Rp 2,48 M</h2>
                            <small class="up">↑ 7,2% dari bulan lalu</small>
                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-icon green">💳</div>

                        <div>
                            <p>Sudah Dibayar</p>
                            <h2>Rp 1,96 M</h2>
                            <small class="up">79,1% pembayaran</small>
                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="stat-icon red">⚠️</div>

                        <div>
                            <p>Total Tunggakan</p>
                            <h2>Rp 518 Jt</h2>
                            <small class="down">↓ 3,4% dari bulan lalu</small>
                        </div>
                    </div>
                </div>

                <!-- CONTENT GRID -->
                <div class="grid">
                    <!-- CHART -->
                    <div class="card chart-card">
                        <div class="card-header">
                            <div>
                                <h3>Grafik Tagihan</h3>
                                <p>Perbandingan tagihan dan pembayaran</p>
                            </div>

                            <select>
                                <option>6 Bulan Terakhir</option>
                                <option>1 Tahun</option>
                            </select>
                        </div>

                        <div class="chart">
                            <div class="y-axis">
                                <span>3 M</span>
                                <span>2 M</span>
                                <span>1 M</span>
                                <span>0</span>
                            </div>

                            <div class="bars">
                                <div class="bar-group">
                                    <div
                                        class="bar tagihan"
                                        style="height: 70%"
                                    ></div>
                                    <div
                                        class="bar bayar"
                                        style="height: 50%"
                                    ></div>
                                    <span>Apr</span>
                                </div>

                                <div class="bar-group">
                                    <div
                                        class="bar tagihan"
                                        style="height: 80%"
                                    ></div>
                                    <div
                                        class="bar bayar"
                                        style="height: 60%"
                                    ></div>
                                    <span>Mei</span>
                                </div>

                                <div class="bar-group">
                                    <div
                                        class="bar tagihan"
                                        style="height: 65%"
                                    ></div>
                                    <div
                                        class="bar bayar"
                                        style="height: 55%"
                                    ></div>
                                    <span>Jun</span>
                                </div>

                                <div class="bar-group">
                                    <div
                                        class="bar tagihan"
                                        style="height: 85%"
                                    ></div>
                                    <div
                                        class="bar bayar"
                                        style="height: 68%"
                                    ></div>
                                    <span>Jul</span>
                                </div>

                                <div class="bar-group">
                                    <div
                                        class="bar tagihan"
                                        style="height: 90%"
                                    ></div>
                                    <div
                                        class="bar bayar"
                                        style="height: 73%"
                                    ></div>
                                    <span>Agu</span>
                                </div>

                                <div class="bar-group">
                                    <div
                                        class="bar tagihan"
                                        style="height: 95%"
                                    ></div>
                                    <div
                                        class="bar bayar"
                                        style="height: 79%"
                                    ></div>
                                    <span>Sep</span>
                                </div>
                            </div>
                        </div>

                        <div class="legend">
                            <span>
                                <i class="legend-tagihan"></i>
                                Tagihan
                            </span>

                            <span>
                                <i class="legend-bayar"></i>
                                Pembayaran
                            </span>
                        </div>
                    </div>

                    <!-- QUICK MENU -->
                    <div class="card quick-card">
                        <div class="card-header">
                            <div>
                                <h3>Aksi Cepat</h3>
                                <p>Kelola data SIMTAG</p>
                            </div>
                        </div>

                        <button class="quick-button">
                            <span>👤</span>
                            <div>
                                <strong>Tambah Pelanggan</strong>
                                <small>Daftarkan pelanggan baru</small>
                            </div>
                            <b>›</b>
                        </button>

                        <button class="quick-button">
                            <span>📏</span>
                            <div>
                                <strong>Input Meter</strong>
                                <small>Masukkan pemakaian meter</small>
                            </div>
                            <b>›</b>
                        </button>

                        <button class="quick-button">
                            <span>🧾</span>
                            <div>
                                <strong>Proses Tagihan</strong>
                                <small>Generate tagihan bulanan</small>
                            </div>
                            <b>›</b>
                        </button>

                        <button class="quick-button">
                            <span>📊</span>
                            <div>
                                <strong>Lihat Laporan</strong>
                                <small>Analisis data tagihan</small>
                            </div>
                            <b>›</b>
                        </button>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="card table-card">
                    <div class="card-header">
                        <div>
                            <h3>Tagihan Terbaru</h3>
                            <p>Data tagihan pelanggan terbaru</p>
                        </div>

                        <button class="view-all">Lihat Semua →</button>
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>No. Pelanggan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Periode</th>
                                    <th>Pemakaian</th>
                                    <th>Total Tagihan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td><strong>01.23.00456</strong></td>
                                    <td>Ahmad Fauzi</td>
                                    <td>September 2026</td>
                                    <td>24 m³</td>
                                    <td><strong>Rp 185.500</strong></td>
                                    <td>
                                        <span class="status paid">Lunas</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong>01.23.00457</strong></td>
                                    <td>Siti Rahma</td>
                                    <td>September 2026</td>
                                    <td>18 m³</td>
                                    <td><strong>Rp 142.000</strong></td>
                                    <td>
                                        <span class="status pending"
                                            >Belum Bayar</span
                                        >
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong>01.23.00458</strong></td>
                                    <td>Budi Santoso</td>
                                    <td>September 2026</td>
                                    <td>31 m³</td>
                                    <td><strong>Rp 236.000</strong></td>
                                    <td>
                                        <span class="status paid">Lunas</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong>01.23.00459</strong></td>
                                    <td>Rina Wulandari</td>
                                    <td>September 2026</td>
                                    <td>15 m³</td>
                                    <td><strong>Rp 121.500</strong></td>
                                    <td>
                                        <span class="status pending"
                                            >Belum Bayar</span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<script setup>
import { ref } from "vue";

const sidebarOpen = ref(false);
</script>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f5f7fb;
    color: #172033;
}

button,
select {
    font-family: inherit;
}

.app {
    display: flex;
    min-height: 100vh;
}

/* SIDEBAR */

.sidebar {
    width: 260px;
    background: white;
    border-right: 1px solid #e7eaf0;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100vh;
    z-index: 100;
}

.logo {
    height: 82px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px;
    border-bottom: 1px solid #f0f1f5;
}

.logo-icon {
    width: 42px;
    height: 42px;
    background: #e9f4ff;
    color: #0878d1;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.logo h2 {
    font-size: 20px;
    color: #0878d1;
}

.logo span {
    font-size: 11px;
    color: #8992a3;
}

.menu {
    padding: 20px 14px;
    flex: 1;
}

.menu-title {
    font-size: 10px;
    color: #a0a7b5;
    font-weight: bold;
    margin: 15px 10px 8px;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    margin-bottom: 5px;
    text-decoration: none;
    color: #687286;
    border-radius: 10px;
    font-size: 14px;
}

.menu-item span {
    width: 20px;
    text-align: center;
}

.menu-item:hover {
    background: #f2f7fc;
    color: #0878d1;
}

.menu-item.active {
    background: #eaf5ff;
    color: #0878d1;
    font-weight: bold;
}

.sidebar-bottom {
    padding: 15px;
    border-top: 1px solid #eee;
}

.user-mini {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-mini div:nth-child(2) {
    flex: 1;
}

.user-mini strong {
    display: block;
    font-size: 13px;
}

.user-mini small {
    color: #929aaa;
    font-size: 11px;
}

/* MAIN */

.main {
    margin-left: 260px;
    width: calc(100% - 260px);
}

/* NAVBAR */

.navbar {
    height: 82px;
    background: white;
    border-bottom: 1px solid #e7eaf0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
}

.navbar h1 {
    font-size: 22px;
    margin-bottom: 5px;
}

.navbar p {
    font-size: 12px;
    color: #8992a3;
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 22px;
}

.notification {
    background: transparent;
    border: 0;
    font-size: 19px;
    position: relative;
    cursor: pointer;
}

.notification span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #e53935;
    position: absolute;
    right: -1px;
    top: 0;
}

.profile {
    display: flex;
    gap: 10px;
    align-items: center;
}

.profile strong {
    display: block;
    font-size: 13px;
}

.profile small {
    font-size: 11px;
    color: #929aaa;
}

.avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #0878d1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

/* CONTENT */

.content {
    padding: 30px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}

.card {
    background: white;
    border: 1px solid #e8ebf1;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(20, 35, 60, 0.03);
}

.stat-card {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
}

.blue {
    background: #eaf5ff;
}

.orange {
    background: #fff4e5;
}

.green {
    background: #e8f8ef;
}

.red {
    background: #ffeded;
}

.stat-card p {
    font-size: 12px;
    color: #8992a3;
    margin-bottom: 5px;
}

.stat-card h2 {
    font-size: 20px;
}

.stat-card small {
    font-size: 10px;
}

.up {
    color: #1b9b5f;
}

.down {
    color: #e05a5a;
}

/* GRID */

.grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 18px;
    margin-top: 20px;
}

.chart-card,
.quick-card,
.table-card {
    padding: 22px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.card-header h3 {
    font-size: 16px;
    margin-bottom: 5px;
}

.card-header p {
    font-size: 11px;
    color: #929aaa;
}

.card-header select {
    padding: 8px 12px;
    border: 1px solid #e2e5ea;
    border-radius: 8px;
    color: #606979;
    background: white;
}

/* CHART */

.chart {
    height: 220px;
    display: flex;
    border-bottom: 1px solid #e6e8ee;
}

.y-axis {
    width: 45px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding-bottom: 25px;
    font-size: 10px;
    color: #a1a8b5;
}

.bars {
    flex: 1;
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    background: repeating-linear-gradient(
        to bottom,
        transparent,
        transparent 53px,
        #f0f2f5 54px
    );
}

.bar-group {
    height: 100%;
    width: 50px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    gap: 5px;
    position: relative;
    padding-bottom: 25px;
}

.bar {
    width: 16px;
    border-radius: 5px 5px 0 0;
}

.bar.tagihan {
    background: #1685d8;
}

.bar.bayar {
    background: #78c4f4;
}

.bar-group span {
    position: absolute;
    bottom: 3px;
    font-size: 10px;
    color: #8992a3;
}

.legend {
    display: flex;
    gap: 20px;
    margin-top: 15px;
    font-size: 11px;
    color: #687286;
}

.legend i {
    display: inline-block;
    width: 9px;
    height: 9px;
    border-radius: 2px;
    margin-right: 5px;
}

.legend-tagihan {
    background: #1685d8;
}

.legend-bayar {
    background: #78c4f4;
}

/* QUICK */

.quick-button {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px;
    border: 1px solid #edf0f4;
    background: white;
    border-radius: 10px;
    margin-bottom: 9px;
    cursor: pointer;
    text-align: left;
}

.quick-button:hover {
    background: #f8fbff;
}

.quick-button > span {
    width: 36px;
    height: 36px;
    background: #f1f6fb;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.quick-button div {
    flex: 1;
}

.quick-button strong {
    display: block;
    font-size: 12px;
}

.quick-button small {
    color: #929aaa;
    font-size: 10px;
}

.quick-button b {
    color: #a3a9b4;
}

/* TABLE */

.table-card {
    margin-top: 20px;
}

.view-all {
    background: none;
    border: 0;
    color: #0878d1;
    font-size: 12px;
    cursor: pointer;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 750px;
}

th {
    background: #f8f9fb;
    color: #7e8797;
    font-size: 11px;
    text-align: left;
    padding: 13px;
}

td {
    padding: 14px 13px;
    border-bottom: 1px solid #edf0f4;
    font-size: 12px;
    color: #606979;
}

td strong {
    color: #273247;
}

.status {
    padding: 5px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: bold;
}

.status.paid {
    color: #178554;
    background: #e8f8ef;
}

.status.pending {
    color: #c67a00;
    background: #fff4df;
}

/* MOBILE */

.mobile-button {
    display: none;
    border: 0;
    background: transparent;
    font-size: 22px;
    cursor: pointer;
}

/* RESPONSIVE */

@media (max-width: 1100px) {
    .stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .sidebar {
        transform: translateX(-100%);
        transition: 0.25s;
    }

    .sidebar.open {
        transform: translateX(0);
    }

    .main {
        margin-left: 0;
        width: 100%;
    }

    .mobile-button {
        display: block;
    }

    .navbar {
        padding: 0 15px;
    }

    .navbar p {
        display: none;
    }

    .profile div:last-child {
        display: none;
    }

    .content {
        padding: 15px;
    }

    .stats {
        grid-template-columns: 1fr;
    }
}
</style>
