@core @core_course
Feature: Activity navigation involving activities with access restrictions
  In order to quickly switch to another activity that has access restrictions
  As a student
  I need to be able to use the activity navigation feature to access the activity after satisfying its access conditions

  Background:
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity  | name    | intro                   | course | idnumber | section |
      | page      | Page 1  | Test page description 1 | C1     | page1    | 0       |
      | page      | Page 2  | Test page description 2 | C1     | page2    | 0       |
      | page      | Page 3  | Test page description 3 | C1     | page3    | 0       |
      | page      | Page 4  | Test page description 4 | C1     | page4    | 0       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    # Set completion for Page 2.
    And I open "Page 2" actions menu
    And I click on "Edit settings" "link" in the "Page 2" activity
    And I expand all fieldsets
    And I set the field "Add requirements" to "1"
    And I set the following fields to these values:
      | Add requirements         | 1                  |
      | View the activity   | 1                                                 |
    And I press "Save and return to course"
    # Require Page 2 to be completed first before Page 3 can be accessed.
    And I open "Page 3" actions menu
    And I click on "Edit settings" "link" in the "Page 3" activity
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the field "Activity or resource" to "Page 2"
    And I press "Save and return to course"
    And I log out

  @javascript
  Scenario: Activity navigation involving activities with access restrictions
    Given I am on the "Page 1" "page activity" page logged in as student1
    Then I should see "Page 2" in the "#next-activity-link" "css_element"
    # Activity that has access restriction should not show up in the dropdown.
    And the "Jump to..." select box should not contain "Page 3"
    And I select "Page 4" from the "Jump to..." singleselect
    # Page 2 should be shown in the previous link since Page 3 is not yet available.
    And I should see "Page 2" in the "#prev-activity-link" "css_element"
    And the "Jump to..." select box should not contain "Page 3"
    # Navigate to Page 2.
    And I click on "Page 2" "link" in the "page-content" "region"
    # Since Page 2 has now been viewed and deemed completed, Page 3 can now be accessed.
    And I should see "Page 3" in the "#next-activity-link" "css_element"
    And the "Jump to..." select box should contain "Page 3"
