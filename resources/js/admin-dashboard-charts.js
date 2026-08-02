import {
    Chart,
    ArcElement,
    BarElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    DoughnutController,
    BarController,
    LineController,
    Filler,
    Tooltip,
    Legend,
} from 'chart.js';

Chart.register(
    ArcElement,
    BarElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    DoughnutController,
    BarController,
    LineController,
    Filler,
    Tooltip,
    Legend
);

const PALETTE = ['#232168', '#3b3990', '#d9a441', '#c6871f', '#22c55e'];

const dataEl = document.getElementById('admin-dashboard-chart-data');
if (dataEl) {
    const data = JSON.parse(dataEl.textContent);

    const trendCanvas = document.getElementById('trend-chart');
    if (trendCanvas) {
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: data.trend.map((t) => t.label),
                datasets: [{
                    data: data.trend.map((t) => t.approved_count),
                    borderColor: '#232168',
                    backgroundColor: 'rgba(35, 33, 104, 0.08)',
                    pointBackgroundColor: '#d9a441',
                    pointBorderColor: '#d9a441',
                    pointRadius: 4,
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                    },
                },
            },
        });
    }

    const coverageCanvas = document.getElementById('coverage-chart');
    if (coverageCanvas && data.coverage.total_students > 0) {
        new Chart(coverageCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Berprestasi', 'Belum Berprestasi'],
                datasets: [{
                    data: [data.coverage.with_approved, data.coverage.without_approved],
                    backgroundColor: ['#232168', '#d9a441'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' },
                },
            },
        });
    }

    const categoryCanvas = document.getElementById('category-chart');
    if (categoryCanvas) {
        new Chart(categoryCanvas, {
            type: 'bar',
            data: {
                labels: data.categories.map((c) => c.label),
                datasets: [{
                    data: data.categories.map((c) => c.count),
                    backgroundColor: data.categories.map((_, i) => PALETTE[i % PALETTE.length]),
                    borderRadius: 6,
                    maxBarThickness: 48,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                    },
                },
            },
        });
    }

    const levelCanvas = document.getElementById('level-chart');
    if (levelCanvas) {
        new Chart(levelCanvas, {
            type: 'bar',
            data: {
                labels: data.levels.map((l) => l.label),
                datasets: [{
                    data: data.levels.map((l) => l.count),
                    backgroundColor: data.levels.map((_, i) => PALETTE[i % PALETTE.length]),
                    borderRadius: 6,
                    maxBarThickness: 40,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                    },
                },
            },
        });
    }
}
