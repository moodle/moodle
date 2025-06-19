@core @javascript @core_form
Feature: hideIf functionality in forms
  For forms including hideIf functions
  As a user
  If I trigger the hideIf condition then the form elements will be hidden

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I log in as "admin"

  Scenario: When 'eq' hideIf conditions are not met, the relevant elements are shown
    When I add an assign activity to course "Course 1" section "1"
    And I expand all fieldsets
    And I set the field "Students submit in groups" to "Yes"
    Then I should see "Require group to make submission"
    And I should see "Require all group members submit"
    And I should see "Grouping for student groups"

  Scenario: When 'eq' hideIf conditions are met, the relevant elements are hidden
    When I add a assign activity to course "Course 1" section "1"
    And I expand all fieldsets
    And I set the field "Students submit in groups" to "No"
    Then I should not see "Require group to make submission"
    And I should not see "Require all group members to submit"
    And I should not see "Grouping for student groups"

  Scenario: The editor is hidden when 'eq' hideIf conditions are met
    Given I am on fixture page "/lib/form/tests/behat/fixtures/editor_hideif_disabledif_form.php"
    And I should see "My test editor"
    When I click on "Hide" "radio"
    Then I should not see "My test editor"

  Scenario: The static element is hidden when 'eq' hideIf conditions are met
    Given I am on fixture page "/lib/form/tests/behat/fixtures/static_hideif_disabledif_form.php"
    And I should see "Static with form elements"
    When I click on "Hide" "radio"
    Then I should not see "Static with form elements"
    And I click on "Enable" "radio"
    And I should see "Static with form elements"

  Scenario: The file picker element is hidden when 'eq' hideIf conditions are met
    Given I am on the "filepicker_hideif_disabledif_form" "core_form > Fixture" page logged in as "admin"
    And "#fitem_id_filepicker" "css_element" should be visible
    When I click on "Hide" "radio"
    Then "#fitem_id_filepicker" "css_element" should not be visible

  @_file_upload
  Scenario: The other element is hidden when the file picker is not empty
    Given I am on the "filepicker_hideif_disabledif_form" "core_form > Fixture" page logged in as "admin"
    When I upload "lib/ddl/tests/fixtures/xmldb_table.xml" file to "File picker" filemanager
    Then I should not see "inputtext2"

  Scenario Outline: Inputs are hidden when hideIf conditions dependent on a multi-select element are met
    Given I am on the "multiselect_hideif_disabledif_form" "core_form > Fixture" page
    When I set the field "multiselect1" to "<selection>"
    Then I <shouldSeeEq_> see "Hide if selection 'eq' []"
    And I <shouldSeeIn_> see "Hide if selection 'in' []"
    And I <shouldSeeNeq_> see "Hide if selection 'neq' []"
    And I <shouldSeeEq1> see "Hide if selection 'eq' ['1']"
    And I <shouldSeeIn1> see "Hide if selection 'in' ['1']"
    And I <shouldSeeNeq1> see "Hide if selection 'neq' ['1']"
    And I <shouldSeeEq12> see "Hide if selection 'eq' ['1', '2']"
    And I <shouldSeeIn12> see "Hide if selection 'in' ['1', '2']"
    And I <shouldSeeNeq12> see "Hide if selection 'neq' ['1', '2']"

    Examples:
      | selection          | shouldSeeEq_ | shouldSeeIn_ | shouldSeeNeq_ | shouldSeeEq1 | shouldSeeIn1 | shouldSeeNeq1 | shouldSeeEq12 | shouldSeeIn12 | shouldSeeNeq12 |
      |                    | should not   | should not   | should        | should       | should       | should not    | should        | should        | should not     |
      | Option 1           | should       | should       | should not    | should not   | should not   | should        | should        | should not    | should not     |
      | Option 2           | should       | should       | should not    | should       | should       | should not    | should        | should not    | should not     |
      | Option 1, Option 2 | should       | should       | should not    | should       | should       | should not    | should not    | should not    | should         |
