@tool_behat
Feature: Behat steps for interacting with form work
  In order to test my Moodle code
  As a developer
  I need the Behat steps for form elements to work reliably

  @javascript
  Scenario: Test fields in containers
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    When I log in as "admin"
    And I am on "Course 1" course homepage
    # Just get to any form.
    And I navigate to "Edit settings" in current page administration
    And I set the field "Course full name" in the "General" "fieldset" to "Frog"
    And I set the following fields in the "Appearance" "fieldset" to these values:
      | Show activity reports   | Yes |
      | Number of announcements | 1   |
    Then the field "Show activity reports" in the "Appearance" "fieldset" matches value "Yes"
    And the field "Show activity reports" in the "Appearance" "fieldset" does not match value "No"
    And the following fields in the "region-main" "region" match these values:
      | Course full name        | Frog |
      | Number of announcements | 1    |
    And the following fields in the "region-main" "region" do not match these values:
      | Course full name        | Course 1 |
      | Number of announcements | 5        |
