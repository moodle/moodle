@mod @mod_assign @javascript
Feature: Within the assignment submissions page, test that we can search for users
  In order to filter specific users in the assignment submissions page
  As a teacher
  I need to be able to see and trigger the search filter

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode | groupmodeforce |
      | Course 1 | C1        | 0        | 1         | 1              |
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
    And the following "groups" exist:
      | name           | course | idnumber |
      | Default group  | C1     | dg       |
      | Advanced group | C1     | ag       |
    And the following "group members" exist:
      | user     | group |
      | student3 | ag    |
      | student5 | dg    |
    And the following "activities" exist:
      | activity | course | name                |
      | assign   | C1     | Test assignment one |
    And the following config values are set as admin:
      | showuseridentity | idnumber,email,city,country,phone1,phone2,department,institution |
    And I am on the "Test assignment one" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"

  Scenario: A teacher can view and trigger the user search
    # Check the placeholder text
    Given I should see "Search users"
    # Confirm the search is currently inactive and results are unfiltered.
    And the following should exist in the "generaltable" table:
      | -1-                |
      | Turtle Manatee     |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Dummy User         |
    And the following should not exist in the "generaltable" table:
      | -1-                |
      | Teacher 1          |
    When I set the field "Search users" to "Turtle"
    And I wait until "View all results (1)" "option_role" exists
    And I confirm "Turtle Manatee" exists in the "Search users" search combo box
    And I confirm "User Example" does not exist in the "Search users" search combo box
    And I click on "Turtle Manatee" "list_item"
    # Business case: This will trigger a page reload and can not dynamically update the table.
    And I wait until the page is ready
    Then the following should exist in the "generaltable" table:
      | -1-                |
      | Turtle Manatee     |
    And the following should not exist in the "generaltable" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Dummy User         |
    And I set the field "Search users" to "Turt"
    And I wait until "View all results (1)" "option_role" exists
    And I click on "Clear search input" "button" in the ".user-search" "css_element"
    And "View all results (1)" "option_role" should not be visible

  Scenario: A teacher can search to find specified users
    # Case: Standard search.
    Given I set the field "Search users" to "Dummy User"
    And I wait until "View all results (1)" "option_role" exists
    When I click on "Dummy User" "list_item"
    Then the following should exist in the "generaltable" table:
      | -1-                |
      | Dummy User         |
    And the following should not exist in the "generaltable" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Turtle Manatee     |

    # Case: No users found.
    When I set the field "Search users" to "Plagiarism"
    And I should see "No results for \"Plagiarism\""
    # Table remains unchanged as the user had no results to select from the dropdown.
    And the following should exist in the "generaltable" table:
      | -1-                |
      | Dummy User         |
    And the following should not exist in the "generaltable" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Turtle Manatee     |

    # Case: Multiple users found and select only one result.
    Then I set the field "Search users" to "User"
    And I wait until "View all results (3)" "option_role" exists
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
    And the following should exist in the "generaltable" table:
      | -1-                |
      | Dummy User         |
    And the following should not exist in the "generaltable" table:
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
    And I wait until "View all results (3)" "option_role" exists
    # Dont need to check if all users are in the dropdown, we checked that earlier in this test.
    And I click on "View all results (3)" "option_role"
    And I wait until the page is ready
    And the following should exist in the "generaltable" table:
      | -1-                |
      | Dummy User         |
      | User Example       |
      | User Test          |
    And the following should not exist in the "generaltable" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | Turtle Manatee     |
    And I click on "Clear" "link" in the ".user-search" "css_element"
    And I wait until the page is ready
    And the following should exist in the "generaltable" table:
      | -1-                |
      | Turtle Manatee     |
      | Student 1          |
      | User Example       |
      | User Test          |
      | Dummy User         |

  Scenario: A teacher can quickly tell that a search is active on the current table
    When I click on "Turtle" in the "Search users" search combo box
    # The search input should contain the name of the user we have selected, so that it is clear that the result pertains to a specific user.
    Then the field "Search users" matches value "Turtle Manatee"
    # Test if we can then further retain the turtle result set and further filter from there.
    And I set the field "Search users" to "Turtle plagiarism"
    And "Turtle Manatee" "list_item" should not be visible
    And I should see "No results for \"Turtle plagiarism\""

  Scenario: A teacher can search for values besides the users' name
    Given I set the field "Search users" to "student5@example.com"
    And I wait until "View all results (1)" "option_role" exists
    And "Turtle Manatee" "list_item" should exist
    And I set the field "Search users" to "@example.com"
    And I wait until "View all results (5)" "option_role" exists
    # Note: All learners match this email & showing emails is current default.
    And I confirm "Dummy User" exists in the "Search users" search combo box
    And I confirm "User Example" exists in the "Search users" search combo box
    And I confirm "User Test" exists in the "Search users" search combo box
    And I confirm "Student 1" exists in the "Search users" search combo box
    And I confirm "Turtle Manatee" exists in the "Search users" search combo box
    # Search on the country field.
    When I set the field "Search users" to "JP"
    And I wait until "Turtle Manatee" "list_item" does not exist
    And I confirm "Dummy User" exists in the "Search users" search combo box
    And I confirm "User Test" exists in the "Search users" search combo box
    # Search on the city field.
    And I set the field "Search users" to "Hanoi"
    And I wait until "User Test" "list_item" does not exist
    And I confirm "Student 1" exists in the "Search users" search combo box
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
    And I wait until "Turtle Manatee" "list_item" exists
    And I confirm "Dummy User" exists in the "Search users" search combo box
    And I confirm "User Example" exists in the "Search users" search combo box
    And I confirm "User Test" exists in the "Search users" search combo box
    And I confirm "Student 1" exists in the "Search users" search combo box
    And I press the down key
    And I press the enter key
    And I wait "1" seconds
    And the following should exist in the "generaltable" table:
      | -1-                |
      | Student 1          |
    And the following should not exist in the "generaltable" table:
      | -1-                |
      | User Example       |
      | User Test          |
      | Dummy User         |
      | Turtle Manatee     |
      | Teacher 1          |

  Scenario: A teacher can set focus and search using the input are with a keyboard
    Given I set the field "Search users" to "ABC"
    And the focused element is "Search users" "field"
    And I wait until "Turtle Manatee" "option_role" exists
    # Basic tests for the page.
    When I press the down key
    And ".active" "css_element" should exist in the "Student 1" "option_role"
    And I press the up key
    And ".active" "css_element" should exist in the "View all results (5)" "option_role"
    And I press the down key
    And ".active" "css_element" should exist in the "Student 1" "option_role"
    And I press the escape key
    And the focused element is "Search users" "field"
    Then I set the field "Search users" to "Goodmeme"
    And I press the down key
    And the focused element is "Search users" "field"
    And I set the field "Search users" to "ABC"
    And I wait until "Turtle Manatee" "option_role" exists
    And I press the down key
    And ".active" "css_element" should exist in the "Student 1" "option_role"
    # Lets check the tabbing order.
    And I set the field "Search users" to "ABC"
    And I click on "Search users" "field"
    And I wait until "Turtle Manatee" "option_role" exists
    And I press the tab key
    And the focused element is "Clear search input" "button"
    And I press the tab key
    And ".groupsearchwidget" "css_element" should exist
    # Ensure we can interact with the input & clear search options with the keyboard.
    # Space & Enter have the same handling for triggering the two functionalities.
    And I set the field "Search users" to "User"
    And I press the up key
    And I press the enter key
    And I wait "1" seconds
    And the following should exist in the "generaltable" table:
      | -1-                |
      | Dummy User         |
      | User Example       |
      | User Test          |
    And the following should not exist in the "generaltable" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | Turtle Manatee     |

  Scenario: Once a teacher searches, it'll apply the currently set filters and inform the teacher as such
    # Set up a basic filtering case.
    Given I click on "Advanced group" in the "Search groups" search combo box
    And the following should exist in the "generaltable" table:
      | -1-                |
      | User Example       |
    And the following should not exist in the "generaltable" table:
      | -1-                |
      | Teacher 1          |
      | Student 1          |
      | User Test          |
      | Dummy User         |
      | Turtle Manatee     |
    # Begin the search checking if we are adhering the filters.
    When I set the field "Search users" to "Turtle"
    Then I confirm "Turtle Manatee" does not exist in the "Search users" search combo box

  Scenario: As a teacher I can dynamically find users whilst ignoring pagination
    Given "11" "users" exist with the following data:
      | username  | students[count]             |
      | firstname | Student                     |
      | lastname  | s[count]                    |
      | email     | students[count]@example.com |
    And "11" "course enrolments" exist with the following data:
      | user   | students[count] |
      | course | C1              |
      | role   |student          |
    And I reload the page
    And the field "perpage" matches value "10"
    And the following should not exist in the "generaltable" table:
      | -1-                |
      | Student s11        |
    When I set the field "Search users" to "11"
    # One of the users' phone numbers also matches.
    And I wait until "View all results (1)" "option_role" exists
    Then I confirm "Student s11" exists in the "Search users" search combo box
