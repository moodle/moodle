@format @format_tiles @format_tiles_photo_picker @javascript @_file_upload
Feature: Teacher can allocate photos to tiles

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname     | shortname | format | coursedisplay | numsections | enablecompletion |
      | Business Law | BL        | tiles  | 0             | 10          | 1                |
      | Course 2     | C2        | tiles  | 0             | 10          | 1                |
    And the following "activities" exist:
      | activity | name         | intro                  | course | idnumber | section | visible |
      | quiz     | Test quiz V  | Test quiz description  | BL     | quiz1    | 1       | 1       |
      | page     | Test page V  | Test page description  | BL     | page1    | 1       | 1       |
      | forum    | Test forum V | Test forum description | BL     | forum1   | 1       | 1       |
      | url      | Test URL V   | Test url description   | BL     | url1     | 1       | 1       |
      | label    | Test label V | Test label description | BL     | label1   | 1       | 1       |
      | url      | Test URL V   | Test url description   | C2     | url1     | 1       | 1       |
      | label    | Test label V | Test label description | C2     | label1   | 1       | 1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | BL     | student        |
      | student1 | C2     | student        |
      | teacher1 | BL     | editingteacher |
      | teacher1 | C2     | editingteacher |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |
      | usejavascriptnav       | 1        | format_tiles |
      | allowphototiles        | 1        | format_tiles |

  # TODO this is monolithic and needs refactoring into smaller scenarios.

  @javascript
  Scenario: Teacher can use photo picker to pick photos (and icons), can backup and restore course, and student can view
    When I log in as "teacher1"
    And I am on "Business Law" course homepage with editing mode on
    And I click on "#tileicon_1" "css_element"
    And I wait until the page is ready
    And I wait "1" seconds
    And "Pick a new icon or background photo" "dialogue" should be visible
    And I follow "Upload new photo"
    And I upload "course/format/tiles/tests/fixtures/blueberries.jpg" file to "Upload new photo" filemanager
    And I press "Save changes"
    And I wait until the page is ready
    And I should see "Image saved for tile 'Tile 1'"
    And I turn editing mode off
    And I wait until the page is ready
    And course "Business Law" tile "1" should show photo "blueberries.jpg"

    And I turn editing mode on
    And I wait until the page is ready
    And I wait "1" seconds
    And I click on "#tileicon_2" "css_element"
    And I wait until the page is ready
    And I wait "1" seconds
    And I click on ".tile-icon[data-original-title=\"Refresh\"]" "css_element" in the "#icon_picker_modal" "css_element"
    And I wait until the page is ready
    And I wait "2" seconds

    And I click on "#tileicon_3" "css_element"
    And I wait until the page is ready
    And I wait "1" seconds
    And "Pick a new icon or background photo" "dialogue" should be visible
    And I follow "Upload new photo"
    And I upload "course/format/tiles/tests/fixtures/strawberries.jpg" file to "Upload new photo" filemanager
    And I press "Save changes"
    And I wait until the page is ready
    And I should see "Image saved for tile 'Tile 3'"

    And I wait "1" seconds
    And I click on "#tileicon_7" "css_element"
    And I wait until the page is ready
    And I wait "1" seconds
    And "Pick a new icon or background photo" "dialogue" should be visible
    And I follow "Photo library"
    And I wait until the page is ready
    And I click on ".photo[title=\"blueberries.jpg\"]" "css_element" in the "#icon_picker_modal" "css_element"
    And I wait until the page is ready

    And I turn editing mode off
    And I wait until the page is ready
    And course "Business Law" tile "1" should show photo "blueberries.jpg"
    And course "Business Law" tile "3" should show photo "strawberries.jpg"
    And course "Business Law" tile "7" should show photo "blueberries.jpg"

    And I backup "Business Law" course using this options:
      | Confirmation | Filename | test_backup.mbz |

    # Remove the photo tiles so we can check they come back on restore.
    And I wait until the page is ready
    And I am on "Business Law" course homepage with editing mode on
    And I wait "1" seconds
    And I click on "#tileicon_1" "css_element"
    And I wait until the page is ready
    And I wait "1" seconds
    And I click on ".tile-icon[data-original-title=\"Refresh\"]" "css_element" in the "#icon_picker_modal" "css_element"
    And I wait until the page is ready
    And I wait "2" seconds

    And I wait until the page is ready
    And I wait "1" seconds
    And I click on "#tileicon_3" "css_element"
    And I wait until the page is ready
    And I wait "1" seconds
    And I click on ".tile-icon[data-original-title=\"Refresh\"]" "css_element" in the "#icon_picker_modal" "css_element"
    And I wait until the page is ready
    And I wait "2" seconds

    And I wait until the page is ready
    And I wait "1" seconds
    And I click on "#tileicon_7" "css_element"
    And I wait until the page is ready
    And I wait "1" seconds
    And I click on ".tile-icon[data-original-title=\"Refresh\"]" "css_element" in the "#icon_picker_modal" "css_element"
    And I wait until the page is ready
    And I wait "2" seconds

    And I navigate to "Restore" in current page administration
    And I wait until the page is ready
    And I merge "test_backup.mbz" backup into the current course after deleting it's contents using this options:
    And I am on site homepage
    And I follow "Business Law"
    And I wait until the page is ready
    And I turn editing mode off
    And I wait until the page is ready
    And course "Business Law" tile "1" should show photo "blueberries.jpg"
    And course "Business Law" tile "3" should show photo "strawberries.jpg"
    And course "Business Law" tile "7" should show photo "blueberries.jpg"

    And I am on "Course 2" course homepage with editing mode on
    And I wait until the page is ready
    And I wait "1" seconds
    And I click on "#tileicon_3" "css_element"
    And I wait until the page is ready
    And I wait "1" seconds
    And "Pick a new icon or background photo" "dialogue" should be visible
    And I follow "Photo library"
    And I wait until the page is ready
    And I click on ".photo[title=\"blueberries.jpg\"]" "css_element" in the "#icon_picker_modal" "css_element"
    And I wait until the page is ready

    And I wait "1" seconds
    And I click on "#tileicon_6" "css_element"
    And I wait until the page is ready
    And I wait "1" seconds
    And "Pick a new icon or background photo" "dialogue" should be visible
    And I follow "Photo library"
    And I wait until the page is ready
    And I click on ".photo[title=\"strawberries.jpg\"]" "css_element" in the "#icon_picker_modal" "css_element"
    And I wait until the page is ready

    And I turn editing mode off
    And I wait until the page is ready
    And course "Course 2" tile "3" should show photo "blueberries.jpg"
    And course "Course 2" tile "6" should show photo "strawberries.jpg"

    And I log out tiles

    And I log in as "admin"
    And I am on "Business Law" course homepage with editing mode on
    And I backup "Business Law" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I wait until the page is ready
    And I wait "1" seconds
    And I restore "test_backup.mbz" backup into a new course using this options:
    And I wait until the page is ready
    And I turn editing mode off
    And course "Business Law copy 1" tile "1" should show photo "blueberries.jpg"
    And course "Business Law copy 1" tile "3" should show photo "strawberries.jpg"
    And course "Business Law copy 1" tile "7" should show photo "blueberries.jpg"
    And I log out

    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | BL     | student        |
    And I log in as "student1"
    And I am on "Business Law" course homepage
    And I wait until the page is ready
    And course "Business Law" tile "1" should show photo "blueberries.jpg"
    And course "Business Law" tile "3" should show photo "strawberries.jpg"
    And course "Business Law" tile "7" should show photo "blueberries.jpg"

    And I am on "Course 2" course homepage
    And I wait until the page is ready
    And course "Course 2" tile "3" should show photo "blueberries.jpg"
    And course "Course 2" tile "6" should show photo "strawberries.jpg"

    And I log out tiles
