@core @core_user
Feature: Edit user enrolment
  In order to manage students' enrolments
  As a teacher
  I need to be able to view enrolment details and edit student enrolments in the course participants page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           | status |
      | teacher1 | C1     | editingteacher | 0      |
      | student1 | C1     | student        | 0      |
      | student2 | C1     | student        | 1      |

  @javascript
  Scenario: Edit a user's enrolment
    Given I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Edit enrolment" "icon" in the "student1" "table_row"
    And I should see "Edit Student 1's enrolment"
    And I set the field "Status" to "Suspended"
    And I click on "Save changes" "button"
    And I click on "Edit enrolment" "icon" in the "student2" "table_row"
    And I should see "Edit Student 2's enrolment"
    And I set the field "timeend[enabled]" to "1"
    And I set the field "timeend[day]" to "1"
    And I set the field "timeend[month]" to "January"
    And I set the field "timeend[year]" to "2017"
    And I set the field "Status" to "Active"
    And I click on "Save changes" "button"
    Then I should see "Suspended" in the "student1" "table_row"
    And I should see "Not current" in the "student2" "table_row"

  @javascript
  Scenario: Disabled enrolment dates stay disabled after saving the edit dialogue
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student3 | Student   | 3        | student3@example.com |
    # The enrolment period must be 1 to 365 days, as that is what the enrolment duration menu offers.
    And the following "course enrolments" exist:
      | user     | course | role    | timestart      | timeend      |
      | student3 | C1     | student | ##4 days ago## | ##tomorrow## |
    And I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Edit enrolment" "icon" in the "student3" "table_row"
    And I set the following fields to these values:
      | timestart[enabled] | 0 |
      | timeend[enabled]   | 0 |
    And I click on "Save changes" "button"
    And I click on "Edit enrolment" "icon" in the "student3" "table_row"
    Then the field "timestart[enabled]" matches value "0"
    And the field "timeend[enabled]" matches value "0"

  @javascript
  Scenario: Unenrol a student
    Given I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Unenrol" "icon" in the "student1" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    Then I should not see "Student 1" in the "participants" "table"

  @javascript
  Scenario: A disabled enrolment end date stays disabled when the start date remains enabled
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    | timestart      | timeend      |
      | student3 | C1     | student | ##4 days ago## | ##tomorrow## |
    And I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Edit enrolment" "icon" in the "student3" "table_row"
    And I set the field "timeend[enabled]" to "0"
    And I click on "Save changes" "button"
    And I click on "Edit enrolment" "icon" in the "student3" "table_row"
    Then the field "timestart[enabled]" matches value "1"
    And the field "timeend[enabled]" matches value "0"

  @javascript
  Scenario: View a student's enrolment details
    Given I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Manual enrolments" "icon" in the "student1" "table_row"
    Then I should see "Enrolment details"
    And I should see "Student 1" in the "Full name" "table_row"
    And I should see "Active" in the "//td[@class='user-enrol-status']" "xpath_element"
    And I should see "Manual enrolments" in the "Enrolment method" "table_row"
    And I should see "Enrolment created"
    And I click on "Cancel" "button" in the "Enrolment details" "dialogue"
    And I click on "Manual enrolments" "icon" in the "student2" "table_row"
    And I should see "Enrolment details"
    And I should see "Student 2" in the "Full name" "table_row"
    And I should see "Suspended" in the "//td[@class='user-enrol-status']" "xpath_element"
    And I should see "Manual enrolments" in the "Enrolment method" "table_row"
    And I should see "Enrolment created"
    And "Edit enrolment" "icon" should exist in the "Enrolment method" "table_row"

  @javascript
  Scenario: View a student's enrolment details for a student enrolled via course meta link where editing can't be done
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 2 | C2        | topics |
    And the following "course enrolments" exist:
      | user     | course | role    | status |
      | student3 | C2     | student | 0      |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Manage enrol plugins" in site administration
    And I click on "Enable" "link" in the "Course meta link" "table_row"
    And I add "Course meta link" enrolment method in "Course 1" with:
      | Link course | C2 |
    And I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    Then I should see "Student 3" in the "participants" "table"
    And "Edit enrolment" "icon" should not exist in the "student3" "table_row"
    And "Unenrol" "icon" should not exist in the "student3" "table_row"
    And I click on "Course meta link (Course 2)" "icon" in the "student3" "table_row"
    And I should see "Enrolment details"
    And I should see "Student 3" in the "Full name" "table_row"
    And I should see "Active" in the "//td[@class='user-enrol-status']" "xpath_element"
    And I should see "Course meta link (Course 2)" in the "Enrolment method" "table_row"
    And "Edit enrolment" "icon" should not exist in the "Enrolment method" "table_row"

  @javascript
  Scenario: Edit a student's enrolment details from the status dialogue
    Given I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Manual enrolments" "icon" in the "student2" "table_row"
    And I click on "Edit enrolment" "icon" in the "Enrolment method" "table_row"
    And I should see "Edit Student 2's enrolment"
    And I set the field "Status" to "Active"
    And I click on "Save changes" "button"
    Then I should see "Active" in the "student2" "table_row"

  @javascript
  Scenario: The date time selector popup calendar is keyboard accessible inside a modal dialogue
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Edit enrolment" "icon" in the "student2" "table_row"
    And I should see "Edit Student 2's enrolment"
    # Enable the "Enrolment ends" date so that its fields, including the calendar toggle button,
    # are not disabled. Clicking the checkbox itself avoids ever tabbing away from a native
    # <select>, which some browsers intercept with their own dropdown-list keyboard handling.
    When I click on "timeend[enabled]" "checkbox"
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    Then the focused element is "Date picker" "button" in the "Enrolment ends" "fieldset"
    And I press the enter key
    And I press the tab key
    And the focused element is "Go to previous month" "button"
    And I press the tab key
    And the focused element is "Go to next month" "button"

  @javascript
  Scenario: Pressing escape in the popup calendar closes only the calendar and not the modal dialogue
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Edit enrolment" "icon" in the "student2" "table_row"
    And I should see "Edit Student 2's enrolment"
    And I click on "timeend[enabled]" "checkbox"
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And the focused element is "Date picker" "button" in the "Enrolment ends" "fieldset"
    And I press the enter key
    And I press the tab key
    And the focused element is "Go to previous month" "button"
    When I press the escape key
    # The calendar closes but the enrolment modal must stay open.
    Then I should see "Edit Student 2's enrolment"
    And "Go to previous month" "button" should not be visible
    And the focused element is "Date picker" "button" in the "Enrolment ends" "fieldset"

  @javascript
  Scenario: The date time selector remains usable after cancelling a modal while its calendar is open
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Edit enrolment" "icon" in the "student2" "table_row"
    And I should see "Edit Student 2's enrolment"
    When I click on "timeend[enabled]" "checkbox"
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    Then the focused element is "Date picker" "button" in the "Enrolment ends" "fieldset"
    And I press the enter key
    And I press the tab key
    And the focused element is "Go to previous month" "button"
    # Dismiss the modal directly via its own Cancel button (mouse click), without releasing
    # the calendar first. The "Edit enrolment" modal destroys itself on ModalEvents.hidden
    # (see public/user/amd/src/status_field.js showEditDialogue), while the shared singleton
    # calendar panel is still reparented inside the modal being destroyed.
    And I click on "Cancel" "button" in the "Edit Student 2's enrolment" "dialogue"
    And I should not see "Edit Student 2's enrolment"
    # Re-open the modal and confirm the shared singleton date picker panel still works.
    And I click on "Edit enrolment" "icon" in the "student2" "table_row"
    And I should see "Edit Student 2's enrolment"
    And I click on "timeend[enabled]" "checkbox"
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And the focused element is "Date picker" "button" in the "Enrolment ends" "fieldset"
    And I press the enter key
    And I press the tab key
    And the focused element is "Go to previous month" "button"

  # Without JS, the user should be redirected to the original edit enrolment form.
  Scenario: Edit a user's enrolment without JavaScript
    Given I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Edit enrolment" "link" in the "student1" "table_row"
    And I should see "Student 1"
    And I set the field "Status" to "Suspended"
    And I click on "Save changes" "button"
    And I click on "Edit enrolment" "link" in the "student2" "table_row"
    And I should see "Student 2"
    And I set the field "timeend[enabled]" to "1"
    And I set the field "timeend[day]" to "1"
    And I set the field "timeend[month]" to "January"
    And I set the field "timeend[year]" to "2017"
    And I set the field "Status" to "Active"
    And I click on "Save changes" "button"
    Then I should see "Suspended" in the "student1" "table_row"
    And I should see "Not current" in the "student2" "table_row"

  Scenario: Disabled enrolment dates stay disabled after saving the edit form without JavaScript
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    | timestart      | timeend      |
      | student3 | C1     | student | ##4 days ago## | ##tomorrow## |
    And I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Edit enrolment" "link" in the "student3" "table_row"
    And I set the following fields to these values:
      | timestart[enabled] | 0 |
      | timeend[enabled]   | 0 |
    And I click on "Save changes" "button"
    And I click on "Edit enrolment" "link" in the "student3" "table_row"
    Then the field "timestart[enabled]" matches value "0"
    And the field "timeend[enabled]" matches value "0"

  Scenario: A disabled enrolment end date stays disabled when the start date remains enabled without JavaScript
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    | timestart      | timeend      |
      | student3 | C1     | student | ##4 days ago## | ##tomorrow## |
    And I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Edit enrolment" "link" in the "student3" "table_row"
    And I set the field "timeend[enabled]" to "0"
    And I click on "Save changes" "button"
    And I click on "Edit enrolment" "link" in the "student3" "table_row"
    Then the field "timestart[enabled]" matches value "1"
    And the field "timeend[enabled]" matches value "0"

  # Without JS, the user should be redirected to the original unenrol confirmation page.
  Scenario: Unenrol a student
    Given I am on the "Course 1" "enrolled users" page logged in as "teacher1"
    When I click on "Unenrol" "link" in the "student1" "table_row"
    And I click on "Continue" "button"
    Then I should not see "Student 1" in the "participants" "table"
