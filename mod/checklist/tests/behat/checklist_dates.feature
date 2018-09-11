@mod @mod_checklist @checklist @javascript
Feature: I can add dates to a checklist and they appear in the calendar.

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
      | student1 | Student   | 1        | student1@asd.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Checklist" to section "1" and I fill the form with:
      | Checklist                 | Test checklist      |
      | Introduction              | This is a checklist |
      | Add due dates to calendar | Yes                 |
    And I follow "Test checklist"
    And I press "Edit dates"
    # A date in the future (should be easy to fix in 10 years time when it fails).
    And I set the following fields to these values:
      | displaytext    | The first list item |
      | duetimedisable | 0                   |
      | duetime[day]   | 25                  |
      | duetime[month] | March               |
      | duetime[year]  | 2025                |
    And I press "Add"
    # A date in the past.
    And I set the following fields to these values:
      | displaytext    | Another list item |
      | duetimedisable | 0                 |
      | duetime[day]   | 18                |
      | duetime[month] | June              |
      | duetime[year]  | 2013              |
    And I press "Add"
    # No date for the last item.
    And I set the following fields to these values:
      | displaytext    | Third list item |
      | duetimedisable | 1               |
    And I press "Add"
    And I log out

  Scenario: When I add dates to items, they appear to the student.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then I should see "25 March 2025" in the "The first list item" "list_item"
    And ".checklist-itemdue" "css_element" should exist in the "The first list item" "list_item"
    And I should see "18 June 2013" in the "Another list item" "list_item"
    And ".checklist-itemoverdue" "css_element" should exist in the "Another list item" "list_item"

  Scenario: When I add dates to items they appear in the course calendar.
    When I log in as "student1"
    And I visit the calendar for course "C1" showing date "25 March 2025"
    Then I should see "The first list item"
    And I should not see "Another list item"
    And I should not see "Third list item"
    When I visit the calendar for course "C1" showing date "18 June 2013"
    Then I should see "Another list item"
    And I should not see "The first list item"
    And I should not see "Third list item"

  Scenario: When I disable the 'add due dates to calendar' feature, dates should not appear in the calendar.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I navigate to "Edit settings" node in "Checklist administration"
    And I set the field "Add due dates to calendar" to "No"
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I visit the calendar for course "C1" showing date "25 March 2025"
    Then I should not see "The first list item"
    When I visit the calendar for course "C1" showing date "18 June 2013"
    Then I should not see "Another list item"
