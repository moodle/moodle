define(['core/chartjs'], function(Chart) {
    return {
        init: function(chartId, labels, grades) {
            const canvas = document.getElementById(chartId);

            if (!canvas) {
                return;
            }

            canvas.setAttribute(
                'aria-label',
                'Course grade progress chart'
            );

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
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    };
});
