@filter @filter_wiris @wiris_mathtype @filter_settings @image_settings @mtmoodle-12
Feature: Filter Settings - Image Settings - Render Type
  In order to enable client-side and server side types
  As an admin
  I need to change the render type

  Background:
    Given the following config values are set as admin:
      | config  | value        | plugin      |
      | toolbar | math = wiris | editor_atto |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | admin | C1     | editingteacher |
    And the "wiris" filter is "on"
    And the "urltolink" filter is "off"
    And I log in as "admin"

  @javascript @4.x @4.x_filter
  Scenario: MTMOODLE-12 - Add a MathML formula and check client side rendering works with Javascript library
    # set render type to "client"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And the MathType filter render type is set to "client"
    And I press "Save changes"
    # set text editor to "HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto and client side rendering on Moodle |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait "1" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mrow><mo>(</mo><mfrac><mi>p</mi><mn>2</mn></mfrac><mo>)</mo></mrow><msup><mi>x</mi><mn>2</mn></msup><msup><mi>y</mi><mrow><mi>p</mi><mo>-</mo><mn>2</mn></mrow></msup><mo>-</mo><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><mi>x</mi></mrow></mfrac><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></mfrac></mrow></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists
    Then I wait until Wirisformula formula exists
    And MathType formula in svg format is correctly displayed

  @javascript @4.x @4.x_filter
  Scenario: MTMOODLE-12 - Add a MathML formula and check server side rendering works with PHP library
    # set render type to "PHP"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And the MathType filter render type is set to "php"
    And I press "Save changes"
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto and php rendering on Moodle |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mrow><mo>(</mo><mfrac><mi>p</mi><mn>2</mn></mfrac><mo>)</mo></mrow><msup><mi>x</mi><mn>2</mn></msup><msup><mi>y</mi><mrow><mi>p</mi><mo>-</mo><mn>2</mn></mrow></msup><mo>-</mo><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><mi>x</mi></mrow></mfrac><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></mfrac></mrow></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists
    Then I wait until Wirisformula formula exists
    And MathType formula in svg format is correctly displayed

  @javascript @3.x @3.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-12 - Add a MathML formula and check client side rendering works with Javascript library
    # set render type to "client"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And the MathType filter render type is set to "client"
    And I press "Save changes"
    # set text editor to "HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto and client side rendering on Moodle |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait "1" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mrow><mo>(</mo><mfrac><mi>p</mi><mn>2</mn></mfrac><mo>)</mo></mrow><msup><mi>x</mi><mn>2</mn></msup><msup><mi>y</mi><mrow><mi>p</mi><mo>-</mo><mn>2</mn></mrow></msup><mo>-</mo><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><mi>x</mi></mrow></mfrac><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></mfrac></mrow></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists
    Then I wait until Wirisformula formula exists
    And MathType formula in svg format is correctly displayed

  @javascript @3.x @3.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-12 - Add a MathML formula and check server side rendering works with PHP library
    # set render type to "PHP"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And the MathType filter render type is set to "php"
    And I press "Save changes"
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto and php rendering on Moodle |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mrow><mo>(</mo><mfrac><mi>p</mi><mn>2</mn></mfrac><mo>)</mo></mrow><msup><mi>x</mi><mn>2</mn></msup><msup><mi>y</mi><mrow><mi>p</mi><mo>-</mo><mn>2</mn></mrow></msup><mo>-</mo><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><mi>x</mi></mrow></mfrac><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></mfrac></mrow></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists
    Then I wait until Wirisformula formula exists
    And MathType formula in svg format is correctly displayed
