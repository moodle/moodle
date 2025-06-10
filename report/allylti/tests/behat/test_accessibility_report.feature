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
# Behat feature for Ally Accessibility report configuration.
#
# @package    report_allylti
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@report @report_allylti
Feature: Accessibility report should not be available to users that don't have Ally configured
  and should be available to users that do have it configured.

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |

  Scenario: Administrator should see Accessibility report after setting up Ally in the site.
    Given I log in as "admin"
    And I navigate to "Ally" in site administration
    And I set the field "Launch URL" to "http://locallaunch.dev"
    And I set the field "Key" to "ltikey"
    And I set the field "Secret" to "secretpassword12345"
    And I press "Save changes"
    Then I should see "Changes saved"
    Then I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    Then I should see "Accessibility report"

  Scenario: Administrator should not see Accessibility without Ally configured in the site.
    Given I log in as "admin"
    Then I am on "Course 1" course homepage
    Then I should not see "Accessibility report"
