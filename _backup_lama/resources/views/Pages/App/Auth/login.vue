<template>
    <main class="login-page">
        <section class="login-card" aria-label="Form login pelanggan">
            <div class="brand"><span class="brand-mark">◆</span><span><small>PDAM</small><strong>TIRTA PINANG</strong></span></div>
            <h1>Selamat Datang</h1>
            <p class="subtitle">Masuk untuk melihat tagihan dan layanan air Anda.</p>
            <form @submit.prevent="submit" class="login-form">
                <label for="email">EMAIL PELANGGAN</label>
                <input id="email" v-model="form.email" type="email" autocomplete="username" placeholder="nama@email.com" required>
                <p v-if="errors.email" class="error">{{ errors.email }}</p>
                <div class="label-row"><label for="password">KATA SANDI</label><a href="#">Lupa kata sandi?</a></div>
                <div class="password-field">
                    <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" placeholder="Masukkan kata sandi" required>
                    <button type="button" @click="showPassword = !showPassword">{{ showPassword ? 'Sembunyikan' : 'Lihat' }}</button>
                </div>
                <label class="remember"><input v-model="form.remember" type="checkbox"><span>Ingat saya</span></label>
                <p v-if="errors.general" class="error general-error">{{ errors.general }}</p>
                <button class="submit-button" type="submit" :disabled="loading">{{ loading ? 'Memproses...' : 'Masuk ke Dashboard' }}</button>
            </form>
            <p class="admin-link">Petugas/admin? <a href="/login">Masuk melalui halaman admin</a></p>
        </section>
    </main>
</template>

<script setup>
import { reactive, ref } from 'vue';

const form = reactive({ email: '', password: '', remember: false });
const errors = reactive({ email: '', general: '' });
const loading = ref(false);
const showPassword = ref(false);

async function submit() {
    loading.value = true;
    errors.email = '';
    errors.general = '';

    try {
        await window.axios.post('/app/login', form, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        window.location.assign('/app/dashboard');
    } catch (error) {
        errors.email = error.response?.data?.errors?.email?.[0] ?? '';
        errors.general = errors.email ? '' : 'Email atau kata sandi pelanggan tidak valid.';
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
:global(*) { box-sizing: border-box; }
:global(body) { margin: 0; background: linear-gradient(145deg, #effbf8, #f5f8ff); color: #162b46; font-family: Inter, ui-sans-serif, system-ui, sans-serif; }
button, input { font: inherit; }
.login-page { min-height: 100vh; display: grid; place-items: center; padding: 24px; }
.login-card { width: min(100%, 440px); padding: 42px 38px; border: 1px solid #e2edf0; border-radius: 25px; background: white; box-shadow: 0 18px 45px #163b5414; }
.brand { display: flex; align-items: center; justify-content: center; gap: 11px; color: #08785b; margin-bottom: 34px; }.brand-mark { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 13px; background: #00a873; color: white; }.brand small, .brand strong { display: block; line-height: 1.1; }.brand small { color: #63738d; letter-spacing: 1px; font-size: 12px; }.brand strong { font-size: 18px; }
h1 { margin: 0; font-size: 29px; text-align: center; }.subtitle { margin: 9px 0 30px; color: #71839b; text-align: center; line-height: 1.5; }
.login-form { display: grid; gap: 9px; }.login-form label, .label-row label { color: #40536e; font-size: 12px; font-weight: 700; letter-spacing: .6px; }.login-form input:not([type="checkbox"]) { width: 100%; padding: 13px 14px; border: 1px solid #d8e3ed; border-radius: 11px; outline: none; color: #172c47; }.login-form input:not([type="checkbox"]):focus { border-color: #00ab79; box-shadow: 0 0 0 3px #00ab7920; }.error { margin: -3px 0 4px; color: #dc3154; font-size: 13px; }
.label-row { display: flex; justify-content: space-between; align-items: center; margin-top: 11px; }.label-row a, .admin-link a { color: #078b67; text-decoration: none; font-size: 13px; }.password-field { position: relative; }.password-field input { padding-right: 80px !important; }.password-field button { position: absolute; top: 50%; right: 10px; transform: translateY(-50%); border: 0; background: transparent; color: #078b67; font-size: 12px; cursor: pointer; }.remember { display: flex; align-items: center; gap: 8px; color: #60738f; font-size: 13px; margin: 7px 0 12px; }.remember input { accent-color: #00a873; }
.submit-button { border: 0; border-radius: 11px; padding: 14px; background: #00a873; color: white; font-weight: 700; cursor: pointer; box-shadow: 0 8px 15px #00a8732b; }.submit-button:disabled { opacity: .7; cursor: wait; }.general-error { text-align: center; }.admin-link { margin: 27px 0 0; color: #71839b; text-align: center; font-size: 13px; }
@media (max-width: 480px) { .login-card { padding: 32px 23px; } }
</style>