@editor @editor_tiny @MDL-88618
Feature: TinyMCE bundled plugins - Accordion and List Styles (advlist)
  In order to create richer content
  As a teacher
  I need to use TinyMCE's Accordion and List Styles plugins

  Background:
    Given I log in as "admin"
    And I open my profile in edit mode

  @javascript
  Scenario: The accordion plugin toolbar button is available
    When I expand all toolbars for the "Description" TinyMCE editor
    Then "accordion" button should exist in the "Description" TinyMCE editor

  @javascript
  Scenario: The accordion option is accessible via the Insert menu
    When I click on the "Insert > Accordion" menu item for the "Description" TinyMCE editor
    And I switch to the "Description" TinyMCE editor iframe
    Then I should see "Accordion summary…"

  @javascript
  Scenario: Accordion content is not stripped by content filtering when saved
    Given I set the field "Description" to "<details class=\"mce-accordion\"><summary class=\"mce-accordion-summary\">Accordion heading</summary><div class=\"mce-accordion-body\"><p>Accordion content</p></div></details>"
    When I click on "Update profile" "button"
    Then "details.mce-accordion" "css_element" should exist
    And "details.mce-accordion summary" "css_element" should exist
    And I should see "Accordion heading"

  @javascript
  Scenario: The advlist plugin does not break existing list functionality
    Given I expand all toolbars for the "Description" TinyMCE editor
    Then "Bullet list" button should exist in the "Description" TinyMCE editor
    And "Numbered list" button should exist in the "Description" TinyMCE editor

  @javascript
  Scenario: The advlist plugin provides extended bullet list style options
    Given I expand all toolbars for the "Description" TinyMCE editor
    Then "Bullet list menu" button should exist in the "Description" TinyMCE editor
    When I click on the "Bullet list menu" button for the "Description" TinyMCE editor
    Then "[role='menuitemradio'][aria-label='Circle']" "css_element" should exist
    And "[role='menuitemradio'][aria-label='Square']" "css_element" should exist

  @javascript
  Scenario: The advlist plugin provides extended numbered list style options
    Given I expand all toolbars for the "Description" TinyMCE editor
    Then "Numbered list menu" button should exist in the "Description" TinyMCE editor
    When I click on the "Numbered list menu" button for the "Description" TinyMCE editor
    Then "[role='menuitemradio'][aria-label='Lower Alpha']" "css_element" should exist
    And "[role='menuitemradio'][aria-label='Upper Roman']" "css_element" should exist

  @javascript
  Scenario Outline: The advlist list style type is preserved through content filtering when saved
    Given I set the field "Description" to "<<tag> style=\"list-style-type: <style>;\"><li>Test item</li></<tag>>"
    When I click on "Update profile" "button"
    And I am on the "Profile advanced editing" page
    Then the field "Description" matches expression "#list-style-type:\s*<style>#"

    Examples:
      | tag | style       |
      | ul  | disc        |
      | ul  | circle      |
      | ul  | square      |
      | ol  | decimal     |
      | ol  | lower-alpha |
      | ol  | upper-alpha |
      | ol  | lower-roman |
      | ol  | upper-roman |
      | ol  | lower-greek |
