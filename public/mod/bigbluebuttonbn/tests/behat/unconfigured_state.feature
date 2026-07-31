@mod @mod_bigbluebuttonbn
Feature: BigBlueButton behaves gracefully when the server is not configured
  In order to avoid confusing errors when BBB has no server configured
  As a user
  I want to see informative messages instead of error pages or broken redirects

  Background:
    Given I enable "bigbluebuttonbn" "mod" plugin
    And the BigBlueButton server is not configured
    And the following "course" exists:
      | fullname    | Test course |
      | shortname   | C1          |
      | category    | 0           |
      | format      | topics      |
      | numsections | 1           |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity        | course | name     | type |
      | bigbluebuttonbn | C1     | BBB Room | 0    |

  Scenario: Admin visiting an unconfigured BBB activity sees a neutral page with a settings link
    When I am on the "BBB Room" "bigbluebuttonbn activity" page logged in as "admin"
    Then I should see "BigBlueButton is not yet configured"
    And I should see "You can complete the setup in Site administration"
    And "BigBlueButton settings" "link" should exist

  Scenario: Teacher visiting an unconfigured BBB activity sees a contact admin message
    When I am on the "BBB Room" "bigbluebuttonbn activity" page logged in as "teacher1"
    Then I should see "BigBlueButton is not yet configured"
    And I should see "Please contact your site administrator to complete the setup"
    And "BigBlueButton settings" "link" should not exist

  Scenario: Student visiting an unconfigured BBB activity sees a contact teacher message
    When I am on the "BBB Room" "bigbluebuttonbn activity" page logged in as "student1"
    Then I should see "BigBlueButton is not yet configured"
    And I should see "Please contact your teacher"
    And "BigBlueButton settings" "link" should not exist

  @javascript
  Scenario: Teacher sees BigBlueButton grayed out in the activity chooser when the server is not configured
    Given I log in as "teacher1"
    And I am on "Test course" course homepage
    And I turn editing mode on
    And I change window size to "large"
    And I click on "Add content" "button" in the "New section" "section"
    When I click on "Activity or resource" "button" in the "New section" "section"
    Then I should see "BigBlueButton" in the "all" "core_course > Activity chooser tab"
    And "Add a new BigBlueButton" "link" should not exist in the "Add an activity or resource" "dialogue"

  @javascript
  Scenario: Admin sees BigBlueButton grayed out in the activity chooser with a settings hint when the server is not configured
    Given I log in as "admin"
    And I am on "Test course" course homepage
    And I turn editing mode on
    And I change window size to "large"
    And I click on "Add content" "button" in the "New section" "section"
    When I click on "Activity or resource" "button" in the "New section" "section"
    Then I should see "BigBlueButton" in the "all" "core_course > Activity chooser tab"
    And "Add a new BigBlueButton" "link" should not exist in the "Add an activity or resource" "dialogue"
