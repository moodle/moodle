<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Test all supported Chart.js charts.
 *
 * @package    core
 * @copyright  2016 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url('/lib/tests/other/chartjstestpage.php');
$PAGE->set_heading('Chart.js library test');
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();

$sales = new \core\chart_series('Sales', [1000, 1170, 660, 1030]);
$expenses = new \core\chart_series('Expenses', [400, 460, 1120, 540]);
$labels = ['2004', '2005', '2006', '2007'];

$chart = new \core\chart_pie();
$chart->set_title('PIE CHART');
$chart->add_series($sales);
$chart->set_labels($labels);

$chart2 = new \core\chart_pie();
$chart2->set_title('DOUGHNUT CHART');
$chart2->set_doughnut(true);
$chart2->add_series($sales);
$chart2->set_labels($labels);

$chart3 = new \core\chart_line();
$chart3->set_title('TENSIONED LINES CHART');
$chart3->add_series($sales);
$chart3->add_series($expenses);
$chart3->set_labels($labels);

$chart4 = new \core\chart_line();
$chart4->set_smooth(true);
$chart4->set_title('SMOOTH LINES CHART');
$chart4->add_series($sales);
$chart4->add_series($expenses);
$chart4->set_labels($labels);

$chart5 = new \core\chart_bar();
$chart5->set_title('BAR CHART');
$chart5->add_series($sales);
$chart5->add_series($expenses);
$chart5->set_labels($labels);

$chart6 = new \core\chart_bar();
$chart6->set_title('HORIZONTAL BAR CHART');
$chart6->set_horizontal(true);
$chart6->add_series($sales);
$chart6->add_series($expenses);
$chart6->set_labels($labels);

$chart7 = new \core\chart_bar();
$chart7->set_title('STACKED BAR CHART');
$chart7->set_stacked(true);
$chart7->add_series($sales);
$chart7->add_series($expenses);
$chart7->set_labels($labels);

$chart8 = new \core\chart_bar();
$chart8->set_title('BAR CHART COMBINED WITH LINE CHART');
$expensesline = new \core\chart_series('Expenses', [400, 460, 1120, 540]);
$expensesline->set_type(\core\chart_series::TYPE_LINE);
$chart8->add_series($expensesline);
$chart8->add_series($sales);
$chart8->set_labels($labels);

$hills = new \core\chart_series('Hills', [700, 870, 660, 950]);
$mountain = new \core\chart_series('Mountain', [400, 460, 1350, 540]);
$sky = new \core\chart_series('Sky', [1400, 1500, 1550, 1500]);
$chart9 = new \core\chart_line();
$chart9->set_title('AREA FILL CHART');
$chart9->add_series($hills);
$chart9->add_series($mountain);
$chart9->add_series($sky);
$chart9->set_labels($labels);
$hills->set_smooth(true);
$hills->set_fill('origin');
$mountain->set_fill('-1');
$sky->set_fill('end');

$chart10 = new \core\chart_bar();
$chart10->set_title('BAR CHART WITH LEGEND OPTIONS (LEGEND POSITION IN THE LEFT)');
$expensesline = new \core\chart_series('Expenses', [400, 460, 1120, 540]);
$expensesline->set_type(\core\chart_series::TYPE_LINE);
$chart10->add_series($expensesline);
$chart10->set_legend_options(['position' => 'left', 'reverse' => true]);
$chart10->add_series($sales);
$chart10->set_labels($labels);

$chart11 = new \core\chart_bar();
$chart11->set_title('BAR CHART WITH LEGEND OPTIONS (LEGEND HIDDEN)');
$expensesline = new \core\chart_series('Expenses', [400, 460, 1120, 540]);
$expensesline->set_type(\core\chart_series::TYPE_LINE);
$chart11->add_series($expensesline);
$chart11->set_legend_options(['display' => false]);
$chart11->add_series($sales);
$chart11->set_labels($labels);

echo $OUTPUT->render($chart);
echo $OUTPUT->render($chart2);
echo $OUTPUT->render($chart3);
echo $OUTPUT->render($chart4);
echo $OUTPUT->render($chart5);
echo $OUTPUT->render($chart6);
echo $OUTPUT->render($chart7);
echo $OUTPUT->render($chart8);
echo $OUTPUT->render($chart9);
echo $OUTPUT->render($chart10);
echo $OUTPUT->render($chart11);

echo $OUTPUT->footer();
