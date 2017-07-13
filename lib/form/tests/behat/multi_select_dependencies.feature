@core_form @javascript @_bug_phantomjs
Feature: Forms with a multi select field dependency
  In order to test multi select field dependency
  As an admin
  I need forms field which depends on multiple select options

  Scenario: Field should be enabled only when all select options are selected
    # Get to the fixture page.
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name | intro                                                                               | course | idnumber |
      | label      | L1   | <a href="../lib/form/tests/fixtures/multi_select_dependencies.php">FixtureLink</a> | C1     | label1   |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    When I follow "FixtureLink"
    Then the "Enter your name" "field" should be disabled
    And I set the field "Choose one or more directions" to "South,West"
    Then the "Enter your name" "field" should be enabled
    And I set the field "Choose one or more directions" to "West"
    Then the "Enter your name" "field" should be disabled
    And I set the field "Choose one or more directions" to "North,West"
    Then the "Enter your name" "field" should be disabled