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
    Then "Selected" "icon" should exist in the "#statusselectedscenario [data-optionnumber='2']" "css_element"
    And "Selected" "icon" should not exist in the "#statusselectedscenario [data-optionnumber='1']" "css_element"
    And "Selected" "icon" should not exist in the "#statusselectedscenario [data-optionnumber='3']" "css_element"

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
