@mod @mod_forum
Feature: As a teacher I need to see an accurate list of subscribed users
  In order to see who is subscribed to a forum
  As a teacher
  I need to view the list of subscribed users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher  | Teacher   | Teacher  | teacher@example.com |
      | student1 | Student   | 1        | student.1@example.com |
      | student2 | Student   | 2        | student.2@example.com |
      | student3 | Student   | 3        | student.3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher  | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
    And the following "group members" exist:
      | user        | group |
      | student1    | G1    |
      | student2    | G2    |
    And the following "groupings" exist:
      | name        | course | idnumber |
      | Grouping 1  | C1     | GG1      |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
    And I log in as "teacher"
    And I follow "Course 1"
    And I turn editing mode on

  @javascript
  Scenario: A forced forum lists all subscribers
    When I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Forced Forum 1 |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Forced subscription |
    And I follow "Forced Forum 1"
    And I follow "Show/edit current subscribers"
    Then I should see "Student 1"
    And I should see "Teacher Teacher"
    And I should see "Student 2"
    And I should see "Student 3"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grouping" "button" in the "Add restriction..." "dialogue"
    And I set the field with xpath "//select[@name='id']" to "Grouping 1"
    And I press "Save and display"
    And I follow "Show/edit current subscribers"
    And I should see "Student 1"
    And I should see "Teacher Teacher"
    And I should not see "Student 2"
    And I should not see "Student 3"

  Scenario: A forced forum does not allow to edit the subscribers
    When I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Forced Forum 2 |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Forced subscription |
      | Visible           | Show |
    And I follow "Forced Forum 2"
    And I follow "Show/edit current subscribers"
    Then I should see "Teacher Teacher"
    And I should see "Student 1"
    And I should see "Student 2"
    And I should see "Student 3"
    And I should not see "Turn editing on"

  Scenario: A forced and hidden forum lists only teachers
    When I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Forced Forum 2 |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Forced subscription |
      | Visible           | Hide |
    And I follow "Forced Forum 2"
    And I follow "Show/edit current subscribers"
    Then I should see "Teacher Teacher"
    And I should not see "Student 1"
    And I should not see "Student 2"
    And I should not see "Student 3"

  @javascript
  Scenario: An automatic forum lists all subscribers
    When I add a "Forum" to section "1" and I fill the form with:
      | Forum name        | Forced Forum 1 |
      | Forum type        | Standard forum for general use |
      | Description       | Test forum description |
      | Subscription mode | Auto subscription |
    And I follow "Forced Forum 1"
    And I follow "Show/edit current subscribers"
    Then I should see "Student 1"
    And I should see "Teacher Teacher"
    And I should see "Student 2"
    And I should see "Student 3"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grouping" "button" in the "Add restriction..." "dialogue"
    And I set the field with xpath "//select[@name='id']" to "Grouping 1"
    And I press "Save and display"
    And I follow "Show/edit current subscribers"
    And I should see "Student 1"
    And I should see "Teacher Teacher"
    And I should not see "Student 2"
    And I should not see "Student 3"
