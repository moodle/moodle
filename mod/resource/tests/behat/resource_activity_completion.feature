@mod @mod_resource @core_completion @_file_upload
Feature: View activity completion information for file resources
  In order to have visibility of file resource completion requirements
  As a student
  I need to be able to view my file resource completion progress

  Background:
    Given the following "users" exist:
      | username | firstname  | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | displayoptions | 0,1,2,3,4,5,6 | resource |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show activity completion conditions | No  |
    And I press "Save and display"

  @javascript
  Scenario Outline: The manual completion button will be shown on the course page for Open, In pop-up, New window and Force download display mode if Show activity completion conditions is set to No
    Given I am on "Course 1" course homepage with editing mode on
    And I add a "File" to section "1"
    And I set the following fields to these values:
      | Name                      | Myfile                                               |
      | id_display                | <display>                                            |
      | Show size                 | 0                                                    |
      | Show type                 | 0                                                    |
      | Show upload/modified date | 0                                                    |
      | Completion tracking       | Students can manually mark the activity as completed |
    And I upload "mod/resource/tests/fixtures/samplefile.txt" file to "Select files" filemanager
    And I press "Save and return to course"
    # Teacher view.
    And the manual completion button for "Myfile" should exist
    And the manual completion button for "Myfile" should be disabled
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then the manual completion button for "Myfile" should exist
    And the manual completion button of "Myfile" is displayed as "Mark as done"
    And I toggle the manual completion state of "Myfile"
    And the manual completion button of "Myfile" is displayed as "Done"

    Examples:
      | display        |
      | Open           |
      | In pop-up      |
      | Force download |
      | New window     |

  @javascript
  Scenario: The manual completion button will be shown on the activity page and course page if Show activity completion conditions is set to Yes
    Given I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Show activity completion conditions" to "Yes"
    And I press "Save and display"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "File" to section "1"
    And I set the following fields to these values:
      | Name                | Myfile                                               |
      | id_display          | Embed                                                |
      | Completion tracking | Students can manually mark the activity as completed |
    And I upload "mod/resource/tests/fixtures/samplefile.txt" file to "Select files" filemanager
    And I click on "Save and return to course" "button"
    # Teacher view.
    And the manual completion button for "Myfile" should exist
    And the manual completion button for "Myfile" should be disabled
    And I am on the "Myfile" "resource activity" page
    And the manual completion button for "Myfile" should exist
    And the manual completion button for "Myfile" should be disabled
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then the manual completion button for "Myfile" should exist
    And I am on the "Myfile" "resource activity" page
    And the manual completion button of "Myfile" is displayed as "Mark as done"
    And I toggle the manual completion state of "Myfile"
    And the manual completion button of "Myfile" is displayed as "Done"

  @javascript
  Scenario: View automatic completion items
    Given I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Show activity completion conditions" to "Yes"
    And I press "Save and display"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "File" to section "1"
    And I set the following fields to these values:
      | Name                | Myfile                                     |
      | id_display          | Embed                                             |
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view        | 1                                                 |
    And I upload "mod/resource/tests/fixtures/samplefile.txt" file to "Select files" filemanager
    And I press "Save and display"
    And I am on "Course 1" course homepage
    # Teacher view.
    And I am on the "Myfile" "resource activity" page
    And "Myfile" should have the "View" completion condition
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I am on the "Myfile" "resource activity" page
    Then the "View" completion condition of "Myfile" is displayed as "done"
