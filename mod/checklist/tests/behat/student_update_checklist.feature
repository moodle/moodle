@mod @mod_checklist @checklist
Feature: A student can update their progress in a checklist

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
    And I log out

  @javascript
  Scenario: When a student ticks/unticks items on a checklist their progress is updated
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Test checklist"
    Then I should see "0%" in the "#checklistprogressrequired" "css_element"
    And I should see "0%" in the "#checklistprogressall" "css_element"
    # Tick item 2.
    When I click on "Checklist required item 2" "text"
    Then I should see "33%" in the "#checklistprogressrequired" "css_element"
    And I should see "20%" in the "#checklistprogressall" "css_element"
    # Tick item 3.
    When I click on "Checklist required item 3" "text"
    Then I should see "67%" in the "#checklistprogressrequired" "css_element"
    And I should see "40%" in the "#checklistprogressall" "css_element"
    # Untick item 2.
    When I click on "Checklist required item 2" "text"
    Then I should see "33%" in the "#checklistprogressrequired" "css_element"
    And I should see "20%" in the "#checklistprogressall" "css_element"
    # Untick item 3.
    When I click on "Checklist required item 3" "text"
    Then I should see "0%" in the "#checklistprogressrequired" "css_element"
    And I should see "0%" in the "#checklistprogressall" "css_element"
    # Tick item 4.
    When I click on "Checklist optional item 4" "text"
    Then I should see "0%" in the "#checklistprogressrequired" "css_element"
    And I should see "20%" in the "#checklistprogressall" "css_element"

  @javascript
  Scenario: When a student updates their progress and then returns to the page their progress is remembered
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    When I click on "Checklist required item 1" "text"
    And I click on "Checklist required item 3" "text"
    And I click on "Checklist optional item 4" "text"
    # Make sure the AJAX request has finished.
    And I wait "2" seconds
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then the following fields match these values:
      | Checklist required item 1 | 1 |
      | Checklist required item 2 | 0 |
      | Checklist required item 3 | 1 |
      | Checklist optional item 4 | 1 |
      | Checklist optional item 5 | 0 |
    # Note - the rounding here is inconsistent between JS & PHP, but I am cautious about fixing it.
    And I should see "66%" in the "#checklistprogressrequired" "css_element"
    And I should see "60%" in the "#checklistprogressall" "css_element"

  @javascript
  Scenario: When a student updates their progress then the teacher can see that progress
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    When I click on "Checklist required item 1" "text"
    And I click on "Checklist required item 3" "text"
    And I click on "Checklist optional item 4" "text"
    # Make sure the AJAX request has finished.
    And I wait "2" seconds
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    When I follow "View progress"
    Then ".level0-checked.c1" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c3" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c4" "css_element" should exist in the "Student 1" "table_row"
    And ".level0-checked.c2" "css_element" should not exist in the "Student 1" "table_row"
    And ".level0-checked.c5" "css_element" should not exist in the "Student 1" "table_row"

  @javascript
  Scenario: Checklists can mark themselves as complete.
    Given I log in as "admin"
    And I set the following administration settings values:
      | enablecompletion | 1 |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" node in "Course administration"
    And I expand all fieldsets
    And I set the field "Enable completion tracking" to "Yes"
    # For Moodle 2.8 and below, this should read "Save changes"
    And I press "Save and display"
    And I follow "Test checklist"
    And I navigate to "Edit settings" node in "Checklist administration"
    And I expand all fieldsets
    And I set the field "Completion tracking" to "Show activity as complete when conditions are met"
    And I set the field "completionpercentenabled" to "1"
    And I set the field "completionpercent" to "100"
    And I press "Save and return to course"
    And "Student 1" user has not completed "Test checklist" activity
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I click on "Checklist required item 1" "text"
    And I click on "Checklist required item 2" "text"
    And I click on "Checklist required item 3" "text"
    And I should see "100%" in the "#checklistprogressrequired" "css_element"
    And I wait "2" seconds
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    Then "Student 1" user has completed "Test checklist" activity
