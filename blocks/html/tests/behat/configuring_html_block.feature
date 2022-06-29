@block @block_html @core_block
Feature: Adding and configuring Text blocks
  In order to have custom blocks on a page
  As admin
  I need to be able to create, configure and change Text blocks

  @javascript
  Scenario: Configuring the Text block with Javascript on
    Given I log in as "admin"
    And I am on site homepage
    When I turn editing mode on
    And I add the "Text" block
    And I configure the "(new text block)" block
    And I set the field "Content" to "Static text without a header"
    Then I should see "Text block title"
    And I press "Save changes"
    Then I should not see "(new text block)"
    And I configure the "block_html" block
    And I set the field "Text block title" to "The Text block header"
    And I set the field "Content" to "Static text with a header"
    And I press "Save changes"
    And "block_html" "block" should exist
    And "The Text block header" "block" should exist
    And I should see "Static text with a header" in the "The Text block header" "block"

  Scenario: Configuring the Text block with Javascript off
    Given I log in as "admin"
    And I am on site homepage
    When I turn editing mode on
    And I add the "Text" block
    And I configure the "(new text block)" block
    And I set the field "Content" to "Static text without a header"
    And I press "Save changes"
    Then I should not see "(new text block)"
    And I configure the "block_html" block
    And I set the field "Text block title" to "The Text block header"
    And I set the field "Content" to "Static text with a header"
    And I press "Save changes"
    And "block_html" "block" should exist
    And "The Text block header" "block" should exist
    And I should see "Static text with a header" in the "The Text block header" "block"
