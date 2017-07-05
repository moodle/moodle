@core_form
Feature: There is a form element allowing to select filetypes
  In order to test the filetypes field
  As an admin
  I need a test form that makes use of the filetypes field

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name | intro                                                              | course | idnumber |
      | label      | L1   | <a href="../lib/form/tests/fixtures/filetypes.php">FixtureLink</a> | C1     | label1   |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "FixtureLink"

  Scenario: File types can be provided via direct input with JavaScript disabled
    Given I set the field "Choose from all file types" to ".png .gif .jpg"
    When I press "Save changes"
    Then the field "Choose from all file types" matches value ".png .gif .jpg"

  @javascript
  Scenario: File types can be provided via direct input with JavaScript enabled
    Given I set the field "Choose from all file types" to ".png .gif .jpg"
    When I press "Save changes"
    Then the field "Choose from all file types" matches value ".png .gif .jpg"