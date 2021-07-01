@editor @editor_tinymce @javascript
Feature: Add or remove items from the TinyMCE editor toolbar
  In order to customize the TinyMCE editor appearance
  As an admin
  I need to add and remove items from the toolbar

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "TinyMCE HTML editor"
    And I press "Save changes"
    And I am on homepage

  Scenario: Remove icons
    When the following config values are set as admin:
      | customtoolbar | fontselect,fontsizeselect,formatselect,\|,undo,redo,\|,search,replace,\|,fullscreen | editor_tinymce |
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Database" to section "1"
    Then "#id_introeditor_tbl .mce_bold" "css_element" should not exist
    And "#id_introeditor_tbl .mce_fullscreen" "css_element" should exist
    And I press "Cancel"

  Scenario: Add icons
    When the following config values are set as admin:
      | customtoolbar | fontselect,fontsizeselect,formatselect,\|,undo,redo,\|,search,replace,\|,fullscreen,anchor | editor_tinymce |
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Database" to section "1"
    Then "#id_introeditor_tbl .mce_bold" "css_element" should not exist
    And "#id_introeditor_tbl .mce_anchor" "css_element" should exist
    And the following config values are set as admin:
      | customtoolbar | fontselect,fontsizeselect,formatselect,\|,undo,redo,\|,search,replace,\|,fullscreen | editor_tinymce |
    And I am on "Course 1" course homepage
    And I add a "Database" to section "1"
    And "#id_introeditor_tbl .mce_anchor" "css_element" should not exist
    And I press "Cancel"

  Scenario: Default icons
    Given I am on "Course 1" course homepage with editing mode on
    When I add a "Database" to section "1"
    And I wait until "#id_introeditor_tbl" "css_element" exists
    Then "#id_introeditor_tbl .mce_bold" "css_element" should exist
    And "#id_introeditor_tbl .mce_anchor" "css_element" should not exist
    And I press "Cancel"
