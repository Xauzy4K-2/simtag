<template>
    <div class="summary-page">
        <header class="topbar">
            <div class="brand-wrap">
                <div class="brand-logo">
                    <div class="brand-logo-mark">◔</div>
                </div>
                <div class="brand-text">
                    <span class="brand-text-small">PDAM KOTA</span>
                    <span class="brand-text-large">TIRTA PINANG</span>
                </div>
            </div>

            <div class="topbar-user">
                <button class="icon-button" aria-label="Notifikasi">🔔</button>
                <div class="user-avatar">AD</div>
                <div class="user-meta">
                    <span class="user-label">Administrator</span>
                    <span class="user-sub">Pengelola</span>
                </div>
            </div>
        </header>

        <main class="main-panel">
            <!-- <div class="page-head">
                <div class="section-label">
                    <span class="section-dot"></span>
                    <span>Pusat Kontrol Operasional</span>
                </div>

                <div class="button-group">
                    <button class="ghost-button">
                        <span>↓</span>
                        Download Excel
                    </button>
                    <button class="ghost-button primary">
                        <span>↓</span>
                        Download PDF
                    </button>
                </div>
            </div> -->

            <div class="welcome-row">
                <div class="welcome-copy">
                    <h1>Selamat datang, <span>Admin</span></h1>
                    <p>Monitoring real-time operasional air bersih, aduan pelanggan, dan rekonsiliasi kas.</p>
                </div>
            </div>

            <div class="toolbar-row">
                <div class="tab-group">
                    <button class="tab-button active">
                        <span>◉</span>
                        Ringkasan
                    </button>
                    <button class="tab-button">
                        <span>◌</span>
                        Pelanggan
                    </button>
                    <button class="tab-button">
                        <span>◌</span>
                        Pertanyaan survei
                    </button>
                    <button class="tab-button">
                        <span>◌</span>
                        Aduan
                    </button>
                    <button class="tab-button">
                        <span>◌</span>
                        Pembayaran
                    </button>
                </div>

                <button class="refresh-button">
                    <span>↻</span>
                    Segarkan ringkasan
                </button>
            </div>

            <section class="stats-grid">
                <article v-for="item in stats" :key="item.label" class="stat-card" :class="item.tone">
                    <div class="stat-head">
                        <div class="stat-icon" :class="item.tone">{{ item.icon }}</div>
                        <span v-if="item.tag" class="small-tag">{{ item.tag }}</span>
                    </div>

                    <div class="stat-body">
                        <p>{{ item.label }}</p>
                        <h2>{{ item.value }}</h2>
                        <div class="stat-meta" :class="item.metaType">
                            <span>{{ item.metaIcon }}</span>
                            <span>{{ item.meta }}</span>
                        </div>
                    </div>
                </article>
            </section>

            <section class="list-grid">
                <article class="list-card">
                    <div class="card-header">
                        <div class="header-title">
                            <span class="header-icon danger">!</span>
                            <h3>Aduan terbaru</h3>
                        </div>
                        <button class="link-button">Lihat semua</button>
                    </div>

                    <ul class="info-list">
                        <li v-for="item in complaints" :key="item.id" class="info-item">
                            <div class="info-text">
                                <span>{{ item.name }}</span>
                            </div>
                            <span class="status-badge" :class="item.statusClass">{{ item.status }}</span>
                        </li>
                    </ul>
                </article>

                <article class="list-card">
                    <div class="card-header">
                        <div class="header-title">
                            <span class="header-icon success">✓</span>
                            <h3>Pembayaran terbaru</h3>
                        </div>
                        <button class="link-button">Lihat semua</button>
                    </div>

                    <ul class="payment-list">
                        <li v-for="item in payments" :key="item.id" class="payment-item">
                            <div class="payment-top">
                                <div class="tag-group">
                                    <span class="mini-tag" :class="item.tagClass">{{ item.tag }}</span>
                                    <span class="payment-label">{{ item.name }}</span>
                                </div>
                                <span class="amount">{{ item.amount }}</span>
                            </div>
                            <div class="payment-meta">
                                <span>{{ item.date }}</span>
                                <span>{{ item.note }}</span>
                            </div>
                        </li>
                    </ul>
                </article>

                <article class="list-card">
                    <div class="card-header">
                        <div class="header-title">
                            <span class="header-icon info">★</span>
                            <h3>Survei terbaru</h3>
                        </div>
                        <button class="link-button">Lihat semua</button>
                    </div>

                    <ul class="survey-list">
                        <li v-for="item in surveys" :key="item.id" class="survey-item">
                            <div class="survey-row">
                                <div class="survey-meta">
                                    <span class="survey-avatar">{{ item.avatar }}</span>
                                    <div>
                                        <strong>{{ item.title }}</strong>
                                        <small>{{ item.subtitle }}</small>
                                    </div>
                                </div>
                                <button class="survey-btn" :class="item.buttonClass">{{ item.buttonText }}</button>
                            </div>
                        </li>
                    </ul>
                </article>
            </section>
        </main>

        <footer class="page-footer">
            <span>© 2026 PDAM Kota Tirta Pinang</span>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const stats = ref([
    { label: 'Pelanggan', value: '12', icon: '👤', tone: 'soft-blue', tag: '+2 aktif', meta: 'Adaan terbaru', metaType: 'up', metaIcon: '↗', },
    { label: 'Aduan terbuka', value: '7', icon: '⚠', tone: 'soft-yellow', tag: '5 tiket terakhir', meta: 'Butuh detail', metaType: 'warn', metaIcon: '↗', },
    { label: 'Pertanyaan survei', value: '20', icon: '📝', tone: 'soft-purple', tag: 'Baru', meta: 'Belum diproses', metaType: 'neutral', metaIcon: '↗', },
    { label: 'Survei', value: '12', icon: '★', tone: 'soft-green', tag: 'Aktif', meta: 'CBS 92%', metaType: 'up', metaIcon: '★', },
    { label: 'Pembayaran', value: 'Rp 3.104.000', icon: '💰', tone: 'soft-emerald', tag: 'Bulan ini', meta: 'Sudah masuk', metaType: 'up', metaIcon: '↗', },
]);

