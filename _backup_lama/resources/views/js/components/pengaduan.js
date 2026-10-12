import { computed, reactive, ref } from 'vue';

export function usePengaduan(props) {
    const customer = computed(() => props.complaintPage?.customer ?? {});
    const latest = computed(() => props.complaintPage?.latest);
    const form = reactive({
        subject: 'Air Tidak Mengalir (Mati)',
        description: '',
        attachment: null,
    });
    const loading = ref(false);
    const error = ref('');

    async function submit(event) {
        loading.value = true;
        error.value = '';
        try {
            const payload = new FormData(event.currentTarget);
            await window.axios.post('/app/pengaduan', payload, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            window.location.reload();
        } catch (exception) {
            error.value = exception.response?.data?.message ?? 'Pengaduan gagal dikirim.';
        } finally {
            loading.value = false;
        }
    }

    function selectAttachment(event) {
        form.attachment = event.target.files[0] ?? null;
    }

    return { customer, latest, form, loading, error, submit, selectAttachment };
}