@mod @mod_subsection
Feature: Users view subsections on course page
  In order to use subsections
  As an user
  I need to view subsections on course page

  Background:
    Given the following "users" exist:
      | username | firstname    | lastname  | email                 |
      | teacher1 | Teacher      | 1         | teacher1@example.com  |
      | student1 | Student      | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname    | category  | numsections   | initsections  |
      | Course 1 | C1           | 0         | 3             | 1             |
    And the following "course enrolments" exist:
      | user        | course    | role              |
      | teacher1    | C1        | editingteacher    |
      | student1    | C1        | student           |
    And the following "activities" exist:
      | activity   | name             		| course    | idnumber | section |
      | subsection | Subsection1      		| C1        | sub1     | 1       |
      | page       | Page1 in Subsection1 | C1        | page11   | 4       |
      | subsection | Subsection2      		| C1        | sub2     | 1       |
      | data       | New database         | C1        | data1    | 3       |
      | page       | New page             | C1        | page1    | 3       |
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
    And I hover "Insert content before 'Subsection2'" "button"
    And I press "Insert content before 'Subsection2'"
    And I click on "Activity or resource" "button" in the ".dropdown-menu.show" "css_element"
    And I click on "Add a new Assignment" "link" in the "Add an activity or resource" "dialogue"
    And I set the following fields to these values:
    | Assignment name | Assignment between subsections |
    And I press "Save and return to course"
    And I wait "5" seconds
    And "Assignment between subsections" "link" should appear after "Page1 in Subsection1" "text"
    And "Assignment between subsections" "link" should appear before "Subsection2" "text"

  @javascript
  Scenario: Teacher can create a subsection at section bottom
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Add content" "button" in the "General" "section"
    And I click on "Subsection" "link" in the ".dropdown-menu.show" "css_element"
    Then I should see "New subsection" in the "General" "section"

  @javascript
  Scenario: Teacher can create a subsection between activities
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I hover "Insert content before 'New page'" "button"
    And I press "Insert content before 'New page'"
    And I click on "Subsection" "link" in the ".dropdown-menu.show" "css_element"
    Then I should see "New subsection" in the "Section 3" "section"
    And "New database" "text" should appear before "New subsection" "text"
    And "New subsection" "text" should appear before "New page" "text"

  @javascript
  Scenario: Teacher can create an activity at section bottom
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Add content" "button" in the "General" "section"
    And I click on "Activity or resource" "button" in the ".dropdown-menu.show" "css_element"
    And I click on "Add a new Forum" "link" in the "Add an activity or resource" "dialogue"
    And I set the field "Forum name" to "New forum"
    And I press "Save and return to course"
    Then I should see "New forum" in the "General" "section"

  @javascript
  Scenario: Teacher can create an activity between activities
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I hover "Insert content before 'New page'" "button"
    And I press "Insert content before 'New page'"
    And I click on "Activity or resource" "button" in the ".dropdown-menu.show" "css_element"
    And I click on "Add a new Forum" "link" in the "Add an activity or resource" "dialogue"
    And I set the field "Forum name" to "New forum"
    And I press "Save and return to course"
    Then I should see "New forum" in the "Section 3" "section"
    And "New database" "text" should appear before "New forum" "text"
    And "New forum" "text" should appear before "New page" "text"

  @javascript
  Scenario: Teacher can subsections after moving the parent section
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I open section "1" edit menu
    And I click on "Move" "link" in the "Section 1" "section"
    And I click on "Section 3" "link" in the "Move section" "dialogue"
    And "Section 1" "section" should appear after "Section 3" "section"
    When I click on "Add content" "button" in the "Section 1" "section"
    And I click on "Subsection" "link" in the ".dropdown-menu.show" "css_element"
    Then I should see "New subsection" in the "Section 1" "section"
