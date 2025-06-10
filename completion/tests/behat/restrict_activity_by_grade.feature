@core @core_completion
Feature: Restrict activity availability through grade conditions
  In order to control activity access through grade condition
  As a teacher
  I need to set grade condition to restrict activity access

  @javascript
  Scenario: Show activity greyed-out to students when grade condition is not satisfied
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | course | activity | idnumber         | name             | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | submissiondrafts |
      | C1     | assign   | Grade assignment | Grade assignment | 1                                   | 0                             | 0                |
      | C1     | page     | Grade page       | Test page name   |                                     |                               |                  |
    # Adding the page like this because id_availableform_enabled needs to be clicked to trigger the action.
    And I am on the "Test page name" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grade" "button" in the "Add restriction..." "dialogue"
    And I click on "min" "checkbox"
    And I set the following fields to these values:
      | id     | Grade assignment |
      | minval | 20               |
    And I press "Save and return to course"

    When I am on the "Course 1" course page logged in as student1
    Then I should see "Not available unless: You achieve higher than a certain score in Grade assignment"
    And I should see "Test page name"
    And "Test page name" "link" should not exist in the "region-main" "region"
    And I am on the "Grade assignment" "assign activity" page
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I should see "Submitted for grading"

    And I am on the "Grade assignment" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student First" "table_row"
    And I set the following fields to these values:
      | Grade | 21 |
    And I press "Save changes"

    And I am on the "Course 1" course page logged in as student1
    And "Test page name" activity should be visible
    And I should not see "Not available unless: You achieve higher than a certain score in Grade assignment"
