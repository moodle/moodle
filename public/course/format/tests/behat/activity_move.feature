@core @core_course @core_courseformat
Feature: Activities can be moved between sections
  In order to rearrange my course contents
  As a teacher
  I need to move activities between sections

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course" exists:
      | fullname      | Course 1 |
      | shortname     | C1       |
      | format        | topics   |
      | coursedisplay | 0        |
      | numsections   | 5        |
      | initsections  | 1        |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name              | course | idnumber | section |
      | forum    | Test forum name   | C1     | 00001    | 1       |
      | forum    | Second forum name | C1     | 00002    | 1       |
      | forum    | Third forum name  | C1     | 00002    | 3       |
      | forum    | Fourth forum name | C1     | 00002    | 3       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Move activity step test
    When I move "Test forum name" activity to section "3"
    Then I should see "Test forum name" in the "Section 3" "section"
    And "Test forum name" "activity" should appear before "Third forum name" "activity"

  @javascript
  Scenario: The teacher can move an activity to another section using the activity action menu
    When I open "Test forum name" actions menu
    And I click on "Move" "link" in the "Test forum name" activity
    And I click on "Section 3" "link" in the "Move activity" "dialogue"
    Then  I should see "Test forum name" in the "Section 3" "section"

  @javascript
  Scenario: The teacher can reorder activities in the same section using the activity action menu
    Given "Test forum name" "activity" should appear before "Second forum name" "activity"
    When I open "Test forum name" actions menu
    And I click on "Move" "link" in the "Test forum name" activity
    And I click on "Second forum name" "link" in the "Move activity" "dialogue"
    Then  I should see "Test forum name" in the "Section 1" "section"
    And "Second forum name" "activity" should appear before "Test forum name" "activity"

  @javascript
  Scenario: The teacher can move an in the middle of a section using the activity action menu
    When I open "Test forum name" actions menu
    And I click on "Move" "link" in the "Test forum name" activity
    And I click on "Expand" "link" in the "movemodalsection3" "region"
    And I click on "Third forum name" "link" in the "Move activity" "dialogue"
    Then  I should see "Test forum name" in the "Section 3" "section"
    And "Third forum name" "activity" should appear before "Test forum name" "activity"
    And "Test forum name" "activity" should appear before "Fourth forum name" "activity"
