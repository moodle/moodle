@tiny @tiny_wiris @wiris_mathtype @tinymce6_insert_formula @mtmoodle-98
Feature: Insert a MathType formula in an assignment's feedbacks
  In order to check that formulas can be included on an assignment feedback
  As an admin
  I need to create a MathType formula on an assignment's feedback

  Background:
    Given the following config values are set as admin:
      | config        | value                        | plugin      |
      | customtoolbar | tiny_mce_wiris_formulaEditor | editor_tiny |
    And the following "users" exist:
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

  @javascript @4.x @4.x_tinymce6 @5.x @5.x_tinymce6
  Scenario: MTMOODLE-98 - Insert a MathType formula in an assignment's feedback
    # 01. Create the assignment
    And I am on "Course 1" course homepage with editing mode on
    And I add an "Assignment" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Assignment name | Test MathType for TinyMCE6 on Moodle |
    And I click on "Online text" "checkbox"
    And I click on "File submissions" "checkbox"
    Then I press "Save and display"
    And I should see "Grade"
    And I wait "1" seconds
    # Suboptimal way to click on the 'Grade' link since "I press", "I follow" and 'I click on "Grade" "link"' don't work.
    And I click on "Grade" "link" in the ".tertiary-navigation" "css_element"
    # 02. Grade the assignment.
    And I click on "More options" in TinyMCE 6 editor toolbar
    And I click on "MathType" "button"
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    Then I press "Save changes"
    # 03. Validate the formula is rendered on both contexts.
    And I wait "1" seconds
    # Go to the editor's iframe to check the wirisformula
    And I switch to iframe with locator "id_assignfeedbackcomments_editor_ifr"
    Then I wait until Wirisformula formula exists
    And a Wirisformula containing 'square root' should exist
    # 04. Validate the formula is rendered on "View All Submissions" page
    # Return to the default frame to check if MathType editor opened
    And I return to default frame
    Then I follow "View all submissions"
    And I wait until Wirisformula formula exists
    And a Wirisformula containing 'square root' should exist

  @javascript @4.0 @4.0_tinymce6
  Scenario: MTMOODLE-98 - Insert a MathType formula in an assignment's feedback
    # 01. Create the assignment
    And I am on "Course 1" course homepage with editing mode on
    And I add an "Assignment" to section "0"
    And I set the following fields to these values:
      | Assignment name | Test MathType for TinyMCE6 on Moodle |
    And I click on "Online text" "checkbox"
    And I click on "File submissions" "checkbox"
    Then I press "Save and display"
    And I should see "Grade"
    And I wait "1" seconds
    # Suboptimal way to click on the 'Grade' link since "I press", "I follow" and 'I click on "Grade" "link"' don't work.
    And I click on ".btn-primary" "css_element" in the "//div[@class='row']" "xpath_element"
    # 02. Grade the assignment.
    And I click on "More options" in TinyMCE 6 editor toolbar
    And I click on "MathType" "button"
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    Then I press "Save changes"
    # 03. Validate the formula is rendered on both contexts.
    And I wait "1" seconds
    # Go to the editor's iframe to check the wirisformula
    And I switch to iframe with locator "id_assignfeedbackcomments_editor_ifr"
    Then I wait until Wirisformula formula exists
    And a Wirisformula containing 'square root' should exist
    # 04. Validate the formula is rendered on "View All Submissions" page
    # Return to the default frame to check if MathType editor opened
    And I return to default frame
    Then I follow "View all submissions"
    And I wait until Wirisformula formula exists
    And a Wirisformula containing 'square root' should exist

