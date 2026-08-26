define(['core/chartjs'], function(Chart) {
    return {
        init: function(chartId, labels, grades) {
            const canvas = document.getElementById(chartId);

            if (!canvas) {
                return;
            }

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Grade (%)',
                        data: grades
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    };
});
