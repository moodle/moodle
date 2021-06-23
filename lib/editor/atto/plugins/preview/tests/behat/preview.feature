@editor @editor_atto @atto @atto_preview @_bug_phantomjs
Feature: Atto preview editor button
  In order to edit big text
  I need to use an editing tool to expand editor.

  Background:
    Given the "emoticon" filter is "on"
    And I log in as "admin"
    And I navigate to "Plugins > Text editors > Atto HTML edito > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to "other = html, preview, bold, charmap"
    And I press "Save changes"
    And I open my profile in edit mode
    And I set the field "Description" to "Wink ;-) emoticon"

  @javascript @atto_preview_active
  Scenario: Click preview button and check activation
    When I click on "Toggle preview" "button"
    Then "button.atto_preview_button.highlight" "css_element" should exist
    And "button.atto_bold_button_bold[disabled=\"disabled\"], button.atto_bold_button[disabled=\"disabled\"]" "css_element" should exist

  @javascript @_switch_iframe @atto_preview_content
  Scenario: Click preview look for iframe contents
    When I click on "Toggle preview" "button"
    And I wait "30" seconds
    And I switch to "atto-preview" iframe
    Then I should see "Wink"
    And "img.icon" "css_element" should exist
