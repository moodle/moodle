@core @core_grades
Feature: Hidden grade items should be hidden when grade category is locked, but should be visible in overridden category
  In order to verify existing grades items display as expected
  As an teacher
  I need to modify grade items and grade categories
  I need to ensure existing grades display in an expected manner

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 |
      | student1 | Student | 1 | student1@example.com | s1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "admin"
    And I press "Add category"
    And I set the following fields to these values:
      | Category name | Test locked category |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Hidden item |
      | Hidden | 1 |
      | Grade category | Test locked category |
    And I press "Save changes"
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I turn editing mode on
    And I give the grade "50.00" to the user "Student 1" for the grade item "Hidden item"
    And I press "Save changes"
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the following settings for grade item "Test locked category":
      | Locked | 1 |

  Scenario: Hidden grade items in locked category is hidden for teacher
    Given I am on the "Course 1" "grades > User report > View" page logged in as teacher1
    And I select "Myself" from the "View report as" singleselect
    When I select "Student 1" from the "Select all or one user" singleselect
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Range | Percentage | Contribution to course total |
      | Test locked category total | 100.00 % | 50.00 | 0–100 | 50.00 % | - |
      | Course total | - | 50.00 | 0–100 | 50.00 % | - |

  Scenario: Hidden grade items in locked category is hidden for student
    When I am on the "Course 1" "grades > User report > View" page logged in as student1
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Range | Percentage | Contribution to course total |
      | Test locked category total | 100.00 % | - | 0–100 | - | - |
      | Course total | - | - | 0–100 | - | - |
    And I should not see "Hidden item"

  Scenario: Hidden grade items in overridden category should show
    Given I am on the "Course 1" "grades > gradebook setup" page
    And I press "Add category"
    And I set the following fields to these values:
      | Category name | Test overridden category B|
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Cat b item |
      | Grade category | Test overridden category B |
    And I press "Save changes"
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "50.00" to the user "Student 1" for the grade item "Test overridden category B total"
    And I press "Save changes"
    And I am on the "Course 1" "grades > User report > View" page logged in as "student1"
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Range | Percentage | Contribution to course total |
      | Test locked category total | 50.00 % | - | 0–100 | - | - |
      | Test overridden category B total | 50.00 % | 50.00 | 0–100 | 50.00 % | - |
      | Course total | - | - | 0–200 | - | - |
