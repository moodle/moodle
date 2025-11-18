@mod @mod_folder @_file_upload @core_filepicker
Feature: Folder files can be viewed without downloading
  In order to view folder files without downloading
  As a teacher
  I need to be able to disable 'Force download of files'

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name       |
      | folder   | C1     | Folder 1   |
    And I am on the "Folder 1" "folder activity editing" page logged in as teacher1
    And I upload "lib/tests/fixtures/test.html" file to "Files" filemanager
    And I upload "lib/tests/fixtures/1.jpg" file to "Files" filemanager
    And I press "Save and return to course"

  @javascript
  Scenario Outline: Files within a folder activity is automatically downloaded
    When I am on the "Folder 1" "folder activity" page
    # As a teacher, confirm that file is downloaded automatically.
    Then following "<filename>" should download a file that:
      | Has mimetype | <mimetype> |
    # Confirm the same behaviour as a student.
    And I am on the "Folder 1" "folder activity" page logged in as student1
    And following "<filename>" should download a file that:
      | Has mimetype | <mimetype> |

    Examples:
      | filename  | mimetype   |
      | 1.jpg     | image/jpeg |
      | test.html | text/html  |

  @javascript
  Scenario Outline: Teacher can disable force download for folder activity
    Given I am on the "Folder 1" "folder activity editing" page
    And I set the following fields to these values:
      | forcedownload | 0 |
    And I press "Save and display"
    When I click on "<filename>" "link"
    # As a teacher, confirm that file is not downloaded.
    Then "<displayedtext>" "<elementtype>" should exist
    # Confirm the same behaviour as a student.
    And I am on the "Folder 1" "folder activity" page logged in as student1
    And I click on "<filename>" "link"
    And "<displayedtext>" "<elementtype>" should exist

    Examples:
      | filename  | displayedtext                   | elementtype   |
      | 1.jpg     | //img[contains(@src, '/1.jpg')] | xpath_element |
      | test.html | This is an example HTML         | text          |
