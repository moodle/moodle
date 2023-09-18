@core @core_grades @gradereport_user @javascript
Feature: Within the User report, a teacher can search for users.
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
    And I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"

  Scenario: A teacher can view and trigger the user search
    # Check the placeholder text
    Given I should see "Search users"
    # Confirm the search is currently inactive and results are unfiltered.
    And the following should exist in the "user-grades" table:
      | -1-                |
      | Turtle Manatee     |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Dummy User         |
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
    When I set the field "Search users" to "Turtle"
    And "View all results (5)" "option_role" should exist
    And I confirm "Turtle Manatee" in "user" search within the gradebook widget exists
    And I confirm "User Example" in "user" search within the gradebook widget does not exist
    And I click on "Turtle Manatee" "list_item"
    # Business case: This will trigger a page reload and can not dynamically update the table.
    And I wait until the page is ready
    Then the following should exist in the "user-grades" table:
      | -1-                |
      | Turtle Manatee     |
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Dummy User         |
    And I set the field "Search users" to "Turt"
    And "View all results (5)" "option_role" should exist
    And I click on "Clear search input" "button" in the ".user-search" "css_element"
    And "View all results (5)" "option_role" should not be visible

  Scenario: A teacher can search the user report to find specified users
    # Case: Standard search.
    Given I click on "Dummy" in the "user" search widget
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

    # Case: No users found.
    When I set the field "Search users" to "Plagiarism"
    And I should see "No results for \"Plagiarism\""
    # Table remains unchanged as the user had no results to select from the dropdown.
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

    # Case: Multiple users found and select only one result.
    Then I set the field "Search users" to "User"
    And "View all results (5)" "option_role" should exist
    And I confirm "Dummy User" in "user" search within the gradebook widget exists
    And I confirm "User Example" in "user" search within the gradebook widget exists
    And I confirm "User Test" in "user" search within the gradebook widget exists
    And I confirm "Turtle Manatee" in "user" search within the gradebook widget does not exist
    # Check if the matched field names (by lines) includes some identifiable info to help differentiate similar users.
    And I confirm "User (student2@example.com)" in "user" search within the gradebook widget exists
    And I confirm "User (student3@example.com)" in "user" search within the gradebook widget exists
    And I confirm "User (student4@example.com)" in "user" search within the gradebook widget exists
    And I click on "Dummy User" "list_item"
    And I wait until the page is ready
    And the following should exist in the "user-grades" table:
      | -1-                |
      | Dummy User         |
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Turtle Manatee     |

    # Business case: When searching with multiple partial matches, show the matches in the dropdown + a "View all results for (Bob)"
    # Business case cont. When pressing enter with multiple partial matches, behave like when you select the "View all results for (Bob)"
    # Case: Multiple users found and select all partial matches.
    And I set the field "Search users" to "User"
    And "View all results (5)" "option_role" should exist
    And I click on "View all results (5)" "option_role"
    And I wait until the page is ready
    And the following should exist in the "user-grades" table:
      | -1-                |
      | Dummy User         |
      | User Example       |
      | User Test          |
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | Turtle Manatee     |
    And I click on "Clear" "link" in the ".user-search" "css_element"
    And I wait until the page is ready
    And the following should exist in the "user-grades" table:
      | -1-                |
      | Turtle Manatee     |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Dummy User         |

    # Case: No users enrolled.
    And I am on the "Course 2" "grades > User report > View" page
    And I set the field "Search users" to "a"
    And I should see "No results for \"a\""

  Scenario: A teacher can quickly tell that a search is active on the current table
    Given I click on "Turtle" in the "user" search widget
    # The search input remains in the field on reload this is in keeping with other search implementations.
    When the field "Search users" matches value "Turtle"
    And I wait until "View all results (5)" "link" does not exist
    # Test if we can then further retain the turtle result set and further filter from there.
    Then I set the field "Search users" to "Turtle plagiarism"
    And "Turtle Manatee" "list_item" should not exist
    And I should see "No results for \"Turtle plagiarism\""

  Scenario: A teacher can search for values besides the users' name
    Given I set the field "Search users" to "student5@example.com"
    And "View all results (5)" "option_role" should exist
    And "Turtle Manatee" "list_item" should exist
    And I set the field "Search users" to "@example.com"
    And "View all results (5)" "option_role" should exist
    # Note: All learners match this email & showing emails is current default.
    And I confirm "Dummy User" in "user" search within the gradebook widget exists
    And I confirm "User Example" in "user" search within the gradebook widget exists
    And I confirm "User Test" in "user" search within the gradebook widget exists
    And I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Turtle Manatee" in "user" search within the gradebook widget exists

    # Search on the country field.
    When I set the field "Search users" to "JP"
    And "View all results (5)" "option_role" should exist
    And I wait until "Turtle Manatee" "list_item" does not exist
    And I confirm "Dummy User" in "user" search within the gradebook widget exists
    And I confirm "User Test" in "user" search within the gradebook widget exists

    # Search on the city field.
    And I set the field "Search users" to "Hanoi"
    And I wait until "User Test" "list_item" does not exist
    Then I confirm "Student 1" in "user" search within the gradebook widget exists

    # Search on the institution field.
    And I set the field "Search users" to "ABCD"
    And "Dummy User" "list_item" should exist
    And I confirm "User Example" in "user" search within the gradebook widget exists
    And I confirm "User Test" in "user" search within the gradebook widget exists
    And I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Turtle Manatee" in "user" search within the gradebook widget exists

      # Search on the department field.
    And I set the field "Search users" to "ABC3"
    And I wait until "User Example" "list_item" does not exist
    And I confirm "User Test" in "user" search within the gradebook widget exists
    And I confirm "Turtle Manatee" in "user" search within the gradebook widget exists

    # Search on the phone1 field.
    And I set the field "Search users" to "4365899871"
    And I wait until "User Test" "list_item" does not exist
    And I confirm "Dummy User" in "user" search within the gradebook widget exists

    # Search on the phone2 field.
    And I set the field "Search users" to "2149871323"
    And I wait until "Dummy User" "list_item" does not exist
    And I confirm "User Test" in "user" search within the gradebook widget exists

    # Search on the institution field then press enter to show the record set.
    And I set the field "Search users" to "ABC"
    And "Turtle Manatee" "list_item" should exist
    And I confirm "Dummy User" in "user" search within the gradebook widget exists
    And I confirm "User Example" in "user" search within the gradebook widget exists
    And I confirm "User Test" in "user" search within the gradebook widget exists
    And I confirm "Student 1" in "user" search within the gradebook widget exists
    And I press the enter key
    And I wait until the page is ready
    And the following should exist in the "user-grades" table:
      | -1-                |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Dummy User         |
      | Turtle Manatee     |
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |

  @accessibility
  Scenario: A teacher can set focus and search using the input are with a keyboard
    Given I set the field "Search users" to "ABC"
    # Basic tests for the page.
    And the page should meet accessibility standards
    And the page should meet "wcag131, wcag141, wcag412" accessibility standards
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests
    # Move onto general keyboard navigation testing.
    When "Turtle Manatee" "option_role" should exist
    And I press the down key
    And the focused element is "Student 1" "option_role"
    And I press the end key
    And the focused element is "View all results (5)" "option_role"
    And I press the home key
    And the focused element is "Student 1" "option_role"
    And I press the up key
    And the focused element is "View all results (5)" "option_role"
    And I press the down key
    And the focused element is "Student 1" "option_role"
    And I press the escape key
    And the focused element is "Search users" "field"
    Then I set the field "Search users" to "Goodmeme"
    And I press the down key
    And the focused element is "Search users" "field"

    And I navigate to "View > User report" in the course gradebook
    And I set the field "Search users" to "ABC"
    And "Turtle Manatee" "option_role" should exist
    And I press the down key
    And the focused element is "Student 1" "option_role"

    # Lets check the tabbing order.
    And I set the field "Search users" to "ABC"
    And "View all results (5)" "option_role" should exist
    And I press the tab key
    And the focused element is "Clear search input" "button" in the ".user-search" "css_element"
    And I press the tab key
    And the focused element is "View all results (5)" "option_role"
    And I press the tab key
    And ".groupsearchwidget" "css_element" should exist
    # Ensure we can interact with the input & clear search options with the keyboard.
    # Space & Enter have the same handling for triggering the two functionalities.
    And I set the field "Search users" to "User"
    And I press the enter key
    And I wait to be redirected
    And the following should exist in the "user-grades" table:
      | -1-                |
      | Dummy User         |
      | User Example       |
      | User Test          |
    And the following should not exist in the "user-grades" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | Turtle Manatee     |
    # Sometimes with behat we get unattached nodes causing spurious failures.
    And I wait "1" seconds
    And I set the field "Search users" to "ABC"
    And "Turtle Manatee" "option_role" should exist
    And I press the tab key
    And the focused element is "Clear search input" "button" in the ".user-search" "css_element"
    And I press the enter key
    And I wait until the page is ready
    And I confirm "Turtle Manatee" in "user" search within the gradebook widget does not exist