const complaints = ref([
    { id: 1, name: 'TP-93901 | Air Mati / Macet', status: 'received', statusClass: 'received' },
    { id: 2, name: 'TP-79874 | Kebocoran Meter Air', status: 'rejected', statusClass: 'rejected' },
    { id: 3, name: 'TP-50401 | Tagihan Salah', status: 'received', statusClass: 'received' },
    { id: 4, name: 'TP-31239 | Pemasangan Baru', status: 'resolved', statusClass: 'resolved' },
    { id: 5, name: 'TP-89918 | Pipa Bocor', status: 'resolved', statusClass: 'resolved' },
]);

const payments = ref([
    { id: 1, tag: 'QRIS', tagClass: 'qr', name: 'Ari Santoso', amount: 'Rp 500.000', date: '9 Sep 2026, 9:20', note: 'Berhasil', },
    { id: 2, tag: 'DANA', tagClass: 'dana', name: 'Dewi Kusuma', amount: 'Rp 500.000', date: '8 Sep 2026, 13:20', note: 'Berhasil', },
    { id: 3, tag: 'BCA', tagClass: 'bank', name: 'Rizky', amount: 'Rp 250.000', date: '7 Sep 2026, 14:40', note: 'Berhasil', },
    { id: 4, tag: 'BCA', tagClass: 'bank', name: 'Teguh', amount: 'Rp 185.400', date: '7 Sep 2026, 14:08', note: 'Berhasil', },
    { id: 5, tag: 'OVO', tagClass: 'ovo', name: 'Susi Lestari', amount: 'Rp 185.400', date: '7 Sep 2026, 16:02', note: 'Berhasil', },
]);

const surveys = ref([
    { id: 1, avatar: 'S', title: 'Karyawan', subtitle: 'Bagaimana kualitas layanan air minum?', buttonText: 'Sangat bagus', buttonClass: 'positive' },
    { id: 2, avatar: 'S', title: 'Pelanggan', subtitle: 'Bagaimana kemampuan petugas pelayanan?', buttonText: 'Puas', buttonClass: 'positive' },
    { id: 3, avatar: 'S', title: 'Pelanggan', subtitle: 'Bagaimana kecepatan respon pelayanan?', buttonText: 'Sangat bagus', buttonClass: 'positive' },
    { id: 4, avatar: 'S', title: 'Pelanggan', subtitle: 'Apakah informasi tagihan mudah dipahami?', buttonText: 'Puas', buttonClass: 'positive' },
]);
</script>

<style scoped>
:global(html, body, #app) {
    margin: 0;
    width: 100%;
    min-height: 100%;
    background: #edf1f5;
    font-family: Inter, 'Segoe UI', sans-serif;
    color: #1f2937;
}

button {
    border: none;
    cursor: pointer;
    font: inherit;
}

.summary-page {
    min-height: 100vh;
    background: #edf1f5;
    color: #1f2937;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    background: #ffffff;
    padding: 14px 20px 14px 18px;
    border-bottom: 1px solid #e7ebf1;
    box-shadow: 0 1px 0 rgba(15, 23, 42, 0.03);
}

.brand-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 260px;
}

