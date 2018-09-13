@mod @mod_checklist @checklist
Feature: A teacher can link a checklist item to a course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | C1        | 0                |
      | Course 2 | C2        | 1                |
      | Course 3 | C3        | 0                |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
      | student1 | Student   | 1        | student1@asd.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | teacher1 | C2     | editingteacher |
      | student1 | C2     | student        |
    And the following config values are set as admin:
      | linkcourses      | 1 | mod_checklist |
      | enablecompletion | 1 |               |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Checklist" to section "1" and I fill the form with:
      | Checklist                                  | Test checklist       |
      | Introduction                               | This is a checklist  |
      | Updates by                                 | Student only         |
      | Check-off when courses or modules complete | Yes, cannot override |

  @javascript
  Scenario: A teacher can link to a course or a URL, but not both
    Given I follow "Test checklist"
    When I set the following fields to these values:
      | displaytext | Item with course link |
      | linkurl     | moodle.org            |
    And the "linkcourseid" "field" should be disabled
    And I press "Add"
    And I follow "Edit this item"
    Then the following fields match these values:
      | displaytext | Item with course link |
      | linkurl     | http://moodle.org     |

    When I set the following fields to these values:
      | displaytext  | Item with course link |
      | linkurl      |                       |
      | linkcourseid | Course 2              |
    And I press "Update"
    And I follow "Preview"
    Then I should see "Item with course link"
    And I follow "Course associated with this item"
    And I should see "Course 2"
    And I should not see "Course 1"

  @javascript
  Scenario: Students cannot check-off items linked to courses, when autocomplete (no override) is on
  Course 2, has completion enabled (should not be able to check-off manually)
  Course 3, does not have completion enabled (should be able to check-off manually)
    Given I follow "Test checklist"
    And I set the following fields to these values:
      | displaytext  | Item with course link |
      | linkcourseid | Course 2              |
    And I press "Add"
    And I set the following fields to these values:
      | displaytext  | Another item with course link |
      | linkcourseid | Course 3                      |
    And I press "Add"
    And I log out

    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
# The first item (linked to Course 2) should be disabled
    Then the "//ol[@class='checklist']/li[1]/input" "xpath_element" should be disabled
    And the field "Item with course link" matches value "0"
    And the field "Another item with course link" matches value "0"

    When I set the field "Another item with course link" to "1"
    And I wait "2" seconds
    And I follow "Test checklist"
    Then the "//ol[@class='checklist']/li[1]/input" "xpath_element" should be disabled
    And the field "Item with course link" matches value "0"
    And the field "Another item with course link" matches value "1"

  @javascript
  Scenario: An item linked to a course is automatically checked-off when that course is completed
    Given I follow "Test checklist"
    And I set the following fields to these values:
      | displaytext  | Item with course link |
      | linkcourseid | Course 2              |
    And I press "Add"
    And I set the following fields to these values:
      | displaytext  | Another item with course link |
      | linkcourseid | Course 3                      |
    And I press "Add"
    And I am on "Course 2" course homepage
    And I navigate to "Course completion" node in "Course administration"
    And I set the following fields to these values:
      | criteria_self | 1 |
    And I press "Save changes"
    And I add the "Self completion" block
    And I log out

    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then I should see "0%" in the "#checklistprogressall" "css_element"
    And the field "Item with course link" matches value "0"

    When I click on "Course associated with this item" "link" in the "Item with course link" "list_item"
    And I follow "Complete course"
    And I press "Yes"
    And I wait "2" seconds
    And I trigger cron
    And I wait "5" seconds
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    Then I should see "50%" in the "#checklistprogressall" "css_element"
    And the field "Item with course link" matches value "1"

  @javascript
  Scenario: An item linked to a course is automatically checked-off if that course is *already* completed
    Given I am on "Course 2" course homepage
    And I navigate to "Course completion" node in "Course administration"
    And I set the following fields to these values:
      | criteria_self | 1 |
    And I press "Save changes"
    And I add the "Self completion" block
    And I log out

    And I log in as "student1"
    And I am on "Course 2" course homepage
    And I follow "Complete course"
    And I press "Yes"
    And I wait "2" seconds
    And I trigger cron
    And I wait "5" seconds
    And I am on site homepage
    And I log out

    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test checklist"
    And I set the following fields to these values:
      | displaytext  | Item with course link |
      | linkcourseid | Course 2              |
    And I press "Add"
    And I set the following fields to these values:
      | displaytext  | Another item with course link |
      | linkcourseid | Course 3                      |
    And I press "Add"

    When I follow "View progress"
    And I click on "View progress for this user" "link" in the "Student 1" "table_row"

    Then I should see "50%" in the "#checklistprogressall" "css_element"
    # No idea why this line doesn't work, but ... it just doesn't.
    And the field "Item with course link" matches value "1"
