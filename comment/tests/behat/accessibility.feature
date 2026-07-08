@core_comment @javascript
Feature: Accessible comment area
  In order to use comments accessibly
  As a user with commenting rights
  I need the comment textarea to use a native placeholder and comments to be posted correctly

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry     | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Comments" block
    And I turn editing mode off

  Scenario: Comment textarea uses a native placeholder attribute
    When I am on "Course 1" course homepage
    Then the "placeholder" attribute of "Add a comment..." "field" should contain "Add a comment"

  @accessibility
  Scenario: Posting a comment adds it to the list
    Given I am on "Course 1" course homepage
    When I set the field "Add a comment..." to "Hello from Behat"
    And I click on "Save comment" "link"
    Then I should see "Hello from Behat"
    And the "Comments" "block" should meet accessibility standards with "best-practice" extra tests
    And I reload the page
    And the "Comments" "block" should meet accessibility standards with "best-practice" extra tests

  Scenario: After posting a comment the textarea is empty and the placeholder reappears
    Given I am on "Course 1" course homepage
    When I set the field "Add a comment..." to "Another comment"
    And I click on "Save comment" "link"
    And I should see "Another comment"
    Then the field "Add a comment..." matches value ""