.brand-logo {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    background: linear-gradient(135deg, #19a26d, #0f766e);
    display: grid;
    place-items: center;
    color: white;
    box-shadow: 0 10px 22px rgba(16, 185, 129, 0.25);
}

.brand-logo-mark {
    font-size: 18px;
    transform: translateY(-1px);
}

.brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1.1;
}

.brand-text-small {
    font-size: 10px;
    letter-spacing: 0.08em;
    color: #64748b;
}

.brand-text-large {
    font-size: 15px;
    letter-spacing: 0.02em;
    font-weight: 800;
    color: #0f172a;
}

.system-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: rgba(16, 185, 129, 0.08);
    border: 1px solid rgba(16, 185, 129, 0.15);
    color: #0f172a;
    border-radius: 999px;
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 600;
    flex: 1;
    max-width: 500px;
}

.status-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #22c55e;
    display: inline-block;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
}

.topbar-user {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    min-width: 190px;
}

.icon-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 10px;
    background: #f3f4f6;
    color: #334155;
    font-size: 13px;
}

.avatar-wrap {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    color: #0f172a;
    font-size: 11px;
    font-weight: 800;
    border: 1px solid #dbe2ea;
}

.user-meta {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.user-label {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
}

.user-sub {
    font-size: 10px;
    color: #64748b;
}

.main-panel {
    max-width: 1440px;
    margin: 0 auto;
    padding: 18px 18px 12px;
}

.page-header {
    margin-top: 8px;
    margin-bottom: 14px;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    font-weight: 700;
    color: #0f172a;
    padding: 7px 10px;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 999px;
}

.status-dot--success {
    background: #2eb67d;
    box-shadow: 0 0 0 3px rgba(46, 182, 125, 0.14);
}

.welcome-row {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
}

.welcome-row h1 {
    font-size: clamp(2rem, 2vw, 3rem);
    font-weight: 800;
    letter-spacing: -0.04em;
    color: #101827;
    margin: 0 0 8px;
}

.welcome-row h1 span {
    color: #111827;
}

.welcome-row p {
    margin: 0;
    font-size: 14px;
    color: #4b5563;
}

.button-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ghost-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #dbebeb;
    color: #0f172a;
    font-weight: 600;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
}

.ghost-button.primary {
    background: #e8f8f0;
    border-color: #c0edda;
    color: #0f766e;
}

.toolbar-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    background: #f6f8fb;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 8px 10px 10px;
    margin-bottom: 18px;
}

.tab-group {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.tab-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 10px;
    background: transparent;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid transparent;
}

.tab-button.active {
    background: #ffffff;
    border-color: #dfe7ef;
    color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
}

.refresh-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #dfe7ef;
    color: #334155;
    font-size: 12px;
    font-weight: 700;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(180px, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid #e7edf3;
    border-radius: 16px;
    padding: 14px 12px 12px;
    min-height: 150px;
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.02);
}

.stat-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 18px;
    background: #f3f4f6;
}

.small-tag {
    background: rgba(15, 118, 110, 0.09);
    border-radius: 999px;
    color: #0f766e;
    font-size: 10px;
    padding: 6px 8px;
    font-weight: 700;
}

.stat-body p {
    margin: 0 0 8px;
    color: #475569;
    font-weight: 600;
    font-size: 13px;
}

.stat-body h2 {
    margin: 0;
    font-size: clamp(1.7rem, 1.4vw, 2.2rem);
    line-height: 1.1;
    color: #101827;
    letter-spacing: -0.04em;
}

.stat-meta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 10px;
    font-size: 12px;
    font-weight: 600;
}

.stat-meta.up {
    color: #0f766e;
}

.stat-meta.warn {
    color: #dc6803;
}

.stat-meta.neutral {
    color: #475569;
}

.soft-blue .stat-icon {
    background: #e0f2fe;
}

.soft-yellow .stat-icon {
    background: #fef3c7;
}

.soft-purple .stat-icon {
    background: #ede9fe;
}

.soft-green .stat-icon {
    background: #dcfce7;
}

.soft-emerald .stat-icon {
    background: #d1fae5;
}

.list-grid {
    display: grid;
    grid-template-columns: 1.2fr 1.5fr 1.4fr;
    gap: 16px;
}

.list-card {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid #e3e8ef;
    border-radius: 18px;
    padding: 14px 14px 0;
    min-height: 355px;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.02);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
    padding-bottom: 8px;
}

.header-title {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.header-icon {
    width: 26px;
    height: 26px;
    display: grid;
    place-items: center;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
}

.header-icon.danger {
    background: #fee2e2;
    color: #b91c1c;
}

.header-icon.success {
    background: #dcfce7;
    color: #15803d;
}

.header-icon.info {
    background: #e0f2fe;
    color: #0369a1;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    letter-spacing: -0.02em;
    color: #111827;
}

.link-button {
    background: transparent;
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 0;
}

.info-list,
.payment-list,
.survey-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.info-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    background: #f8fafc;
    border: 1px solid #edf2f7;
    border-radius: 12px;
    padding: 10px 10px;
    margin-bottom: 8px;
}

