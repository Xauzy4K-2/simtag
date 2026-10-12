<script setup>
import { useTagihan } from '../../../js/components/tagihan.js';
const props = defineProps({ billPage: { type: Object, default: () => ({}) } });
const { bill, paid, money, date, copyReference } = useTagihan(props);
</script>

<template>
    <div class="bill-page">
        <header class="bill-header"><a href="/app/dashboard" aria-label="Kembali">‹</a><h1>TAGIHAN AIR</h1><a class="profile" href="#profil" aria-label="Profil">♙</a></header>
        <main class="bill-content">
            <section v-if="bill" class="meter-card card">
                <div class="meter-values"><div><b>PEMAKAIAN</b><strong>{{ bill.usage_m3.toLocaleString('id-ID') }} <small>m³</small></strong></div><div><b>PERIODE</b><strong>{{ bill.period }}</strong></div></div>
                <div class="usage-total"><span>ϟ</span> Total Pemakaian Bulan Ini: <strong>{{ bill.usage_m3.toLocaleString('id-ID') }} m³</strong></div>
            </section>
            <section v-if="bill" class="detail-card card">
                <h2>RINCIAN BIAYA</h2>
                <div class="detail-row"><span>Status Pembayaran</span><b class="payment-status" :class="{ paid }">{{ paid ? 'Lunas' : 'Belum Lunas' }}</b></div>
                <div class="detail-row"><span>Tarif Air Bersih</span><strong>{{ money(bill.water_rate) }}</strong></div>
                <div class="detail-row"><span>Biaya Administrasi</span><strong>{{ money(bill.administration_fee) }}</strong></div>
                <div v-if="bill.penalty_fee" class="detail-row"><span>Denda</span><strong>{{ money(bill.penalty_fee) }}</strong></div>
                <div class="total-row"><span>Total Pembayaran</span><strong>{{ money(bill.total_amount) }}</strong></div>
                <p class="due">Batas pembayaran: {{ date(bill.due_date) }}</p>
            </section>
            <section v-if="bill" class="method-card card">
                <h2>PILIH METODE PEMBAYARAN</h2>
                <div class="method-select">{{ bill.payment?.method || 'Metode pembayaran belum tersedia' }} <span>⌄</span></div>
                <div class="payment-box"><div><span>Referensi Pembayaran</span><strong>{{ bill.payment?.reference || 'Belum tersedia' }}</strong></div><button v-if="bill.payment?.reference" type="button" @click="copyReference">Salin</button></div>
            </section>
            <section v-else class="empty-card card"><strong>Belum ada tagihan</strong><p>Tagihan Anda belum tersedia di database.</p></section>
            <button v-if="bill && !paid" class="pay-button" type="button">BAYAR / SELESAIKAN PEMBAYARAN <span>→</span></button>
        </main>
        <nav class="bottom-nav"><a href="/app/dashboard"><span>⌂</span>Beranda</a><a class="active" href="/app/tagihan"><span>▤</span>Tagihan</a><a href="/app/pengaduan"><span>◌</span>Pengaduan</a><a href="/app/profil"><span>♙</span>Profil</a><a href="#riwayat"><span>•••</span>Riwayat</a></nav>
    </div>
</template>

