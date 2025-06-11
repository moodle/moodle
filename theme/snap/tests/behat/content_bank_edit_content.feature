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
# Test for edit the content in the content bank.
#
# @package    theme_snap
# @author     Rafael Becerra <rafael.becerrarodriguez@openlms.net>
# @copyright  Copyright (c) 2020 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course @theme_snap_contentbank
Feature: When the Moodle theme is set to Snap, the content in the content bank can be renamed or deleted if needed.

  Background:
    Given the following config values are set as admin:
      | defaulthomepage | 0                           |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference |
      | private_files | System       |   1       |
    And I change window size to "large"
    And I log in as "admin"
    And I click on "button[data-original-title='Toggle block drawer']" "css_element"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on site homepage
    And I switch edit mode in Snap
    And I click on "button[data-original-title='Toggle block drawer']" "css_element"
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And I click on "button[data-original-title='Toggle block drawer']" "css_element"
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "//p[contains(text(),'filltheblanks.h5p')]" "xpath_element"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"

  @javascript
  Scenario: Admins can delete content from the content bank
    Given I click on "#dropdown-actions" "css_element"
    And I should see "Delete"
    When I click on "a[data-action='deletecontent']" "css_element"
    And I should see "Are you sure you want to delete the content 'filltheblanks.h5p'"
    And I click on "Cancel" "button" in the "Delete content" "dialogue"
    Then I should see "filltheblanks.h5p"
    And I click on "#dropdown-actions" "css_element"
    And I click on "a[data-action='deletecontent']" "css_element"
    And I click on "Delete" "button" in the "Delete content" "dialogue"
    And I wait until the page is ready
    And I should see "Content deleted."
    And "//div[contains(@class, 'core_contentbank_viewcontent')]/h2[contains(text(), 'filltheblanks.h5p')]" "xpath_element" should not exist

  @javascript
  Scenario: Admins can rename content from the content bank
    Given I click on "#dropdown-actions" "css_element"
    And I should see "Rename"
    When I click on "a[data-action='renamecontent']" "css_element"
    And I should see "Rename content"
    And I click on "Cancel" "button" in the "Rename content" "dialogue"
    Then I should see "filltheblanks.h5p"
    And I click on "#dropdown-actions" "css_element"
    And I click on "a[data-action='renamecontent']" "css_element"
    And I set the field "Content name" to "newfile.h5p"
    And I click on "Rename" "button" in the "Rename content" "dialogue"
    And I wait until the page is ready
    And I should see "Content renamed."
    And I should see "newfile.h5p"
