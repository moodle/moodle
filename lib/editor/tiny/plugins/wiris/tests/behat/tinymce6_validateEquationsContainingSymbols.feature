@tiny @tiny_wiris @wiris_mathtype @tinymce6_insert_formula @tinymce6_symbols_and_attributes @mtmoodle-96
Feature: Insert equations containing sensible symbols
  In order to check if some symbols render correctly
  I need to create a formula

  Background:
    Given the following config values are set as admin:
      | config        | value                        | plugin      |
      | customtoolbar | tiny_mce_wiris_formulaEditor | editor_tiny |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | admin | C1     | editingteacher |
    And the "wiris" filter is "on"
    And the "mathjaxloader" filter is "off"
    And the "urltolink" filter is "off"
    And I log in as "admin"

  @javascript @4.x @4.x_tinymce6
  Scenario: MTMOODLE-96 - Checking «<»>§&¨"`' symbols in text
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name         | Test MathType for TinyMCE6 on Moodle |
      | Page content | «<»>§&¨"`'                           |
    And I press "Save and display"
    Then "«<»>§&¨" "text" should exist
    Then "`'" "text" should exist
    Then Wirisformula should not exist

  @javascript @4.x @4.x_tinymce6
  Scenario: MTMOODLE-96 - Checking french quotes in text
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name         | Test MathType for TinyMCE6 on Moodle |
      | Page content | &laquo;Bonjour&raquo;                |
    And I press "Save and display"
    Then "«Bonjour»" "text" should exist
    Then Wirisformula should not exist

  @javascript @4.x @4.x_tinymce6
  Scenario: MTMOODLE-96 - Checking «<»>§&¨"`' symbols in MATHML
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for TinyMCE6 on Moodle |
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I wait "5" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mo>&#xAB;</mo><mo>&lt;</mo><mo>&#xBB;</mo><mo>&gt;</mo><mo>&#xA7;</mo><mo>&amp;</mo><mo>&#xA8;</mo><mo>&quot;</mo></math>'
    And I wait "3" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    Then a Wirisformula containing "« less than » greater than § & ¨ &quot;" should exist

  @javascript @3.x @3.x_tinymce6 @4.0 @4.0_tinymce6
  Scenario: MTMOODLE-96 - Checking «<»>§&¨"`' symbols in text
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name         | Test MathType for TinyMCE6 on Moodle |
      | Page content | «<»>§&¨"`'                           |
    And I press "Save and display"
    Then "«<»>§&¨" "text" should exist
    Then "`'" "text" should exist
    Then Wirisformula should not exist

  @javascript @3.x @3.x_tinymce6 @4.0 @4.0_tinymce6
  Scenario: MTMOODLE-96 - Checking french quotes in text
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name         | Test MathType for TinyMCE6 on Moodle |
      | Page content | &laquo;Bonjour&raquo;                |
    And I press "Save and display"
    Then "«Bonjour»" "text" should exist
    Then Wirisformula should not exist

  @javascript @3.x @3.x_tinymce6 @4.0 @4.0_tinymce6
  Scenario: MTMOODLE-96 - Checking «<»>§&¨"`' symbols in MATHML
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for TinyMCE6 on Moodle |
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I wait "5" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mo>&#xAB;</mo><mo>&lt;</mo><mo>&#xBB;</mo><mo>&gt;</mo><mo>&#xA7;</mo><mo>&amp;</mo><mo>&#xA8;</mo><mo>&quot;</mo></math>'
    And I wait "3" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    Then a Wirisformula containing "« less than » greater than § & ¨ &quot;" should exist
