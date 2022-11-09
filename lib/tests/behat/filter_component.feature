@core @javascript
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
    And I am on the "Course 1" "Course" page logged in as "teacher1"
    And I change window size to "large"
    And I navigate to "View > Grader report" in the course gradebook

  Scenario: A teacher can open the filer component
    Given I should see "Name" "button"
    When I press "Name"
    # Meeting outcome: We will use a placeholder pulse pattern.
    # And I wait until "pulsing placholder" "css_element" exists
    # And I wait until "Apply filter" "button" exists
    # Meeting outcome: "All" option will still exist for the time being, with the followup of toggling active nodes to disable filter
    # Meeting outcome: Deferred for later in the release after A+B testing done in-house.
    And I should see "A loading spinner"
    And I wait until "Apply filter" "button" exists
    # https://www.w3.org/WAI/ARIA/apg/patterns/toolbar/
    Then I should see "27" occurrences of "Filter option" in the "First name" "Tool bar"
    And I should see "27" occurrences of "Filter option" in the "Last name" "Tool bar"
    # Meeting outcome: Upon selection, the table should update like the participants filter
    # Given the above meeting outcome, This button will likely not need to exist
    And I should see "Close" "button"
    And I should see "Apply filter" "button"

  # We need to decide if we want the page to reload or if the page content reloads like in the Course participants filters
  Scenario: A teacher can filter the grader report to limit users reported
    Given I press "Name"
    And I wait until "Apply filter" "button" exists
    When I select "D" in the "First name" "Toolbar"
    And I press "Apply filter"
    # Assuming we close the dropdown once filter is applied
    And I wait until "Apply filter" "Button" does not exist
    # We should only have one user that matches the "D" first name
    Then the following should exist in the "user-grades" table:
      | -1-                |
      | Dummy User         |
    # All other users should not be shown to the user based on filtering
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Turtle Manatee     |

    # Test filtering on last name
    # Meeting outcome: Indication will be: First(T)
    # Meeting outcome: If all is selected, we will not show it i.e. First (D) and NOT First (D) Last (All)
    And I press "First (D)"
    And I select "All" in the "First name" "Toolbar"
    And I select "M" in the "Last name" "Toolbar"
    And I press "Apply filter"
    # Assuming we close the dropdown once filter is applied
    And I wait until "Apply filter" "Button" does not exist
    And I should see "First (U) Last(T)" "button"
    # We should only have one user that matches the "T" first name
    And the following should exist in the "user-grades" table:
      | -1-                |
      | Turtle Manatee     |
    # All other users should not be shown to the user based on filtering
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Dummy User         |

    # Test filtering on first && last name
    And I press "Name"
    And I select "U" in the "First name" "Toolbar"
    And I select "T" in the "Last name" "Toolbar"
    And I press "Apply filter"
    # Assuming we close the dropdown once filter is applied
    And I wait until "Apply filter" "Button" does not exist
    # We should only have one user that matches the "T" first name
    And the following should exist in the "user-grades" table:
      | -1-                |
      | User Test          |
    # All other users should not be shown to the user based on filtering
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | Dummy User         |
      | Turtle Manatee     |

  Scenario: A teacher can quickly tell that a filter is applied to the current table
    Given I press "Name"
    And I wait until "Apply filter" "button" exists
    When I select "T" in the "First name" "Toolbar"
    And I press "Apply filter"
    # Assuming we close the dropdown once filter is applied
    And I wait until "Apply filter" "Button" does not exist
    # Check if the name button indicates if a filter is active
    Then I should see "First (T)"
    # Meeting outcome: No decision yet if we alter the colors / active state / focus state
    And I should see that "Name (T)" "button" is highlighted

    # Check if removing the filter, removes the highlight and user notice of applied filters
    And I press "First (T)"
    And I wait until "Apply filter" "button" exists
    And I select "All" in the "First name" "Toolbar"
    And I press "Apply filter"
    # Assuming we close the dropdown once filter is applied
    And I wait until "Apply filter" "Button" does not exist
    # Check if the name button indicates if a filter is active
    And I should see "Name"
    # Meeting outcome: We do not show First(All) Last(All)
    And I should not see "First (T)"
    And I should not see that "Name" "button" is highlighted

  Scenario: A teacher can close the filter either by clicking close or clicking off the dropdown
    # Meeting outcome: We would like this report to act like the participants filter page with the dynamic table
    # Meeting outcome: Should we save state of user input when clicking off or click close?: - Maybe not needed? based on if we were to filter instantly on the user selection like we do in the participants page.
    Given I press "Name"
    And I wait until "Apply filter" "button" exists
    When I press "Close"
    Then I should not see "Apply filter"

    # Click off the drop down
    And I press "Name"
    And I wait until "Apply filter" "button" exists
    And I click on "First name" "link" in the "gradereport-grader-table" "table"
    And I should not see "Apply filter"

  # This can be expanded for left/right/up/down/home & end keys
  Scenario: A teacher can set focus and navigate the filter with the keyboard
    Given I press "Name"
    And I wait until "Apply filter" "button" exists
    And the focused element is "All" "button" in the "First name" "Toolbar"
    When I press the right key
    Then the focused element is "A" "button" in the "First name" "Toolbar"
    And I press the tab key
    And the focused element is "All" "button" in the "Last name" "Toolbar"
