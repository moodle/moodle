@editor @editor_tiny @tiny_html @javascript
Feature: Edit HTML in TinyMCE
  To write rich text - I need to be able to easily edit the HTML.

  Scenario: View HTML in TinyMCE source code view
    Given I log in as "admin"
    When I open my profile in edit mode
    And I set the field "Description" to "This is my draft"
    And I click on the "View > Source code" menu item for the "Description" TinyMCE editor
    And I should see "Source code"
    Then I should see "<p>This is my draft</p>" source code for the "Description" TinyMCE editor

  Scenario: View multiline HTML with indenting in TinyMCE source code view
    Given I log in as "admin"
    When I open my profile in edit mode
    And I set the field "Description" to "<div><p>This is my draft</p></div>"
    And I click on the "View > Source code" menu item for the "Description" TinyMCE editor
    And I should see "Source code"
    Then I should see this multiline source code for the "Description" TinyMCE editor:
      """
      <div>
        <p>This is my draft</p>
      </div>
      """

  Scenario: Permissions can be configured to control access to HTML features
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "roles" exist:
      | name           | shortname | description         | archetype      |
      | Custom teacher | custom1   | Limited permissions | editingteacher |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | custom1        |
    And the following "activity" exists:
      | activity | assign          |
      | course   | C1              |
      | name     | Test assignment |
    And the following "permission overrides" exist:
      | capability    | permission | role    | contextlevel | reference |
      | tiny/html:use | Prohibit   | custom1 | Course       | C1        |
    # Check plugin access as a role with prohibited permissions.
    And I log in as "teacher2"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    And I set the field "Activity instructions" to "<div><p>This is my draft</p></div>"
    When I click on the "View > Source code" menu item for the "Activity instructions" TinyMCE editor
    Then "#id_activityeditor_codeMirrorContainer" "css_element" should not exist
    # Check plugin access as a role with allowed permissions.
    And I log in as "teacher1"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    And I set the field "Activity instructions" to "<div><p>This is my draft</p></div>"
    And I click on the "View > Source code" menu item for the "Activity instructions" TinyMCE editor
    And "#id_activityeditor_codeMirrorContainer" "css_element" should exist
