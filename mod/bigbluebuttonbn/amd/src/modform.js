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
 * JS for the mod_form page on mod_bigbluebuttonbn plugin.
 *
 * @module      mod_bigbluebuttonbn/modform
 * @copyright   2021 Blindside Networks Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import Notification from 'core/notification';
import Templates from "core/templates";

/**
 * Get all selectors in one place.
 *
 */
const ELEMENT_SELECTOR = {
    instanceTypeSelection: () => document.querySelector('select#id_type'),
    instanceTypeProfiles: () => document.querySelector('[data-profile-types]'),
    participantData: () => document.querySelector('[data-participant-data]'),
    participantList: () => document.getElementsByName('participants')[0],
    participantTable: () => document.getElementById('participant_list_table'),
    participantSelectionType: () => document.getElementsByName('bigbluebuttonbn_participant_selection_type')[0],
    participantSelection: () => document.getElementsByName('bigbluebuttonbn_participant_selection')[0],
    participantAddButton: () => document.getElementsByName('bigbluebuttonbn_participant_selection_add')[0],
};
/**
 * Initialise the moodle form code.
 *
 * This will help hide or show items depending on the selection of the instance type.
 *
 * @method init
 * @param {object} info
 */
export const init = (info) => {
    const selectedType = ELEMENT_SELECTOR.instanceTypeSelection();
    const instanceTypeProfiles = JSON.parse(ELEMENT_SELECTOR.instanceTypeProfiles().dataset.profileTypes);

    let profileType = info.instanceTypeDefault;
    if (selectedType !== null && selectedType.selectedIndex !== -1) {
        profileType = selectedType.options[selectedType.selectedIndex].value;
    }

    const isFeatureEnabled = (profileType, feature) => {
        const features = instanceTypeProfiles[profileType].features;
        return (features.indexOf(feature) !== -1);
    };
    applyInstanceTypeProfile(profileType, isFeatureEnabled);

    // Change form visible fields depending on the selection.
    selectedType.addEventListener('change', (e) => {
        applyInstanceTypeProfile(e.target.value, isFeatureEnabled);
    });

    ELEMENT_SELECTOR.participantSelectionType().addEventListener('change', (e) => {
        const currentTypeSelect = e.target;
        updateSelectionFromType(currentTypeSelect);
    });

    ELEMENT_SELECTOR.participantAddButton().addEventListener('click', (e) => {
        e.stopPropagation();
        e.preventDefault();
        participantAddFromCurrentSelection();
    });

    participantListInit();
};

/**
 * Show or hide form element depending on the selected profile
 *
 * @param {string} profileType
 * @param {function} isFeatureEnabled
 */
