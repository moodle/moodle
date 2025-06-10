@atto @atto_wiris @wiris_mathtype @atto_modal_window @mtmoodle-99
Feature: Collapse plugin compatibility with MathType for atto
  In order to use MathType with collapse filter enabled
  As an admin
  I need to write a mathtype formula

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | admin | C1     | editingteacher |
    And the "wiris" filter is "on"
    And the "urltolink" filter is "off"
    And the "mathjaxloader" filter is "off"
    And I log in as "admin"

  @javascript @4.x @4.x_atto
  Scenario: MTMOODLE-99 - Insert a Formula with collapse plugin enabled
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
      """
      collapse = collapse
      style1 = title, bold, italic
      list = unorderedlist, orderedlist
      links = link
      files = image, media, recordrtc, managefiles
      style2 = underline, strike, subscript, superscript
      align = align
      indent = indent
      insert = equation, charmap, table, clear
      undo = undo
      accessibility = accessibilitychecker, accessibilityhelper
      math = wiris
      other = html
      """
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "Collapse" in "Page content" field in Atto editor
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "2" seconds
    Then I wait until Wirisformula formula exists

  @javascript @3.x @3.x_atto @4.0 @4.0_atto
  Scenario: MTMOODLE-99 - Insert a Formula with collapse plugin enabled
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
      """
      collapse = collapse
      style1 = title, bold, italic
      list = unorderedlist, orderedlist
      links = link
      files = image, media, recordrtc, managefiles
      style2 = underline, strike, subscript, superscript
      align = align
      indent = indent
      insert = equation, charmap, table, clear
      undo = undo
      accessibility = accessibilitychecker, accessibilityhelper
      math = wiris
      other = html
      """
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "Collapse" in "Page content" field in Atto editor
    And I press "MathType" in "Page content" field in Atto editor
    And I wait "1" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "2" seconds
    Then I wait until Wirisformula formula exists

@javascript @4.x @4.x_atto
  Scenario: MTMOODLE-99 - Insert a Formula without collapse plugin enabled
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
      """
      style1 = title, bold, italic
      list = unorderedlist, orderedlist
      links = link
      files = image, media, recordrtc, managefiles
      style2 = underline, strike, subscript, superscript
      align = align
      indent = indent
      insert = equation, charmap, table, clear
      undo = undo
      accessibility = accessibilitychecker, accessibilityhelper
      math = wiris
      other = html
      """
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "2" seconds
    Then I wait until Wirisformula formula exists

  @javascript @3.x @3.x_atto @4.0 @4.0_atto
  Scenario: MTMOODLE-99 - Insert a Formula without collapse plugin enabled
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
      """
      style1 = title, bold, italic
      list = unorderedlist, orderedlist
      links = link
      files = image, media, recordrtc, managefiles
      style2 = underline, strike, subscript, superscript
      align = align
      indent = indent
      insert = equation, charmap, table, clear
      undo = undo
      accessibility = accessibilitychecker, accessibilityhelper
      math = wiris
      other = html
      """
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I wait "1" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "2" seconds
    Then I wait until Wirisformula formula exists