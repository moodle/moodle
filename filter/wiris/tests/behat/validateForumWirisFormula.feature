@filter @filter_wiris @wiris_mathtype @moodle_activities @page_render @mtmoodle-6
Feature: Render in moodle forums
  In order to check the pages rendering
  As an admin
  I need to change the configuration

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "activities" exist:
      | activity | course | idnumber | name            | type    |
      | forum    | C1     | forum1   | Test forum name | general |
    And the "wiris" filter is "on"
    And the MathType filter render type is set to "php"
    And I log in as "admin"
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

    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"

@javascript @4.x @4.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-6 - Check MathType renders a wiris formula in moodle forums discussion
    And I am on the "Test forum name" "forum activity" page logged in as admin
    And I click on "Add discussion topic" "link"
    And I set the following fields to these values:
      | Subject | Discussion with an equation |
    # insert Wirisformula in forum
    And I press "MathType" in "Message" field in Atto editor
    And I wait "3" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>1</mn></math>'
    And I wait "3" seconds
    And I press accept button in MathType Editor
    And I wait "1" seconds
    And I press "Post to forum"
    And I follow "Discussion with an equation"
    And I wait until Wirisformula formula exists
    Then a Wirisformula containing "1 plus 1" should exist

@javascript @4.x @4.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-6 - Check MathType renders a wiris formula in a reply of a moodle forums discussion
    And the following "mod_forum > discussions" exist:
      | user  | forum  | name               | message                |
      | admin | forum1 | Forum discussion 1 | Reply with an equation |
    And I am on the "Test forum name" "forum activity" page logged in as admin
    And I follow "Forum discussion 1"
    And I click on "Reply" "link"
    And I click on "Advanced" "button"
    And I set the following fields to these values:
      | Message | Reply to a forum with an equation |
    # insert Wirisformula in forum
    And I press "MathType" in "Message" field in Atto editor
    And I wait "3" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><msqrt><mi>x</mi></msqrt></math>'
    And I wait "3" seconds
    And I press accept button in MathType Editor
    And I wait "1" seconds
    And I press "Post to forum"
    And I wait until Wirisformula formula exists
    Then a Wirisformula containing "square root of x" should exist

@javascript @3.x @3.x_filter
  Scenario: MTMOODLE-6 - Check MathType renders a wiris formula in moodle forums discussion
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I click on "Add a new discussion topic" "link"
    And I set the following fields to these values:
      | Subject | Discussion with an equation |
    # insert Wirisformula in forum
    And I press "MathType" in "Message" field in Atto editor
    And I wait "3" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>1</mn></math>'
    And I wait "3" seconds
    And I press accept button in MathType Editor
    And I wait "1" seconds
    And I press "Post to forum"
    And I follow "Discussion with an equation"
    And I wait until Wirisformula formula exists
    Then a Wirisformula containing "1 plus 1" should exist

@javascript @3.x @3.x_filter
  Scenario: MTMOODLE-6 - Check MathType renders a wiris formula in a reply of a moodle forums discussion
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I click on "Add a new discussion topic" "link"
    And I set the following fields to these values:
      | Subject | Discussion with an equation |
    # insert Wirisformula in forum
    And I press "MathType" in "Message" field in Atto editor
    And I wait "3" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>1</mn></math>'
    And I wait "3" seconds
    And I press accept button in MathType Editor
    And I wait "1" seconds
    And I press "Post to forum"
    And I follow "Discussion with an equation"
    And I click on "Reply" "link"
    And I click on "Advanced" "button"
    And I set the following fields to these values:
      | Message | Reply to a forum with an equation |
    # insert Wirisformula in forum
    And I press "MathType" in "Message" field in Atto editor
    And I wait "3" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><msqrt><mi>x</mi></msqrt></math>'
    And I wait "3" seconds
    And I press accept button in MathType Editor
    And I wait "1" seconds
    And I press "Post to forum"
    And I wait until Wirisformula formula exists
    Then a Wirisformula containing "square root of x" should exist