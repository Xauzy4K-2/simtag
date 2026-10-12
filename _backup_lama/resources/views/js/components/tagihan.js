import { computed } from 'vue';

export function useTagihan(props) {
    const bill = computed(() => props.billPage?.bill);
    const paid = computed(() => ['paid', 'success'].includes(bill.value?.status));
    const money = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value ?? 0);
    const date = (value) => value
        ? new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }).format(new Date(`${value}T00:00:00`))
        : 'Belum ditentukan';
    const copyReference = async () => {
        const reference = bill.value?.payment?.reference;
        if (reference && navigator.clipboard) {
            await navigator.clipboard.writeText(reference);
        }
    };

    return { bill, paid, money, date, copyReference };
}