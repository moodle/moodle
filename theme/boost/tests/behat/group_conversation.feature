@javascript @theme_boost
Feature: Create conversations for course's groups in Boost theme
  In order to manage a course group in a course
  As a user
  I need to be able to ensure group conversations reflect the memberships of course groups

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student0 | Student   | 0        | student0@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
    And the following "course enrolments" exist:
      | user     | course | role |
      | teacher1 | C1     | editingteacher |
      | student0 | C1     | student |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | student3 | C1     | student |
      | student4 | C1     | student |
    And the following "groups" exist:
      | name    | course | idnumber | enablemessaging |
      | Group 1 | C1     | G1       | 1               |
      | Group 2 | C1     | G2       | 1               |
      | Group 3 | C1     | G3       | 0               |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1 |
      | student0 | G1 |
      | student1 | G1 |
      | student2 | G1 |
      | student3 | G1 |
      | teacher1 | G2 |
      | teacher1 | G3 |
      | student0 | G3 |

  Scenario: View group conversation's participants numbers
    Given I log in as "teacher1"
    Then I open messaging
    And I select "Group 1" conversation in messaging
    And "5 participants" "group_message_header" should exist
    And I go back in "view-conversation" message drawer
    And I select "Group 2" conversation in messaging
    And "1 participants" "group_message_header" should exist

  Scenario: View group conversation's participants list
    Given I log in as "teacher1"
    Then I open messaging
    # Check Group 1 participants list.
    And I select "Group 1" conversation in messaging
    And I open messaging information
    And "Teacher 1" "group_message_member" should not exist
    And "Student 0" "group_message_member" should exist
    And "Student 1" "group_message_member" should exist
    And "Student 2" "group_message_member" should exist
    And "Student 3" "group_message_member" should exist
    And "Student 4" "group_message_member" should not exist
    And I go back in "group-info-content-container" message drawer
    And I go back in "view-conversation" message drawer
    # Check Group 2 participants list.
    And I select "Group 2" conversation in messaging
    And I open messaging information
    And "Teacher 1" "group_message_member" should not exist
    And "No participants" "group_message_member" should exist
    And "Student 4" "group_message_member" should not exist

  Scenario: Check group conversation members are synced when a new group member is added
    Given I log in as "teacher1"
    Then I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I add "Student 4 (student4@example.com)" user to "Group 1" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I open messaging
    And I select "Group 1" conversation in messaging
    And "6 participants" "group_message_header" should exist
    And I open messaging information
    And "Student 4" "group_message_member" should exist
    And I go back in "group-info-content-container" message drawer
    And I go back in "view-conversation" message drawer
    And I select "Group 2" conversation in messaging
    And "2 participants" "group_message_header" should exist
    And I open messaging information
    And "No participants" "group_message_member" should not exist
    And "Student 4" "group_message_member" should exist
