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
 * Part of mod_plugnmeet.
 *
 * @package     mod_plugnmeet
 * @copyright   2022 mynaparrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
?>

<div class="recordings mb-6">
    <h3 class="mb-3"><?php echo get_string('recordings', 'plugnmeet'); ?></h3>

    <table class="table table-striped" style="min-width: 600px">
        <thead>
        <tr>
            <th scope="col"><?php echo get_string('recording_date', 'plugnmeet'); ?></th>
            <th scope="col"><?php echo get_string('meeting_date', 'plugnmeet'); ?></th>
            <th scope="col"><?php echo get_string('file_size', 'plugnmeet'); ?></th>
        </tr>
        </thead>
        <tbody id="recordingListsBody"></tbody>
        <tfoot id="recordingListsFooter" style="display: none"></tfoot>
    </table>
</div>

<script type="text/javascript">
    const canEdit = <?php echo $canedit; ?>;
    let isShowingPagination = false;
    let roomId = '<?php echo $moduleinstance->roomid; ?>',
        totalRecordings = 0,
        currentPage = 1,
        limitPerPage = 20;

    window.addEventListener('load', () => {
        const data = {
            instanceId: <?php echo $cm->instance; ?>,
            from: 0,
            limit: limitPerPage,
            order_by: 'DESC',
            room_id: roomId,
        };
        fetchRecordings(data);
    });

    function fetchRecordings(data) {
        require(['core/ajax'], function (ajax) {
            ajax.call([
                {
                    methodname: 'mod_plugnmeet_get_recordings',
                    args: data,
                    done: (res) => {
                        if (!res.status) {
                            showMessage(res.msg);
                            return;
                        }
                        if (!res.result.total_recordings) {
                            showMessage('no recordings');
                            return;
                        }

                        const recordings = JSON.parse(res.result.recordings_list);
                        if (
                            res.result.total_recordings > recordings.length &&
                            !isShowingPagination
                        ) {
                            totalRecordings = res.result.total_recordings;
                            showPagination();
                            isShowingPagination = true;
                        }

                        let html = '';
                        for (let i = 0; i < recordings.length; i++) {
                            const recording = recordings[i];
                            html += '<tr>';
                            html +=
                                '<td class="center">' +
                                new Date(recording.creation_time * 1e3).toLocaleString() +
                                '</td>';
                            html +=
                                '<td class="center">' +
                                new Date(recording.room_creation_time * 1e3).toLocaleString() +
                                '</td>';
                            html += '<td class="center">' + recording.file_size + '</td>';

                            html += '<td class="center"><button onclick="downloadRecording(event)" class="btn btn-success btn-sm downloadRecording" id="' +
                                recording.record_id +
                                '"><?php echo get_string('download', 'plugnmeet'); ?></button></td>';

                            if (canEdit) {
                                html += '<td class="center"><button onclick="deleteRecording(event)" class="btn btn-danger btn-sm deleteRecording" id="' +
                                    recording.record_id +
                                    '"><?php echo get_string('delete', 'plugnmeet'); ?></button></td>';
                            }

                            html += '</tr>';
                        }

                        document.getElementById('recordingListsBody').innerHTML = html;
                    },
                    fail: (ex) => {
                        console.log(ex);
                    },
                },
            ]);
        });
    }

    function showPagination() {
        currentPage = 1;
        document.getElementById('recordingListsFooter').style.display = '';

        html =
            '<nav role="navigation" aria-label="Pagination"><ul class="pagination justify-content-end mt-2">';

        html += '<li class="page-item mr-4"><span>';
        html += '<button id="backward" class="btn btn-success btn-sm" aria-hidden="true" disabled>&laquo;</button>';
        html += '</span></li>';

        html += '<li class="page-item"><span>';
        html += '<button id="forward" class="btn btn-success btn-sm" aria-hidden="true">&raquo;</button>';
        html += '</span></li>';

        html += '</ul></nav>';

        document.getElementById('recordingListsFooter').innerHTML =
            '<tr><td colspan="5"> ' + html + ' </td></tr>';
    }

    let showPre = false,
        showNext = true;
    document.addEventListener('click', function (e) {
        if (e.target.id === 'backward') {
            e.preventDefault();
            if (!showPre) {
                return;
            }
            currentPage--;
            paginate(currentPage);
        } else if (e.target.id === 'forward') {
            e.preventDefault();
            if (!showNext) {
                return;
            }
            currentPage++;
            paginate(currentPage);
        }
    });

    function paginate(currentPage) {
        document.getElementById('recordingListsBody').innerHTML = '';
        const from = (currentPage - 1) * limitPerPage;

        if (currentPage === 1) {
            showPre = false;
            document.getElementById('backward').setAttribute('disabled', 'disabled');
        } else {
            showPre = true;
            document.getElementById('backward').removeAttribute('disabled');
        }

        if (currentPage >= totalRecordings / limitPerPage) {
            showNext = false;
            document.getElementById('forward').setAttribute('disabled', 'disabled');
        } else {
            showNext = true;
            document.getElementById('forward').removeAttribute('disabled');
        }

        const data = {
            from,
            limit: limitPerPage,
            order_by: 'DESC',
            room_id: roomId,
        };
        fetchRecordings(data);
    }

    function showMessage(msg) {
        document.getElementById('recordingListsBody').innerHTML =
            '<tr>' +
            '<td ' +
            'colspan="6" ' +
            'class="center">' +
            msg +
            '</td>' +
            '</tr>';
    }

    function downloadRecording(e) {
        e.preventDefault();
        const recordId = e.target.attributes.getNamedItem('id').value;

        require(['core/ajax'], function (ajax) {
            ajax.call([
                {
                    methodname: 'mod_plugnmeet_get_recording_download_link',
                    args: {
                        instanceId: <?php echo $cm->instance; ?>,
                        recordId,
                    },
                    done: (res) => {
                        if (res.status) {
                            window.open(res.url, '_blank');
                        } else {
                            alert(res.msg);
                        }
                    },
                    fail: (ex) => {
                        console.log(ex);
                    },
                },
            ]);
        });
    }

    function deleteRecording(e) {
        e.preventDefault();
        const recordId = e.target.attributes.getNamedItem('id').value;
        if (
            confirm("<?php echo get_string('sure_to_delete', 'plugnmeet'); ?>") !== true
        ) {
            return;
        }

        require(['core/ajax'], function (ajax) {
            ajax.call([
                {
                    methodname: 'mod_plugnmeet_delete_recording',
                    args: {
                        instanceId: <?php echo $cm->instance; ?>,
                        recordId,
                    },
                    done: (res) => {
                        if (res.status) {
                            document
                                .getElementById(recordId)
                                .parentElement.parentElement.remove();
                        } else {
                            alert(res.msg);
                        }
                    },
                    fail: (ex) => {
                        console.log(ex);
                    },
                },
            ]);
        });
    }
</script>
