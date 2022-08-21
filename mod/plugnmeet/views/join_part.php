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

<div class="mb-6 row">
    <div class="join mr-6 col">
        <button onclick="join(event)" id="joinBtn"
                class='btn btn-success btn-lg'><?php echo get_string('join', 'plugnmeet'); ?>
        </button>
    </div>
    <?php if ($canedit): ?>
        <div class="end col" style="display: none">
            <button onclick="endRoom(event)" id="endBtn"
                    class='btn btn-danger btn-lg'><?php echo get_string('end', 'plugnmeet'); ?></button>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    let isActiveRoom = false;
    const isAdmin = <?php echo $isadmin; ?>;
    const moodleRoot = '<?php echo $CFG->wwwroot; ?>';

    window.addEventListener('load', () => {
        checkIfRoomActive(false);
        setInterval(() => {
            checkIfRoomActive(false);
        }, 1000 * 60 * 5);
    });

    function checkIfRoomActive(join = false) {
        require(['core/ajax'], function (ajax) {
            ajax.call([
                {
                    methodname: 'mod_plugnmeet_isactive_room',
                    args: {
                        instanceId: <?php echo $cm->instance; ?>,
                        room_id: '<?php echo $moduleinstance->roomid; ?>',
                    },
                    done: (res) => {
                        if (join) {
                            if (!res.isActive) {
                                create_and_join_conference();
                            } else {
                                join_conference();
                            }
                        } else {
                            isActiveRoom = res.isActive;
                            toggleEndBtn();
                        }
                    },
                    fail: (ex) => {
                        console.log(ex);
                    },
                },
            ]);
        });
    }

    function join(e) {
        e.preventDefault();
        checkIfRoomActive(true);
        btnToggle('joinBtn');
    }

    function endRoom() {
        if (!isAdmin) {
            return;
        }
        btnToggle('endBtn');

        require(['core/ajax'], function (ajax) {
            ajax.call([
                {
                    methodname: 'mod_plugnmeet_end_room',
                    args: {
                        instanceId: <?php echo $cm->instance; ?>,
                        room_id: '<?php echo $moduleinstance->roomid; ?>',
                    },
                    done: (res) => {
                        btnToggle('endBtn');
                        if (res.status) {
                            isActiveRoom = false;
                            toggleEndBtn();
                        } else {
                            console.log(res.msg);
                            alert(res.msg);
                        }
                    },
                    fail: (ex) => {
                        btnToggle('endBtn');
                        console.log(ex);
                    },
                },
            ]);
        });
    }

    function toggleEndBtn() {
        if (!isAdmin) {
            return;
        }
        if (isActiveRoom) {
            document.querySelector('.end').style.display = '';
        } else {
            document.querySelector('.end').style.display = 'none';
        }
    }

    function create_and_join_conference() {
        require(['core/ajax'], function (ajax) {
            ajax.call([
                {
                    methodname: 'mod_plugnmeet_create_room',
                    args: {
                        instanceId: <?php echo $cm->instance; ?>,
                        join: true,
                        isAdmin: <?php echo $isadmin; ?>,
                    },
                    done: (res) => {
                        btnToggle('joinBtn');
                        if (res.status) {
                            isActiveRoom = true;
                            toggleEndBtn();

                            const url = moodleRoot + '/mod/plugnmeet/conference.php?access_token=' + res.access_token +
                                '&id=<?php echo optional_param("id", 0, PARAM_INT); ?>';

                            const windowOpen = window.open(url, "_blank");
                            if (!windowOpen) {
                                setTimeout(() => {
                                    window.location.href = url
                                }, 2000);
                            }
                        } else {
                            alert(res.msg);
                        }
                    },
                    fail: (ex) => {
                        btnToggle('joinBtn');
                        console.log(ex);
                    },
                },
            ]);
        });
    }

    function join_conference() {
        require(['core/ajax'], function (ajax) {
            ajax.call([
                {
                    methodname: 'mod_plugnmeet_get_join_token',
                    args: {
                        instanceId: <?php echo $cm->instance; ?>,
                        isAdmin: <?php echo $isadmin; ?>,
                    },
                    done: (res) => {
                        btnToggle('joinBtn');
                        if (res.status) {
                            isActiveRoom = true;
                            toggleEndBtn();

                            const url = moodleRoot + '/mod/plugnmeet/conference.php?access_token=' +
                                res.access_token +
                                '&id=<?php echo optional_param("id", 0, PARAM_INT); ?>';

                            const windowOpen = window.open(url, "_blank");
                            if (!windowOpen) {
                                setTimeout(() => {
                                    window.location.href = url
                                }, 2000);
                            }
                        } else {
                            alert(res.msg);
                        }
                    },
                    fail: (ex) => {
                        btnToggle('joinBtn');
                        console.log(ex);
                    },
                },
            ]);
        });
    }

    function btnToggle(id) {
        document.getElementById(id).disabled
            ? document.getElementById(id).removeAttribute('disabled')
            : document.getElementById(id).setAttribute('disabled', 'disabled');
    }

</script>
