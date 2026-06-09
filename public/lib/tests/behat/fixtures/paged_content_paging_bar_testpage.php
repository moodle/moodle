<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Fixture page for paged content paging bar tests.
 *
 * @package   core
 * @copyright 2026 Catalyst IT Australia Pty Ltd
 * @author    Cameron Ball <cameronball@catalyst-au.net>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $PAGE, $OUTPUT;
$PAGE->set_url('/lib/tests/behat/fixtures/paged_content_paging_bar_testpage.php');
$PAGE->add_body_class('limitedwidth');
require_login();
$PAGE->set_context(core\context\system::instance());
$PAGE->set_title('Paged content paging bar fixture page');

echo $OUTPUT->header();
echo html_writer::tag('h2', 'Paged content paging bar fixture');
echo html_writer::div('', '', ['id' => 'paged-content-paging-bar-fixture']);

$inlinejs = <<<'EOF'
require(
    ['core/paged_content', 'core/templates', 'core/notification'],
    (PagedContent, Templates, Notification) => {
        const root = document.getElementById('paged-content-paging-bar-fixture');
        const pages = [1, 2, 3].map((pageNumber) => {
            const firstItem = ((pageNumber - 1) * 10) + 1;
            const items = Array.from({length: 10}, (_, index) => `Item ${firstItem + index}`);

            return {
                active: pageNumber === 1,
                number: pageNumber,
                page: pageNumber,
                content: `<div class="paged-items">${items.join(', ')}</div>`,
            };
        });

        Templates.render('core/paged_content', {
            pagingbar: {
                showitemsperpageselector: false,
                itemsperpage: [{value: 10, active: true}],
                previous: true,
                first: true,
                next: true,
                last: true,
                activepagenumber: 1,
                hidecontrolonsinglepage: true,
                pages,
                barsize: 10,
            },
            pages,
            skipjs: true,
            ignorecontrolwhileloading: true,
            controlplacementbottom: false,
        }).then((html, js) => {
            Templates.replaceNodeContents(root, html, js);
            PagedContent.init(root.querySelector('[data-region="paged-content-container"]'));
            return;
        }).fail(Notification.exception);
    }
);
EOF;

$PAGE->requires->js_amd_inline($inlinejs);

echo $OUTPUT->footer();
