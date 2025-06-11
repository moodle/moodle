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
# Test for book "Location and availability of blocks in the Book activity"
#
# @package    theme_snap
# @author     Dayana Pardo <dayana.pardo@openlms.net>
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: Display of blocks in book activities with snap theme

  Background:
    Given the following config values are set as admin:
      | theme | snap |

    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |

    And the following "courses" exist:
      | fullname     | shortname | format  |
      | Test Course  | C1        | topics  |

    And the following "activities" exist:
      | activity | name      | intro        | course | idnumber | section |
      | book     | Test Book | Test content | C1     | book1    | 1       |

    And the following "mod_book > chapter" exists:
      | book    | Test Book                       |
      | title   | First chapter                   |
      | content | This is First chapter's content |

    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1  | C1      | editingteacher  |

  @javascript
  Scenario: Blocks are correctly displayed in the book activity
    Given I log in as "teacher1"
    When I am on the course main page for "C1"

    When I am on the "Test Book" "book activity" page
    Then I should see "Test content"

    Then "#moodle-blocks" "css_element" should exist
    And "#block-region-side-pre" "css_element" should exist
    And ".block_book_toc" "css_element" should exist
    And I should see "Table of contents"

    When I log out
    And I log in as "student1"
    And I am on the "Test Book" "book activity" page

    Then "#moodle-blocks" "css_element" should exist
    And "#block-region-side-pre" "css_element" should exist
    And ".block_book_toc" "css_element" should exist
    And I should see "Table of contents"
