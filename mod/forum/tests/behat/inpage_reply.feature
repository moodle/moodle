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
      | section     | 1                      |
      | idnumber    | 0001                   |
      | name        | Test forum name        |
      | description | Test forum description |
    And the following "mod_forum > discussions" exist:
      | forum | course | user     | name         | message                              |
      | 0001  | C1     | teacher1 | Discussion 1 | Discussion contents 1, first message |
      | 0001  | C1     | teacher1 | Discussion 2 | Discussion contents 2, first message |

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
