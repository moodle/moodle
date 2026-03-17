define(['https://cdn.jsdelivr.net/npm/chart.js'], function() {

    return {
        init: function() {

            const ctx = document.getElementById('wellbeingChart');
            if (!ctx) return;

            const labels = JSON.parse(document.querySelector('script[type="application/json"]#labels')?.textContent || '[]');
            const scores = JSON.parse(document.querySelector('script[type="application/json"]#scores')?.textContent || '[]');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Wellbeing Score',
                        data: scores,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            min: 0,
                            max: 100
                        }
                    }
                }
            });
        }
    };
});