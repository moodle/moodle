@core @core_course @app @javascript
Feature: Test course list shown on app start tab
  In order to select a course
  As a student
  I need to see the correct list of courses

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "users" exist:
      | username |
      | student1 |
      | student2 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | student2 | C2     | student |

  Scenario: Student is registered on one course
    When I enter the app
    And I log in as "student1"
    Then I should see "Course 1"
    And I should not see "Course 2"

  Scenario: Student is registered on two courses (shortnames not displayed)
    When I enter the app
    And I log in as "student2"
    Then I should see "Course 1"
    And I should see "Course 2"
    And I should not see "C1"
    And I should not see "C2"

  Scenario: Student is registered on two courses (shortnames displayed)
    Given the following config values are set as admin:
      | courselistshortnames | 1 |
    When I enter the app
    And I log in as "student2"
    Then I should see "Course 1"
    And I should see "Course 2"
    And I should see "C1"
    And I should see "C2"

  Scenario: Student uses course list to enter course, then leaves it again
    When I enter the app
    And I log in as "student2"
    And I press "Course 2" near "Course overview" in the app
    Then the header should be "Course 2" in the app
    And I press the back button in the app
    Then the header should be "Acceptance test site" in the app

  Scenario: Student uses filter feature to reduce course list
    Given the following config values are set as admin:
      | courselistshortnames | 1 |
    And the following "courses" exist:
      | fullname | shortname |
      | Frog 3   | C3        |
      | Frog 4   | C4        |
      | Course 5 | C5        |
      | Toad 6   | C6        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student2 | C3     | student |
      | student2 | C4     | student |
      | student2 | C5     | student |
      | student2 | C6     | student |
    # Create bogus courses so that the main ones aren't shown in the 'recently accessed' part.
    # Because these come later in alphabetical order, they may not be displayed in the lower part
    # which is OK.
    And the following "courses" exist:
      | fullname | shortname |
      | Zogus 1  | Z1        |
      | Zogus 2  | Z2        |
      | Zogus 3  | Z3        |
      | Zogus 4  | Z4        |
      | Zogus 5  | Z5        |
      | Zogus 6  | Z6        |
      | Zogus 7  | Z7        |
      | Zogus 8  | Z8        |
      | Zogus 9  | Z9        |
      | Zogus 10 | Z10       |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student2 | Z1     | student |
      | student2 | Z2     | student |
      | student2 | Z3     | student |
      | student2 | Z4     | student |
      | student2 | Z5     | student |
      | student2 | Z6     | student |
      | student2 | Z7     | student |
      | student2 | Z8     | student |
      | student2 | Z9     | student |
      | student2 | Z10    | student |
    When I enter the app
    And I log in as "student2"
    Then I should see "C1"
    And I should see "C2"
    And I should see "C3"
    And I should see "C4"
    And I should see "C5"
    And I should see "C6"
    And I press "more" near "Course overview" in the app
    And I press "Filter my courses" in the app
    And I set the field "Filter my courses" to "fr" in the app
    Then I should not see "C1"
    And I should not see "C2"
    And I should see "C3"
    And I should see "C4"
    And I should not see "C5"
    And I should not see "C6"
    And I press "more" near "Course overview" in the app
    And I press "Filter my courses" in the app
    Then I should see "C1"
    And I should see "C2"
    And I should see "C3"
    And I should see "C4"
    And I should see "C5"
    And I should see "C6"
