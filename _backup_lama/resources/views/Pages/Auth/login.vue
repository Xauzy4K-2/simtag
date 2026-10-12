<template>
    <main class="login-page">
        <section class="login-card" aria-label="Form login admin">
            <div class="brand-wrap">
                <div class="brand-icon" aria-hidden="true">
                    <svg viewBox="0 0 32 32" role="img">
                        <path d="M16 5c-4.4 0-8 3.4-8 7.8C8 19.1 16 27 16 27s8-7.9 8-14.2C24 8.4 20.4 5 16 5zm0 10.1a3.3 3.3 0 1 1 0-6.7 3.3 3.3 0 0 1 0 6.7z" fill="currentColor"/>
                    </svg>
                </div>
            </div>

            <h1>PERUMDAM TIRTA PINANG</h1>
            <p class="subtitle-top">KOTA PANGKALPINANG</p>
            <p class="meta-text">Pusat Kendali Administrasi &amp; Sistem Distribusi Air Bersih</p>

            <form @submit.prevent="submit" class="login-form">
                <label for="email">EMAIL OTORITAS / NIP ADMIN</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9zm2.2-.5 6.8 5.2 6.8-5.2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <input id="email" v-model="form.email" type="email" autocomplete="username" placeholder="admin@tirtapinang.co.id" required>
                </div>
                <p v-if="errors.email" class="error">{{ errors.email }}</p>

                <div class="field-head">
                    <label for="password">KATA SANDI ADMIN</label>
                </div>
                <div class="input-wrap password-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M7 10V8a5 5 0 1 1 10 0v2M6 10h12a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required>
                    <button type="button" class="eye-button" aria-label="Tampilkan kata sandi" @click="showPassword = !showPassword">
                        <svg v-if="!showPassword" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg v-else viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 3l18 18M10.6 10.6A3 3 0 0 0 13.4 13.4M9.1 5.5A10.9 10.9 0 0 1 12 5c6.5 0 10 7 10 7a16.6 16.6 0 0 1-5 6.1M6.2 6.2A16.7 16.7 0 0 0 2 12s3.5 7 10 7a10.7 10.7 0 0 0 5.1-1.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <div class="form-options">
                    <label class="remember-check">
                        <input v-model="form.remember" type="checkbox">
                        <span>Ingat sesi saya (30 hari)</span>
                    </label>
                    <a href="#">Lupa kata sandi?</a>
                </div>

                <p v-if="errors.general" class="error general-error">{{ errors.general }}</p>
                <button class="submit-button" type="submit" :disabled="loading">
                    <span class="button-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M5 12h11m0 0-4-4m4 4-4 4M5 5.5v13" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    {{ loading ? 'Memverifikasi...' : 'Masuk ke Dashboard Admin' }}
                </button>
            </form>
        </section>

        <footer class="page-footer">
            <span>© 2025 Perumdam Tirta Pinang Kota Pangkalpinang. Hak Cipta Dilindungi.</span>
        </footer>
    </main>
</template>

<script setup>
import { reactive, ref } from 'vue';

const form = reactive({ email: 'admin@tirtapinang.co.id', password: '', remember: false });
const errors = reactive({ email: '', general: '' });
const loading = ref(false);
const showPassword = ref(false);

async function submit() {
    loading.value = true;
    errors.email = '';
    errors.general = '';

    try {
        await window.axios.post('/login', form, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });
        window.location.assign('/summary');
    } catch (error) {
        errors.email = error.response?.data?.errors?.email?.[0] ?? '';
        errors.general = errors.email ? '' : 'Email atau kata sandi tidak valid.';
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:global(*) { box-sizing: border-box; }
:global(body) {
    margin: 0;
    background: #f2f3f5;
    font-family: 'Inter', sans-serif;
    color: #102538;
}

button, input {
    font: inherit;
}

button {
    cursor: pointer;
}

.login-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 32px 20px 18px;
    background: #f3f4f4;
    position: relative;
}

.page-nav {
    width: min(100%, 440px);
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
}

.nav-button,
.nav-toggle {
    border: 0;
    background: transparent;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #1a2d2c;
}

.nav-button {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    opacity: 0.8;
}

.nav-button svg {
    width: 18px;
    height: 18px;
}

