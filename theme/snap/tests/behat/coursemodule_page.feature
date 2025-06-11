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
# Tests for page module.
#
# @package    theme_snap
# @copyright  Copyright (c) 2015 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @snap_page_resource
Feature: Open page module inline
  As any user
  I need to view page modules inline and have auto completion tracking updated.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | category | groupmode | enablecompletion | initsections |
      | Course 1 | C1        | topics | 0        | 1         | 1                |      1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | admin    | C1     | teacher |
      | student1 | C1     | student |
    And the following config values are set as admin:
      | lazyload_mod_page | 0 | theme_snap |

  @javascript
  Scenario Outline: Page mod is created and opened inline.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And the following "activities" exist:
      | activity | course | idnumber | name       | intro        | content       | completion | completionview |
      | page     | C1     | page1    | Test page1 | Test page 1  | page content1 | 0          | 0              |
    And I log out
    Then I log in as "student1"
    And I am on the course main page for "C1"
    And I should not see "page content1"
    And I click on "li .contentafterlink .pagemod-readmore" "css_element"
    And I wait until "#pagemod-content-container" "css_element" is visible
    # The above step basically waits for the page content to load up.
    And I should see "page content1"
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: Page mod completion updates on read more and affects availability for other modules and sections.
    Given the following "activities" exist:
      | activity | course | idnumber  | name              | intro                 | content                 | completion | completionview | section |
      | page     | C1     | pagec     | Page completion   | Page completion intro | Page completion content | 1          | 1              | 0       |
      | page     | C1     | pager     | Page restricted   | Page restricted intro | Page restricted content | 0          | 0              | 0       |
      | page     | C1     | pagec2    | Page completion 2 | Page comp2      intro | Page comp2      content | 1          | 1              | 1       |
    And the following "activities" exist:
      | activity | course | idnumber     | name            | section |
      | assign   | C1     | assigntest   | Assignment Test | 2       |
    And I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option>   | theme_snap |
      | resourcedisplay     | <Option 2> | theme_snap |
    And I am on the course main page for "C1"
    # Restrict the second page module to only be accessible after the first page module is marked complete.
    And I restrict course asset "Page restricted" by completion of "Page completion"
    # Restrict section one of the course to only be accessible after the first page module is marked complete.
    And I follow "Section 1"
    And I click on "#section-1 .edit-summary" "css_element"
    And I set the section name to "Section 1"
    And I apply asset completion restriction "Page completion" to section
    And I follow "Section 2"
    And I click on "#section-2 .edit-summary" "css_element"
    And I set the section name to "Section 2"
    And I apply asset completion restriction "Page completion 2" to section
    And I log out
    And I log in as "student1"
    And I am on the course main page for "C1"
    Then I should not see "page content2"
    # Note: nth-of-type(2) corresponds to the second section in the TOC.
    And I should see "Conditional" in the "#chapters h3:nth-of-type(2)" "css_element"
    And I should see "Conditional" in the "#chapters h3:nth-of-type(3)" "css_element"
    And "img[alt*='Not completed: Page completion']" "css_element" should exist
    And I click on "//a[@class='snap-conditional-tag']" "xpath_element"
    And I should see "Not available unless: The activity Page completion is marked complete"
    And I follow "Section 1"
    And "#chapters h3:nth-of-type(2) li.snap-visible-section" "css_element" should exist
    # Make sure Section 1 show section availability info.
    Then I should see availability info "Not available unless: The activity Page completion is marked complete"
    And I follow "Introduction"
    And I click on "li .contentafterlink .pagemod-readmore" "css_element"
    And I wait until "#section-0 .pagemod-content[data-content-loaded=\"1\"]" "css_element" is visible
    # The above step basically waits for the page module content to load up.
    Then I should see "Page completion content"
    And I should not see availability info "Not available unless: The activity Page completion is marked complete"
    And I should not see "Conditional" in the "#chapters h3:nth-of-type(2)" "css_element"
    And I should see "Progress: 1 / 1" in the "#chapters h3:nth-of-type(1)" "css_element"
    And "#chapters h3:nth-of-type(1) li.snap-visible-section" "css_element" should exist
    And ".snap-conditional-tag[data-content*='Page completion']" "css_element" should not exist
    And "img[alt='Completed: Page completion. Select to mark as not complete.']" "css_element" should exist
    And I follow "Section 1"
    # Make sure Section 1 does not show section availability info.
    Then I should not see availability info "Not available unless: The activity Page completion is marked complete"
    And I should see "Page completion 2"
    And "img[alt*='Not completed: Page completion 2']" "css_element" should exist
    And I click on "li[aria-label='Section 1']" "css_element"
    And I click on "//p[contains(text(), 'Page completion 2')]/ancestor::div[contains(@class, 'activityinstance')]//button[contains(@class, 'pagemod-readmore')]" "xpath_element"
    And I wait until "#section-1 .pagemod-content[data-content-loaded=\"1\"]" "css_element" is visible
    Then "img[alt*='Not completed: Page completion 2']" "css_element" should not exist
    And "img[alt='Completed: Page completion 2. Select to mark as not complete.']" "css_element" should exist
    And "#chapters h3:nth-of-type(2) li.snap-visible-section" "css_element" should exist
    And I should not see "Conditional" in the "#chapters h3:nth-of-type(3)" "css_element"
    Examples:
      | Option     | Option 2 |
      | 0          | list     |
      | 1          | card     |

  @javascript
  Scenario Outline: Page mod is opened in a new window by default.
    Given the following "activities" exist:
      | activity | course  | idnumber  | name   | intro      | content      | section |
      | page     | C1      | pagec     | Page   | Page intro | Page content | 0       |
    And I log in as "admin"
    And I am on the course main page for "C1"
    And I click on "<Page resource selector>" "css_element"
    # Page content will be opened in a new window.
    And I wait until the page is ready
    Then I should see "Page content"
    Examples:
      | Page resource selector                                            |
      # Open with read more button link.
      | li .contentafterlink .pagemod-readmore                            |
      # Open with page resource title link.
      | li[data-type='Page'] h3.snap-asset-link a.mod-link p.instancename |
