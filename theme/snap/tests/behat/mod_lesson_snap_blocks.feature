# This file is part of Moodle - https://moodle.org/
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
# along with Moodle.  If not, see <https://www.gnu.org/licenses/>.
#
# Test for checking the navigation menu setting in the Lesson activity.
#
# @package    theme_snap
# @author     Juan Felipe Orozco Escobar <juanfelipe.orozcoescobar@openlms.net>
# @copyright  Copyright (c) 2025 Open LMS (https://www.openlms.net)
# @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: Display navigation menu in the Lesson activity

  Background:

    Given the following "users" exist:
      | username  | firstname   | lastname        | email                 |
      | teacher1  | TeacherName | TeacherLastname | teacher1@example.com  |
      | student1  | StudentName | StudentLastname | student1@example.com  |

    And the following "courses" exist:
      | fullname     | shortname | format  |
      | Test Course  | C1        | topics  |

    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1  | C1      | editingteacher  |

    And the following "activities" exist:
      | activity | name             | intro               | course | idnumber | section |
      | lesson   | Test lesson name | Test lesson content | C1     | lesson1  | 1       |

    And the following "mod_lesson > page" exist:
      | lesson           | qtype   | title              | content        |
      | Test lesson name | numeric | Numerical question | What is 1 + 2? |

    And the following "mod_lesson > answers" exist:
      | page               | answer          | jumpto        | score |
      | Numerical question | 3               | End of lesson | 1     |
      | Numerical question | @#wronganswer#@ | Next page     | 0     |

  @javascript
  Scenario: Navigation menu is displayed when the 'Display menu' setting is enabled in the Lesson activity
    Given I log in as "teacher1"
    When I am on the course main page for "C1"
    When I am on the "Test lesson name" "lesson activity" page
    And I should not see "Lesson menu"
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Display menu | Yes |
    And I press "Save and display"
    And I should see "Lesson menu"
