@core @javascript @core_form
Feature: disabledIf functionality in forms
  For forms including disabledIf functions
  As a user
  If I trigger the disabledIf condition then the form elements will be disabled

  # Note: if you are looking for the Behat tests for the filepicker widget,
  # you will find them in repository/tests/behat.

  Scenario: The file manager is disabled when disabledIf conditions are met
    Given I am on the "filemanager_hideif_disabledif_form" "core_form > Fixture" page logged in as "admin"
    When I click on "Disable" "radio"
    # Test standard file manager.
    Then the "disabled" attribute of "input#id_some_filemanager" "css_element" should contain "true"
    # Test file manager in a group.
    And the "disabled" attribute of "input#id_filemanager_group_some_filemanager_group" "css_element" should contain "true"

  Scenario: The static element is disabled when 'eq' disabledIf conditions are met
    Given I log in as "admin"
    And I am on fixture page "/lib/form/tests/behat/fixtures/static_hideif_disabledif_form.php"
    And I should see "Static with form elements"
    When I click on "Disable" "radio"
    And the "class" attribute of "#fitem_id_some_static" "css_element" should contain "text-muted"
    And the "disabled" attribute of "input#id_some_static_username" "css_element" should contain "true"
    And the "disabled" attribute of "Check" "button" should contain "true"
    Then I click on "Enable" "radio"
    And the "class" attribute of "#fitem_id_some_static" "css_element" should not contain "text-muted"
    And the "#id_some_static_username" "css_element" should be enabled
    And the "class" attribute of "Check" "button" should not contain "disabled"

  Scenario: The file picker element is disabled when 'eq' disabledIf conditions are met
    Given I am on the "filepicker_hideif_disabledif_form" "core_form > Fixture" page logged in as "admin"
    And the "#id_filepicker" "css_element" should be enabled
    When I click on "Disable" "radio"
    Then the "#id_filepicker" "css_element" should be disabled

  @_file_upload
  Scenario: The other element is disabled when the file picker is not empty
    Given I am on the "filepicker_hideif_disabledif_form" "core_form > Fixture" page logged in as "admin"
    When I upload "lib/ddl/tests/fixtures/xmldb_table.xml" file to "File picker" filemanager
    Then the "inputtext1" "field" should be disabled

  Scenario Outline: Inputs are disabled when disabledIf conditions dependent on a multi-select element are met
    Given I am on the "multiselect_hideif_disabledif_form" "core_form > Fixture" page logged in as "admin"
    When I set the field "multiselect1" to "<selection>"
    Then the "#id_disabledIfEq_" "css_element" should be <enabledEq_>
    And the "#id_disabledIfIn_" "css_element" should be <enabledIn_>
    And the "#id_disabledIfNeq_" "css_element" should be <enabledNeq_>
    And the "#id_disabledIfEq1" "css_element" should be <enabledEq1>
    And the "#id_disabledIfIn1" "css_element" should be <enabledIn1>
    And the "#id_disabledIfNeq1" "css_element" should be <enabledNeq1>
    And the "#id_disabledIfEq12" "css_element" should be <enabledEq12>
    And the "#id_disabledIfIn12" "css_element" should be <enabledIn12>
    And the "#id_disabledIfNeq12" "css_element" should be <enabledNeq12>

    Examples:
      | selection          | enabledEq_ | enabledIn_ | enabledNeq_ | enabledEq1 | enabledIn1 | enabledNeq1 | enabledEq12 | enabledIn12 | enabledNeq12 |
      |                    | disabled   | disabled   | enabled     | enabled    | enabled    | disabled    | enabled     | enabled     | disabled     |
      | Option 1           | enabled    | enabled    | disabled    | disabled   | disabled   | enabled     | enabled     | disabled    | disabled     |
      | Option 2           | enabled    | enabled    | disabled    | enabled    | enabled    | disabled    | enabled     | disabled    | disabled     |
      | Option 1, Option 2 | enabled    | enabled    | disabled    | enabled    | enabled    | disabled    | disabled    | disabled    | enabled      |
