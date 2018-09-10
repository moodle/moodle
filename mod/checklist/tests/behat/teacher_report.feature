@mod @mod_checklist @checklist
Feature: Teachers can view student's progress

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
      | Updates by   | Student only        |
    And the following items exist in checklist "Test checklist":
      | text                      | required |
      | Checklist required item 1 | required |
      | Checklist required item 2 | required |
      | Checklist required item 3 | required |
      | Checklist optional item 4 | optional |
      | Checklist optional item 5 | optional |
    And the following items are checked off in checklist "Test checklist" for user "student1":
      | itemtext                  | studentmark |
      | Checklist required item 1 | yes         |
      | Checklist required item 2 | yes         |
      | Checklist optional item 5 | yes         |
    And I log out

  Scenario: A teacher can view a student's progress in a report
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    When I follow "View progress"
    Then ".level0-checked.c1" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c2" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c3" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c4" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c5" "css_element" should exist in the "Student 1" "table_row"

  @javascript
  Scenario: A teacher can show/hide optional items in a report
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I follow "View progress"
    When I click on "Hide optional items" "link_or_button"
    Then I should see "Checklist required item 1"
    And I should see "Checklist required item 2"
    And I should see "Checklist required item 3"
    And I should not see "Checklist optional item 4"
    And I should not see "Checklist optional item 5"
    And ".level0-checked.c1" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c2" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c3" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c4" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c5" "css_element" should not exist in the "Student 1" "table_row"
    # Re-show optional items.
    When I click on "Show optional items" "link_or_button"
    Then I should see "Checklist required item 1"
    And I should see "Checklist required item 2"
    And I should see "Checklist required item 3"
    And I should see "Checklist optional item 4"
    And I should see "Checklist optional item 5"
    And ".level0-checked.c1" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c2" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c3" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c4" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c5" "css_element" should exist in the "Student 1" "table_row"

  @javascript
  Scenario: A teacher can switch to progress bars and back again
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I follow "View progress"
    When I press "Show progress bars"
    Then I should not see "Checklist required item 1"
    And I should not see "Checklist optional item 5"
    And I should see "60%" in the ".checklist_percentcomplete" "css_element"
    # Re-show full details
    When I press "Show full details"
    Then I should see "Checklist required item 1"
    And I should see "Checklist optional item 5"
    And ".level0-checked.c1" "css_element" should exist in the "Student 1" "table_row"

  @javascript
  Scenario: A teacher can view the full details for a student's checklist
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I follow "View progress"
    And I click on "View progress for this user" "link"
    Then I should see "Checklist for Student 1"
    And the following fields match these values:
      | Checklist required item 1 | 1 |
      | Checklist required item 2 | 1 |
      | Checklist required item 3 | 0 |
      | Checklist optional item 4 | 0 |
      | Checklist optional item 5 | 1 |
    And the "Checklist required item 1" "checkbox" should be disabled
    And the "Checklist optional item 4" "checkbox" should be disabled
    And I should see "60%" in the "#checklistprogressall" "css_element"
    # Check the 'toggle names & dates' feature, whilst we're here.
    When I press "Toggle names & dates"
    Then ".itemuserdate" "css_element" should exist in the "Checklist required item 1" "list_item"
    Then ".itemuserdate" "css_element" should not exist in the "Checklist required item 3" "list_item"
    When I press "Toggle names & dates"
    Then ".itemuserdate" "css_element" should not exist in the "Checklist required item 1" "list_item"

  @javascript
  Scenario: A teacher can add a comment to a checklist that a student can then view
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I follow "View progress"
    And I click on "View progress for this user" "link"
    When I press "Add comments"
    # Add a comment to item 2 in the checklist.
    And I set the field with xpath "//*[@id='checklistouter']/li[2]/input[@type='text']" to "This is a comment"
    # Match the lower-case button name, rather than 'Save' which also matches a hidden button.
    And I press "save"
    Then I should see "Teacher 1: This is a comment" in the "Checklist required item 2" "list_item"
    # Check the student can also see it.
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then I should see "Teacher 1: This is a comment" in the "Checklist required item 2" "list_item"
