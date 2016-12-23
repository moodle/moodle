@block @block_private_files @_file_upload @javascript
Feature: The private files block allows users to store files privately in moodle on dashboard
  In order to store a private file in moodle
  As a user
  I can upload the file to my private files area using the private files block on the dashboard

  Scenario: Upload a file to the private files block from the dashboard
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And I log in as "teacher1"
    And "Private files" "block" should exist
    And I should see "No files available" in the "Private files" "block"
    When I follow "Manage private files..."
    And I upload "blocks/private_files/tests/fixtures/testfile.txt" file to "Files" filemanager
    And I press "Save changes"
    Then I should see "testfile.txt" in the "Private files" "block"
