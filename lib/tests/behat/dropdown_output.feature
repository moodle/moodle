@core @javascript
Feature: Test dropdown output module
  In order to show extra information to the user
  As a user
  I need to interact with the dropdown output modules

  Background:
    # Get to the fixture page.
    Given I log in as "admin"
    And I am on fixture page "/lib/tests/behat/fixtures/dropdown_output_testpage.php"
    And I should not see "Dialog content"

  Scenario: User can open a dropdown dialog
    When I click on "Open dialog" "button" in the "regularscenario" "region"
    Then I should see "Dialog content" in the "regularscenario" "region"

  Scenario: Dropdown dialog can have rich content inside
    When I click on "Open dialog" "button" in the "richcontent" "region"
    Then I should see "Some rich content" in the "richcontent" "region"
    And "Link 1" "link" should exist in the "richcontent" "region"
    And "Eye icon" "icon" should exist in the "richbutton" "region"

  Scenario: HTML attributtes can be overriden in dropdowns
    When I click on "Open dialog" "button" in the "cssoverride" "region"
    Then I should see "Dialog content" in the "cssoverride" "region"
    And ".extraclass" "css_element" should exist in the "cssoverride" "region"
    And "[data-foo='bar']" "css_element" should exist in the "extraattributes" "region"
    And I should see "Custom ID button found" in the "customid" "region"
    And "#CustomDropdownButtonId" "css_element" should exist in the "customid" "region"
    And ".dialog-big" "css_element" should exist in the "widths" "region"
    And ".dialog-small" "css_element" should exist in the "widths" "region"

  Scenario: User can open a dropdown status
    When I click on "Open dialog" "button" in the "statusregularscenario" "region"
    Then I should see "Option 1" in the "statusregularscenario" "region"
    And I should see "Option 1 description" in the "statusregularscenario" "region"
    And I should see "Option 2" in the "statusregularscenario" "region"
    And I should see "Option 2 description" in the "statusregularscenario" "region"
    And "Eye icon 1" "icon" should exist in the "statusregularscenario" "region"
    And "Eye icon 2" "icon" should exist in the "statusregularscenario" "region"

  Scenario: Dropdown status can have as selected option
    When I click on "Open dialog" "button" in the "statusselectedscenario" "region"
    Then "Selected" "icon" in the "#statusselectedscenario [data-optionnumber='2']" "css_element" should be visible
    And "Selected" "icon" in the "#statusselectedscenario [data-optionnumber='1']" "css_element" should not be visible
    And "Selected" "icon" in the "#statusselectedscenario [data-optionnumber='3']" "css_element" should not be visible

  Scenario: Dropdown status can have a disabled option
    When I click on "Open dialog" "button" in the "statusdisablescenario" "region"
    Then ".disabled" "css_element" should exist in the "#statusdisablescenario [data-optionnumber='2']" "css_element"
    And ".disabled" "css_element" should not exist in the "#statusdisablescenario [data-optionnumber='1']" "css_element"
    And ".disabled" "css_element" should not exist in the "#statusdisablescenario [data-optionnumber='3']" "css_element"

  Scenario: Dropdown status can have a extra attribute in the options
    When I click on "Open dialog" "button" in the "statusoptionextrasscenario" "region"
    Then "[data-foo='bar']" "css_element" should exist in the "#statusoptionextrasscenario [data-optionnumber='2']" "css_element"
    And "[data-foo='bar']" "css_element" should not exist in the "#statusoptionextrasscenario [data-optionnumber='1']" "css_element"
    And "[data-foo='bar']" "css_element" should not exist in the "#statusoptionextrasscenario [data-optionnumber='3']" "css_element"

  Scenario: Dropdown status can define urls in options
    Given I should see "Foo param value: none"
    When I click on "Open dialog" "button" in the "statusoptionurl" "region"
    And I click on "Option 2" "link" in the "statusoptionurl" "region"
    Then I should see "Foo param value: bar"

  Scenario: Dropdowns dialogs can be controlled via javascript
    Given "Open dialog" "button" should exist in the "dialogjscontrolssection" "region"
    And I should see "The dropdown is hidden" in the "dialogjscontrolssection" "region"
    # Change button text.
    When I click on "Change button text" "button" in the "dialogjscontrolssection" "region"
    Then "New button text" "button" should exist in the "dialogjscontrolssection" "region"
    # Open dropdown.
    And I click on "Open" "button" in the "dialogjscontrolssection" "region"
    And I should see "Dialog content" in the "dialogjscontrolssection" "region"
    And I should see "The dropdown is visible" in the "dialogjscontrolssection" "region"
    # Close dropdown.
    And I click on "Close" "button" in the "dialogjscontrolssection" "region"
    And I should not see "Dialog content" in the "dialogjscontrolssection" "region"
    And I should see "The dropdown is hidden" in the "dialogjscontrolssection" "region"

  Scenario: Dropdown status can sync the clicked option with the button text
    Given I should see "Option 2" in the "statussyncbutton" "region"
    When I click on "Option 2" "button" in the "statussyncbutton" "region"
    And "Selected" "icon" in the "#statussyncbutton [data-optionnumber='2']" "css_element" should be visible
    And "Selected" "icon" in the "#statussyncbutton [data-optionnumber='3']" "css_element" should not be visible
    And I click on "Option 3" "link" in the "statussyncbutton" "region"
    Then I should see "Option 3" in the "statussyncbutton" "region"
    And I should not see "Option 2" in the "statussyncbutton" "region"
    And I click on "Option 3" "button" in the "statussyncbutton" "region"
    And "Selected" "icon" in the "#statussyncbutton [data-optionnumber='2']" "css_element" should not be visible
    And "Selected" "icon" in the "#statussyncbutton [data-optionnumber='3']" "css_element" should be visible

  Scenario: Dropdowns status can be controlled via javascript
    Given "Open dialog" "button" should exist in the "statusjscontrolsection" "region"
    And I should see "The status value is option2" in the "statusjscontrolsection" "region"
    # Change value.
    When I click on "Change selected value" "button" in the "statusjscontrolsection" "region"
    Then I should see "The status value is option3" in the "statusjscontrolsection" "region"
    And I click on "Open dialog" "button" in the "statusjscontrolsection" "region"
    And "Selected" "icon" in the "#statusjscontrolsection [data-optionnumber='2']" "css_element" should not be visible
    And "Selected" "icon" in the "#statusjscontrolsection [data-optionnumber='3']" "css_element" should be visible
    # Enable button sync.
    And I click on "Enable sync" "button" in the "statusjscontrolsection" "region"
    And I should see "Option 3" in the "statusjscontrolsection" "region"
    And I click on "Option 3" "button" in the "statusjscontrolsection" "region"
    And I click on "Option 2" "link" in the "statusjscontrolsection" "region"
    And I should see "The status value is option2" in the "statusjscontrolsection" "region"
    And I should see "Option 2" in the "statusjscontrolsection" "region"
    # Trigger change event with button text sync.
    And I click on "Change selected value" "button" in the "statusjscontrolsection" "region"
    And I should see "Option 3" in the "statusjscontrolsection" "region"
    And I should see "The status value is option3" in the "statusjscontrolsection" "region"
    # Disable button text sync.
    And I click on "Disable sync" "button" in the "statusjscontrolsection" "region"
    And I click on "Option 3" "button" in the "statusjscontrolsection" "region"
    And I click on "Option 1" "link" in the "statusjscontrolsection" "region"
    And I should see "Option 3" in the "statusjscontrolsection" "region"
    And I should see "The status value is option1" in the "statusjscontrolsection" "region"
    And I click on "Change selected value" "button" in the "statusjscontrolsection" "region"
    And I should see "Option 3" in the "statusjscontrolsection" "region"
    And I should see "The status value is option2" in the "statusjscontrolsection" "region"
    # Disable update.
    And I click on "Disable update" "button" in the "statusjscontrolsection" "region"
    And I click on "Option 3" "button" in the "statusjscontrolsection" "region"
    And I click on "Option 1" "link" in the "statusjscontrolsection" "region"
    And I should see "The status value is option2" in the "statusjscontrolsection" "region"
    And I click on "Option 3" "button" in the "statusjscontrolsection" "region"
    And "Selected" "icon" in the "#statusjscontrolsection [data-optionnumber='1']" "css_element" should not be visible
    And "Selected" "icon" in the "#statusjscontrolsection [data-optionnumber='2']" "css_element" should be visible

  Scenario: Dropdown status content is accessible with keyboard
    Given I click on "Focus helper" "button" in the "statussyncbutton" "region"
    When I press the tab key
    # Open and close dropdown with enter key.
    Then I press the enter key
    And the focused element is "[data-for='dropdowndialog_button']" "css_element" in the "statussyncbutton" "region"
    And I should see "Option 1" in the "statussyncbutton" "region"
    And I press the enter key
    And the focused element is "[data-for='dropdowndialog_button']" "css_element" in the "statussyncbutton" "region"
    And I should not see "Option 1" in the "statussyncbutton" "region"
    # Open and close with down and up keys.
    And I press the down key
    And the focused element is "[data-optionnumber='1'] a" "css_element" in the "statussyncbutton" "region"
    And I should see "Option 1" in the "statussyncbutton" "region"
    And I press the up key
    And the focused element is "[data-for='dropdowndialog_button']" "css_element" in the "statussyncbutton" "region"
    And I should see "Option 1" in the "statussyncbutton" "region"
    And I press the up key
    And the focused element is "[data-for='dropdowndialog_button']" "css_element" in the "statussyncbutton" "region"
    And I should not see "Option 1" in the "statussyncbutton" "region"
    # Select to option 3 and check user cannot go beyond that.
    And I press the down key
    And the focused element is "[data-optionnumber='1'] a" "css_element" in the "statussyncbutton" "region"
    And I press the down key
    And the focused element is "[data-optionnumber='2'] a" "css_element" in the "statussyncbutton" "region"
    And I press the down key
    And the focused element is "[data-optionnumber='3'] a" "css_element" in the "statussyncbutton" "region"
    And I press the down key
    And the focused element is "[data-optionnumber='3'] a" "css_element" in the "statussyncbutton" "region"
    And I press the enter key
    And I should see "Option 3" in the "statussyncbutton" "region"
    # Close dropdown with escape key.
    And I press the down key
    And the focused element is "[data-optionnumber='1'] a" "css_element" in the "statussyncbutton" "region"
    And I should see "Option 1" in the "statussyncbutton" "region"
    And I press the escape key
    And the focused element is "[data-for='dropdowndialog_button']" "css_element" in the "statussyncbutton" "region"
    And I should not see "Option 1" in the "statussyncbutton" "region"
