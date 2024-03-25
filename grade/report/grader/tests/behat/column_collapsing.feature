@core @javascript @gradereport @gradereport_grader
Feature: Within the grader report, test that we can collapse columns
  In order to reduce usage of visual real estate
  As a teacher
  I need to be able to change how the report is displayed

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "grade categories" exist:
      | fullname                 | course |
      | Some cool grade category | C1     |
    And the following "custom profile fields" exist:
      | datatype | shortname | name                  |
      | text     | enduro    | Favourite enduro race |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber | phone1     | phone2     | department | institution | city    | country  |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       | 1234567892 | 1234567893 | ABC1       | ABCD        | Perth   | AU       |
      | student1 | Student   | 1        | student1@example.com | s1       | 3213078612 | 8974325612 | ABC1       | ABCD        | Hanoi   | VN       |
      | student2 | Dummy     | User     | student2@example.com | s2       | 4365899871 | 7654789012 | ABC2       | ABCD        | Tokyo   | JP       |
      | student3 | User      | Example  | student3@example.com | s3       | 3243249087 | 0875421745 | ABC2       | ABCD        | Olney   | GB       |
      | student4 | User      | Test     | student4@example.com | s4       | 0987532523 | 2149871323 | ABC3       | ABCD        | Tokyo   | JP       |
      | student5 | Turtle    | Manatee  | student5@example.com | s5       | 1239087780 | 9873623589 | ABC3       | ABCD        | Perth   | AU       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name                  | gradecategory            |
      | assign   | C1     | a1       | Test assignment one   | Some cool grade category |
      | assign   | C1     | a3       | Test assignment three | Some cool grade category |
    And the following "activities" exist:
      | activity | course | idnumber | name                  |
      | assign   | C1     | a2       | Test assignment two   |
      | assign   | C1     | a4       | Test assignment four  |
    And the following config values are set as admin:
      | showuseridentity | idnumber,email,city,country,phone1,phone2,department,institution,profile_field_enduro |
    And I change window size to "large"
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"

  Scenario: An admin collapses a user info column and then reloads the page to find the column still collapsed
    Given "Email" "text" in the "First name / Last name" "table_row" should be visible
    And I click on user profile field menu "Email"
    And I choose "Collapse" in the open action menu
    And "Email" "text" in the "First name / Last name" "table_row" should not be visible
    And I click on user profile field menu "profile_field_enduro"
    And I choose "Collapse" in the open action menu
    And "Favourite enduro race" "text" in the "First name / Last name" "table_row" should not be visible
    When I reload the page
    Then "Email" "text" in the "First name / Last name" "table_row" should not be visible
    # Check that the collapsed column is only for the user that set it.
    And I am on the "Course 1" "Course" page logged in as "admin"
    And I change window size to "large"
    And I navigate to "View > Grader report" in the course gradebook
    And "Email" "text" in the "First name / Last name" "table_row" should be visible

  Scenario: A teacher collapses a grade item column and then reloads the page to find the column still collapsed
    Given "Test assignment one" "link" in the "First name / Last name" "table_row" should be visible
    And I click on grade item menu "Test assignment one" of type "gradeitem" on "grader" page
    And I choose "Collapse" in the open action menu
    And "Test assignment one" "link" in the "First name / Last name" "table_row" should not be visible
    When I reload the page
    And "Test assignment one" "link" in the "First name / Last name" "table_row" should not be visible

  Scenario: When a user collapses a column, inform them within the report and tertiary nav area
    Given I click on grade item menu "Test assignment one" of type "gradeitem" on "grader" page
    When I choose "Collapse" in the open action menu
    And "Test assignment one" "link" in the "First name / Last name" "table_row" should not be visible
    Then I should see "Expand column Test assignment one"
    And I should see "Collapsed columns 1"

  Scenario: Collapsed columns can have their name searched and triggered to expand but the contents are not searched
    Given "ID number" "text" in the "First name / Last name" "table_row" should be visible
    And I click on user profile field menu "idnumber"
    And I choose "Collapse" in the open action menu
    # Opens the tertiary trigger button.
    And I click on "Collapsed columns" "combobox"
    # This is checking that the column name search dropdown exists.
    And I wait until "Search collapsed columns" "field" exists
    # Default state contains the collapsed column names.
    And I should see "ID number"
    # Search for a column that was not hidden.
    When I set the field "Search collapsed columns" to "Email"
    And I should see "No results for \"Email\""
    # Search for a ID number value inside the column that was hidden.
    Then I set the field "Search collapsed columns" to "s5"
    And I should see "No results for \"s5\""
    # Search for a column that was hidden.
    And I set the field "Search collapsed columns" to "ID"
    And I should see "ID number"

  Scenario: Expand multiple columns at once
    Given I click on grade item menu "Test assignment one" of type "gradeitem" on "grader" page
    And I choose "Collapse" in the open action menu
    And I click on grade item menu "Test assignment two" of type "gradeitem" on "grader" page
    And I choose "Collapse" in the open action menu
    And I click on grade item menu "Test assignment three" of type "gradeitem" on "grader" page
    And I choose "Collapse" in the open action menu
    And I click on grade item menu "Test assignment four" of type "gradeitem" on "grader" page
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Email"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Phone1"
    And I choose "Collapse" in the open action menu
    And I click on "Collapsed columns" "combobox"
    # This is checking that the column name search dropdown exists.
    When I wait until "Search collapsed columns" "field" exists
    And I click on "Test assignment one" "checkbox" in the "form" "gradereport_grader > collapse search"
    And I click on "Test assignment three" "checkbox" in the "form" "gradereport_grader > collapse search"
    And I click on "Phone" "checkbox" in the "form" "gradereport_grader > collapse search"
    And I click on "Expand" "button" in the "form" "gradereport_grader > collapse search"
    And "Test assignment one" "link" in the "First name / Last name" "table_row" should be visible
    And "Test assignment three" "link" in the "First name / Last name" "table_row" should be visible
    And "Phone" "text" in the "First name / Last name" "table_row" should be visible
    And "Email" "text" in the "First name / Last name" "table_row" should not be visible
    And "Test assignment two" "link" in the "First name / Last name" "table_row" should not be visible
    And "Test assignment four" "link" in the "First name / Last name" "table_row" should not be visible

  Scenario: If there is only one collapsed column it expands
    Given I click on user profile field menu "Email"
    And I choose "Collapse" in the open action menu
    And "Email" "text" in the "First name / Last name" "table_row" should not be visible
    When I press "Expand column Email"
    And I wait until the page is ready
    Then "Email" "text" in the "First name / Last name" "table_row" should be visible

  Scenario: When a grade item is collapsed, the grade category is shown alongside the column name.
    Given I click on grade item menu "Test assignment one" of type "gradeitem" on "grader" page
    And I choose "Collapse" in the open action menu
    And I click on grade item menu "Test assignment two" of type "gradeitem" on "grader" page
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Email"
    And I choose "Collapse" in the open action menu
    And "Test assignment one" "link" in the "First name / Last name" "table_row" should not be visible
    And "Test assignment two" "link" in the "First name / Last name" "table_row" should not be visible
    And "Email" "text" in the "First name / Last name" "table_row" should not be visible
    # Opens the tertiary trigger button.
    When I click on "Collapsed columns" "combobox"
    # This is checking that the column name search dropdown exists.
    And I wait until "Search collapsed columns" "field" exists
    # Add ordering test as well.
    And I should see "Test assignment one" in the "form" "gradereport_grader > collapse search"
    And I should see "Some cool grade category" in the "form" "gradereport_grader > collapse search"
    And I should see "Test assignment two" in the "form" "gradereport_grader > collapse search"
    And I should see "Course 1" in the "form" "gradereport_grader > collapse search"
    And I should see "Email" in the "form" "gradereport_grader > collapse search"
    And I should not see "Category div" in the "form" "gradereport_grader > collapse search"

  Scenario: Toggling edit mode should not show all collapsed columns
    Given I click on user profile field menu "Email"
    And I choose "Collapse" in the open action menu
    And "Email" "text" in the "First name / Last name" "table_row" should not be visible
    When I turn editing mode on
    And I wait until the page is ready
    Then "Email" "text" in the "First name / Last name" "table_row" should not be visible

  Scenario: Resulting columns from hidden grade categories cant be collapsed
    # Hiding columns already tested elsewhere, これはこれ、それはそれ。
    Given I click on grade item menu "Some cool grade category" of type "category" on "grader" page
    And I choose "Show totals only" in the open action menu
    And I should not see "Test assignment name 1"
    And I should see "Some cool grade category total"
    When I click on grade item menu "Some cool grade category" of type "category" on "grader" page
    Then I should not see "Collapse" in the ".dropdown-menu.show" "css_element"

  @accessibility
  Scenario: A teacher can manipulate the report display in an accessible way
    # Hide a bunch of columns.
    Given I click on user profile field menu "Email"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Phone1"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Phone2"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Country"
    And I choose "Collapse" in the open action menu
    # Basic tests for the page.
    When I click on "Collapsed columns" "combobox"
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests
    # Move onto general keyboard navigation testing.
    Then the focused element is "Search collapsed columns" "field"
    And I press the escape key
    And the focused element is "Collapsed columns" "combobox"
    And I click on "Collapsed columns" "combobox"
    # Lets check the tabbing order.
    And I set the field "Search collapsed columns" to "phone"
    And I wait until "Mobile phone" "checkbox" exists
    And I press the tab key
    And the focused element is "Clear search input" "button" in the ".dropdown-menu.show" "css_element"
    And I press the escape key
    And I press the tab key
    # The course grade category menu.
    And the focused element is "Cell actions" "button"
    # Tab over to the collapsed columns.
    And I click on user profile field menu "city"
    And I press the escape key
    And I press the tab key
    And the focused element is "Expand column Country" "button"
    And I press the enter key
    And I press the tab key
    And the focused element is "Expand column Phone" "button"
    And I press the enter key
    And "Email" "text" in the "First name / Last name" "table_row" should not be visible
    And "Phone" "text" in the "First name / Last name" "table_row" should be visible
    And "Mobile phone" "text" in the "First name / Last name" "table_row" should not be visible
    And "Country" "text" in the "First name / Last name" "table_row" should be visible
    # Ensure that things did not start failing after we did some manipulation.
    And the page should meet accessibility standards
    And the page should meet "wcag131, wcag141, wcag412" accessibility standards
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests

  Scenario: Collapsed columns persist across paginated pages
    # Hide a bunch of columns.
    Given I click on user profile field menu "Email"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Phone1"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Phone2"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Country"
    And I choose "Collapse" in the open action menu
    # Ensure we are ready to move onto the next step.
    When I should see "Collapsed columns 4"
    # Confirm our columns are hidden.
    And "Email" "text" in the "First name / Last name" "table_row" should not be visible
    And "Phone" "text" in the "First name / Last name" "table_row" should not be visible
    And "Mobile phone" "text" in the "First name / Last name" "table_row" should not be visible
    And "Country" "text" in the "First name / Last name" "table_row" should not be visible
    # Navigate to the next paginated page and ensure our columns are still hidden.
    Then I set the field "perpage" to "100"
    And I should see "Collapsed columns 4"
    And "Email" "text" in the "First name / Last name" "table_row" should not be visible
    And "Phone" "text" in the "First name / Last name" "table_row" should not be visible
    And "Mobile phone" "text" in the "First name / Last name" "table_row" should not be visible
    And "Country" "text" in the "First name / Last name" "table_row" should not be visible

  Scenario: If a column is actively sorted and then collapsed the active sort on the page should become First name
    # This behaviour is inline with other tables where we collapse columns that are sortable.
    Given I click on user profile field menu "Email"
    And I choose "Descending" in the open action menu
    And I wait to be redirected
    And I click on user profile field menu "Email"
    When I choose "Collapse" in the open action menu
    And I wait to be redirected
    And "Email" "text" in the "First name / Last name" "table_row" should not be visible
    Then "Dummy User" "table_row" should appear before "Student 1" "table_row"
    And "Student 1" "table_row" should appear before "Turtle Manatee" "table_row"
    And "Turtle Manatee" "table_row" should appear before "User Example" "table_row"

  Scenario: If multiple columns are collapsed, then all the user to expand all of them at once
    # Hide a bunch of columns.
    Given I click on user profile field menu "Email"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Phone1"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Phone2"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Country"
    And I choose "Collapse" in the open action menu
    # Ensure we are ready to move onto the next step.
    And I wait until "Collapsed columns" "combobox" exists
    When I click on "Collapsed columns" "combobox"
    And I click on "Select all" "checkbox"
    And I click on "Expand" "button" in the "form" "gradereport_grader > collapse search"
    # All of the previously collapsed columns should now be visible.
    And "Email" "text" in the "First name / Last name" "table_row" should be visible
    And "Phone" "text" in the "First name / Last name" "table_row" should be visible
    And "Mobile phone" "text" in the "First name / Last name" "table_row" should be visible
    And "Country" "text" in the "First name / Last name" "table_row" should be visible

  Scenario: If multiple columns are collapsed, when selecting all and then unselecting an option, the select all is then unchecked
    # Hide some columns.
    Given I click on user profile field menu "Email"
    And I choose "Collapse" in the open action menu
    And I click on user profile field menu "Country"
    And I choose "Collapse" in the open action menu
    # Ensure we are ready to move onto the next step.
    And I wait until "Collapsed columns" "combobox" exists
    When I click on "Collapsed columns" "combobox"
    And I click on "Select all" "checkbox"
    And I click on "Email" "checkbox" in the "form" "gradereport_grader > collapse search"
    # The select all option should now be unchecked, Checking the form or option role is iffy with behat so we use the id.
    Then the field "Select all" matches value ""
