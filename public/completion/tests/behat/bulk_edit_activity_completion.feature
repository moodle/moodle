@core @core_completion @javascript
Feature: Allow teachers to bulk edit activity completion rules in a course.
  In order to avoid editing single activities
  As a teacher
  I need to be able to edit the completion rules for a group of activities.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | First | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 30 |
      | assign | C1 | a2 | Test assignment two | Submit something! | 10 |
      | assign | C1 | a3 | Test assignment three | Submit something! | 15 |
      | assign | C1 | a4 | Test assignment four | Submit nothing! | 15 |
    And I log out

  # Given I am a teacher in a course with completion tracking enabled and activities present.
  # When I bulk edit activity completion rules for activities of the same kind.
  # Then the completion rules should be updated for all selected activities.
  Scenario: Bulk edit activity completion rules
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Bulk edit activity completion"
    And I click on "Test assignment one" "checkbox"
    And I click on "Test assignment two" "checkbox"
    And I click on "Edit" "button"
    And I should see "The changes will affect the following 2 activities or resources:"
    And I set the following fields to these values:
      | Add requirements           | 1 |
      | View the activity          | 1 |
      | Make a submission          | 1 |
      | Receive a grade            | 1 |
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
    And I should see "With conditions" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "View the activity" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "Receive a grade" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "Make a submission" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should not see "Completion expected on" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "With conditions" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should see "View the activity" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should see "Receive a grade" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should see "Make a submission" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should not see "Completion expected on" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"

  # Same conditions as above,
  # However if completionpassgrade is set, only the completionpassgrade detail should be shown.
  # It is implied requires grade is selected as it passgrade is dependent on it.
  Scenario: Bulk edit passing grade completion
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Bulk edit activity completion"
    And I click on "Test assignment one" "checkbox"
    And I click on "Test assignment two" "checkbox"
    And I click on "Edit" "button"
    And I should see "The changes will affect the following 2 activities or resources:"
    And I set the field "Add requirements" to "1"
    And I should see "Make a submission"
    And I set the field "Receive a grade" to "1"
    And I set the field "Passing grade" to "1"
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
    And I should see "With conditions" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "Passing grade" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should not see "Completion expected on" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "With conditions" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should see "Passing grade" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should not see "Completion expected on" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"

  @accessibility
  Scenario: Evaluate the accessibility of the bulk edit activity completion page
    Given I am on the "Course 1" course page logged in as "teacher1"
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Bulk edit activity completion"
    And the page should meet accessibility standards
