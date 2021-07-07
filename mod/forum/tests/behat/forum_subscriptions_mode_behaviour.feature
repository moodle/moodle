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
      | intro            | Test forum description |
      | name             | Test forum name        |
      | type             | general                |
      | forcesubscribe   | 1                      |
    And I log in as "teacher"
    And I am on "Course 1" course homepage

  Scenario: A change from Forced subscription to Auto subcription causes all participants to be subscribed
    Given I follow "Test forum name"
    And I navigate to "Subscription mode > Auto subscription" in current page administration
    When I navigate to "Show/edit current subscribers" in current page administration
    Then I should not see "There are no subscribers yet for this forum"
    And I should see "Student 1"
    And I should see "student.1@example.com"
    And I should see "Student 2"
    And I should see "student.2@example.com"
    And I should see "Teacher Tom"
    And I should see "teacher@example.com"
