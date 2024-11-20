@core @javascript @core_form
Feature: disabledIf functionality in forms
  For forms including disabledIf functions
  As a user
  If I trigger the disabledIf condition then the form elements will be disabled

  Background:
    Given I log in as "admin"

  Scenario: The file manager is disabled when disabledIf conditions are met
    Given I am on fixture page "/lib/form/tests/behat/fixtures/filemanager_hideif_disabledif_form.php"
    When I click on "Disable" "radio"
    # Test standard file manager.
    Then the "disabled" attribute of "input#id_some_filemanager" "css_element" should contain "true"
    # Test file manager in a group.
    And the "disabled" attribute of "input#id_filemanager_group_some_filemanager_group" "css_element" should contain "true"

  Scenario: The static element is disabled when 'eq' disabledIf conditions are met
    Given I am on fixture page "/lib/form/tests/behat/fixtures/static_hideif_disabledif_form.php"
    And I should see "Static with form elements"
    When I click on "Disable" "radio"
    And the "class" attribute of "#fitem_id_some_static" "css_element" should contain "text-muted"
    And the "disabled" attribute of "input#id_some_static_username" "css_element" should contain "true"
    And the "disabled" attribute of "Check" "button" should contain "true"
    Then I click on "Enable" "radio"
    And the "class" attribute of "#fitem_id_some_static" "css_element" should not contain "text-muted"
    And the "#id_some_static_username" "css_element" should be enabled
    And the "class" attribute of "Check" "button" should not contain "disabled"
