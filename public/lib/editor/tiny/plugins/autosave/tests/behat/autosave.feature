@editor @editor_tiny @tiny_autosave
Feature: Tiny editor autosave
    In order to prevent data loss
    As a content creator
    I need my content to be saved automatically

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode | description | summaryformat |
      | Course 1 | C1        | 0        | 1         |             | 1             |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |

  @javascript
  Scenario: Restore a draft on user profile page
    Given I log in as "teacher1"
    And I open my profile in edit mode
    And I set the field "Description" to "This is my draft"
    And I log out
    When I log in as "teacher1"
    And I open my profile in edit mode
    Then the field "Description" matches value "This is my draft"

  @javascript
  Scenario: Do not restore a draft if files have been modified
    Given the following "user private file" exists:
      | user     | teacher2                                                |
      | filepath | lib/editor/tiny/tests/behat/fixtures/tinyscreenshot.png |
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I set the field "Course summary" to "This is my draft"
    And I log out
    And I am on the "Course 1" course page logged in as teacher2
    And I navigate to "Settings" in current page administration
    And I set the field "Course summary" to "<p>Image test</p>"
    And I select the "p" element in position "1" of the "Course summary" TinyMCE editor
    And I click on the "Image" button for the "Course summary" TinyMCE editor
    And I click on "Browse repositories" "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "tinyscreenshot.png" "link"
    And I click on "Select this file" "button"
    And I set the field "How would you describe this image to someone who cannot see it?" to "It's the Moodle"
    And I click on "Save" "button" in the "Image details" "dialogue"
    And I click on "Save and display" "button"
    When I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Settings" in current page administration
    Then I should not see "This is my draft"

  @javascript
  Scenario: Do not restore a draft if text has been modified
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I set the field "Course summary" to "This is my draft"
    And I am on the "Course 1" course page logged in as teacher2
    And I navigate to "Settings" in current page administration
    And I set the field "Course summary" to "Modified text"
    And I click on "Save and display" "button"
    When I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Settings" in current page administration
    Then I should not see "This is my draft" in the "#id_summary_editor" "css_element"
    And the field "Course summary" matches value "<p>Modified text</p>"

  @javascript
  Scenario: Draft should not be restored if the form was submitted via Javascript
    Given I am on the "Course 1" course page logged in as teacher1
    And I follow "Calendar" in the user menu
    And I click on "New event" "button"
    And I click on "Show more..." "link" in the "New event" "dialogue"
    And I set the field "Event title" to "Test course event"
    And I set the field "Description" to "This is my draft"
    And I click on "Save" "button"
    And I click on "New event" "button"
    When I click on "Show more..." "link" in the "New event" "dialogue"
    Then the field "Description" matches value ""

  @javascript
  Scenario: Permissions can be configured to control access to autosave
    Given the following "roles" exist:
      | name           | shortname | description         | archetype      |
      | Custom teacher | custom1   | Limited permissions | editingteacher |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher3 | Teacher   | 3        | teacher3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher3 | C1     | custom1 |
    And the following "activity" exists:
      | activity | assign          |
      | course   | C1              |
      | name     | Test assignment |
    And the following "permission overrides" exist:
      | capability        | permission | role    | contextlevel | reference |
      | tiny/autosave:use | Prohibit   | custom1 | Course       | C1        |
    # Check plugin access as a role with prohibited permissions.
    And I log in as "teacher3"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    And I set the field "Activity instructions" to "This is my draft"
    And I log out
    And I log in as "teacher3"
    And I am on the "Test assignment" Activity page
    When I navigate to "Settings" in current page administration
    Then the field "Activity instructions" matches value ""
    # Check plugin access as a role with allowed permissions.
    And I log in as "teacher1"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    And I set the field "Activity instructions" to "This is my draft"
    And I log out
    And I log in as "teacher1"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    And the field "Activity instructions" matches value "This is my draft"
