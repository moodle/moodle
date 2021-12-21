@block @block_private_files @_file_upload
Feature: The private files block allows users to store files privately in moodle on front page.
  In order to store a private file in moodle
  As a teacher
  I can upload the file to my private files area using the private files block from the front page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Private files" block
    And I log out

  Scenario: Try to view the private files block as a guest
    Given I log in as "guest"
    When I am on site homepage
    Then "Private files" "block" should not exist

  @javascript
  Scenario: Upload a file to the private files block from the frontpage
    Given I log in as "teacher1"
    And I am on site homepage
    And "Private files" "block" should exist
    And I should see "No files available" in the "Private files" "block"
    When I follow "Manage private files..."
    And I upload "blocks/private_files/tests/fixtures/testfile.txt" file to "Files" filemanager
    And I press "Save changes"
    Then I should see "testfile.txt" in the "Private files" "block"
