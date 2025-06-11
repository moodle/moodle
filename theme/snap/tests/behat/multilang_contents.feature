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
# Test for multilang strings in Snap.
#
# @package    theme_snap
# @copyright  Copyright (c) 2020 Open LMS. (https://www.openlms.net)
# @author     2018 Rafael Becerra <rafael.becerrarodriguez@openlms.net>
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: The site displays only the language that user has selected for multilang strings.

  Background:
    Given the following config values are set as admin:
      | theme | snap |
      | linkadmincategories | 0 |
    And the following "courses" exist:
      | fullname | shortname | idnumber |
      | Course 1 | Course 1  | C1       |
    And I log in as "admin"
    And I am on site homepage
    And I go to "Site administration > Plugins > Filters > Manage filters" in snap administration
    And I click on "On" "option" in the "#activemultilang" "css_element"
    And I click on "Content and headings" "option" in the "#applytomultilang" "css_element"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Settings" in current page administration
    And I set the field with xpath "//select[@id='id_s__frontpageloggedin0']" to "Announcements"
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Site news on front page displays only in english.
    Given I log in as "admin"
    And I am on site homepage
    And I click on "//div/a[contains(text(),'Add a new topic')]" "xpath_element"
    And I set the field "subject" to "Test discussion"
    And I set the field "Message" to "<span lang=\"en\" class=\"multilang\">English text</span><span lang=\"it\" class=\"multilang\">Italian text</span>"
    And I press "Post to forum"
    And I am on site homepage
    And ".news-article.clearfix" "css_element" should exist
    And I click on "//div/p/a[contains(text(),'Read more')]" "xpath_element"
    And I should see "English text"
    And I should not see "Italian text"
    And I log out

  @javascript
  Scenario: Language is changed site footer displays in only english.
    Given the following config values are set as admin:
      | footnote | <span lang="en" class="multilang">English text</span><span lang="it" class="multilang">Italian text</span> | theme_snap |
    When I log in as "admin"
    Then "#snap-footer-content" "css_element" should exist
    And I should see "English text"
    And I should not see "Italian text"
    And I log out

  @javascript
  Scenario: Course header for the category displays in only english.
    Given the following "categories" exist:
      | category | name                                                                                                       | idnumber | sortorder |
      | 0        | <span lang="en" class="multilang">English text</span><span lang="it" class="multilang">Italian text</span> | 1        | 1         |
    When I log in as "admin"
    And I am on course index
    And I should see "English text"
    And I should not see "Italian text"
    And I log out
