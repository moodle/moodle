@filter @filter_wiris
Feature: Check performance
In order to check the performance setting
As an admin
I must not see any JSON response for images services

  Background:
    Given the following config values are set as admin:
      | config | value | plugin |
      | toolbar | math = wiris | editor_atto |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin  | C1     | editingteacher |
    And the "wiris" filter is "on"
    And I log in as "admin"

  @javascript
  Scenario: Create image in both formats and check
    And I navigate to "Plugins" in site administration
    And I follow "MathType by WIRIS"
    And I check image performance mode off
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><msqrt><mi>x</mi></msqrt></math>'
    And I press accept button in MathType Editor
    And I check if MathType formula src is equals to 'http://localhost:8000/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16'
    And I go to link "/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16"
    Then an svg image is correctly displayed
    And I go back
    And I navigate to "Plugins" in site administration
    And I follow "MathType by WIRIS"
    And I set the following fields to these values:
      | Image format | png |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><msqrt><mi>x</mi></msqrt></math>'
    And I press accept button in MathType Editor
    And I check if MathType formula src is equals to 'http://localhost:8000/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16'
    And I go to link "/filter/wiris/integration/showimage.php?formula=44f73ec2e9d0d59f10516949d446049e&cw=27&ch=19&cb=16"
    Then an png image is correctly displayed
