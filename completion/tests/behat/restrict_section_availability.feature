@core @core_completion
Feature: Restrict sections availability through completion conditions
  In order to control section's contents access through activities completion
  As a teacher
  I need to restrict sections availability using different conditions

  @javascript
  Scenario: Show section greyed-out to students when completion conditions are not satisfied
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@asd.com |
      | student1 | Student | First | student1@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I set the following administration settings values:
      | Enable completion tracking | 1 |
      | Enable conditional access | 1 |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I follow "Edit settings"
    And I fill the moodle form with:
      | Enable completion tracking | Yes |
    And I press "Save changes"
    And I add a "Label" to section "1" and I fill the form with:
      | Label text | Test label |
      | Completion tracking | Students can manually mark the activity as completed |
    And I add a "Page" to section "2" and I fill the form with:
      | Name | Test page name |
      | Description | Test page description |
      | Page content | Test page contents |
    When I click on "Edit summary" "link" in the "#section-2" "css_element"
    And I fill the moodle form with:
      | id_conditioncompletiongroup_0_conditionsourcecmid | Test label |
      | id_conditioncompletiongroup_0_conditionrequiredcompletion | must be marked complete |
      | Before section can be accessed | Show section greyed-out, with restriction information |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "Not available until the activity Test label is marked complete."
    And I should not see "Test page name"
    And I press "Not completed: Test label. Select to mark as complete."
    And I should see "Test page name"
    And I should not see "Not available until the activity Test label is marked complete."
