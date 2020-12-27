@core @core_user @javascript
Feature: Bulk message
  In order to communicate with my students
  As a teacher
  I need to be able to send a message to all my students

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher@example.com  |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |

  Scenario: Send a message to students from participants list
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Select all" "checkbox"
    And I set the field "With selected users..." to "Send a message"
    And I should see "Send message to 3 people"
    And I set the following fields to these values:
      | bulk-message | "Hello world!" |
    When I press "Send message to 3 people"
    Then I should see "Message sent to 3 people"
