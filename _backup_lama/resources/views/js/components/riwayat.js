import { computed, ref } from 'vue';

export function useRiwayat(props) {
    const selected = ref('today');
    const activities = computed(() => props.historyPage?.activities ?? []);
    const filteredActivities = computed(() => activities.value.filter((activity) => {
        const now = new Date();
        const date = new Date(activity.date);
        if (selected.value === 'all') return true;
        if (selected.value === 'today') return date.toDateString() === now.toDateString();
        if (selected.value === 'week') return (now - date) <= 7 * 86400000;
        return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
    }));
    const money = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value ?? 0);
    const statusLabel = (status) => ({
        success: 'Berhasil',
        paid: 'Lunas',
        pending: 'Menunggu',
        open: 'Diproses',
        in_progress: 'Diproses',
        resolved: 'Selesai',
        failed: 'Gagal',
    }[status] ?? status);

    return { selected, filteredActivities, money, statusLabel };
}