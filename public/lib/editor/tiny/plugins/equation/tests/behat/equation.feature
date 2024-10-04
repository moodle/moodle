@editor @editor_tiny @tiny_equation
Feature: Equation editor
  To teach maths to students, I need to write equations

  @javascript
  Scenario: Create an equation using TinyMCE
    Given I log in as "admin"
    When I open my profile in edit mode
    And I set the field "Description" to "<p>Equation test</p>"
    # Set field on the bottom of page, so equation editor dialogue is visible.
    And I expand all fieldsets
    And I set the field "Picture description" to "Test"
    And I expand all toolbars for the "Description" TinyMCE editor
    And I click on the "Equation editor" button for the "Description" TinyMCE editor
    And the "class" attribute of "Edit equation using" "field" should contain "text-ltr"
    And I set the field "Edit equation using" to " = 1 \div 0"
    And I click on "\infty" "button"
    And I click on "Save equation" "button"
    And I click on "Update profile" "button"
    And I follow "Profile" in the user menu
    # MathJax 3.2.2 renders matemathical equation using css classes, so it will not work against the pre-rendered code like "\infty".
    # That said, we can instead check the rendered text using the rendered equation or symbol "∞".
    Then "∞" "text" should exist

  @javascript
  Scenario: Edit an equation using TinyMCE
    Given I log in as "admin"
    When I open my profile in edit mode
    And I set the field "Description" to "<p>\( \pi \)</p>"
    # Set field on the bottom of page, so equation editor dialogue is visible.
    And I expand all fieldsets
    And I set the field "Picture description" to "Test"
    And I expand all toolbars for the "Description" TinyMCE editor
    And I click on the "Equation editor" button for the "Description" TinyMCE editor
    And the "class" attribute of "Edit equation using" "field" should contain "text-ltr"
    Then the field "Edit equation using" matches value " \pi "
    And I click on "Save equation" "button"
    And the field "Description" matches value "<p>\( \pi \)</p>"

  @javascript
  Scenario: Permissions can be configured to control access to equation editor
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
      | capability        | permission | role    | contextlevel | reference |
      | tiny/equation:use | Prohibit   | custom1 | Course       | C1        |
    # Check plugin access as a role with prohibited permissions.
    And I log in as "teacher2"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    When I click on the "Insert" menu item for the "Activity instructions" TinyMCE editor
    Then I should not see "Equation editor"
    # Check plugin access as a role with allowed permissions.
    And I log in as "teacher1"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    And I click on the "Insert" menu item for the "Activity instructions" TinyMCE editor
    And I should see "Equation editor"