.info-text {
    font-size: 13px;
    color: #1f2937;
    line-height: 1.4;
    word-break: break-word;
}

.status-badge {
    min-width: 66px;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: lowercase;
}

.status-badge.received {
    background: #e0f2fe;
    color: #0369a1;
}

.status-badge.rejected {
    background: #fee2e2;
    color: #b91c1c;
}

.status-badge.resolved {
    background: #dcfce7;
    color: #166534;
}

.payment-item {
    background: #f8fafc;
    border: 1px solid #edf2f7;
    border-radius: 12px;
    padding: 10px 12px;
    margin-bottom: 8px;
}

.payment-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 8px;
}

.tag-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.mini-tag {
    min-width: 54px;
    padding: 5px 8px;
    border-radius: 8px;
    text-align: center;
    font-size: 10px;
    font-weight: 800;
    color: #1f2937;
}

.mini-tag.qr {
    background: #e0f2fe;
    color: #0f766e;
}

.mini-tag.dana {
    background: #e9d5ff;
    color: #7c3aed;
}

.mini-tag.bank {
    background: #dcfce7;
    color: #166534;
}

.mini-tag.ovo {
    background: #fef3c7;
    color: #92400e;
}

.payment-label {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
}

.amount {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
}

.payment-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    color: #64748b;
    font-size: 12px;
}

.survey-item {
    border: 1px solid #edf2f7;
    background: #f8fafc;
    border-radius: 12px;
    padding: 10px 12px;
    margin-bottom: 8px;
}

.survey-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.survey-meta {
    display: flex;
    align-items: center;
    gap: 10px;
}

.survey-avatar {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #dbeafe;
    color: #1d4ed8;
    display: grid;
    place-items: center;
    font-size: 11px;
    font-weight: 800;
}

.survey-meta strong {
    display: block;
    font-size: 13px;
    color: #0f172a;
}

.survey-meta small {
    display: block;
    font-size: 11px;
    color: #64748b;
    margin-top: 2px;
}

.survey-btn {
    white-space: nowrap;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 10px;
    border: 1px solid transparent;
}

.survey-btn.positive {
    background: #dcfce7;
    color: #166534;
}

.page-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    max-width: 1440px;
    margin: 18px auto 0;
    padding: 14px 18px 20px;
    color: #64748b;
    font-size: 12px;
    border-top: 1px solid rgba(148, 163, 184, 0.2);
}

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(3, minmax(160px, 1fr));
    }

    .list-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .topbar {
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .system-status {
        order: 3;
        width: 100%;
        max-width: 100%;
    }

    .welcome-row,
    .toolbar-row,
    .page-footer {
        flex-direction: column;
        align-items: flex-start;
    }

    .button-group {
        width: 100%;
    }

    .ghost-button {
        flex: 1;
    }

    .stats-grid {
        grid-template-columns: repeat(2, minmax(160px, 1fr));
    }

    .tab-group {
        width: 100%;
    }

    .tab-button,
    .refresh-button {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 520px) {
    .topbar {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        width: calc(100% - 20px);
        min-height: 0;
        margin: 2px 10px 0;
        padding: 12px 12px 11px;
        border-radius: 0 0 12px 12px;
        align-items: center;
    }

    .brand-wrap {
        width: 100%;
        min-width: 0;
        gap: 9px;
    }

    .brand-logo {
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        border-radius: 9px;
    }

    .brand-logo-mark {
        font-size: 14px;
    }

    .brand-text-small {
        font-size: 8px;
        letter-spacing: .08em;
    }

    .brand-text-large {
        font-size: 12px;
        line-height: 1.05;
    }

    .topbar-user {
        width: 100%;
        min-width: 0;
        justify-content: flex-start;
        gap: 9px;
        padding-left: 31px;
    }

    .icon-button {
        width: 21px;
        height: 21px;
        padding: 0;
        border-radius: 6px;
        background: #fff7ec;
        font-size: 11px;
    }

    .user-avatar {
        width: 24px;
        height: 24px;
        flex: 0 0 24px;
        font-size: 9px;
        background: #f4f5f7;
        border: 0;
    }

    .user-meta {
        display: flex;
        line-height: 1.1;
    }

    .user-label {
        font-size: 10px;
    }

    .user-sub {
        margin-top: 2px;
        font-size: 8px;
    }

    .main-panel {
        padding-top: 15px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .welcome-row h1 {
        font-size: 2rem;
    }
}
</style>
