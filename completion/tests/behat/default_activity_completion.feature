@core @core_completion
Feature: Allow teachers to edit the default activity completion rules in a course.
  In order to set the activity completion defaults for new activities
  As a teacher
  I need to be able to edit the completion rules for a group of activities.

  # Given I am a teacher in a course with completion tracking enabled and activities present.
  # When I edit activity completion defaults for activity types.
  # Then the completion rule defaults should apply only to activities created from that point onwards.
  @javascript
  Scenario: Bulk edit activity completion default rules
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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    When I navigate to "Course completion" in current page administration
    And I select "Default activity completion" from the "Course completion tertiary navigation" singleselect
    And I click on "Assignments" "checkbox"
    And I click on "Edit" "button"
    And I should see "Completion tracking"
    And I should see "The changes will affect the following 1 activities or resources:"
    And I should see "Student must submit to this activity to complete it"
    And I set the following fields to these values:
      | completion | Show activity as complete when conditions are met|
      | completionview | 1 |
      | completionusegrade | 1 |
      | completionsubmit | 1 |
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
    And I should see "With conditions" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
    And I should see "Student must view this activity to complete it" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
    And I should see "Student must receive a grade to complete this activity" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
    And I should see "Student must submit to this activity to complete it" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
    And I should not see "Completion expected on" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
