@block @block_private_files @_file_upload @javascript
Feature: The private files block allows users to store files privately in moodle on course page
  In order to store a private file in moodle
  As a teacher
  I can upload the file to my private files area using the private files block in a course

  Scenario: Upload a file to the private files block from a course
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | Course       | C1        | course-view-*   | side-pre      |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I should see "No files available" in the "Private files" "block"
    When I follow "Manage private files..."
    And I upload "blocks/private_files/tests/fixtures/testfile.txt" file to "Files" filemanager
    And I press "Save changes"
    Then I should see "testfile.txt" in the "Private files" "block"
