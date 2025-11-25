@core @core_grades @gradereport_singleview @javascript
Feature: Within the singleview report, a teacher can search for users.
  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
      | Course 2 | C2        | 0        | 0         |
    And the following "users" exist:
      | username | firstname  | lastname | email                | idnumber | phone1     | phone2     | department | institution | city    | country  |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       | 1234567892 | 1234567893 | ABC1       | ABCD        | Perth   | AU       |
      | student1 | Student   | 1        | student1@example.com | s1       | 3213078612 | 8974325612 | ABC1       | ABCD        | Hanoi   | VN       |
      | student2 | Dummy     | User     | student2@example.com | s2       | 4365899871 | 7654789012 | ABC2       | ABCD        | Tokyo   | JP       |
      | student3 | User      | Example  | student3@example.com | s3       | 3243249087 | 0875421745 | ABC2       | ABCD        | Olney   | GB       |
      | student4 | User      | Test     | student4@example.com | s4       | 0987532523 | 2149871323 | ABC3       | ABCD        | Tokyo   | JP       |
      | student5 | Turtle    | Manatee  | student5@example.com | s5       | 1239087780 | 9873623589 | ABC3       | ABCD        | Perth   | AU       |
    # Note: Add groups etc so we can test that the search ignores those filters as well if we go down the filter dataset path.
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher1 | C2     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name                |
      | assign   | C1     | a1       | Test assignment one |
    And the following config values are set as admin:
      | showuseridentity | idnumber,email,city,country,phone1,phone2,department,institution |
    And I change window size to "large"
    And I am on the "Course 1" "grades > Single view > View" page logged in as "teacher1"
    And I click on "Users" "link" in the ".page-toggler" "css_element"

  Scenario: A teacher can view and trigger the user search
    # Check the placeholder text (no users are initially shown).
    Given I should see "Search users"
    And I should see "Search for a user to view all their grades"
    When I set the field "Search users" to "Turtle"
    And I confirm "Turtle Manatee" exists in the "Search users" search combo box
    And I confirm "User Example" does not exist in the "Search users" search combo box
    And I click on "Turtle Manatee" "list_item"
    # Business case: This will trigger a page reload and can not dynamically update the table.
    And I wait until the page is ready
    And "Turtle Manatee" "heading" should exist
    And "Teacher 1" "heading" should not exist
    And "Student 1" "heading" should not exist
    And "User Example" "heading" should not exist
    And "User Test" "heading" should not exist
    And "Dummy User" "heading" should not exist
    And I set the field "Search users" to "Turt"
    And I wait until "Turtle Manatee" "option_role" exists
    And I click on "Clear search input" "button" in the ".user-search" "css_element"
    And "Turtle Manatee" "option_role" should not be visible

  Scenario: A teacher can search the single view report to find specified users
    # Case: Standard search.
    Given I click on "Dummy" in the "Search users" search combo box
    And "Dummy User" "heading" should exist
    And "Teacher 1" "heading" should not exist
    And "Student 1" "heading" should not exist
    And "User Example" "heading" should not exist
    And "User Test" "heading" should not exist
    And "Turtle Manatee" "heading" should not exist

    # Case: No users found.
    When I set the field "Search users" to "Plagiarism"
    And I should see "No results for \"Plagiarism\""
    # Table remains unchanged as the user had no results to select from the dropdown.
    And "Dummy User" "heading" should exist
    And "Teacher 1" "heading" should not exist
    And "Student 1" "heading" should not exist
    And "User Example" "heading" should not exist
    And "User Test" "heading" should not exist
    And "Turtle Manatee" "heading" should not exist

    # Case: Multiple users found and select only one result.
    Then I set the field "Search users" to "User"
    And I wait until "Dummy User" "option_role" exists
    And I confirm "Dummy User" exists in the "Search users" search combo box
    And I confirm "User Example" exists in the "Search users" search combo box
    And I confirm "User Test" exists in the "Search users" search combo box
    And I confirm "Turtle Manatee" does not exist in the "Search users" search combo box
    # Check if the matched field names (by lines) includes some identifiable info to help differentiate similar users.
    And I confirm "User (student2@example.com)" exists in the "Search users" search combo box
    And I confirm "User (student3@example.com)" exists in the "Search users" search combo box
    And I confirm "User (student4@example.com)" exists in the "Search users" search combo box
    And I click on "Dummy User" "list_item"
    And I wait until the page is ready
    And "Dummy User" "heading" should exist
    And "Teacher 1" "heading" should not exist
    And "Student 1" "heading" should not exist
    And "User Example" "heading" should not exist
    And "User Test" "heading" should not exist
    And "Turtle Manatee" "heading" should not exist

    # Case: No users enrolled.
    And I am on the "Course 2" "grades > Single view > View" page
    And I click on "Users" "link" in the ".page-toggler" "css_element"
    And I set the field "Search users" to "a"
    And I wait until "No results for \"a\"" "text" exists

  Scenario: A teacher can quickly tell that a search is active on the current table
    Given I click on "Turtle" in the "Search users" search combo box
    And I wait until the page is ready
    # The search input remains in the field on reload this is in keeping with other search implementations.
    When the field "Search users" matches value "Turtle Manatee"
    # The users get preloaded for accessibility reasons.
    And "Turtle Manatee" "option_role" should exist
    # Test if we can then further retain the turtle result set and further filter from there.
    Then I set the field "Search users" to "Turtle plagiarism"
    And I wait until "Turtle Manatee" "option_role" does not exist
    And I should see "No results for \"Turtle plagiarism\""

  Scenario: A teacher can search for values besides the users' name
    Given I set the field "Search users" to "student5@example.com"
    And I wait until "Turtle Manatee" "list_item" exists
    And I set the field "Search users" to "@example.com"
    And I wait until "Dummy User" "list_item" exists
    # Note: All learners match this email & showing emails is current default.
    And I confirm "Dummy User" exists in the "Search users" search combo box
    And I confirm "User Example" exists in the "Search users" search combo box
    And I confirm "User Test" exists in the "Search users" search combo box
    And I confirm "Student 1" exists in the "Search users" search combo box
    And I confirm "Turtle Manatee" exists in the "Search users" search combo box

    # Search on the country field.
    When I set the field "Search users" to "JP"
    And I wait until "Dummy User" "list_item" exists
    And I wait until "Turtle Manatee" "list_item" does not exist
    And I confirm "Dummy User" exists in the "Search users" search combo box
    And I confirm "User Test" exists in the "Search users" search combo box

    # Search on the city field.
    And I set the field "Search users" to "Hanoi"
    And I wait until "User Test" "list_item" does not exist
    Then I confirm "Student 1" exists in the "Search users" search combo box

    # Search on the institution field.
    And I set the field "Search users" to "ABCD"
    And I wait until "Dummy User" "list_item" exists
    And I confirm "User Example" exists in the "Search users" search combo box
    And I confirm "User Test" exists in the "Search users" search combo box
    And I confirm "Student 1" exists in the "Search users" search combo box
    And I confirm "Turtle Manatee" exists in the "Search users" search combo box

      # Search on the department field.
    And I set the field "Search users" to "ABC3"
    And I wait until "User Example" "list_item" does not exist
    And I confirm "User Test" exists in the "Search users" search combo box
    And I confirm "Turtle Manatee" exists in the "Search users" search combo box

    # Search on the phone1 field.
    And I set the field "Search users" to "4365899871"
    And I wait until "User Test" "list_item" does not exist
    And I confirm "Dummy User" exists in the "Search users" search combo box

    # Search on the phone2 field.
    And I set the field "Search users" to "2149871323"
    And I wait until "Dummy User" "list_item" does not exist
    And I confirm "User Test" exists in the "Search users" search combo box

    # Search on the institution field then press enter to show the record set.
    And I set the field "Search users" to "ABC"
    And "Turtle Manatee" "list_item" should exist
    And I confirm "Dummy User" exists in the "Search users" search combo box
    And I confirm "User Example" exists in the "Search users" search combo box
    And I confirm "User Test" exists in the "Search users" search combo box
    And I confirm "Student 1" exists in the "Search users" search combo box
    And I press the down key
    And I press the enter key
    And I wait until the page is ready
    And "Student 1" "heading" should exist
    And "User Example" "heading" should not exist
    And "User Test" "heading" should not exist
    And "Dummy User" "heading" should not exist
    And "Turtle Manatee" "heading" should not exist
    And "Teacher 1" "heading" should not exist

  @accessibility
  Scenario: A teacher can set focus and search using the input are with a keyboard
    Given I set the field "Search users" to "ABC"
    # Basic tests for the page.
    And the page should meet accessibility standards
    And the page should meet "wcag131, wcag141, wcag412" accessibility standards
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests
    # Move onto general keyboard navigation testing.
    When I wait until "Turtle Manatee" "option_role" exists
    And I press the down key
    And ".active" "css_element" should exist in the "Student 1" "option_role"
    And I press the up key
    And ".active" "css_element" should exist in the "Dummy User" "option_role"
    And I press the down key
    And ".active" "css_element" should exist in the "Student 1" "option_role"
    And I press the escape key
    And the focused element is "Search users" "field"
    Then I set the field "Search users" to "Goodmeme"
    And I press the down key
    And the focused element is "Search users" "field"

    And I navigate to "View > Single view" in the course gradebook
    And I click on "Users" "link" in the ".page-toggler" "css_element"
    And I set the field "Search users" to "ABC"
    And I wait until "Turtle Manatee" "option_role" exists
    And I press the down key
    And ".active" "css_element" should exist in the "Student 1" "option_role"

    # Lets check the tabbing order.
    And I set the field "Search users" to "ABC"
    And I wait until "Turtle Manatee" "option_role" exists
    And I press the tab key
    And the focused element is "Clear search input" "button" in the ".user-search" "css_element"
    And I press the tab key
    And the focused element is not "Search users" "field"
    # Ensure we can interact with the clear search with the keyboard.
    # Sometimes with behat we get unattached nodes causing spurious failures.
    And I set the field "Search users" to "ABC"
    And I wait "1" seconds
    And I press the tab key
    And the focused element is "Clear search input" "button" in the ".user-search" "css_element"
    And I press the enter key
    And I confirm "Turtle Manatee" does not exist in the "Search users" search combo box

  Scenario: A teacher can clear the user search.
    # A teacher searches for and selects a specific user.
    Given I click on "Turtle" in the "Search users" search combo box
    And "Turtle Manatee" "heading" should exist
    When I click on "Clear" "link"
    # Page reloads with search field cleared and no student details displayed (empty state).
    And I wait until the page is ready
    Then the field "Search users" matches value ""
    And "Turtle Manatee" "heading" should not exist
