@core @core_completion
Feature: Allow teachers to bulk edit activity completion rules in a course.
  In order to avoid editing single activities
  As a teacher
  I need to be able to edit the completion rules for a group of activities.

  # Given I am a teacher in a course with completion tracking enabled and activities present.
  # When I bulk edit activity completion rules for activities of the same kind.
  # Then the completion rules should be updated for all selected activities.
  @javascript
  Scenario: Bulk edit activity completion rules
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 300 |
      | assign | C1 | a2 | Test assignment two | Submit something! | 100 |
      | assign | C1 | a3 | Test assignment three | Submit something! | 150 |
      | assign | C1 | a4 | Test assignment four | Submit nothing! | 150 |
    And I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    When I navigate to "Course completion" in current page administration
    And I follow "Bulk edit activity completion"
    And I click on "Test assignment one" "checkbox"
    And I click on "Test assignment two" "checkbox"
    And I click on "Edit" "button"
    And I should see "Completion tracking"
    And I should see "The changes will affect the following 2 activities or resources:"
    And I should see "Student must submit to this activity to complete it"
    And I select "Show activity as complete when conditions are met" from the "completion" singleselect
    And I click on "completionview" "checkbox"
    And I click on "completionusegrade" "checkbox"
    And I click on "completionsubmit" "checkbox"
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
    And I should see "With conditions" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "Student must view this activity to complete it" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "Student must receive a grade to complete this activity" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "Student must submit to this activity to complete it" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should not see "Completion expected on" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment one']]" "xpath_element"
    And I should see "With conditions" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should see "Student must view this activity to complete it" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should see "Student must receive a grade to complete this activity" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should see "Student must submit to this activity to complete it" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
    And I should not see "Completion expected on" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Test assignment two']]" "xpath_element"
