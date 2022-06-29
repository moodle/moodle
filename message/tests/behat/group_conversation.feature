@core @core_message @javascript
Feature: Create conversations for course's groups
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
    And the following config values are set as admin:
      | messaging        | 1 |
      | messagingminpoll | 1 |

  Scenario: Group conversations are restricted to members
    Given I log in as "teacher1"
    Then I open messaging
    And I open the "Group" conversations list
    And "Group 1" "core_message > Message" should exist
    And "Group 2" "core_message > Message" should exist
    And "Group 3" "core_message > Message" should not exist
    And I log out
    And I log in as "student1"
    And I open messaging
    And I open the "Group" conversations list
    And "Group 1" "core_message > Message" should exist
    And "Group 2" "core_message > Message" should not exist
    And "Group 3" "core_message > Message" should not exist

  Scenario: View group conversation's participants numbers
    Given I log in as "teacher1"
    Then I open messaging
    And I open the "Group" conversations list
    And I select "Group 1" conversation in messaging
    And I should see "5 participants" in the "Group 1" "core_message > Message header"
    And I go back in "view-conversation" message drawer
    And I select "Group 2" conversation in messaging
    And I should see "1 participants" in the "Group 2" "core_message > Message header"

  Scenario: View group conversation's participants list
    Given I log in as "teacher1"
    Then I open messaging
    And I open the "Group" conversations list
    # Check Group 1 participants list.
    And I select "Group 1" conversation in messaging
    And I open messaging information
    And "Teacher 1" "core_message > Message member" should not exist
    And "Student 0" "core_message > Message member" should exist
    And "Student 1" "core_message > Message member" should exist
    And "Student 2" "core_message > Message member" should exist
    And "Student 3" "core_message > Message member" should exist
    And "Student 4" "core_message > Message member" should not exist
    And I go back in "group-info-content-container" message drawer
    And I go back in "view-conversation" message drawer
    # Check Group 2 participants list.
    And I select "Group 2" conversation in messaging
    And I open messaging information
    And "Teacher 1" "core_message > Message member" should not exist
    And "No participants" "core_message > Message member" should exist
    And "Student 4" "core_message > Message member" should not exist

  Scenario: Check group conversation members are synced when a new group member is added
    Given I log in as "teacher1"
    Then I am on the "Course 1" "groups" page
    And I add "Student 4 (student4@example.com)" user to "Group 1" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I open messaging
    And I open the "Group" conversations list
    And I select "Group 1" conversation in messaging
    And I should see "6 participants" in the "Group 1" "core_message > Message header"
    And I open messaging information
    And "Student 4" "core_message > Message member" should exist
    And I go back in "group-info-content-container" message drawer
    And I go back in "view-conversation" message drawer
    And I select "Group 2" conversation in messaging
    And I should see "2 participants" in the "Group 2" "core_message > Message header"
    And I open messaging information
    And "No participants" "core_message > Message member" should not exist
    And "Student 4" "core_message > Message member" should exist
