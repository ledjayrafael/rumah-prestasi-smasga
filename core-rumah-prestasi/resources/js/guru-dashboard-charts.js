import {
    Chart,
    ArcElement,
    BarElement,
    CategoryScale,
    LinearScale,
    DoughnutController,
    BarController,
    Tooltip,
    Legend,
} from 'chart.js';

Chart.register(
    ArcElement,
    BarElement,
    CategoryScale,
    LinearScale,
    DoughnutController,
    BarController,
    Tooltip,
    Legend
);

const dataEl = document.getElementById('guru-dashboard-chart-data');
if (dataEl) {
    const data = JSON.parse(dataEl.textContent);

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
                    backgroundColor: ['#232168', '#d9a441', '#22c55e'],
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
}
