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
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show activity completion conditions | No  |
    And I press "Save and display"

  @javascript
  Scenario Outline: The manual completion button will be shown on the course page for Open, In pop-up, New window and Force download display mode if Show activity completion conditions is set to No
    Given the following "activities" exist:
      | activity | course | name   | display   | showsize | showtype | showdate | completion | defaultfilename                            | popupwidth | popupheight | uploaded |
      | resource | C1     | Myfile | <display> | 0        | 0        | 0        | 1          | mod/resource/tests/fixtures/samplefile.txt | 620        | 450         | 1        |
    And I am on "Course 1" course homepage with editing mode on
    # Teacher view.
    And the manual completion button for "Myfile" should exist
    And the manual completion button for "Myfile" should be disabled
    # Student view.
    When I am on the "Course 1" course page logged in as student1
    Then the manual completion button for "Myfile" should exist
    And the manual completion button of "Myfile" is displayed as "Mark as done"
    And I toggle the manual completion state of "Myfile"
    And the manual completion button of "Myfile" is displayed as "Done"

    Examples:
      | display |
      | 5       |
      | 6       |
      | 4       |
      | 3       |

  @javascript
  Scenario: The manual completion button will be shown on the activity page and course page if Show activity completion conditions is set to Yes
    Given the following "activities" exist:
      | activity | course | name   | display | defaultfilename                            | uploaded |
      | resource | C1     | Myfile | 1       | mod/resource/tests/fixtures/samplefile.txt | 1        |
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Show activity completion conditions" to "Yes"
    And I press "Save and display"
    And I am on the "Myfile" "resource activity editing" page
    And I set the following fields to these values:
      | Completion tracking | Students can manually mark the activity as completed |
    And I click on "Save and return to course" "button"
    # Teacher view.
    And the manual completion button for "Myfile" should exist
    And the manual completion button for "Myfile" should be disabled
    And I am on the "Myfile" "resource activity" page
    And the manual completion button for "Myfile" should exist
    And the manual completion button for "Myfile" should be disabled
    # Student view.
    When I am on the "Course 1" course page logged in as student1
    Then the manual completion button for "Myfile" should exist
    And I am on the "Myfile" "resource activity" page
    And the manual completion button of "Myfile" is displayed as "Mark as done"
    And I toggle the manual completion state of "Myfile"
    And the manual completion button of "Myfile" is displayed as "Done"

  @javascript
  Scenario: View automatic completion items
    Given the following "activities" exist:
      | activity | course | name   | display | defaultfilename                            | uploaded |
      | resource | C1     | Myfile | 1       | mod/resource/tests/fixtures/samplefile.txt | 1        |
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Show activity completion conditions" to "Yes"
    And I press "Save and display"
    And I am on the "Myfile" "resource activity editing" page
    And I set the following fields to these values:
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view        | 1                                                 |
    And I press "Save and display"
    # Teacher view.
    And I am on the "Myfile" "resource activity" page
    And "Myfile" should have the "View" completion condition
    # Student view.
    When I am on the "Myfile" "resource activity" page logged in as student1
    Then the "View" completion condition of "Myfile" is displayed as "done"
