@filter @filter_wiris @filter_wiris_render @filter_wiris_render_server
Feature: Server-side rendering to PNG
In order to enable server-side rendering
As an admin
I need to change the render type

  Background:
    Given the following config values are set as admin:
      | config | value | plugin |
      | toolbar | math = wiris | editor_atto |
      | imageformat | png | filter_wiris |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin  | C1     | editingteacher |
    And the "wiris" filter is "on"
    And the MathType filter render type is set to "php"
    And I log in as "admin"

  @javascript
  Scenario: Add a MathML formula and check MathType renders it correctly with PHP library
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto and server side rendering on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mrow><mo>(</mo><mfrac><mi>p</mi><mn>2</mn></mfrac><mo>)</mo></mrow><msup><mi>x</mi><mn>2</mn></msup><msup><mi>y</mi><mrow><mi>p</mi><mo>-</mo><mn>2</mn></mrow></msup><mo>-</mo><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><mi>x</mi></mrow></mfrac><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></mfrac></mrow></math>'
    And I press accept button in MathType Editor
    And I press "Save and display"
    Then Wirisformula should exist
    And MathType formula in png format is correctly displayed
