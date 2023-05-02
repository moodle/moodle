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
# Tests for visibility of admin block by user type and page.
#
# @package    filter_oembed
# @copyright  2016 Guy Thomas <gthomas@moodlerooms.com>
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@filter @filter_oembed
Feature: Admin can use the provider management page to view / edit / enable / disable providers.

  @javascript
  Scenario: Admin user carries out various provider management tasks.
  #This is Done as one scenario for performance.
    Given I log in as "admin"
    When I navigate to "Manage providers" node in "Site administration>Plugins>Filters>Oembed Filter"
    Then "#providermanagement" "css_element" should exist
    # Test filtering list.
    And I should see "YouTube" in the "oembedproviders" "table"
    When I filter the provider list to "Vimeo"
    Then I should not see "YouTube" in the "oembedproviders" "table"
    And I should see "Vimeo" in the "oembedproviders" "table"
    And the provider "Vimeo" is disabled
    # Test enable / disable
    When I toggle the provider "Vimeo"
    Then the provider "Vimeo" is enabled
    When I toggle the provider "Vimeo"
    Then the provider "Vimeo" is disabled
    # Test edit
    When I edit the provider "Vimeo" with the values:
    | Provider Name | Zimeo |
    Then I should see "Created new local provider definition for \"Zimeo\"." in the "oembedproviders" "table"
