@mod @mod_forum
Feature: Posting to all groups in a separate group discussion is restricted to users with access to all groups
  In order to post to all groups in a forum with separate groups
  As a teacher
  I need to have the accessallgroups capability or be a member of all of the groups

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | noneditor1 | Non-editing teacher | 1 | noneditor1@example.com |
      | noneditor2 | Non-editing teacher | 2 | noneditor2@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | noneditor1 | C1 | teacher |
      | noneditor2 | C1 | teacher |
      | student1 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group A | C1 | G1 |
      | Group B | C1 | G2 |
      | Group C | C1 | G3 |
    And the following "group members" exist:
      | user | group |
      | teacher1 | G1 |
      | teacher1 | G2 |
      | noneditor1 | G1 |
      | noneditor1 | G2 |
      | noneditor1 | G3 |
      | noneditor2 | G1 |
      | noneditor2 | G2 |
      | student1 | G1 |
      | student1 | G2 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Standard forum name |
      | Forum type | Standard forum for general use |
      | Description | Standard forum description |
      | Group mode | Separate groups |
    And I log out

  Scenario: Teacher with accessallgroups can post in all groups
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Standard forum name"
    When I click on "Add a new discussion topic" "button"
    Then the "Group" select box should contain "All participants"
    And the "Group" select box should contain "Group A"
    And the "Group" select box should contain "Group B"
    And I should see "Post a copy to all groups"

  @javascript
  Scenario: Teacher in all groups but without accessallgroups can only post in their groups
    And I log in as "admin"
    And I set the following system permissions of "Non-editing teacher" role:
      | moodle/site:accessallgroups | Prohibit |
    And I log out
    Given I log in as "noneditor1"
    And I follow "Course 1"
    And I follow "Standard forum name"
    When I click on "Add a new discussion topic" "button"
    Then the "Group" select box should not contain "All participants"
    And the "Group" select box should contain "Group A"
    And the "Group" select box should contain "Group B"
    And I should see "Post a copy to all groups"

  @javascript
  Scenario: Teacher in some groups and without accessallgroups can only post in their groups
    And I log in as "admin"
    And I set the following system permissions of "Non-editing teacher" role:
      | moodle/site:accessallgroups | Prohibit |
    And I log out
    Given I log in as "noneditor1"
    And I follow "Course 1"
    And I follow "Standard forum name"
    When I click on "Add a new discussion topic" "button"
    Then the "Group" select box should not contain "All participants"
    And the "Group" select box should contain "Group A"
    And the "Group" select box should contain "Group B"
    And I should see "Post a copy to all groups"
