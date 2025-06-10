@filter @filter_wiris @wiris_mathtype @filter_settings @connection_settings @mtmoodle-23
Feature: Filter Settings - Connection Settings - Service protocol
  In order to check the service protocol
  As an admin
  I need to change the configuration

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
    And I log in as "admin"

  @javascript @4.x @4.x_filter
  Scenario: MTMOODLE-23 - Check if a Wiris formula renders with protocol 'http'
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    # set the service protocol to "http"
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Service protocol | http |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto and server side rendering on Moodle |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mrow><mo>(</mo><mfrac><mi>p</mi><mn>2</mn></mfrac><mo>)</mo></mrow><msup><mi>x</mi><mn>2</mn></msup><msup><mi>y</mi><mrow><mi>p</mi><mo>-</mo><mn>2</mn></mrow></msup><mo>-</mo><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><mi>x</mi></mrow></mfrac><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></mfrac></mrow></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists
    Then Wirisformula should exist

  @javascript @4.x @4.x_filter
  Scenario: MTMOODLE-23 - Check if a Wiris formula renders with protocol 'https'
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    # set the service protocol to "http"
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Service protocol | https |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto and server side rendering on Moodle |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mrow><mo>(</mo><mfrac><mi>p</mi><mn>2</mn></mfrac><mo>)</mo></mrow><msup><mi>x</mi><mn>2</mn></msup><msup><mi>y</mi><mrow><mi>p</mi><mo>-</mo><mn>2</mn></mrow></msup><mo>-</mo><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><mi>x</mi></mrow></mfrac><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></mfrac></mrow></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists
    Then Wirisformula should exist

  @javascript @3.x @3.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-23 - Check if a Wiris formula renders with protocol 'http'
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    # set the service protocol to "http"
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Service protocol | http |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto and server side rendering on Moodle |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mrow><mo>(</mo><mfrac><mi>p</mi><mn>2</mn></mfrac><mo>)</mo></mrow><msup><mi>x</mi><mn>2</mn></msup><msup><mi>y</mi><mrow><mi>p</mi><mo>-</mo><mn>2</mn></mrow></msup><mo>-</mo><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><mi>x</mi></mrow></mfrac><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></mfrac></mrow></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists
    Then Wirisformula should exist

  @javascript @3.x @3.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-23 - Check if a Wiris formula renders with protocol 'https'
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    # set the service protocol to "http"
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Service protocol | https |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto and server side rendering on Moodle |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mrow><mo>(</mo><mfrac><mi>p</mi><mn>2</mn></mfrac><mo>)</mo></mrow><msup><mi>x</mi><mn>2</mn></msup><msup><mi>y</mi><mrow><mi>p</mi><mo>-</mo><mn>2</mn></mrow></msup><mo>-</mo><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><mi>x</mi></mrow></mfrac><mfrac><mn>1</mn><mrow><mn>1</mn><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></mfrac></mrow></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists
    Then Wirisformula should exist
