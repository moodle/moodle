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
    And the following "activity" exists:
      | activity    | forum                  |
      | course      | C1                     |
      | idnumber    | 0001                   |
      | name        | Test forum name        |
    And the following "mod_forum > discussions" exist:
      | forum | course | user     | name         | message                              |
      | 0001  | C1     | teacher1 | Discussion 1 | Discussion contents 1, first message |
      | 0001  | C1     | teacher1 | Discussion 2 | Discussion contents 2, first message |

  Scenario: Confirm inpage replies work
    Given I am on the "Course 1" course page logged in as student1
    When I reply "Discussion 2" post from "Test forum name" forum using an inpage reply with:
      | post | Discussion contents 1, third message |
    Then I should see "Discussion contents 1, third message"
    And I reload the page
    And I should see "Discussion contents 1, third message"

  Scenario: Confirm inpage replies work - private reply
    Given I am on the "Course 1" course page logged in as teacher1
    When I reply "Discussion 2" post from "Test forum name" forum using an inpage reply with:
      | post | Discussion contents 1, third message |
      | privatereply | 1                            |
    Then I should see "Discussion contents 1, third message"
    And I should see "This is a private reply. (Teachers and other users with the capability to view private replies can also see it.)"
    And I reload the page
    And I should see "Discussion contents 1, third message"
