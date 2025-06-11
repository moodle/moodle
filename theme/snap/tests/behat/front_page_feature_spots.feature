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
# Tests for feature spots in the front page.
#
# @package    theme_snap
# @author     Rafael Becerra <rafael.becerrarodriguez@openlms.net>
# @copyright  Copyright (c) 2020 Open LMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @snap_feature_spots
Feature: Correct functionality of feature spots in the front page with every possible setting

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following config values are set as admin:
      | fs_heading           | Heading for feature spots | theme_snap |
      | fs_one_title         | Title for spot 1          | theme_snap |
      | fs_two_title         | Title for spot 2          | theme_snap |
      | fs_three_title       | Title for spot 3          | theme_snap |
      | fs_one_text          | Content for spot 1        | theme_snap |
      | fs_two_text          | Content for spot 2        | theme_snap |
      | fs_three_text        | Content for spot 3        | theme_snap |
      | fs_one_title_link    | https://www.moodle.com    | theme_snap |
      | fs_three_title_link  | /course/view.php?id=1     | theme_snap |
      | fs_one_title_link_cb | 1                         | theme_snap |
  @javascript
  Scenario: Snap Feature spots are correctly configured. Just for text and title.
    Given I log in as "admin"
    And I am on site homepage
    And I should see "Heading for feature spots"
    And I should see "Title for spot 1"
    And I should see "Title for spot 2"
    And I should see "Title for spot 3"
    # No content text should be valid.
    And the following config values are set as admin:
      | fs_two_text          |       | theme_snap |
    And I am on site homepage
    And I should see "Title for spot 2"

  @javascript
  Scenario: Snap Feature spots have links as a title.
    Given I log in as "admin"
    And I am on site homepage
    And ".snap-feature-block a.snap-feature-link" "css_element" should exist
    And the "href" attribute of "//div[@class='snap-feature-block']//a[contains(text(), 'Title for spot 1')]" "xpath_element" should contain "https://www.moodle.com"
    And the "href" attribute of "//div[@class='snap-feature-block']//a[contains(text(), 'Title for spot 3')]" "xpath_element" should contain "course/view.php?id=1"
  @javascript
  Scenario: Snap Feature spots title links opens in a new window when the checkbox is checked.
    Given I log in as "admin"
    And I am on site homepage
    And the "target" attribute of "//div[@class='snap-feature-block']//a[contains(text(), 'Title for spot 1')]" "xpath_element" should contain "_blank"
  @javascript
  Scenario: Snap Feature spots settings page validate that the links exists as links, internal and external.
    Given I log in as "admin"
    And the following config values are set as admin:
      | linkadmincategories | 0 |
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    And I follow "Feature spots"
    And I set the field with xpath "//div[@class='form-text defaultsnext']//input[@id='id_s_theme_snap_fs_one_title_link']" to "testnolink"
    And I press "Save changes"
    And I should see "Some settings were not changed due to an error."
    And I follow "Feature spots"
    And I should see "This value is not valid"
    And I set the field with xpath "//div[@class='form-text defaultsnext']//input[@id='id_s_theme_snap_fs_one_title_link']" to "/course/view.php?id=1"
    And I press "Save changes"
    And I should see "Changes saved"
    And I follow "Feature spots"
    And I set the field with xpath "//div[@class='form-text defaultsnext']//input[@id='id_s_theme_snap_fs_one_title_link']" to "http://www.google.com"
    And I press "Save changes"
    And I should see "Changes saved"
