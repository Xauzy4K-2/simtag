<script setup>
import { usePengaduan } from '../../../js/components/pengaduan.js';
const props = defineProps({ complaintPage: { type: Object, default: () => ({}) } });
const { customer, latest, form, loading, error, submit, selectAttachment } = usePengaduan(props);
</script>

<template>
    <div class="complaint-page">
        <header class="page-header"><a href="/app/dashboard">‹</a><h1>FORM PENGADUAN</h1><a class="profile" href="#profil">♙</a></header>
        <main class="content">
            <section class="reporter card"><b>● &nbsp; DATA PELAPOR</b><p>Nama: <strong>{{ customer.name || 'Pelanggan' }}</strong><span>ID: {{ customer.number || '-' }}</span></p></section>
            <form id="complaint-form" class="form-card card" @submit.prevent="submit">
                <label for="subject">KATEGORI MASALAH</label>
                <select id="subject" name="subject" v-model="form.subject"><option>Air Tidak Mengalir (Mati)</option><option>Kebocoran Pipa</option><option>Meter Air Rusak</option><option>Tagihan Tidak Sesuai</option><option>Lainnya</option></select>
                <label for="description">DESKRIPSI MASALAH</label><textarea id="description" name="description" v-model="form.description" required placeholder="Tulis rincian keluhan..." />
                <label for="attachment">LAMPIRAN FOTO (OPSIONAL)</label>
                <label class="attachment-picker" for="attachment">📷 <span>{{ form.attachment?.name || 'Ambil atau Unggah Foto Kendala' }}</span></label>
                <input id="attachment" name="attachment" class="file-input" type="file" accept="image/jpeg,image/png,image/webp" @change="selectAttachment">
                <p v-if="error" class="error">{{ error }}</p>
            </form>
            <section class="status-card card"><div class="status-title"><b>STATUS PENGADUAN TERKINI</b><a href="/app/pengaduan">↻ Segarkan</a></div><div v-if="latest" class="latest"><strong>{{ latest.subject }}</strong><span>{{ latest.status }} · {{ latest.created_at }}</span><p>{{ latest.description }}</p><a v-if="latest.attachment_url" class="attachment-preview" :href="latest.attachment_url" target="_blank" rel="noopener"><img :src="latest.attachment_url" alt="Foto lampiran pengaduan"></a></div><div v-else class="empty"><span>▤</span><p>Belum ada pengaduan aktif.</p></div></section>
            <button class="submit" form="complaint-form" type="submit" :disabled="loading">{{ loading ? 'MENGIRIM...' : 'KIRIM PENGADUAN' }} <span>→</span></button>
        </main>
        <nav class="bottom-nav"><a href="/app/dashboard"><span>⌂</span>Beranda</a><a href="/app/tagihan"><span>▤</span>Tagihan</a><a class="active" href="/app/pengaduan"><span>◌</span>Pengaduan</a><a href="/app/profil"><span>♙</span>Profil</a><a href="#riwayat"><span>•••</span>Riwayat</a></nav>
    </div>
</template>

<style scoped>
:global(*) { box-sizing: border-box; }
:global(body) { margin: 0; background: #f7faff; color: #1d304b; font-family: Inter, ui-sans-serif, system-ui, sans-serif; }
.complaint-page { width: min(100%, 580px); min-height: 100vh; margin: auto; padding-bottom: 100px; background: #f7faff; }
.page-header { height: 114px; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; background: #fff; border-bottom: 1px solid #dce6ef; }.page-header h1 { margin: 0; font-size: 26px; letter-spacing: 1px; }.page-header > a:first-child { color: #40536f; font-size: 54px; line-height: 1; text-decoration: none; }.profile { width: 62px; height: 62px; display: grid; place-items: center; border-radius: 50%; background: #657991; color: white; font-size: 30px; text-decoration: none; }
.content { padding: 30px; }.card { margin-bottom: 28px; padding: 28px; border: 1px solid #e0e8f1; border-radius: 25px; background: white; box-shadow: 0 2px 5px #29476610; }.reporter { background: #f0f7ff; border-color: #c8e0ff; }.reporter > b { color: #2265df; font-size: 19px; }.reporter p { margin: 18px 0 0; color: #647892; font-size: 19px; }.reporter strong { color: #15233a; margin-left: 8px; }.reporter p span { float: right; padding: 7px 12px; border-radius: 10px; color: #1544aa; background: #dbeaff; }
.form-card label { display: block; margin: 3px 0 14px; color: #40536e; font-size: 18px; font-weight: 800; }.form-card label:not(:first-child) { margin-top: 28px; }.form-card select, .form-card textarea, .form-card input { width: 100%; border: 2px solid #cbd9e8; border-radius: 19px; padding: 17px 20px; color: #15243b; background: white; font: inherit; font-size: 18px; }.form-card textarea { min-height: 160px; resize: vertical; }.file-input { display: none !important; }.attachment-picker { display: flex !important; align-items: center; justify-content: center; gap: 14px; min-height: 88px; margin-top: 0 !important; padding: 17px 20px; border: 2px dashed #cbd9e8; border-radius: 19px; color: #526984 !important; font-size: 18px !important; font-weight: 400 !important; cursor: pointer; }.attachment-picker span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }.error { color: #e31d45; font-size: 14px; }.status-title { display: flex; justify-content: space-between; padding-bottom: 20px; border-bottom: 1px solid #e2eaf2; color: #40536e; font-size: 18px; }.status-title a { color: #009b70; text-decoration: none; font-weight: 600; }.empty { padding: 38px 0 25px; text-align: center; color: #627792; font-size: 19px; }.empty span { display: grid; width: 70px; height: 70px; margin: 0 auto; place-items: center; border-radius: 50%; background: #f0f5fb; color: #8ca0b9; font-size: 32px; }.latest { padding-top: 20px; }.latest strong, .latest span { display: block; }.latest span { margin-top: 8px; color: #008a65; font-size: 14px; text-transform: capitalize; }.latest p { color: #60748f; line-height: 1.5; }.submit { width: calc(100% - 60px); margin: 0 30px 25px; border: 0; border-radius: 20px; padding: 21px; background: #e92024; color: white; font-size: 22px; font-weight: 800; box-shadow: 0 10px 20px #e9202433; }.submit span { float: right; font-size: 30px; }.submit:disabled { opacity: .7; }
.attachment-preview { display: block; overflow: hidden; margin-top: 18px; border-radius: 16px; }
.attachment-preview img { display: block; width: 100%; max-height: 260px; object-fit: cover; }
.bottom-nav { position: fixed; z-index: 20; right: 0; bottom: 0; left: 0; display: grid; grid-template-columns: repeat(5, 1fr); width: min(100%, 580px); height: 79px; margin: auto; border-top: 1px solid #dce6ef; background: white; }.bottom-nav a { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; color: #91a3bd; font-size: 14px; text-decoration: none; }.bottom-nav span { height: 25px; font-size: 27px; line-height: 22px; }.bottom-nav a.active { color: #009b70; font-weight: 700; }
@media (max-width: 500px) { .content { padding: 18px; }.card { padding: 22px 18px; border-radius: 22px; }.page-header { height: 96px; padding: 0 20px; }.page-header h1 { font-size: 21px; }.page-header > a:first-child { font-size: 44px; }.reporter p, .form-card select, .form-card textarea { font-size: 16px; }.reporter p span { font-size: 13px; }.form-card label { font-size: 16px; }.submit { width: calc(100% - 36px); margin-right: 18px; margin-left: 18px; font-size: 17px; } }
</style>