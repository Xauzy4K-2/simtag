<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    dashboard: {
        type: Object,
        default: () => ({}),
    },
});

const sidebarOpen = ref(false);
const data = computed(() => props.dashboard ?? {});
const customer = computed(() => data.value.customer ?? {});
const bill = computed(() => data.value.bill);
const usage = computed(() => data.value.usage ?? { items: [], average: 0 });
const isPaid = computed(() => bill.value?.status === 'paid');

const money = (value) => new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
}).format(value ?? 0);

const monthName = (value) => value
    ? value.charAt(0).toUpperCase() + value.slice(1)
    : '-';

const billStatus = computed(() => isPaid.value ? 'Sudah Dibayar' : 'Belum Bayar');
const dueLabel = computed(() => bill.value?.due_date
    ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long' }).format(new Date(`${bill.value.due_date}T00:00:00`))
    : 'Belum ditentukan');
</script>

<template>
    <div class="user-app">
        <aside class="user-sidebar" :class="{ 'is-open': sidebarOpen }">
            <button class="close-menu" aria-label="Tutup menu" @click="sidebarOpen = false">×</button>
            <div class="brand">
                <span class="brand-mark">◆</span>
                <span><small>PDAM</small><strong>TIRTA PINANG</strong></span>
            </div>
            <nav>
                <a class="active" href="/app/dashboard">⌂ <span>Beranda</span></a>
                <a href="/app/tagihan">▤ <span>Tagihan</span></a>
                <a href="/app/pengaduan">◌ <span>Pengaduan</span></a>
                <a href="/app/profil">♙ <span>Profil</span></a>
                <a href="#riwayat">••• <span>Riwayat</span></a>
            </nav>
        </aside>

        <main class="user-main">
            <header class="user-header">
                <button class="menu-button" aria-label="Buka menu" @click="sidebarOpen = true">☰</button>
                <div class="brand desktop-brand">
                    <span class="brand-mark">◆</span>
                    <span><small>PDAM</small><strong>TIRTA PINANG</strong></span>
                </div>
                <a class="profile-button" href="#profil" aria-label="Profil pengguna">♙</a>
            </header>

            <section class="welcome">
                <div>
                    <h1>Halo, {{ customer.name ?? 'Pelanggan' }}!</h1>
                    <p>ID: {{ customer.number ?? '-' }} <b>•</b> Pelanggan Tirta Pinang</p>
                </div>
                <span class="status"><i /> {{ customer.status === 'active' ? 'AKTIF' : 'TIDAK AKTIF' }}</span>
            </section>

            <section id="tagihan" class="bill-card">
                <div class="bill-top">
                    <span>TOTAL TAGIHAN BULAN INI</span>
                    <b :class="{ paid: isPaid }">{{ billStatus }}</b>
                </div>
                <strong>{{ bill ? money(bill.amount) : 'Tidak ada tagihan' }}</strong>
                <div class="bill-bottom">
                    <span>Batas Waktu: <b>{{ dueLabel }}</b></span>
                    <a v-if="bill && !isPaid" href="#pembayaran">Bayar Sekarang <span>›</span></a>
                </div>
            </section>

            <section class="quick-actions">
                <a href="/app/tagihan"><span class="action-icon blue">▱</span>Cek Tagihan</a>
                <a href="/app/pengaduan"><span class="action-icon red">!</span>Lapor Rusak</a>
                <a href="#survei"><span class="action-icon yellow">☆</span>Isi Survei</a>
                <a href="#riwayat"><span class="action-icon purple">◷</span>Log Aktivitas</a>
            </section>

            <section class="panel usage-panel">
                <div class="panel-title">
                    <h2>TREN KONSUMSI AIR <span>(3 BULAN TERAKHIR)</span></h2>
                    <b>Rata-rata: {{ usage.average }} m³</b>
                </div>
                <div class="bars">
                    <div v-for="(item, index) in usage.items" :key="item.period" class="bar-item">
                        <strong>{{ item.usage_m3 }} m³</strong>
                        <div class="bar-track"><i :class="{ current: index === usage.items.length - 1 }" :style="{ height: `${Math.max(item.usage_m3 * 3, 8)}px` }" /></div>
                        <span :class="{ current: index === usage.items.length - 1 }">{{ monthName(item.month) }}</span>
                    </div>
                </div>
            </section>

            <section class="panel quality-panel">
                <div class="panel-title">
                    <h2>LIVE STATUS KUALITAS AIR<br>WILAYAH ANDA</h2>
                    <button @click="$forceUpdate()">↻ Segarkan</button>
                </div>
                <div class="quality-grid">
                    <div><span class="quality-icon blue">▱</span><small>Kejernihan</small><b>{{ data.quality?.clarity ?? 'Data belum tersedia' }}</b></div>
                    <div><span class="quality-icon green">ϟ</span><small>Tekanan</small><b>{{ data.quality?.pressure ?? 'Data belum tersedia' }}</b></div>
                    <div><span class="quality-icon purple">♧</span><small>Kadar pH Air</small><b>{{ data.quality?.ph ?? 'Data belum tersedia' }}</b></div>
                </div>
            </section>

            <section id="pengaduan" class="help-card">
                <span>!</span>
                <div><b>Pusat Hubungan & Laporan Kendala</b><p>{{ data.open_complaint ? `Laporan aktif: ${data.open_complaint.subject}` : 'Air mati secara mendadak atau ada kebocoran pipa utama? Kirim aduan instan.' }}</p></div>
            </section>
        </main>

        <nav class="bottom-nav" aria-label="Navigasi utama">
            <a class="active" href="/app/dashboard"><span>⌂</span>Beranda</a>
            <a href="/app/tagihan"><span>▤</span>Tagihan</a>
            <a href="/app/pengaduan"><span>◌</span>Pengaduan</a>
            <a href="/app/profil"><span>♙</span>Profil</a>
            <a href="#riwayat"><span>•••</span>Riwayat</a>
        </nav>
    </div>
