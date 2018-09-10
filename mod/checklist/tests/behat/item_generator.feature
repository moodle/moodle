@mod @mod_checklist @checklist
Feature: Check the item generator generates items as expected

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
      | Checklist    | Test checklist      |
      | Introduction | This is a checklist |
    And I log out

  Scenario: I add some items with a generator and the student can see them
    Given the following items exist in checklist "Test checklist":
      | text            | required | duetime       |
      | The first item  | required | 21 April 2018 |
      | The second item | optional |               |
      | The third item  | heading  |               |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then "The first item" "text" should appear before "The second item" "text"
    And "The second item" "text" should appear before "The third item" "text"
    And I should see "All items"
    And I should see "Required items"
    And "label.itemoptional" "css_element" should appear after "The first item" "text"
    And "label.itemheading" "css_element" should appear after "label.itemoptional" "css_element"
    And I should see "21 April 2018" in the "The first item" "list_item"

  Scenario: I add some items in two batches, with only the required elements, using the generator and the student can see them
    Given the following items exist in checklist "Test checklist":
      | text            |
      | The first item  |
      | The second item |
      | The third item  |
    And the following items exist in checklist "Test checklist":
      | text            |
      | The fourth item |
      | The last item   |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then "The first item" "text" should appear before "The second item" "text"
    And "The second item" "text" should appear before "The third item" "text"
    And "The third item" "text" should appear before "The fourth item" "text"
    And "The fourth item" "text" should appear before "The last item" "text"

  @javascript
  Scenario: I add some items to a student checklist and mark them as complete and the student sees them as complete
    Given the following items exist in checklist "Test checklist":
      | text            |
      | The first item  |
      | The second item |
      | The third item  |
      | The fourth item |
      | The last item   |
    And the following items are checked off in checklist "Test checklist" for user "student1":
      | itemtext        | studentmark |
      | The second item | yes         |
      | The fourth item | yes         |
      | The first item  | no          |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    # This test seems to fail without the @javascript tag.
    Then the following fields match these values:
      | The first item  | 0 |
      | The second item | 1 |
      | The third item  | 0 |
      | The fourth item | 1 |
      | The last item   | 0 |

  Scenario: I add some items to a student checklist and mark them as complete and the student sees them as complete
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I follow "Edit settings"
    And I set the field "Updates by" to "Teacher only"
    And I press "Save and return to course"
    And I log out
    And the following items exist in checklist "Test checklist":
      | text            |
      | The first item  |
      | The second item |
      | The third item  |
      | The fourth item |
      | The last item   |
    And the following items are checked off in checklist "Test checklist" for user "student1":
      | itemtext        | teachermark | teachername |
      | The second item | yes         | teacher1    |
      | The fourth item | yes         | admin       |
      | The first item  | no          | teacher1    |
      | The third item  | none        | teacher1    |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then ".teachermarkyes" "css_element" should exist in the "The second item" "list_item"
    And ".teachermarkyes" "css_element" should exist in the "The fourth item" "list_item"
    And ".teachermarkno" "css_element" should exist in the "The first item" "list_item"
    And ".teachermarkundecided" "css_element" should exist in the "The third item" "list_item"
    And ".teachermarkundecided" "css_element" should exist in the "The last item" "list_item"
