@atto @atto_wiris @wiris_mathtype @pending
Feature: Compatibility with Multilang2
  In order to check if MathType is compatible with Multilang2 plugin for Atto
  As an admin
  I need not to be able to use both MathType and Multilang2 for Atto

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
    And the "mathjaxloader" filter is "off"
    And the "urltolink" filter is "off"
    And the "multilang2" filter is "on"
    And I log in as "admin"

  @javascript
  Scenario: Activate Multilang2 and insert content
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
      """
      math = wiris
      other = multilang2, html
      """
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I set the field "Page content" to multiline:
      """
      {mlang es}<p>content in language Spanish.</p>{mlang}
      {mlang en}<p>content in language English.</p>{mlang}
      {mlang other}<p>content for other languages.</p>{mlang}
      """
    And I press "Save and display"
    And I should not see "content in language Spanish"
    And I navigate to "Edit settings" in current page administration
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>20</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    Then I wait until Wirisformula formula exists

    Then I navigate to "Edit settings" in current page administration
    And I set the field "Page content" to multiline:
      """
      {mlang es}<p>content in language Spanish.</p>{mlang}
      {mlang en}<p><math><mfrac><mn>1</mn><msqrt><mn>20</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>content in language English.</p>{mlang}
      {mlang other}<p>content for other languages.</p>{mlang}
      """
    And I press "Save and display"
    Then I should see "content in language English"
    And I wait until Wirisformula formula exists
