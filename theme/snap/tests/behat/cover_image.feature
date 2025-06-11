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
# Tests for cover image uploading.
#
# @package    theme_snap
# @copyright  Copyright (c) 2016 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_color_check @_file_upload
Feature: When the moodle theme is set to Snap, cover image can be set for site and courses.

  Background:
    Given the following config values are set as admin:
      | defaulthomepage                | 0 |            |

  @javascript
  Scenario: Editing teachers can change and delete course cover image.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher2  | 1        | teacher2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | editingteacher |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | teacher        |
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    Then I should see "Change cover image"
    And I should not see cover image in page header
    And I click on "#snap-coverimagecontrol label" "css_element"
    And ".cropper-container" "css_element" should not exist
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_small.png" to the file picker for Snap
    And ".cropper-container" "css_element" should exist
    And I click on "#id_snap_cover_image_save_button" "css_element"
    # Test cancelling upload
    And I wait until ".btn.cancel" "css_element" is visible
    And I click on ".btn.cancel" "css_element"
    Then I should not see cover image in page header
    And ".snap_cover_image_dialogue" "css_element" should be visible
    And ".cropper-container" "css_element" should exist
    # Test confirming upload
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_small.png" to the file picker for Snap
    And I click on "Overwrite" "button"
    And I click on "#id_snap_cover_image_save_button" "css_element"
    And I wait until ".btn.ok" "css_element" is visible
    And I click on ".btn.ok" "css_element"
    Then I should see cover image in page header
    And I reload the page
    Then I should see cover image in page header
    # Test changing the image again
    And I click on "#snap-coverimagecontrol label" "css_element"
    And ".cropper-container" "css_element" should exist
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/bpd_bikes_1280px.jpg" to the file picker for Snap
    And ".cropper-container" "css_element" should exist
    And I click on "#id_snap_cover_image_save_button" "css_element"
    And I wait until ".btn.ok" "css_element" is visible
    And I click on ".btn.ok" "css_element"
    Then I should see cover image in page header
    # Breadcrumb has now white background and site font-color.
    And I check element ".mast-image .breadcrumb a" with color "#82009E"
    And I reload the page
    And I check element ".mast-image .breadcrumb a" with color "#82009E"
    Then I should see cover image in page header
    # Test deleting cover image
    And I click on "#snap-coverimagecontrol label" "css_element"
    And I click on "Delete cover image" "button" in the "Image properties" "dialogue"
    And ".snap_cover_image_delete_image_dialogue" "css_element" should be visible
    And I click on "#delete_image_cancel_button" "css_element"
    Then I should see cover image in page header
    And I click on "#snap-coverimagecontrol label" "css_element"
    And I click on "Delete cover image" "button" in the "Image properties" "dialogue"
    And ".snap_cover_image_delete_image_dialogue" "css_element" should be visible
    And I click on "#delete_image_confirm_button" "css_element"
    Then I should not see cover image in page header
    Then I log in as "admin"
    And I go to "Site administration > Development" in snap administration
    And I follow "Purge caches"
    And I press "Purge all caches"
    Then I should see "All caches were purged"
    And I reload the page
    # Test cover image can only be set on main course page
    And I am on the course main page for "C1"
    Then I should see "Change cover image"
    And I click on "#snap-coverimagecontrol label" "css_element"
    And ".cropper-container" "css_element" should not exist
    And I am on the course main page for "C1"
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Gradebook setup" in current page administration
    Then I should not see "Change cover image"
    # Test that non-editing teachers can't change cover image. (no need to test with students as they have less caps)
    And I log out
    And I log in as "teacher2"
    And I am on the course main page for "C1"
    Then I should not see "Change cover image"

  @javascript
  Scenario: A warning will be presented if the cover image is of a low resolution.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    Then I should see "Change cover image"
    And I should not see cover image in page header
    And I click on "#snap-coverimagecontrol label" "css_element"
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_lt1024px.png" to the file picker for Snap
    Then I should see "For best quality, we recommend a larger image of at least 1024px width"
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_small.png" to the file picker for Snap
    Then I should not see "For best quality, we recommend a larger image of at least 1024px width"

  @javascript
  Scenario: Admin user can change and delete site cover image.
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | user1    | User      | 1        | user1@example.com    |
    And I log in as "admin"
    And I am on site homepage
    Then I should not see "Change cover image"
    And I click on "#admin-menu-trigger" "css_element"
    And I switch edit mode in Snap
    Then I should see "Change cover image"
    And I should not see cover image in page header
    And I click on "#snap-coverimagecontrol label" "css_element"
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_small.png" to the file picker for Snap
    And I click on "#id_snap_cover_image_save_button" "css_element"
    # Test cancelling upload
    And I wait until ".btn.cancel" "css_element" is visible
    And I click on ".btn.cancel" "css_element"
    Then I should not see cover image in page header
    And ".snap_cover_image_dialogue" "css_element" should be visible
    And ".cropper-container" "css_element" should exist
    # Test confirming upload
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_small.png" to the file picker for Snap
    And I click on "Overwrite" "button"
    And I click on "#id_snap_cover_image_save_button" "css_element"
    And I wait until ".btn.ok" "css_element" is visible
    And I click on ".btn.ok" "css_element"
    Then I should see cover image in page header
    And I reload the page
    Then I should see cover image in page header
    # Test deleting cover image
    And I click on "#snap-coverimagecontrol label" "css_element"
    And I click on "Delete cover image" "button" in the "Image properties" "dialogue"
    And ".snap_cover_image_delete_image_dialogue" "css_element" should be visible
    And I click on "#delete_image_cancel_button" "css_element"
    Then I should see cover image in page header
    And I click on "#snap-coverimagecontrol label" "css_element"
    And I click on "Delete cover image" "button" in the "Image properties" "dialogue"
    And ".snap_cover_image_delete_image_dialogue" "css_element" should be visible
    And I click on "#delete_image_confirm_button" "css_element"
    Then I should not see cover image in page header
    # Test non admin user can't change site image.
    And I log out
    And I log in as "user1"
    And I am on site homepage
    Then I should not see "Change cover image"

  @javascript
  Scenario: Admin user can change and delete category cover image.
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | user1    | User      | 1        | user1@example.com    |
    And the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
    And I log in as "admin"
    And I am on the course category page for category with idnumber "CAT1"
    Then I should see "Change cover image"
    And I should not see cover image in page header
    And I click on "#snap-coverimagecontrol label" "css_element"
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_small.png" to the file picker for Snap
    And I click on "#id_snap_cover_image_save_button" "css_element"
    # Test cancelling upload
    And I wait until ".btn.cancel" "css_element" is visible
    And I click on ".btn.cancel" "css_element"
    Then I should not see cover image in page header
    And ".snap_cover_image_dialogue" "css_element" should be visible
    And ".cropper-container" "css_element" should exist
    # Test confirming upload
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_small.png" to the file picker for Snap
    And I click on "Overwrite" "button"
    And I click on "#id_snap_cover_image_save_button" "css_element"
    And I wait until ".btn.ok" "css_element" is visible
    And I click on ".btn.ok" "css_element"
    Then I should see cover image in page header
    And I reload the page
    Then I should see cover image in page header
    # Test non admin user can't change site image.
    And I log out
    And I log in as "user1"
    And I am on the course category page for category with idnumber "CAT1"
    Then I should not see "Change cover image"

  @javascript
  Scenario: A warning will be presented if the cover image in a course constrast is not compliant with WG3
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    Then I should see "Change cover image"
    And I should not see cover image in page header
    And I click on "#snap-coverimagecontrol input" "css_element"
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_small.png" to the file picker for Snap
    Then I should see "This image could have contrast problems due not compliance with the WCAG 2.0 minimum ratio value 4.5:1."
    And I click on "#id_snap_cover_image_save_button" "css_element"
    And I wait until ".btn.ok" "css_element" is visible
    And I click on ".btn.ok" "css_element"
    And I click on "#snap-coverimagecontrol input" "css_element"
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/black_cover.jpg" to the file picker for Snap
    And I click on "#id_snap_cover_image_save_button" "css_element"
    And I wait until ".btn.ok" "css_element" is visible
    And I click on ".btn.ok" "css_element"
    Then I should not see "This image could have contrast problems due not compliance with the WCAG 2.0 minimum ratio value 4.5:1."

  @javascript
  Scenario: A warning will be presented if the cover image in a category constrast is not compliant with WG3
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    Given I log in as "admin"
    And I am on course index
    Then I should see "Change cover image"
    And I should not see cover image in page header
    And I click on "#snap-coverimagecontrol label" "css_element"
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/testpng_small.png" to the file picker for Snap
    Then I should see "This image could have contrast problems due not compliance with the WCAG 2.0 minimum ratio value 4.5:1"
    And I click on "#id_snap_cover_image_save_button" "css_element"
    And I wait until ".btn.ok" "css_element" is visible
    And I click on ".btn.ok" "css_element"
    And I click on "#snap-coverimagecontrol label" "css_element"
    And I click on "Upload a new image" "button" in the "Image properties" "dialogue"
    And I upload "/theme/snap/tests/fixtures/black_cover.jpg" to the file picker for Snap
    And I click on "#id_snap_cover_image_save_button" "css_element"
    And I wait until ".btn.ok" "css_element" is visible
    And I click on ".btn.ok" "css_element"
    Then I should not see "This image could have contrast problems due not compliance with the WCAG 2.0 minimum ratio value 4.5:1"
