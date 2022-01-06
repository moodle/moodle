/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Friederike Schwager (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

// R: The first parameter has to be Y, because it is a default YUI-object (demanded by moodle).
function setCharts(Y, names, otherquestions, myquestions, otheranswers, myanswers, otherprivate, myprivate, otherprotectedquestions, myprotectedquestions, otherprotectedanswers, myprotectedanswers) {
    require(['core/chartjs'], function (Chart) {
        // On small screens set width depending on number of annotators. Otherwise the diagram is very small.
        let width = Math.max(names.length * 25, 300);
        width = names.length * 40;
        if (window.innerWidth < width) {
            document.getElementById('chart-container').style.width = width + "px";
        }

        var maxValue = calculateMax(otherquestions, myquestions, otheranswers, myanswers, otherprivate, myprivate, otherprotectedquestions, myprotectedquestions, otherprotectedanswers, myprotectedanswers);

        var borderCol = 'rgb(250, 245, 235)';
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: names,
                datasets: [{
                        label: M.util.get_string('mypublicquestions', 'pdfannotator'),
                        stack: 'questions',
                        data: myquestions,
                        backgroundColor: 'rgb(0,84,159)',
                        borderColor: borderCol,
                        borderWidth: 1,
                    }, {
                        label: M.util.get_string('publicquestions', 'pdfannotator') + ' ' + M.util.get_string('by_other_users', 'pdfannotator'),
                        stack: 'questions',
                        data: otherquestions,
                        backgroundColor: 'rgb(142,186,229)',
                        borderColor: borderCol,
                        borderWidth: 1,
                    }, {
                        label: M.util.get_string('myprotectedquestions', 'pdfannotator'),
                        stack: 'questions',
                        data: myprotectedquestions,
                        backgroundColor: 'rgb(0, 152, 161)',
                        borderColor: borderCol,
                        borderWidth: 1,
                    }, {
                        label: M.util.get_string('protected_questions', 'pdfannotator') + ' ' + M.util.get_string('by_other_users', 'pdfannotator'),
                        stack: 'questions',
                        data: otherprotectedquestions,
                        backgroundColor: 'rgb(137, 204, 207)',
                        borderColor: borderCol,
                        borderWidth: 1,
                    },
                    {
                        label: M.util.get_string('mypublicanswers', 'pdfannotator'),
                        stack: 'answers',
                        data: myanswers,
                        backgroundColor: 'rgb(87, 171, 39)',
                        borderColor: 'rgb(0, 0, 0)',
                        borderWidth: 1,
                        borderColor: borderCol,
                        borderWidth: 1,
                    }, 
                    {
                        label: M.util.get_string('publicanswers', 'pdfannotator') + ' ' + M.util.get_string('by_other_users', 'pdfannotator'),
                        stack: 'answers',
                        data: otheranswers,
                        backgroundColor: 'rgb(184, 214, 152)',
                        borderColor: 'rgb(0, 0, 0)',
                        borderWidth: 1,
                        borderColor: borderCol,
                        borderWidth: 1,
                    }, 
                    {
                        label: M.util.get_string('myprotectedanswers', 'pdfannotator'),
                        stack: 'answers',
                        data: myprotectedanswers,
                        backgroundColor: 'rgb(189, 205, 0)',
                        borderColor: borderCol,
                        borderWidth: 1,
                    }, {
                        label: M.util.get_string('protected_answers', 'pdfannotator') + ' ' + M.util.get_string('by_other_users', 'pdfannotator'),
                        stack: 'answers',
                        data: otherprotectedanswers,
                        backgroundColor: 'rgb(224, 230, 154)',
                        borderColor: borderCol,
                        borderWidth: 1,
                    },
                    {
                        label: M.util.get_string('myprivate', 'pdfannotator'),
                        stack: 'private',
                        data: myprivate,
                        backgroundColor: 'rgb(246, 168, 0)',
                        borderColor: borderCol,
                        borderWidth: 1,
                    },
                    {
                        label: M.util.get_string('private_comments', 'pdfannotator') + ' ' + M.util.get_string('by_other_users', 'pdfannotator'),
                        stack: 'private',
                        data: otherprivate,
                        backgroundColor: 'rgb(253, 212, 143)',
                        borderColor: borderCol,
                        borderWidth: 1,
                    }]
            },
            options: {
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: M.util.get_string('chart_title', 'pdfannotator'),
                    fontSize: 20
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                scales: {
                    xAxes: [{
                            stacked: true,
                            ticks: {
                                autoSkip: false
                            }
                        }],
                    yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                                max: maxValue + 2
                            }
                        }]
                },
                tooltips: {
                    mode: 'x'
                },
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0
                    }
                }
            }
        });

    });
}

/**
 * Calculate the height of the diagramm in the statistic
 */
function calculateMax(otherquestions, myquestions, otheranswers, myanswers, otherprivate, myprivate, otherprotectedquestions, myprotectedquestions, otherprotectedanswers, myprotectedanswers) {
    let max = 0;
    for (let i = 0; i < otherquestions.length; ++i) {
        if (otherquestions[i] + myquestions[i] + otherprotectedquestions[i] + myprotectedquestions[i] > max) {
            max = otherquestions[i] + myquestions[i] + otherprotectedquestions[i] + myprotectedquestions[i];
        }
        if (otheranswers[i] + myanswers[i] + otherprotectedanswers[i] + myprotectedanswers[i] > max) {
            max = otheranswers[i] + myanswers[i] + otherprotectedanswers[i] + myprotectedanswers[i];
        }
        if (otherprivate[i] + myprivate[i] > max) {
            max = otherprivate[i] + myprivate[i];
        }
    } 

    return max;
}
