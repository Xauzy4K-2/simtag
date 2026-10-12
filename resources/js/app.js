import '@fortawesome/fontawesome-free/css/all.min.css';
import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import {
    Chart,
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Filler,
    Legend,
    LinearScale,
    LineController,
    LineElement,
    PointElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    ArcElement, BarController, BarElement, CategoryScale, DoughnutController, Filler,
    Legend, LinearScale, LineController, LineElement, PointElement, Tooltip,
);
Chart.defaults.font.family = "'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif";
Chart.defaults.color = '#64748b';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.maintainAspectRatio = false;
if (window.matchMedia?.('(prefers-reduced-motion: reduce)').matches) Chart.defaults.animation = false;
window.Chart = Chart;

/**
 * Grafik deklaratif: <canvas data-chart='{"type":"bar", ...}'></canvas>
 */
function initCharts(root = document) {
    root.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        if (canvas._chart) return;
        try {
            const config = JSON.parse(canvas.dataset.chart);
            canvas._chart = new Chart(canvas, config);
        } catch (e) {
            console.error('Konfigurasi grafik tidak valid', e);
        }
    });
}

/** Konfirmasi sebelum submit: <form data-confirm="Yakin?"> */
document.addEventListener('submit', (event) => {
    const form = event.target;
    const message = form.dataset?.confirm;
    if (message && !window.confirm(message)) {
        event.preventDefault();
        return;
    }
    // Cegah submit ganda dan beri indikator proses
    const button = form.querySelector('button[type="submit"][data-loading]');
    if (button && !event.defaultPrevented) {
        setTimeout(() => {
            button.disabled = true;
            button.dataset.original = button.innerHTML;
            button.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> ${button.dataset.loading || 'Memproses...'}`;
        }, 0);
    }
});

// Kembalikan tombol bila halaman dipulihkan dari cache (tombol back browser)
window.addEventListener('pageshow', () => {
    document.querySelectorAll('button[data-original]').forEach((button) => {
        button.disabled = false;
        button.innerHTML = button.dataset.original;
        delete button.dataset.original;
    });
});

window.Alpine = Alpine;
Alpine.plugin(collapse);

/** Komponen form survei pelanggan: progres pengisian & validasi ringan. */
Alpine.data('surveyForm', (required = [], total = 0) => ({
    answers: {},
    tried: false,
    required,
    total,
    get answered() {
        return Object.values(this.answers).filter((v) => v !== null && v !== '').length;
    },
    get progress() {
        return this.total ? Math.round((this.answered / this.total) * 100) : 0;
    },
    get missing() {
        return this.required.filter((id) => !this.answers[id]);
    },
    set(id, value) {
        this.answers[id] = value;
    },
    submit(event) {
        this.tried = true;
        if (this.missing.length) {
            event.preventDefault();
            const el = document.getElementById(`question-${this.missing[0]}`);
            el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el?.classList.add('ring-2', 'ring-rose-300');
            setTimeout(() => el?.classList.remove('ring-2', 'ring-rose-300'), 2200);
        }
    },
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => initCharts());