<style scoped>
:global(*) { box-sizing: border-box; }:global(body) { margin: 0; background: #f7faff; color: #1d304b; font-family: Inter, ui-sans-serif, system-ui, sans-serif; }
.bill-page { width: min(100%, 580px); min-height: 100vh; margin: auto; padding-bottom: 100px; background: #f7faff; }.bill-header { height: 124px; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; background: white; border-bottom: 1px solid #e5edf5; }.bill-header h1 { margin: 0; font-size: 30px; letter-spacing: 2px; }.bill-header > a:first-child { color: #40536f; font-size: 54px; line-height: 1; text-decoration: none; }.profile { width: 72px; height: 72px; display: grid; place-items: center; border-radius: 50%; background: #f2f6fb; color: #40536f; font-size: 34px; text-decoration: none; }
.bill-content { padding: 30px; }.card { margin-bottom: 30px; padding: 30px; border: 1px solid #e8eef5; border-radius: 27px; background: white; box-shadow: 0 2px 6px #2947660c; }.meter-values { display: grid; grid-template-columns: 1fr 1fr; text-align: center; }.meter-values > div + div { border-left: 1px solid #e2eaf2; }.meter-values b, .card h2 { color: #91a3ba; font-size: 20px; letter-spacing: .5px; }.meter-values strong { display: block; margin-top: 17px; font-size: 34px; color: #233a59; }.meter-values small { color: #8da0b8; font-size: 20px; }.meter-values > div:last-child strong { color: #06a46f; }.usage-total { margin-top: 27px; padding-top: 22px; border-top: 2px dashed #dae4ef; color: #62758f; font-size: 20px; text-align: center; }.usage-total span { margin-right: 12px; color: #00aa76; font-size: 28px; }.usage-total strong { color: #172a45; }
.card h2 { margin: 0 0 28px; }.detail-row, .total-row { display: flex; align-items: center; justify-content: space-between; gap: 15px; margin: 21px 0; font-size: 20px; }.detail-row strong { color: #101e33; }.payment-status { padding: 8px 17px; border-radius: 22px; background: #fff0f2; color: #e51f4d; }.payment-status.paid { background: #e9fbf4; color: #00845f; }.total-row { margin-top: 27px; padding-top: 27px; border-top: 1px solid #e7edf4; font-weight: 700; }.total-row strong { color: #2563e8; font-size: 34px; }.due { margin: 0; color: #7487a0; font-size: 14px; text-align: right; }
.method-select { display: flex; justify-content: space-between; align-items: center; padding: 19px 24px; border: 1px solid #d9e4ef; border-radius: 18px; font-size: 20px; }.method-select span { font-size: 28px; color: #60748d; }.payment-box { display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-top: 22px; padding: 20px; border: 1px solid #a8f1d2; border-radius: 20px; background: #f1fdf8; }.payment-box span { display: block; color: #657a94; font-size: 16px; }.payment-box strong { display: block; margin-top: 10px; color: #087c5a; font-size: 18px; word-break: break-all; }.payment-box button { border: 0; border-radius: 12px; padding: 12px 15px; background: #d3f8e8; color: #087c5a; cursor: pointer; }.empty-card { padding: 45px 25px; text-align: center; }.empty-card strong { font-size: 24px; }.empty-card p { color: #71839b; }.pay-button { width: 100%; border: 0; border-radius: 20px; padding: 22px; background: #2563e8; color: white; font-size: 21px; font-weight: 800; box-shadow: 0 12px 24px #2563e833; }.pay-button span { float: right; font-size: 30px; line-height: 20px; }
.bottom-nav { position: fixed; z-index: 20; right: 0; bottom: 0; left: 0; display: grid; grid-template-columns: repeat(5, 1fr); width: min(100%, 580px); height: 79px; margin: auto; border-top: 1px solid #dce6ef; background: white; }.bottom-nav a { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; color: #91a3bd; font-size: 14px; text-decoration: none; }.bottom-nav span { height: 25px; font-size: 27px; line-height: 22px; }.bottom-nav a.active { color: #009b70; font-weight: 700; }
@media (max-width: 500px) { .bill-header { height: 96px; padding: 0 20px; }.bill-header h1 { font-size: 23px; }.bill-header > a:first-child { font-size: 44px; }.profile { width: 58px; height: 58px; font-size: 27px; }.bill-content { padding: 18px 18px 30px; }.card { margin-bottom: 24px; padding: 24px 20px; border-radius: 24px; }.meter-values b, .card h2 { font-size: 17px; }.meter-values strong { font-size: 30px; }.usage-total { font-size: 16px; }.detail-row { font-size: 17px; }.total-row strong { font-size: 27px; }.method-select { font-size: 17px; padding: 17px; }.pay-button { font-size: 17px; padding: 19px 16px; } }
</style>