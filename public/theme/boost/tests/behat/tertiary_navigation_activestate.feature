@core @javascript @theme_boost
Feature: Menu navigation accurately updates checkmarks in tertiary navigation
In order to correctly navigate the menu items
As a teacher
I need to see accurate checkmarks when navigating back and forward

  Background:
    Given the following "courses" exist:
      | fullname | shortname | newsitems |
      | Course 1 | C1        | 5 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Ensure the tertiary navigation checkmark updates correctly when navigating in the Grades page
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Grades" in current page administration
    Then dropdown item "Grader report" should be active
    When I click on "Grader report" "combobox"
    And I select "Scales" from the dropdown
    Then dropdown item "Scales" should be active
    And dropdown item "Grader report" should not be active

  Scenario: Ensure the tertiary navigation checkmark updates correctly after pressing browser back button in the Grades page
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Grades" in current page administration
    Then I should see "Grades" is active in secondary navigation
    When I click on "Grader report" "combobox"
    And I select "Scales" from the dropdown
    And I click on "Scales" "combobox"
    And I select "Grade letters" from the dropdown
    And I click on "Grade letters" "combobox"
    And I select "Import" from the dropdown
    And I click on "Import" "combobox"
    And I select "Export" from the dropdown
    And I click on "Export" "combobox"
    And I press the "back" button in the browser
    Then dropdown item "Import" should be active
    When I press the "back" button in the browser
    Then dropdown item "Grade letters" should be active
    When I press the "back" button in the browser
    Then dropdown item "Scales" should be active
    When I press the "back" button in the browser
    Then dropdown item "Grader report" should be active

  Scenario: Ensure the tertiary navigation checkmark updates correctly after pressing browser forward button in the Grades page
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Participants" in current page administration
    Then I should see "Participants" is active in secondary navigation
    When I click on "Enrolled users" "combobox"
    And I select "Groups" from the dropdown
    And I click on "Groups" "combobox"
    And I select "Permissions" from the dropdown
    And I press the "back" button in the browser
    And I press the "back" button in the browser
    And I press the "forward" button in the browser
    Then dropdown item "Groups" should be active

  Scenario: Ensure the tertiary navigation checkmark updates correctly after pressing browser back button in the Participants page
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Participants" in current page administration
    Then I should see "Participants" is active in secondary navigation
    When I click on "Enrolled users" "combobox"
    And I select "Permissions" from the dropdown
    And I click on "Permissions" "combobox"
    And I select "Groups" from the dropdown
    And I click on "Groups" "combobox"
    And I select "Role renaming" from the dropdown
    And I click on "Role renaming" "combobox"
    And I press the "back" button in the browser
    Then dropdown item "Groups" should be active
    When I press the "back" button in the browser
    Then dropdown item "Permissions" should be active

  Scenario: Ensure the tertiary navigation checkmark updates correctly after pressing browser forward button in the Participants page
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Participants" in current page administration
    Then I should see "Participants" is active in secondary navigation
    When I click on "Enrolled users" "combobox"
    And I select "Groups" from the dropdown
    And I click on "Groups" "combobox"
    And I select "Permissions" from the dropdown
    And I press the "back" button in the browser
    And I press the "back" button in the browser
    And I press the "forward" button in the browser
    Then dropdown item "Groups" should be active

  Scenario: Ensure the tertiary navigation checkmark updates correctly after pressing browser back button in the Reports page
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Competency breakdown" "link"
    Then I should see "Reports" is active in secondary navigation
    When I click on "Competency breakdown" "combobox"
    And I select "Logs" from the dropdown
    And I click on "Logs" "combobox"
    And I select "Course participation" from the dropdown
    And I click on "Course participation" "combobox"
    And I select "Activity report" from the dropdown
    And I click on "Activity report" "combobox"
    And I press the "back" button in the browser
    Then dropdown item "Course participation" should be active
    When I press the "back" button in the browser
    Then dropdown item "Logs" should be active

  Scenario: Ensure the tertiary navigation checkmark updates correctly after pressing browser forward button in the Reports page
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Competency breakdown" "link"
    Then I should see "Reports" is active in secondary navigation
    When I click on "Competency breakdown" "combobox"
    And I select "Activity report" from the dropdown
    And I click on "Activity report" "combobox"
    And I select "Course participation" from the dropdown
    And I press the "back" button in the browser
    And I press the "forward" button in the browser
    Then dropdown item "Course participation" should be active
    And dropdown item "Competency breakdown" should not be active
    And dropdown item "Logs" should not be active
    And dropdown item "Live logs" should not be active
    And dropdown item "Activity report" should not be active

  Scenario: Admin can see checkmark beside menu item they are currently on after pressing browser back button when
  jumping between secondary navigation menu
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to "Participants" in current page administration
    Then I should see "Participants" is active in secondary navigation
    When I click on "Enrolled users" "combobox"
    And I navigate to "Grades" in current page administration
    And I press the "back" button in the browser
    Then I should see "Participants" is active in secondary navigation
    And dropdown item "Enrolled users" should be active
    When I navigate to "Reports" in current page administration
    And I click on "Competency breakdown" "link"
    And I navigate to "Competencies" in current page administration
    And I press the "back" button in the browser
    Then I should see "Reports" is active in secondary navigation
    And dropdown item "Competency breakdown" should be active

  Scenario: Ensure checkmark is not updated when disableactive is enabled
    Given I log in as "teacher1"
    When I am on fixture page "/lib/tests/behat/fixtures/select_menu_disableactive_testpage.php"
    Then the field with xpath "//input[@name='fixtureselect']" matches value "opt1"
    When I click on "//*[@role='combobox']" "xpath_element"
    Then dropdown item "Option 1" should be active
    And I select "Option 2" from the dropdown
    Then dropdown item "Option 2" should not be active
    And dropdown item "Option 1" should be active
    And the field with xpath "//input[@name='fixtureselect']" matches value "opt2"
