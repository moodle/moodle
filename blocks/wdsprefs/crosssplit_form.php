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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www . gnu . org/copyleft/gpl . html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");

class crosssplit_form extends moodleform {

    public function definition() {
        global $CFG, $PAGE, $OUTPUT;

        // Add the step parameter.
        $this->_form->addElement('hidden', 'step', 'assign');
        $this->_form->setType('step', PARAM_TEXT);

        $mform = $this->_form;

        // Get the secitons.
        $sectiondata = $this->_customdata['sectiondata'] ?? [];

        // Count the sections.
        $sectioncount = count($sectiondata);

        // Get the shell count.
        $shellcount = $this->_customdata['shellcount'] ?? 2;

        // The numbers for our strings.
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $secword = $formatter->format($sectioncount);
        $shellword = $formatter->format($shellcount);

        // Get the period.
        $period = $this->_customdata['period'] ?? [];

        // Get the teacher.
        $teacher = $this->_customdata['teacher'] ?? 2;

        // If the user is doing something silly, send them back.
        if ($shellcount > $sectioncount) {
            redirect(
                $CFG->wwwroot . '/blocks/wdsprefs/crosssplit.php',
                get_string('wdsprefs:toomanyshells',
                    'block_wdsprefs', [
                        'shell' => $shellcount,
                        'sec' => $sectioncount,
                        'shellword' => $shellword,
                        'secword' => $secword,
                    ]
                ),
                null,
                core\output\notification::NOTIFY_WARNING
            );
        }

        if ($shellcount == 1 && $sectioncount == 1) {
            redirect(
                $CFG->wwwroot . '/blocks/wdsprefs/crosssplit.php',
                get_string('wdsprefs:onetoone', 'block_wdsprefs'),
                null,
                core\output\notification::NOTIFY_WARNING
            );
        }


        // Instructions.
        $mform->addElement('html',
            '<div class="alert alert-info"><p>' .
            get_string('wdsprefs:crosssplitinstructions3',
                'block_wdsprefs', [
                    'shell' => $shellcount,
                    'sec' => $sectioncount,
                    'shellword' => $shellword,
                    'secword' => $secword,
                ]
            ) .
            '</p></div>'
        );

        $mform->addElement('header', 'assignshellsheader',
            get_string('wdsprefs:assignshellsheader', 'block_wdsprefs'));

        // Add hidden fields for each shell to store selections.
        for ($i = 1; $i <= $shellcount; $i++) {
            $mform->addElement('hidden', "shell_{$i}_data", '');
            $mform->setType("shell_{$i}_data", PARAM_RAW);
        }

        // Start dual list container.
        $mform->addElement('html', '<div class="duallist-container">');

        // Available sections (single box on left).
        $mform->addElement('html', '<div class="duallist-available"><label>' .
            get_string('wdsprefs:availablesections', 'block_wdsprefs') .
            '</label><select class="form-control" id="available_sections" ' .
            'multiple size="10">');

        // Loop through the sectiondata.
        foreach ($sectiondata as $value => $label) {
            $mform->addElement('html',
                '<option value="' . $value . '">' .
                $label . '</option>');
        }

        $mform->addElement('html', '</select></div>');

        // Add the control buttons.
        $mform->addElement('html', '
            <div class="duallist-controls">
                <button type="button" class="btn btn-secondary mb-2" id="move-to-shell-btn">
                    ' . $OUTPUT->pix_icon('t/right', '') . ' Add to Shell</button>
                <button type="button" class="btn btn-secondary" id="move-back-btn">
                    ' . $OUTPUT->pix_icon('t/left', '') . ' Remove</button>
            </div>');

        // Shell sections (multiple boxes on right).
        $mform->addElement('html', '<div class="duallist-shells"><label>' .
            get_string('wdsprefs:availableshells', 'block_wdsprefs') . '</label>'
        );

        // Create the shell select boxes.
        for ($i = 1; $i <= $shellcount; $i++) {
            $mform->addElement('html', '
                <div class="duallist-shell"><label>' .
                "$period (Shell $i) for $teacher" .
                '</label><select class="form-control shell-select" ' .
                'id="shell_' . $i . '" data-shell-num="' . $i . '" multiple size="10"></select></div>');
        }

        $mform->addElement('html', '</div></div>');

        // Add JavaScript INLINE to manage the dual list functionality.
        $mform->addElement('html', '
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            let activeShellId = "shell_1";

            // Set active shell when clicked
            function setActiveShell(shellId) {
                document.querySelectorAll(".duallist-shell").forEach(shell => {
                    shell.classList.remove("active-shell");
                });

                const shellContainer = document.getElementById(shellId).parentElement;
                shellContainer.classList.add("active-shell");

                activeShellId = shellId;
            }

            // Initialize by setting first shell as active
            setActiveShell("shell_1");

            // Add click event to shells
            document.querySelectorAll(".duallist-shell").forEach(shell => {
                shell.addEventListener("click", function() {
                    const selectElement = this.querySelector("select");
                    if (selectElement) {
                        setActiveShell(selectElement.id);
                    }
                });
            });

            // Make select elements forward click events
            document.querySelectorAll(".shell-select").forEach(select => {
                select.addEventListener("click", function(e) {
                    e.stopPropagation();
                    setActiveShell(this.id);
                });
            });

            // Move selected options to active shell
            document.getElementById("move-to-shell-btn").addEventListener("click", function() {
                const available = document.getElementById("available_sections");
                const target = document.getElementById(activeShellId);

                if (available && target) {
                    const selectedOptions = Array.from(available.selectedOptions);

                    selectedOptions.forEach(option => {
                        const newOption = document.createElement("option");
                        newOption.value = option.value;
                        newOption.text = option.text;
                        target.appendChild(newOption);

                        available.removeChild(option);
                    });

                    // Update hidden fields
                    updateHiddenFields();
                }
            });

            // Move selected options back to available
            document.getElementById("move-back-btn").addEventListener("click", function() {
                const available = document.getElementById("available_sections");

                document.querySelectorAll(".shell-select").forEach(shellSelect => {
                    const selectedOptions = Array.from(shellSelect.selectedOptions);

                    selectedOptions.forEach(option => {
                        const newOption = document.createElement("option");
                        newOption.value = option.value;
                        newOption.text = option.text;
                        available.appendChild(newOption);

                        shellSelect.removeChild(option);
                    });
                });

                // Update hidden fields
                updateHiddenFields();
            });

            // Update hidden fields with current selections
            function updateHiddenFields() {
                document.querySelectorAll(".shell-select").forEach(select => {
                    const shellNum = select.getAttribute("data-shell-num");
                    const values = Array.from(select.options).map(opt => opt.value);

                    // Store as JSON in hidden field
                    document.querySelector(`input[name="shell_${shellNum}_data"]`).value =
                        JSON.stringify(values);
                });
            }

            // Handle form submission
            const form = document.querySelector("form.mform");
            if (form) {
                form.addEventListener("submit", function(e) {
                    // Final update of hidden fields before submission
                    updateHiddenFields();
                });
            }
        });
        </script>
        ');

        $this->add_action_buttons(true, get_string('submit'));
    }
}
