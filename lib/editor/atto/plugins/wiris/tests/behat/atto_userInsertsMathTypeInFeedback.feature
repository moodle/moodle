@atto @atto_wiris @wiris_mathtype @atto_insert_formula @mtmoodle-98
Feature: Insert a MathType formula in an assignment's feedback
  In order to check that formulas can be included on an assignment feedback
  As an admin
  I need to create a MathType formula on an assignment's feedback

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | editingteacher |
      | student1 | C1     | student        |
    And the "wiris" filter is "on"
    And the "mathjaxloader" filter is "off"
    And the "urltolink" filter is "off"
    And I log in as "admin"
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
    """
    style1 = title, bold, italic
    list = unorderedlist, orderedlist, indent
    links = link
    files = emojipicker, image, media, recordrtc, managefiles, h5p
    accessibility = accessibilitychecker, accessibilityhelper
    style2 = underline, strike, subscript, superscript
    align = align
    insert = equation, charmap, table, clear
    undo = undo
    other = html
    math = wiris
    """
    And I press "Save changes"

  @javascript @4.x @4.x_atto
  Scenario: MTMOODLE-98 - Insert a MathType formula in an assignment's feedback
    # 01. Create the assignment
    And I am on "Course 1" course homepage with editing mode on
    And I add an "Assignment" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Assignment name | Test MathType for Atto on Moodle |
    And I click on "Online text" "checkbox"
    And I click on "File submissions" "checkbox"
    Then I press "Save and display"
    And I should see "Grade"
    And I wait "1" seconds
    # Suboptimal way to click on the 'Grade' link since "I press", "I follow" and 'I click on "Grade" "link"' don't work.
    And I click on ".btn-primary" "css_element" in the "//div[@class='row']" "xpath_element"
    # 02. Grade the assignment.
    And I click on "MathType" "button"
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    Then I press "Save changes"
    # 03. Validate the formula is rendered on both contexts.
    And I wait "1" seconds
    Then I wait until Wirisformula formula exists
    And a Wirisformula containing 'square root' should exist
    # 04. Validate the formula is rendered on "View All Submissions" page
    Then I follow "View all submissions"
    And I wait until Wirisformula formula exists
    And a Wirisformula containing 'square root' should exist

  @javascript @4.0 @4.0_atto
  Scenario: MTMOODLE-98 - Insert a MathType formula in an assignment's feedback
    # 01. Create the assignment
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "0"
    And I set the following fields to these values:
      | Assignment name | Test MathType for Atto on Moodle |
    And I click on "Online text" "checkbox"
    And I click on "File submissions" "checkbox"
    Then I press "Save and display"
    And I should see "Grade"
    And I wait "1" seconds
    # Suboptimal way to click on the 'Grade' link since "I press", "I follow" and 'I click on "Grade" "link"' don't work.
    And I click on ".btn-primary" "css_element" in the "//div[@class='row']" "xpath_element"
    # 02. Grade the assignment.
    And I click on "MathType" "button"
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    Then I press "Save changes"
    # 03. Validate the formula is rendered on both contexts.
    And I wait "1" seconds
    Then I wait until Wirisformula formula exists
    And a Wirisformula containing 'square root' should exist
    # 04. Validate the formula is rendered on "View All Submissions" page
    Then I follow "View all submissions"
    And I wait until Wirisformula formula exists
    And a Wirisformula containing 'square root' should exist

