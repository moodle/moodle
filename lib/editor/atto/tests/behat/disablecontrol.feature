@editor @editor_atto @atto @editor_moodleform
Feature: Atto with enable/disable function.
  In order to test enable/disable function
  I create a sample page to test this feature.
  As a user
  I need to enable/disable all buttons/plugins and content of editor if "enable/disable" feature enabled.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity | name | intro                                                                                              | course | idnumber |
      | label    | L1   | <a href="../lib/editor/tests/fixtures/disable_control_example.php">Control Enable/Disable Atto</a> | C1     | label1   |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I click on "Control Enable/Disable Atto" "link" in the "region-main" "region"

  @javascript
  Scenario: Check disable Atto editor.
    When I set the field "mycontrol" to "Disable"
    Then the "disabled" attribute of "button.atto_collapse_button" "css_element" should be set
    And the "disabled" attribute of "button.atto_title_button" "css_element" should be set
    And the "disabled" attribute of "button.atto_bold_button" "css_element" should be set
    And the "disabled" attribute of "button.atto_italic_button" "css_element" should be set
    And the "disabled" attribute of "button.atto_unorderedlist_button_insertUnorderedList" "css_element" should be set
    And the "disabled" attribute of "button.atto_orderedlist_button_insertOrderedList" "css_element" should be set
    And the "disabled" attribute of "button.atto_link_button" "css_element" should be set
    And the "disabled" attribute of "button.atto_link_button_unlink" "css_element" should be set
    And the "disabled" attribute of "button.atto_image_button" "css_element" should be set
    And the "contenteditable" attribute of "div#id_myeditoreditable" "css_element" should contain "false"

  @javascript
  Scenario: Check enable Atto editor.
    When I set the field "mycontrol" to "Enable"
    Then the "disabled" attribute of "button.atto_collapse_button" "css_element" should not be set
    And the "disabled" attribute of "button.atto_title_button" "css_element" should not be set
    And the "disabled" attribute of "button.atto_bold_button" "css_element" should not be set
    And the "disabled" attribute of "button.atto_italic_button" "css_element" should not be set
    And the "disabled" attribute of "button.atto_unorderedlist_button_insertUnorderedList" "css_element" should not be set
    And the "disabled" attribute of "button.atto_orderedlist_button_insertOrderedList" "css_element" should not be set
    And the "disabled" attribute of "button.atto_link_button" "css_element" should not be set
    And the "disabled" attribute of "button.atto_link_button_unlink" "css_element" should not be set
    And the "disabled" attribute of "button.atto_image_button" "css_element" should not be set
    And the "contenteditable" attribute of "div#id_myeditoreditable" "css_element" should contain "true"
