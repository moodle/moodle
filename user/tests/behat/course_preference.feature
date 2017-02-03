@core @core_user
Feature: As a user, "Course preferences" allows me to set my course preference(s).
  Background:
    Given I log in as "admin"
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
    And I am on site homepage
    And I follow "Preferences" in the user menu
    And I follow "Course preferences"

  @javascript
  Scenario: As a user, "activity chooser" should be the default.
    # See that the "activity chooser" is enabled by default.
    Given the field "enableactivitychooser" matches value "1"
    # See that the "activity chooser" is actually shown by default in course page.
    When I am on homepage
    And I follow "Course 1"
    And I should not see "Add an activity or resource" in the "Topic 1" "section"
    And I turn editing mode on
    Then I should see "Add an activity or resource" in the "Topic 1" "section"
    And I should not see "Add a resource..." in the "Topic 1" "section"

  @javascript
  Scenario: As a user, "activity chooser" should be disabled when I uncheck it in "Course preferences"
    Given I set the field "enableactivitychooser" to "0"
    And I press "Save changes"
    When I am on homepage
    And I follow "Course 1"
    And I should not see "Add a resource..." in the "Topic 1" "section"
    And I turn editing mode on
    Then I should see "Add a resource..." in the "Topic 1" "section"
    And I should not see "Add an activity or resource" in the "Topic 1" "section"