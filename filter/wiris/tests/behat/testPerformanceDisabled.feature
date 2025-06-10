@filter @filter_wiris @wiris_mathtype @filter_settings @image_settings @mtmoodle-17
Feature: Filter Settings - Image Settings - Performance mode
  In order to check the performance setting
  As an admin
  I must not see any JSON response for images services

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
  Scenario: MTMOODLE-17 - Validate formula renders with Performance mode OFF
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I check image performance mode off
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><msqrt><mi>x</mi></msqrt></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I check if MathType formula src is equals to 'http://localhost:8000/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16'
    And I go to link "/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16"
    Then an svg image is correctly displayed

  @javascript @4.x @4.x_filter
  Scenario: MTMOODLE-17 - Validate formula renders with Performance mode ON
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I check image performance mode on
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><msqrt><mi>x</mi></msqrt></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I check if MathType formula src is equals to 'http://localhost:8000/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16'
    And I go to link "/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16"
    Then an svg image is correctly displayed

  @javascript @3.x @3.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-17 - Validate formula renders with Performance mode OFF
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I check image performance mode off
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><msqrt><mi>x</mi></msqrt></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I check if MathType formula src is equals to 'http://localhost:8000/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16'
    And I go to link "/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16"
    Then an svg image is correctly displayed

  @javascript @3.x @3.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-17 - Validate formula renders with Performance mode ON
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I check image performance mode on
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><msqrt><mi>x</mi></msqrt></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I check if MathType formula src is equals to 'http://localhost:8000/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16'
    And I go to link "/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16"
    Then an svg image is correctly displayed
