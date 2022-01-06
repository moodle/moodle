@report @javascript @tool_lp
Feature: See the competencies for an activity on the course competencies page.
  As a student
  In order to see only the competencies for an activity in the course competencies page.

  Background:
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Test-Framework | ID-FW1 |
    And the following lp "competencies" exist:
      | shortname | framework |
      | Test-Comp1 | ID-FW1 |
      | Test-Comp2 | ID-FW1 |
    Given the following "courses" exist:
      | shortname | fullname   | enablecompletion |
      | C1        | Course 1   | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber | completion | completionview |
      | page     | PageName1  | PageDesc1  | C1     | PAGE1    | 1          | 1              |
      | page     | PageName2  | PageDesc2  | C1     | PAGE2    | 1          | 1              |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Competencies"
    And I press "Add competencies to course"
    And "Competency picker" "dialogue" should be visible
    And I select "Test-Comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I press "Add competencies to course"
    And "Competency picker" "dialogue" should be visible
    And I select "Test-Comp2" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    And I navigate to "Edit settings" in current page administration
    And I follow "Expand all"
    And I set the field "Course competencies" to "Test-Comp1"
    And I press "Save and return to course"
    And I log out

  @javascript
  Scenario: Go to the competency course competencies page.
    When I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Competencies"
    Then I should see "Test-Comp1"
    And I should see "Test-Comp2"
    And I set the field "Filter competencies by resource or activity" to "PageName1"
    And I press the enter key
    And I should see "Test-Comp1"
    And I should not see "Test-Comp2"
    And I set the field "Filter competencies by resource or activity" to "PageName2"
    And I press the enter key
    And I should not see "Test-Comp1"
    And I should not see "Test-Comp2"
    And I should see "No competencies have been linked to this activity or resource."

  @javascript
  Scenario: None course competencies page.
    When I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "PageName1"
    Then I should see "Test page content"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "PageName1"
    Then I should see "Test page content"
