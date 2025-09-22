@mod @mod_forum
Feature: Changes to the subscription mode of a forum can change subcribers of a forum

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher  | Teacher   | Tom      | teacher@example.com   |
      | student1 | Student   | 1        | student.1@example.com |
      | student2 | Student   | 2        | student.2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activity" exists:
      | activity         | forum                  |
      | idnumber         | f01                    |
      | course           | C1                     |
      | name             | Test forum name        |
      | type             | general                |
      | forcesubscribe   | 1                      |

  Scenario: A change from Forced subscription to Auto subcription causes all participants to be subscribed
    Given I am on the "Test forum name" "forum activity" page logged in as teacher
    And I navigate to "Subscriptions" in current page administration
    When I select "Auto subscription" from the "Subscription mode" singleselect
    Then I should not see "There are no subscribers yet for this forum."
    And I navigate to "Subscriptions" in current page administration
    And the following should exist in the "subscribers-table" table:
      | Full name | Email address |
      | Student 1 | student.1@example.com |
      | Student 2 | student.2@example.com |
      | Teacher Tom | teacher@example.com |
