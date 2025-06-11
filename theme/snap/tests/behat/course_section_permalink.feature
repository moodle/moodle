# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Tests deleting sections in snap.
#
# @package    theme_snap
# @author     Bryan Cruz <bryan.cruz@openlms.net>
# @copyright  2024 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course @theme_snap_course_section
Feature: When the moodle theme is set to Snap, teachers can see permalink modal.

    Background:
        Given the following "course" exists:
            | fullname         | Course 1 |
            | shortname        | C1       |
            | category         | 0        |
            | enablecompletion | 1        |
            | numsections      | 4        |
            | initsections     | 1        |
        And the following "activities" exist:
            | activity | name              | intro                       | course | idnumber | section |
            | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
            | book     | Activity sample 2 | Test book description       | C1     | sample2  | 1       |
            | choice   | Activity sample 3 | Test choice description     | C1     | sample3  | 2       |
        And I log in as "admin"

    @javascript
    Scenario: Create a permalink
        Given I am on the course main page for "C1"
        And I follow "Section 1"
        And I click on "#extra-actions-dropdown-1" "css_element"
        And I click on "#section-1 .snap-permalink" "css_element"
        And I should see "Permalink"
        Then I should see "Copy to clipboard"
        And I click on "Copy to clipboard" "link"
        Then I should see "Text copied to clipboard"