@mod @mod_lesson
Feature: Display the course linear navigation in the lesson pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in lesson pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname | enablelinearnav |
      | Course1  | C1        | 0               |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student  | C1     | student        |
      | teacher  | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name    | course | idnumber  |
      | lesson   | Lesson1 | C1     | lesson1   |
      | page     | Page1   | C1     | page1     |
    Given the following "mod_lesson > page" exist:
      | lesson  | qtype     | title         | content      |
      | Lesson1 | truefalse | Test question | Test content |
    And the following "mod_lesson > answers" exist:
      | page          | answer | jumpto    | score |
      | Test question | right  | Next page | 1     |
      | Test question | wrong  | This page | 0     |
    And I am on the "Lesson1" "lesson activity editing" page logged in as "teacher"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Link to next activity                  | Page1 |
      | Provide option to try a question again | Yes   |
      | Maximum number of tries per question   | 2     |
      | Allow student review                   | Yes   |
    And I press "Save and display"
    And I am on the "Course1" "course editing" page
    And I expand all fieldsets
    And I set the field "Enable linear navigation" to "Yes"
    And I press "Save and display"

  @javascript
  Scenario: As a student I should see the course linear navigation in lesson pages that allow it
    When I am on the "Lesson1" "lesson activity" page logged in as "student"
    Then the course linear navigation should be visible
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And the course linear navigation should be visible
    And I press "Yes, I'd like to try again"
    And the course linear navigation should be visible
    And I set the following fields to these values:
      | right | 1 |
    And I press "Submit"
    And I should see "Congratulations - end of lesson reached"
    And the course linear navigation should be visible
    And I should see "Review lesson"
    And I should not see "Go to Page1"
    And I should not see "Return to Course1"
    And I should not see "View grades"
    And I follow "Review lesson"
    And the course linear navigation should be visible

  @javascript
  Scenario: As a teacher I should see the course linear navigation in lesson pages that allow it
    When I am on the "Lesson1" "lesson activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    # Edition pages.
    But I click on "Edit lesson" "button"
    And the course linear navigation should not be visible
    And I select edit type "Expanded"
    And I follow "Import questions"
    And the course linear navigation should not be visible
    And I am on the "Lesson1" "lesson activity" page
    And I click on "Edit lesson" "button"
    And I click on "Delete page: Test question" "link"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I click on "Edit page contents" "button"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I click on "Grade essays" "button"
    And the course linear navigation should not be visible
    # Reports page.
    And I navigate to "Reports" in current page administration
    And the course linear navigation should not be visible
    # Overrides page.
    And I navigate to "Overrides" in current page administration
    And the course linear navigation should not be visible
    And I follow "Add user override"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Override user           | Student 1 |
      | Allow multiple attempts | Yes       |
    And I press "Save"
    And I click on "Delete" "link" in the "Student 1" "table_row"
    And the course linear navigation should not be visible

  Scenario: The Link to next activity setting is hidden when linear navigation is enabled
    Given I am on the "Lesson1" "lesson activity editing" page logged in as "teacher"
    When I expand all fieldsets
    Then I should see "Appearance"
    And I should not see "Link to next activity"

  Scenario: The Link to next activity setting is shown when linear navigation is disabled
    Given I am on the "Course1" "course editing" page logged in as "teacher"
    And I expand all fieldsets
    And I set the field "Enable linear navigation" to "No"
    And I press "Save and display"
    When I am on the "Lesson1" "lesson activity editing" page
    And I expand all fieldsets
    Then I should see "Link to next activity"
    # Check that the links to next activity are shown in the lesson pages when linear navigation is disabled.
    And I am on the "Lesson1" "lesson activity" page logged in as "student"
    And I set the following fields to these values:
      | right | 1 |
    And I press "Submit"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Go to Page1"
    And I should see "Return to Course1"
    And I should see "View grades"
