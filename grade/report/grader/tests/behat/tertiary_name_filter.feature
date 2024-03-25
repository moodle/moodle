@core @javascript @gradereport_grader
Feature: Within the grader report, test that we can open our generic filter dropdown component
  In order to filter down the users on the page
  As a teacher
  I need to be able to see the filter and select a combination of parameters
  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
      | student1 | Student   | 1        | student1@example.com | s1       |
      | student2 | Dummy     | User     | student2@example.com | s2       |
      | student3 | User      | Example  | student3@example.com | s3       |
      | student4 | User      | Test     | student4@example.com | s4       |
      | student5 | Turtle    | Manatee  | student5@example.com | s5       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name                |
      | assign   | C1     | a1       | Test assignment one |
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"

  Scenario: A teacher can open the filter component
    Given I should see "Filter by name"
    When I click on "Filter by name" "combobox"
    Then I should see "27" node occurrences of type "input" in the "First name" "core_grades > initials bar"
    And I should see "27" node occurrences of type "input" in the "Last name" "core_grades > initials bar"
    And "input[data-action=cancel]" "css_element" should exist
    And "input[data-action=save]" "css_element" should exist

  Scenario: A teacher can filter the grader report to limit users reported
    Given I click on "Filter by name" "combobox"
    And I wait until "input[data-action=save]" "css_element" exists
    When I select "D" in the "First name" "core_grades > initials bar"
    And I press "Apply"
    And I wait to be redirected
    # We should only have one user that matches the "D" first name
    Then the following should exist in the "user-grades" table:
      | -1-                |
      | Dummy User         |
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Turtle Manatee     |

    # Test filtering on last name
    # Business logic: If all is selected, we will not show it i.e. First (D) and NOT First (D) Last (All)
    And I click on "First (D)" "combobox"
    And I select "All" in the "First name" "core_grades > initials bar"
    And I select "M" in the "Last name" "core_grades > initials bar"
    And I press "Apply"
    And I wait to be redirected
    # We should only have one user that matches the "T" first name
    And the following should exist in the "user-grades" table:
      | -1-                |
      | Turtle Manatee     |
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Dummy User         |

    # Test filtering on first && last name
    And I click on "Last (M)" "combobox"
    And I select "U" in the "First name" "core_grades > initials bar"
    And I select "T" in the "Last name" "core_grades > initials bar"
    And I press "Apply"
    And I wait to be redirected
    # We should only have one user that matches the "T" first name
    And the following should exist in the "user-grades" table:
      | -1-                |
      | User Test          |
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | Dummy User         |
      | Turtle Manatee     |
    # Final cheeky check to ensure our button matches.
    And I click on "First (U) Last (T)" "combobox"

  Scenario: A teacher can quickly tell that a filter is applied to the current table
    Given I click on "Filter by name" "combobox"
    And I wait until "input[data-action=save]" "css_element" exists
    When I select "T" in the "First name" "core_grades > initials bar"
    And I press "Apply"
    And I wait to be redirected
    Then I should see "First (T)"

    # Check if removing the filter, removes the highlight and user notice of applied filters
    And I click on "First (T)" "combobox"
    And I wait until "input[data-action=save]" "css_element" exists
    And I select "All" in the "First name" "core_grades > initials bar"
    And I press "Apply"
    And I wait to be redirected
    # Check if the name button indicates if a filter is active
    And I should see "Filter by name"
    And I should not see "First (T)"

  Scenario: A teacher can close the filter either by clicking close or clicking off the dropdown
    Given I click on "Filter by name" "combobox"
    And "input[data-action=save]" "css_element" should be visible
    When I click on "input[data-action=cancel]" "css_element"
    Then "input[data-action=save]" "css_element" should not be visible

    # Click off the drop down
    And I click on "Filter by name" "combobox"
    And "input[data-action=save]" "css_element" should be visible
    And I change window size to "large"
    And I click on user profile field menu "fullname"
    And "input[data-action=save]" "css_element" should not be visible

  Scenario: A teacher using a language besides english can reset the initials bar
    Given the following "language customisations" exist:
      | component | stringid | value  |
      | core      | all      | すべて  |
    And I click on "Filter by name" "combobox"
    And "input[data-action=save]" "css_element" should be visible
    And I select "T" in the "First name" "core_grades > initials bar"
    And I press "Apply"
    And I wait to be redirected
    And I click on "First (T)" "combobox"
    And I wait until "input[data-action=save]" "css_element" exists

    When I select "すべて" in the "First name" "core_grades > initials bar"
    And I press "Apply"
    And I wait to be redirected
    Then I should not see "First (すべて) Last (すべて)"
    And the following should exist in the "user-grades" table:
      | -1-                |
      | Dummy User         |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Turtle Manatee     |

  Scenario: A teacher can search and then filter by first or last name
    Given I set the field "Search users" to "Student 1"
    And I click on "Student 1" in the "user" search widget
    And I click on "Filter by name" "combobox"
    And I select "S" in the "First name" "core_grades > initials bar"
    When I press "Apply"
    And the field "Search users" matches value "Student 1"
    Then the following should exist in the "user-grades" table:
      | -1-                | -1-                  | -3- |
      | Student 1          | student1@example.com | -   |
    And the following should not exist in the "user-grades" table:
      | -1-                | -1-                  | -3- |
      | Teacher 1          | teacher1@example.com | -   |
      | Dummy User         | student2@example.com | -   |
      | User Example       | student3@example.com | -   |
      | User Test          | student4@example.com | -   |
      | Turtle Manatee     | student5@example.com | -   |
    And I click on "First (S)" "combobox"
    And I select "M" in the "First name" "core_grades > initials bar"
    And I press "Apply"
    And the following should not exist in the "user-grades" table:
      | -1-                | -1-                  | -3- |
      | Student 1          | student1@example.com | -   |
      | Teacher 1          | teacher1@example.com | -   |
      | Dummy User         | student2@example.com | -   |
      | User Example       | student3@example.com | -   |
      | User Test          | student4@example.com | -   |
      | Turtle Manatee     | student5@example.com | -   |

  Scenario: A teacher can search for all users then filter with the initials bar
    Given I set the field "Search users" to "User"
    And I click on "View all results (3)" "option_role"
    And the following should exist in the "user-grades" table:
      | -1-                | -1-                  | -3- |
      | User Example       | student3@example.com | -   |
      | User Test          | student4@example.com | -   |
      | Dummy User         | student2@example.com | -   |
    And the following should not exist in the "user-grades" table:
      | -1-                | -1-                  | -3- |
      | Student 1          | student1@example.com | -   |
      | Teacher 1          | teacher1@example.com | -   |
      | Turtle Manatee     | student5@example.com | -   |
    When I click on "Filter by name" "combobox"
    And I select "E" in the "Last name" "core_grades > initials bar"
    And I press "Apply"
    Then the following should exist in the "user-grades" table:
      | -1-                | -1-                  | -3- |
      | User Example       | student3@example.com | -   |
    And the following should not exist in the "user-grades" table:
      | -1-                | -1-                  | -3- |
      | Student 1          | student1@example.com | -   |
      | Teacher 1          | teacher1@example.com | -   |
      | Dummy User         | student2@example.com | -   |
      | User Test          | student4@example.com | -   |
      | Turtle Manatee     | student5@example.com | -   |

  # This can be expanded for left/right/home & end keys but will have to be done in conjunction with the non mini render.
  @accessibility
  Scenario: A teacher can set focus and navigate the filter with the keyboard
    Given the page should meet accessibility standards
    And the page should meet "wcag131, wcag141, wcag412" accessibility standards
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests
    And I click on "Filter by name" "combobox"
    And "input[data-action=save]" "css_element" should be visible
    And the focused element is "All" "button" in the "First name" "core_grades > initials bar"
    When I press the tab key
    Then the focused element is "input[value=A]" "css_element" in the "First name" "core_grades > initials bar"
    And I press the tab key
    And the focused element is "input[value=B]" "css_element" in the "First name" "core_grades > initials bar"
