@mod @mod_subsection
Feature: Users view subsections on course page
  In order to use subsections
  As an user
  I need to view subsections on course page

  Background:
    Given I enable "subsection" "mod" plugin
    And the following "users" exist:
      | username | firstname    | lastname  | email                 |
      | teacher1 | Teacher      | 1         | teacher1@example.com  |
      | student1 | Student      | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname    | category  | numsections   |
      | Course 1 | C1           | 0         | 2             |
    And the following "course enrolments" exist:
      | user        | course    | role              |
      | teacher1    | C1        | editingteacher    |
      | student1    | C1        | student           |
    And the following "activities" exist:
      | activity   | name             		| course    | idnumber | section |
      | subsection | Subsection1      		| C1        | sub1     | 1       |
      | page       | Page1 in Subsection1 | C1        | page11   | 3       |
      | subsection | Subsection2      		| C1        | sub2     | 1       |

  @javascript
  Scenario: Student can view, expand and collapse subsections on course page
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Subsection1" in the "region-main" "region"
    And I should see "Page1 in Subsection1" in the "Subsection1" "activity"
    And I click on "Collapse" "link" in the "Subsection1" "activity"
    And I should not see "Page1 in Subsection1" in the "Subsection1" "activity"
    And I click on "Expand" "link" in the "Subsection1" "activity"
    And I click on "Page1 in Subsection1" "link" in the "Subsection1" "activity"

  @javascript
  Scenario: Teacher can create activities inside subsections on course page
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    # Add an assignment to the top of Subsection1.
    And I hover "Insert an activity or resource before 'Page1 in Subsection1'" "button"
    And I press "Insert an activity or resource before 'Page1 in Subsection1'"
    And I click on "Add a new Assignment" "link" in the "Add an activity or resource" "dialogue"
    And I set the following fields to these values:
    | Assignment name | Assignment1 in Subsection1 |
    And I press "Save and return to course"
    Then I should see "Assignment1 in Subsection1" in the "Subsection1" "activity"
    # Add an assignment to the empty Subsection2.
    And I add an "assign" activity to course "Course 1" section "4" and I fill the form with:
    | Assignment name | Assignment1 in Subsection2 |
    And I should see "Assignment1 in Subsection2" in the "Subsection2" "activity"

  @javascript
  Scenario: Teacher can create activities between subsections on course page
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I hover "Insert an activity or resource before 'Subsection2'" "button"
    And I press "Insert an activity or resource before 'Subsection2'"
    And I click on "Add a new Assignment" "link" in the "Add an activity or resource" "dialogue"
    And I set the following fields to these values:
    | Assignment name | Assignment between subsections |
    And I press "Save and return to course"
    And I wait "5" seconds
    And "Assignment between subsections" "link" should appear after "Page1 in Subsection1" "text"
    And "Assignment between subsections" "link" should appear before "Subsection2" "text"
