@mod @mod_forum @javascript
Feature: Students can reply to a discussion in page.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 |
      | Message | Discussion contents 1, first message |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 |
      | Message | Discussion contents 2, first message |
    And I log out

  Scenario: Confirm inpage replies work
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    Given I reply "Discussion 2" post from "Test forum name" forum using an inpage reply with:
      | post | Discussion contents 1, third message |
    Then I should see "Discussion contents 1, third message"
    When I reload the page
    Then I should see "Discussion contents 1, third message"

  Scenario: Confirm inpage replies work - private reply
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I reply "Discussion 2" post from "Test forum name" forum using an inpage reply with:
      | post | Discussion contents 1, third message |
      | privatereply | 1                            |
    Then I should see "Discussion contents 1, third message"
    Then I should see "This is a private reply. (Teachers and other users with the capability to view private replies can also see it.)"
    When I reload the page
    Then I should see "Discussion contents 1, third message"