const applyInstanceTypeProfile = (profileType, isFeatureEnabled) => {
    let showAll = isFeatureEnabled(profileType, 'all');
    const showFieldset = (id, show) => {
        // Show room settings validation.
        const node = document.querySelector('#' + id);
        if (!node) {
            return;
        }
        if (show) {
            node.style.display = 'block';
            return;
        }
        node.style.display = 'none';
    };
    const showInput = (id, show) => {
        // Show room settings validation.
        const node = document.querySelector('#' + id);
        if (!node) {
            return;
        }
        var ancestor = node.closest('div').closest('div');
        if (show) {
            ancestor.style.display = 'block';
            return;
        }
        ancestor.style.display = 'none';
    };
    const showFormGroup = (id, show) => {
        // Show room settings validation.
        const node = document.querySelector('#fgroup_id_' + id);
        if (!node) {
            return;
        }
        if (show) {
            node.classList.remove('hidden');
            return;
        }
        node.classList.add('hidden');
    };
    // Show room settings validation.
    showFieldset('id_room', showAll ||
        isFeatureEnabled(profileType, 'showroom'));
    showInput('id_record', showAll ||
        isFeatureEnabled(profileType, 'showroom'));
    // Show recordings settings validation.
    showFieldset('id_recordings', showAll ||
        isFeatureEnabled(profileType, 'showrecordings'));
    // Show recordings imported settings validation.
    showInput('id_recordings_imported', showAll ||
        isFeatureEnabled(profileType, 'showrecordings'));
    // Show lock settings validation.
    showFieldset('id_lock', showAll ||
        isFeatureEnabled(profileType, 'lock'));
    // Show guest settings validation.
    showFieldset('id_guestaccess', showAll ||
        isFeatureEnabled(profileType, 'showroom'));
    // Preuploadpresentation feature validation.
    showFieldset('id_preuploadpresentation', showAll ||
        isFeatureEnabled(profileType, 'preuploadpresentation'));
    // Participants feature validation.
    showFieldset('id_permissions', showAll ||
        isFeatureEnabled(profileType, 'permissions'));
    // Schedule feature validation.
    showFieldset('id_schedule', showAll ||
        isFeatureEnabled(profileType, 'schedule'));
    // Common module settings validation.
    showFieldset('id_modstandardelshdr', showAll ||
        isFeatureEnabled(profileType, 'modstandardelshdr'));
    // Restrict access validation.
    showFieldset('id_availabilityconditionsheader', showAll ||
        isFeatureEnabled(profileType, 'availabilityconditionsheader'));
    // Tags validation.
    showFieldset('id_tagshdr', showAll || isFeatureEnabled(profileType, 'tagshdr'));
    // Competencies validation.
    showFieldset('id_competenciessection', showAll ||
        isFeatureEnabled(profileType, 'competenciessection'));
    // Standards grading feature validation.
    showFieldset('id_modstandardgrade', showAll ||
        isFeatureEnabled(profileType, 'modstandardgrade'));
    // Completion validation.
    showFieldset('id_activitycompletionheader', showAll ||
        isFeatureEnabled(profileType, 'activitycompletionheader'));
    // Completion validation.
    showFormGroup('completionattendancegroup', showAll ||
        isFeatureEnabled(profileType, 'completionattendance'));
    // Completion validation.
    showFormGroup('completionengagementgroup', showAll ||
        isFeatureEnabled(profileType, 'completionengagement'));
};

/**
 * Init the participant list
 */
const participantListInit = () => {
    const participantData = JSON.parse(ELEMENT_SELECTOR.participantData().dataset.participantData);
    const participantList = getParticipantList();
    participantList.forEach(participant => {
        const selectionTypeValue = participant.selectiontype;
        const selectionValue = participant.selectionid;
        const selectionRole = participant.role;
        if (participant.selectiontype === 'all' ||
            typeof participantData[participant.selectiontype].children[participant.selectionid] !== 'undefined') {
            // Add it to the form, but don't add the delete button if it is the first item.
            participantAddToForm(selectionTypeValue, selectionValue, selectionRole, true).then();
        }
    });
};

/**
 * Add rows to the participant list depending on the current selection.
 *
 * @param {string} selectionTypeValue
 * @param {string} selectionValue
 * @param {string} selectedRole
 * @param {boolean} canRemove
 * @returns {Promise<void>}
 */
const participantAddToForm = async(selectionTypeValue, selectionValue, selectedRole, canRemove) => {
    const participantData = JSON.parse(ELEMENT_SELECTOR.participantData().dataset.participantData);
    const sviewer = await getString('mod_form_field_participant_bbb_role_viewer', 'mod_bigbluebuttonbn');
    const smoderator = await getString('mod_form_field_participant_bbb_role_moderator', 'mod_bigbluebuttonbn');
    let roles = {
        viewer: {'id': 'viewer', label: sviewer},
        moderator: {'id': 'moderator', label: smoderator}
    };
    roles[selectedRole].isselected = true;
    try {
        const listTable = document.querySelector('#participant_list_table tbody');
        const templateContext = {
            'selectiontypevalue': selectionTypeValue,
            'selectionvalue': selectionValue,
            'participanttype': participantData[selectionTypeValue].name,
            'participantvalue':
                (selectionTypeValue !== 'all') ?
                    participantData[selectionTypeValue].children[selectionValue].name : null,
            'roles': Object.values(roles),
            'canRemove': canRemove
        };
        const {html, js} = await Templates.renderForPromise('mod_bigbluebuttonbn/participant_form_add', templateContext);
        const newNode = Templates.appendNodeContents(listTable, html, js)[0];
        newNode.querySelector('.participant-select').addEventListener('change', () => {
            participantListRoleUpdate(selectionTypeValue, selectionValue);
        });
        // Now add the callbacks: participantListRoleUpdate() and participantRemove().
        const removeNode = newNode.querySelector('.remove-button');
        if (removeNode) {
            removeNode
                .addEventListener('click', () => {
                    participantRemove(selectionTypeValue, selectionValue);
                });
        }

    } catch (e) {
        Notification.exception(e);
    }
};
/*

 */

