@editor @editor_atto @atto
Feature: Atto editor with customised toolbar
  In order to develop plugins that use Atto for specialised purposes
  As a developer
  I need to be able to configure Atto toolbar per-instance to include different plugins

  Background:
    # Get to the fixture page.
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name | intro                                                                                  | course | idnumber |
      | label      | L1   | <a href="../lib/editor/atto/tests/fixtures/custom_toolbar_example.php">FixtureLink</a> | C1     | label1   |
    When I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "FixtureLink"

  @javascript
  Scenario: Confirm that both editors have different toolbars but still function
    Then ".atto_link_button" "css_element" should exist in the ".normaldiv" "css_element"
    And ".atto_link_button" "css_element" should not exist in the ".specialdiv" "css_element"
    And ".atto_bold_button_bold" "css_element" should exist in the ".normaldiv" "css_element"
    And ".atto_italic_button_italic" "css_element" should exist in the ".normaldiv" "css_element"
    And ".atto_bold_button_bold" "css_element" should exist in the ".specialdiv" "css_element"
    And ".atto_italic_button_italic" "css_element" should exist in the ".specialdiv" "css_element"