</template>

<style scoped>
:global(*) { box-sizing: border-box; }
:global(body) { margin: 0; background: #f7faff; color: #152640; font-family: Inter, ui-sans-serif, system-ui, sans-serif; }
.user-app { min-height: 100vh; }
.user-sidebar { display: none; }
.user-main { max-width: 620px; margin: auto; padding-bottom: 32px; }
.bottom-nav { display: none; }
.user-header { height: 96px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; background: white; border-bottom: 1px solid #e5edf5; }
.menu-button, .profile-button { border: 0; background: transparent; color: #192d48; font-size: 29px; cursor: pointer; text-decoration: none; }
.profile-button { width: 60px; height: 60px; border-radius: 50%; background: #f1f6fb; border: 1px solid #dce7f1; display: grid; place-items: center; font-size: 29px; }
.brand { display: flex; align-items: center; gap: 11px; color: #08785b; }
.brand-mark { width: 48px; height: 48px; border-radius: 13px; display: grid; place-items: center; background: #00a873; color: white; font-size: 20px; }
.brand small, .brand strong { display: block; line-height: 1.1; }
.brand small { color: #63738d; letter-spacing: 1px; font-size: 13px; }
.brand strong { font-size: 19px; }
.welcome { padding: 29px 24px 24px; display: flex; align-items: center; justify-content: space-between; gap: 15px; }
h1 { margin: 0 0 7px; font-size: 27px; letter-spacing: -1px; }
.welcome p { margin: 0; color: #61748f; font-size: 18px; }.welcome p b { color: #8a9bb0; }
.status { flex: none; color: #078f69; border: 1px solid #76efc7; border-radius: 24px; padding: 10px 15px; font-weight: 700; }.status i { display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #09b980; }
.bill-card { position: relative; overflow: hidden; margin: 0 24px 24px; padding: 29px; border-radius: 24px; color: white; background: linear-gradient(115deg, #1767ed, #4e42cb); box-shadow: 0 10px 20px #2768dc33; }
.bill-card::after { content: ''; position: absolute; width: 112px; height: 112px; right: 38px; top: 59px; border-radius: 50%; background: #ffffff1f; }
.bill-top, .bill-bottom { display: flex; justify-content: space-between; align-items: center; gap: 12px; }.bill-top span { font-size: 17px; letter-spacing: .7px; }.bill-top b { padding: 7px 15px; border-radius: 10px; background: #f63864; font-size: 16px; }.bill-top b.paid { background: #06a876; }
.bill-card > strong { position: relative; z-index: 1; display: block; font-size: 48px; margin: 17px 0 22px; letter-spacing: -1px; }.bill-bottom { position: relative; z-index: 1; border-top: 1px solid #ffffff3d; padding-top: 17px; font-size: 16px; }.bill-bottom a { padding: 10px 18px; border-radius: 12px; background: #ffffff38; color: white; text-decoration: none; font-weight: 700; }.bill-bottom a span { font-size: 27px; vertical-align: -2px; margin-left: 7px; }
.quick-actions, .panel, .help-card { margin: 0 24px 24px; border-radius: 24px; background: white; box-shadow: 0 2px 4px #1c38500d, 0 1px 2px #1c385014; }
.quick-actions { display: grid; grid-template-columns: repeat(4, 1fr); padding: 23px 10px; gap: 4px; }.quick-actions a { text-align: center; color: #14253f; text-decoration: none; font-size: 16px; }.action-icon { display: grid; place-items: center; width: 72px; height: 72px; margin: 0 auto 12px; border-radius: 23px; font-size: 33px; }.blue { color: #1665e8; background: #edf5ff; }.red { color: #f34261; background: #fff0f3; }.yellow { color: #f6a500; background: #fff9e9; }.purple { color: #8127e7; background: #f7efff; }
.panel { padding: 25px; }.panel-title { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }.panel h2 { margin: 0; font-size: 18px; letter-spacing: .7px; }.panel h2 span { color: #8ca0bd; font-weight: 400; }.panel-title > b { color: #078961; background: #effcf7; border: 1px solid #a2f2d0; border-radius: 9px; padding: 10px 13px; white-space: nowrap; font-size: 16px; }
.bars { display: flex; align-items: flex-end; justify-content: space-around; height: 245px; padding: 38px 20px 10px; }.bar-item { display: flex; flex-direction: column; align-items: center; gap: 9px; color: #40536f; }.bar-track { height: 140px; width: 72px; display: flex; align-items: flex-end; background: #e5ebf4; border-radius: 11px; overflow: hidden; }.bar-track i { width: 100%; display: block; background: #c7d2e1; border-radius: 11px; }.bar-track i.current { background: #09b980; box-shadow: 0 5px 8px #09b98044; }.bar-item span.current { color: #078f69; font-weight: 700; }
.quality-panel { padding-bottom: 25px; }.quality-panel button { border: 1px solid #85ebc7; background: #effcf7; border-radius: 9px; padding: 9px 14px; color: #087d5e; font-size: 16px; }.quality-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 11px; margin-top: 22px; }.quality-grid > div { min-height: 126px; border: 1px solid #c4ddff; border-radius: 17px; padding: 15px 9px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 7px; }.quality-grid > div:nth-child(2) { border-color: #a7f0d2; }.quality-grid > div:nth-child(3) { border-color: #ccd0ff; }.quality-icon { width: 39px; height: 39px; border-radius: 50%; display: grid; place-items: center; font-size: 22px; }.quality-grid small { color: #627691; font-size: 15px; }.quality-grid b { color: #073ca2; font-size: 15px; }.quality-grid > div:nth-child(2) b { color: #056b4d; }
.help-card { display: flex; gap: 17px; padding: 22px 25px; border-left: 6px solid #e8edf4; }.help-card > span { flex: none; width: 44px; height: 44px; border-radius: 50%; display: grid; place-items: center; background: #ffe6ec; color: #ec244e; font-weight: 800; }.help-card b { color: #e4234b; font-size: 17px; }.help-card p { color: #536681; margin: 8px 0 0; line-height: 1.5; }
.close-menu { display: none; }
@media (min-width: 900px) { .user-app { display: flex; max-width: 1200px; margin: auto; }.user-sidebar { display: block; width: 225px; flex: none; background: white; border-right: 1px solid #e5edf5; min-height: 100vh; padding: 28px 17px; }.user-sidebar .brand { margin: 0 8px 45px; }.user-sidebar nav a { display: block; padding: 14px 16px; margin: 5px 0; border-radius: 12px; color: #687b96; text-decoration: none; }.user-sidebar nav a.active, .user-sidebar nav a:hover { background: #ebfaf5; color: #078f69; }.user-sidebar nav a:first-letter { font-size: 21px; }.user-main { width: 620px; margin: 0 auto; }.user-header { border-radius: 0; }.menu-button { display: none; }.close-menu { display: none; } }
@media (max-width: 500px) { .desktop-brand { display: flex; }.user-header { height: 82px; padding: 0 18px; }.desktop-brand .brand-mark { width: 43px; height: 43px; }.desktop-brand strong { font-size: 16px; }.welcome { padding: 24px 18px 20px; align-items: flex-start; }.welcome h1 { font-size: 24px; }.welcome p { font-size: 15px; }.status { align-self: flex-end; }.bill-card, .quick-actions, .panel, .help-card { margin-left: 18px; margin-right: 18px; }.bill-card { padding: 24px 22px; }.bill-card > strong { font-size: 38px; }.bill-bottom { align-items: flex-start; flex-direction: column; }.quick-actions { padding: 18px 4px; }.quick-actions a { font-size: 12px; }.action-icon { width: 56px; height: 56px; font-size: 25px; }.panel { padding: 20px 16px; }.panel-title { flex-direction: column; }.bars { padding-left: 0; padding-right: 0; }.bar-track { width: 53px; }.quality-grid { gap: 6px; }.quality-grid > div { padding-left: 2px; padding-right: 2px; }.quality-grid small, .quality-grid b { font-size: 12px; } .user-main { padding-bottom: 92px; } .bottom-nav { position: fixed; z-index: 20; right: 0; bottom: 0; left: 0; display: grid; grid-template-columns: repeat(5, 1fr); height: 79px; border-top: 1px solid #dce6ef; background: #fff; box-shadow: 0 -4px 16px #1f41630b; } .bottom-nav a { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; color: #91a3bd; font-size: 14px; text-decoration: none; } .bottom-nav a span { height: 25px; font-size: 27px; line-height: 22px; } .bottom-nav a.active { color: #009b70; font-weight: 700; } }
</style>

<style>
/* Keep the user experience as a phone-sized dashboard even on a desktop preview. */
.user-app {
    width: 100%;
    max-width: 580px;
    min-height: 100vh;
    margin: 0 auto;
    background: #f7faff;
}

.user-sidebar {
    display: none !important;
}

.user-main {
    width: 100%;
    max-width: 580px;
    padding-bottom: 100px;
}

.bottom-nav {
    position: fixed;
    z-index: 20;
    right: 0;
    bottom: 0;
    left: 0;
    display: grid !important;
    grid-template-columns: repeat(5, 1fr);
    width: min(100%, 580px);
    height: 79px;
    margin: 0 auto;
    border-top: 1px solid #dce6ef;
    background: #fff;
    box-shadow: 0 -4px 16px #1f41630b;
}

.bottom-nav a {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    color: #91a3bd;
    font-size: 14px;
    text-decoration: none;
}

.bottom-nav a span {
    height: 25px;
    font-size: 27px;
    line-height: 22px;
}

.bottom-nav a.active {
    color: #009b70;
    font-weight: 700;
}

@media (min-width: 501px) {
    .user-header {
        height: 96px;
    }

    .welcome {
        padding: 29px 24px 24px;
    }

    .bill-card,
    .quick-actions,
    .panel,
    .help-card {
        margin-right: 24px;
        margin-left: 24px;
    }
}
</style>
