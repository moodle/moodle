@editor @editor_tinymce @tinymce @editor_moodleform
Feature: Tinymce with enable/disable function.
  In order to test enable/disable function
  I set default editor is Tinymce editor, and I create a sample page to test this feature.
  As a user
  I need to enable/disable all buttons/plugins and content of editor if "enable/disable" feature enabled.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity | name | intro                                                                                                 | course | idnumber |
      | label    | L1   | <a href="../lib/editor/tests/fixtures/disable_control_example.php">Control Enable/Disable Tinymce</a> | C1     | label1   |
    And I log in as "admin"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "TinyMCE HTML editor"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Control Enable/Disable Tinymce"

  @javascript
  Scenario: Check disable Tinymce editor.
    When I click on "option[value=1]" "css_element" in the "select#id_mycontrol" "css_element"
    Then the "class" attribute of "a#id_myeditor_pdw_toggle" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "table#id_myeditor_formatselect" "css_element" should contain "mceListBoxDisabled"
    And the "class" attribute of "a#id_myeditor_bold" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_italic" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_bullist" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_numlist" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_link" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_unlink" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_moodlenolink" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_image" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_moodlemedia" "css_element" should contain "mceButtonDisabled"
    And I switch to "id_myeditor_ifr" iframe
    And the "contenteditable" attribute of "body" "css_element" should contain "false"

  @javascript
  Scenario: Check enable Tinymce editor.
    When I click on "option[value=0]" "css_element" in the "select#id_mycontrol" "css_element"
    Then the "class" attribute of "a#id_myeditor_pdw_toggle" "css_element" should contain "mceButtonEnabled"
    And the "class" attribute of "table#id_myeditor_formatselect" "css_element" should contain "mceListBoxEnabled"
    And the "class" attribute of "a#id_myeditor_bold" "css_element" should contain "mceButtonEnabled"
    And the "class" attribute of "a#id_myeditor_italic" "css_element" should contain "mceButtonEnabled"
    And the "class" attribute of "a#id_myeditor_bullist" "css_element" should contain "mceButtonEnabled"
    And the "class" attribute of "a#id_myeditor_numlist" "css_element" should contain "mceButtonEnabled"
    And the "class" attribute of "a#id_myeditor_link" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_unlink" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_moodlenolink" "css_element" should contain "mceButtonDisabled"
    And the "class" attribute of "a#id_myeditor_image" "css_element" should contain "mceButtonEnabled"
    And the "class" attribute of "a#id_myeditor_moodlemedia" "css_element" should contain "mceButtonEnabled"
    And I switch to "id_myeditor_ifr" iframe
    And the "contenteditable" attribute of "body" "css_element" should contain "true"
