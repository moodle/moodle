@block @block_myprofile
Feature: The logged in user block allows users to view their profile information in an activity
  In order to enable the logged in user block in an activity
  As a teacher
  I can add the logged in user block to an activity and view my information

  Scenario: View the logged in user block by a user in an activity
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | One | teacher1@example.com | T1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name           | intro                 |
      | page     | C1     | page1    | Test page name | Test page description |
    And the following "blocks" exist:
      | blockname | contextlevel    | reference | pagetypepattern | defaultregion |
      | myprofile | Activity module | page1     | mod-page-*      | side-pre      |
    When I am on the "Test page name" "page activity" page logged in as teacher1
    Then I should see "Teacher One" in the "Logged in user" "block"
