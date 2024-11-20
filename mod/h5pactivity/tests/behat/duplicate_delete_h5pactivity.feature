@mod @mod_h5pactivity
Feature: Duplicate and delete a h5pactivity
  In order to quickly create and delete h5p activities
  As a teacher
  I need to duplicate or delete h5pactivity inside the same course

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Duplicate and delete h5p activity
    Given the following "activities" exist:
      | activity    | course | name           | packagefilepath                      |
      | h5pactivity | C1     | H5P Activity 1 | h5p/tests/fixtures/filltheblanks.h5p |
    And I am on the "H5P Activity 1" "h5pactivity activity" page logged in as teacher1
    # Initial confirmation that no error occurs when viewing h5p activity
    And I should see "You are in preview mode."
    And I am on "Course 1" course homepage with editing mode on
    # Duplicate the h5p activity
    When I duplicate "H5P Activity 1" activity
    # Confirm that h5p activity was duplicated successfully
    Then I should see "H5P Activity 1 (copy)"
    And I am on the "H5P Activity 1 (copy)" "h5pactivity activity" page
    # Confirm there are no errors when viewing duplicate h5p activity
    And I should see "You are in preview mode."
    And I am on the "Course 1" course page
    # Delete the duplicate h5p activity
    And I delete "H5P Activity 1 (copy)" activity
    # Confirm duplicate was deleted successfully
    And I should not see "H5P Activity 1 (copy)"
    And I am on the "H5P Activity 1" "h5pactivity activity" page
    # Confirm there are no errors on the original h5p activity after deleting the duplicate
    And I should see "You are in preview mode."
