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
# Behat feature for Ally report configuration.
#
# @package    report_allylti
# @copyright  Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@report @report_allylti
Feature: Configure LTI settings for Ally report launches
  In order to be able to launch the Ally reports
  As an Administrator
  I want to be able to configure the settings for those LTI launches

  Scenario: Administrator can configure the settings for the Admin Report LTI launch
    Given I log in as "admin"
    And I navigate to "Ally" in site administration
    And I set the field "Launch URL" to "http://locallaunch.dev"
    And I set the field "Key" to "ltikey"
    And I set the field "Secret" to "secretpassword12345"
    And I press "Save changes"
    Then I should see "Changes saved"
