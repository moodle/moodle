@filter @filter_wiris @filter_wiris_render @filter_wiris_render_image
Feature: Check image format
In order to check image formats (png, svg)
As an admin
I need to see the correct image format

  Background:
    Given the following config values are set as admin:
      | config | value | plugin |
      | imageformat | svg | filter_wiris |
    And the "wiris" filter is "on"
    And I log in as "admin"

  @javascript
  Scenario: Change image format and check
    And I go to link "/filter/wiris/integration/test.php"
    Then MathType formula in svg format is correctly displayed
    And I go back
    And I navigate to "Plugins" in site administration
    And I follow "MathType by WIRIS"
    And I set the following fields to these values:
      | Image format | png |
    And I press "Save changes"
    And I go to link "/filter/wiris/integration/test.php"
    Then MathType formula in png format is correctly displayed
