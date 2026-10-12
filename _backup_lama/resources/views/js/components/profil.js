import { computed, reactive, ref } from 'vue';

export function useProfil(props) {
    const profile = computed(() => props.profilePage ?? {});
    const form = reactive({
        full_name: profile.value.name ?? '',
        email: profile.value.email ?? '',
        address: profile.value.address ?? '',
        phone: profile.value.phone ?? '',
    });
    const password = reactive({
        current_password: '',
        password: '',
        password_confirmation: '',
    });
    const profileLoading = ref(false);
    const passwordLoading = ref(false);
    const message = ref('');

    async function send(url, payload, loading) {
        loading.value = true;
        message.value = '';
        try {
            await window.axios.post(
                url,
                { ...payload, _method: 'PUT' },
                {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                },
            );
            message.value = 'Perubahan berhasil disimpan.';
        } catch (error) {
            message.value = error.response?.data?.message ?? 'Perubahan gagal disimpan.';
        } finally {
            loading.value = false;
        }
    }

    return { form, password, profileLoading, passwordLoading, message, send };
}
