@mod @mod_forum @forumreport @forumreport_summary
Feature: Message users in the summary report
  In order to encourage users to participate
  As a teacher
  I should be able to send messages to those who are not taking part

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | name   | description     | course | idnumber |
      | forum    | forum1 | C1 first forum  | C1     | forum1   |
    And the following forum discussions exist in course "Course 1":
      | user     | forum  | name        | message         |
      | teacher1 | forum1 | discussion1 | t1 earliest     |
      | teacher1 | forum1 | discussion2 | t1 between      |
      | student1 | forum1 | discussion3 | s1 latest       |
    And the following forum replies exist in course "Course 1":
      | user     | forum  | discussion  | message         |
      | teacher1 | forum1 | discussion1 | t1 between      |
      | teacher1 | forum1 | discussion2 | t1 latest       |
      | student1 | forum1 | discussion1 | s1 earliest     |

  @javascript
  Scenario: Message some users
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student3 | C1     | student |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "forum1"
    And I navigate to "Summary report" in current page administration
    And I click on "Select 'Student 1'" "checkbox"
    And I click on "Select 'Student 3'" "checkbox"
    And I set the field "With selected users..." to "Send a message"
    And I set the field "bulk-message" to "blah blah"
    And I click on "Send message to 2 people" "button"
    Then I should see "Message sent to 2 people"
    And I log out
    And I log in as "student1"
    And I should see "1" in the "//*[@title='Toggle messaging drawer']/../*[@data-region='count-container']" "xpath_element"
    And I log out
    And I log in as "student3"
    And I should see "1" in the "//*[@title='Toggle messaging drawer']/../*[@data-region='count-container']" "xpath_element"
    And I log out
    And I log in as "student2"
    And I should not see "1" in the "//*[@title='Toggle messaging drawer']/../*[@data-region='count-container']" "xpath_element"

  @javascript
  Scenario: Message all users
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "forum1"
    And I navigate to "Summary report" in current page administration
    And I click on "Select all" "checkbox"
    And I set the field "With selected users..." to "Send a message"
    Then I should see "Send message to 3 people"

  @javascript
  Scenario: Ensure no message options when messaging is disabled
    Given I log in as "admin"
    And I set the following administration settings values:
      | messaging | 0 |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "forum1"
    And I navigate to "Summary report" in current page administration
    Then I should not see "With selected users..."
    And I should not see "Select all"