.nav-toggle {
    width: 34px;
    height: 22px;
    border-radius: 999px;
    border: 1px solid rgba(16, 66, 61, 0.2);
    background: rgba(255, 255, 255, 0.3);
    padding: 3px;
    justify-content: flex-start;
}

.toggle-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: block;
    background: #13bda1;
    box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.08);
}

.login-card {
    width: min(100%, 440px);
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid rgba(0, 0, 0, 0.04);
    border-radius: 18px;
    box-shadow: 0 8px 30px rgba(15, 28, 33, 0.04);
    padding: 22px 28px 20px;
}

.brand-wrap {
    display: flex;
    justify-content: center;
    margin: 4px 0 16px;
}

.brand-icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #0b4743, #0a7f6d);
    color: #effff9;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
}

.brand-icon svg {
    width: 24px;
    height: 24px;
}

h1 {
    margin: 0;
    text-align: center;
    font-size: 1.05rem;
    line-height: 1.25;
    letter-spacing: 0.02em;
    color: #1a2d2d;
}

.subtitle-top {
    margin: 8px 0 4px;
    text-align: center;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: #1f3d3b;
}

.meta-text {
    margin: 0 0 22px;
    text-align: center;
    font-size: 0.74rem;
    color: #687686;
    line-height: 1.5;
}

.login-form {
    width: 100%;
}

.login-form label {
    display: block;
    margin: 16px 0 8px;
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: #1a2c37;
}

.field-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 18px;
}

.field-head label {
    margin: 0;
}

.field-badge {
    font-size: 0.59rem;
    color: #5d6f72;
    white-space: nowrap;
}

.input-wrap {
    position: relative;
    width: 100%;
}

.input-wrap input {
    width: 100%;
    border: 1px solid #d5dde3;
    border-radius: 10px;
    background: rgba(249, 250, 251, 0.7);
    padding: 12px 14px 12px 42px;
    font-size: 0.82rem;
    color: #142738;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.password-wrap input {
    padding-right: 40px;
}

.input-wrap input:focus {
    border-color: rgba(14, 136, 115, 0.6);
    box-shadow: 0 0 0 3px rgba(19, 189, 161, 0.08);
}

.input-wrap input::placeholder {
    color: #939fae;
}

.input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    color: #7e8d9d;
}

.input-icon svg,
.eye-button svg,
.button-icon svg {
    width: 16px;
    height: 16px;
}

.eye-button {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    border: 0;
    background: transparent;
    color: #8b9aa7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.form-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 14px 0 20px;
    min-height: 20px;
}

.remember-check {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 0.7rem;
    font-weight: 400;
    letter-spacing: 0;
    color: #586b7d;
}

.remember-check input {
    accent-color: #0d8a72;
    width: 14px;
    height: 14px;
    margin: 0;
}

.form-options a {
    color: #0b8a71;
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 600;
}

.error {
    margin: 8px 0 0;
    color: #d63a3a;
    font-size: 0.65rem;
    line-height: 1.4;
}

.general-error {
    text-align: center;
    margin: 0 0 14px;
}

.submit-button {
    width: 100%;
    border: 0;
    border-radius: 10px;
    background: linear-gradient(135deg, #0a7f6d 0%, #0f937d 100%);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 46px;
    padding: 12px 16px;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    box-shadow: 0 8px 22px rgba(9, 126, 104, 0.2);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.submit-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(9, 126, 104, 0.26);
}

.submit-button:disabled {
    opacity: 0.75;
    cursor: wait;
}

.button-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.page-footer {
    width: min(100%, 500px);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 8px 10px;
    margin-top: 24px;
    color: #6f7a84;
    font-size: 0.62rem;
    text-align: center;
    line-height: 1.5;
}

.footer-mark {
    display: inline-block;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #0e9f7d;
    color: white;
    font-size: 0.72rem;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.footer-line {
    width: 1px;
    height: 12px;
    background: rgba(118, 129, 140, 0.5);
}

@media (max-width: 480px) {
    .login-page {
        padding-top: 24px;
    }

    .page-nav {
        width: min(100%, 360px);
    }

    .login-card {
        padding: 20px 18px 18px;
        border-radius: 16px;
    }

    .meta-text {
        font-size: 0.69rem;
    }

    .form-options {
        flex-direction: column;
        align-items: flex-start;
    }

    .submit-button {
        font-size: 0.8rem;
    }
}
</style>
