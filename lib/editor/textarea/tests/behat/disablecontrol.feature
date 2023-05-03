@editor @editor_textarea @texarea @editor_moodleform
Feature: Text area with enable/disable function.
  In order to test enable/disable function
  I set default editor is Text area editor, and I create a sample page to test this feature.
  As a user
  I need to enable/disable content of editor if "enable/disable" feature enabled.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity | name | intro                                                                                                   | course | idnumber |
      | label    | L1   | <a href="../lib/editor/tests/fixtures/disable_control_example.php">Control Enable/Disable Text area</a> | C1     | label1   |
    And I log in as "admin"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I click on "Control Enable/Disable Text area" "link" in the "region-main" "region"

  @javascript
  Scenario: Check disable Text area editor.
    When I set the field "mycontrol" to "Disable"
    Then the "readonly" attribute of "textarea#id_myeditor" "css_element" should be set

  @javascript
  Scenario: Check enable Text area editor.
    When I set the field "mycontrol" to "Enable"
    Then the "readonly" attribute of "textarea#id_myeditor" "css_element" should not be set
