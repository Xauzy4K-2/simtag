<script setup>
import { useProfil } from '../../../js/components/profil.js';

const props = defineProps({ profilePage: { type: Object, default: () => ({}) } });
const { form, password, profileLoading, passwordLoading, message, send } = useProfil(props);
</script>


<template>
    <div class="profile-page">
        <header class="page-header"><a href="/app/dashboard">‹</a><h1>PENGATURAN AKUN</h1><a class="profile-icon" href="#profil">♧</a></header>
        <main class="content">
            <section class="avatar-section"><div class="avatar">♙</div><button type="button">◎ &nbsp; Ganti Desain Avatar</button></section>
            <section class="card"><h2 class="green-title">▌ DATA DIRI PELANGGAN</h2><form @submit.prevent="send('/app/profil', form, profileLoading)">
                <label>NAMA LENGKAP</label><input v-model="form.full_name" required>
                <label>EMAIL</label><input v-model="form.email" type="email" required>
                <label>ALAMAT</label><input v-model="form.address" placeholder="Masukkan alamat lengkap pelanggan">
                <label>NOMOR HANDPHONE</label><input v-model="form.phone" required>
                <button class="save green" :disabled="profileLoading">{{ profileLoading ? 'MENYIMPAN...' : 'SIMPAN PERUBAHAN PROFIL' }}</button>
            </form></section>
            <section class="card"><h2 class="blue-title">▌ UBAH PASSWORD</h2><form @submit.prevent="send('/app/profil/password', password, passwordLoading)">
                <label>PASSWORD LAMA</label><input v-model="password.current_password" type="password" required>
                <label>PASSWORD BARU</label><input v-model="password.password" type="password" minlength="8" placeholder="Masukkan password baru" required>
                <label>KONFIRMASI PASSWORD</label><input v-model="password.password_confirmation" type="password" required placeholder="Ulangi password baru">
                <button class="save blue" :disabled="passwordLoading">{{ passwordLoading ? 'MENYIMPAN...' : 'SIMPAN PASSWORD BARU' }}</button>
            </form></section>
            <p v-if="message" class="message">{{ message }}</p>
        </main>
        <nav class="bottom-nav"><a href="/app/dashboard"><span>⌂</span>Beranda</a><a href="/app/tagihan"><span>▤</span>Tagihan</a><a href="/app/pengaduan"><span>◌</span>Pengaduan</a><a class="active" href="/app/profil"><span>♙</span>Profil</a><a href="/app/riwayat"><span>•••</span>Riwayat</a></nav>
    </div>
</template>

<style scoped>
:global(*) { box-sizing: border-box; }:global(body) { margin: 0; background: #f7faff; color: #1d304b; font-family: Inter, ui-sans-serif, system-ui, sans-serif; }.profile-page { width: min(100%, 580px); min-height: 100vh; margin: auto; padding-bottom: 100px; background: #f7faff; }.page-header { height: 90px; display: flex; align-items: center; justify-content: space-between; padding: 0 34px; background: #fff; border-bottom: 1px solid #dce6ef; }.page-header h1 { margin: 0; font-size: 21px; letter-spacing: 1px; }.page-header > a:first-child { color: #40536f; font-size: 49px; line-height: 1; text-decoration: none; }.profile-icon { color: #00a873; font-size: 32px; text-decoration: none; }.content { padding: 25px 23px; }.avatar-section { display: grid; justify-items: center; gap: 16px; padding-bottom: 33px; }.avatar { display: grid; width: 138px; height: 138px; place-items: center; border: 5px solid #dff3ff; border-radius: 50%; background: #16a7df; color: white; font-size: 60px; }.avatar-section button { border: 1px solid #82efc9; border-radius: 22px; padding: 9px 18px; background: #f1fff9; color: #008b64; font-size: 16px; }.card { margin-bottom: 27px; padding: 24px; border: 1px solid #dce5ef; border-radius: 23px; background: white; box-shadow: 0 2px 5px #2947660c; }.card h2 { margin: 0 0 18px; padding-bottom: 16px; border-bottom: 1px solid #e5edf4; font-size: 18px; letter-spacing: .5px; }.green-title { color: #00966e; }.blue-title { color: #078bc6; }.card label { display: block; margin: 20px 0 9px; color: #60748f; font-size: 15px; font-weight: 700; letter-spacing: .5px; }.card input { width: 100%; border: 1px solid #d5e0eb; border-radius: 16px; padding: 15px 19px; background: #f6f9fc; color: #172a45; font-size: 17px; outline: none; }.card input:focus { border-color: #00a873; box-shadow: 0 0 0 3px #00a8731f; }.save { width: 100%; margin-top: 25px; border: 0; border-radius: 15px; padding: 16px; color: white; font-size: 16px; font-weight: 800; }.save.green { background: #00a873; }.save.blue { background: #078dca; }.save:disabled { opacity: .7; }.message { color: #008b64; text-align: center; }.bottom-nav { position: fixed; z-index: 20; right: 0; bottom: 0; left: 0; display: grid; grid-template-columns: repeat(5, 1fr); width: min(100%, 580px); height: 79px; margin: auto; border-top: 1px solid #dce6ef; background: white; }.bottom-nav a { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; color: #91a3bd; font-size: 14px; text-decoration: none; }.bottom-nav span { height: 25px; font-size: 27px; line-height: 22px; }.bottom-nav a.active { color: #009b70; font-weight: 700; }@media (max-width: 500px) { .content { padding: 22px 18px; }.page-header { padding: 0 22px; }.card { padding: 20px; }.avatar { width: 135px; height: 135px; } }
</style>