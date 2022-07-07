@core @core_course
Feature: View subfolders in a course in-line
  In order to provide different view options for folders
  As a teacher
  I need to add a folders and subfolders and view them inline

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1 | topics | 0 | 5 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Folder" to section "3" and I fill the form with:
      | Name | Test folder |
      | Display folder contents | On a separate page |
      | Show subfolders expanded | |
    And I should see "Test folder"
    And I follow "Test folder"
    And I press "Edit"
    And I press "Create folder"
    And I set the field "New folder name" to "Test subfolder 1"
    And I click on "button.fp-dlg-butcreate" "css_element" in the "div.fp-mkdir-dlg" "css_element"
    And I press "Save changes"

  @javascript
  Scenario: Add a folder with two subfolders - view on separate page
    Given I am on "Course 1" course homepage
    And I should not see "Test subfolder 1"
    And I follow "Test folder"
    And I should see "Test subfolder 1"
    And I press "Edit"
    And I press "Create folder"
    And I set the field "New folder name" to "Test subfolder 2"
    And I click on "button.fp-dlg-butcreate" "css_element" in the "div.fp-mkdir-dlg" "css_element"
    And I press "Save changes"
    When I am on "Course 1" course homepage
    Then I should not see "Test subfolder 2"
    And I follow "Test folder"
    And I should see "Test subfolder 2"
    Given I navigate to "Edit settings" in current page administration
    And I set the field "Show subfolders expanded" to "1"
    When I am on "Course 1" course homepage
    Then I should not see "Test subfolder 2"
    And I follow "Test folder"
    And I should see "Test subfolder 2"

  @javascript
  Scenario: Make the subfolders viewable inline on the course page
    Given I press "Edit"
    And I click on "div.fp-filename" "css_element" in the "div.fp-filename-field" "css_element"
    And I press "Create folder"
    And I set the field "New folder name" to "Test sub subfolder"
    And I click on "button.fp-dlg-butcreate" "css_element" in the "div.fp-mkdir-dlg" "css_element"
    And I press "Save changes"
    And I navigate to "Edit settings" in current page administration
    When I set the field "Display folder contents" to "Inline on a course page"
    And I press "Save and return to course"
    Then I should see "Test subfolder 1"
    And I should not see "Test sub subfolder"
    Given I open "Test folder" actions menu
    When I click on "Edit settings" "link" in the "Test folder" activity
    And I set the field "Show subfolders expanded" to "1"
    And I press "Save and return to course"
    Then I should see "Test subfolder 1"
    And I should see "Test sub subfolder"