/**
 * Update the related form element with the list value.
 *
 * @param {object} list
 */
const participantListUpdate = (list) => {
    const participantList = ELEMENT_SELECTOR.participantList();
    participantList.value = JSON.stringify(list);
};

/**
 *
 * @returns {any}
 */
const getParticipantList = () => {
    const participantListValue = ELEMENT_SELECTOR.participantList().value;
    if (participantListValue) {
        return JSON.parse(participantListValue);
    }
    return [];
};

/**
 * Remove participant both in the table/form and in the form element.
 *
 * @param {string} selectionTypeValue
 * @param {string} selectionValue
 */
const participantRemove = (selectionTypeValue, selectionValue) => {
    const pList = getParticipantList();
    const id = 'participant_list_tr_' + selectionTypeValue + '-' + selectionValue;
    const participantListTable = ELEMENT_SELECTOR.participantTable();
    const selectionid = (selectionValue === '' ? null : selectionValue);
    for (let i = 0; i < pList.length; i++) {
        if (pList[i].selectiontype === selectionTypeValue &&
            pList[i].selectionid === selectionid) {
            pList.splice(i, 1);
        }
    }
    // Remove from the form.
    for (let i = 0; i < participantListTable.rows.length; i++) {
        if (participantListTable.rows[i].id === id) {
            participantListTable.deleteRow(i);
        }
    }
    // Update value in the form.
    participantListUpdate(pList);
};

/**
 * Role update
 *
 * @param {string} type
 * @param {string} id
 */
const participantListRoleUpdate = (type, id) => {
    // Update in memory.
    const participantListRoleSelection = document.querySelector(`#participant_list_tr_${type}-${id} .participant-select`);
    const pList = getParticipantList();

    for (var i = 0; i < pList.length; i++) {
        if (pList[i].selectiontype === type && pList[i].selectionid === id) {
            pList[i].role = participantListRoleSelection.value;
        }
    }
    // Update in the form.
    participantListUpdate(pList);
};

/**
 * Add participant from the currently selected options
 */
const participantAddFromCurrentSelection = () => {
    let selectionType = ELEMENT_SELECTOR.participantSelectionType();
    let selection = ELEMENT_SELECTOR.participantSelection();
    const pList = getParticipantList();
    // Lookup to see if it has been added already.
    for (var i = 0; i < pList.length; i++) {
        if (pList[i].selectiontype === selectionType.value &&
            pList[i].selectionid === selection.value) {
            return;
        }
    }
    pList.push({
        "selectiontype": selectionType.value,
        "selectionid": selection.value,
        "role": "viewer"
    });
    // Add it to the form.
    participantAddToForm(selectionType.value, selection.value, 'viewer', true).then();
    // Update in the form.
    participantListUpdate(pList);
};

/**
 * Update selectable options when changing types
 *
 * @param {HTMLNode} currentTypeSelect
 */
const updateSelectionFromType = (currentTypeSelect) => {
    const createNewOption = (selectItem, label, value) => {
        const option = document.createElement('option');
        option.text = label;
        option.value = value;

        selectItem.add(option);
    };

    const participantData = JSON.parse(ELEMENT_SELECTOR.participantData().dataset.participantData);
    // Clear all selection items.
    const participantSelect = ELEMENT_SELECTOR.participantSelection();
    while (participantSelect.firstChild) {
        participantSelect.removeChild(participantSelect.firstChild);
    }
    // Add options depending on the selection.
    if (currentTypeSelect.selectedIndex !== -1) {
        const options = Object.values(participantData[currentTypeSelect.value].children);
        options.forEach(option => {
            createNewOption(participantSelect, option.name, option.id);
        });

        if (currentTypeSelect.value === 'all') {
            createNewOption(participantSelect, '---------------', 'all');
            participantSelect.disabled = true;
        } else {
            participantSelect.disabled = false;
        }
    }
};
