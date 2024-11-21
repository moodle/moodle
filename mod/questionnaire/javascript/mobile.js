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
 * Support for Moodle Mobile integration
 */


var questionsFormFields = {};
this.questionsFormErrors = {};
for (const fieldkey in this.CONTENT_OTHERDATA.fields) {
    questionsFormFields[fieldkey] = [];
    questionsFormFields[fieldkey][0] = '';
    for (const itemid in
        this.CONTENT_OTHERDATA.questions[this.CONTENT_OTHERDATA.pagenum][this.CONTENT_OTHERDATA.fields[fieldkey].id]) {
        questionsFormFields[fieldkey][0] =
            this.CONTENT_OTHERDATA.questions[this.CONTENT_OTHERDATA.pagenum][this.CONTENT_OTHERDATA.fields[fieldkey].id]
                [itemid].value;
    }
    if (this.CONTENT_OTHERDATA.fields[fieldkey].required === 'y') {
        questionsFormFields[fieldkey][1] = this.Validators.required;
        this.questionsFormErrors[fieldkey] = this.CONTENT_OTHERDATA.fields[fieldkey].errormessage;
    }
}
this.questionsForm = this.FormBuilder.group(questionsFormFields);