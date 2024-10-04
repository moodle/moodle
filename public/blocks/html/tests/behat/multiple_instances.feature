@block @block_html
Feature: Adding and configuring multiple Text blocks
  In order to have one or multiple Text blocks on a page
  As admin
  I need to be able to create, configure and change Text blocks

  Background:
    Given I log in as "admin"
    And I am on site homepage
    When I turn editing mode on
    And I add the "Text" block

  Scenario: Other users can not see Text block that has not been configured
    Then "(new text block)" "block" should exist
    And I log out
    And "(new text block)" "block" should not exist
    And "block_html" "block" should not exist

  Scenario: Other users can see Text block that has been configured even when it has no header
    And I configure the "(new text block)" block
    And I set the field "Content" to "Static text without a header"
    And I press "Save changes"
    Then I should not see "(new text block)"
    And I log out
    And I am on homepage
    And "block_html" "block" should exist
    And I should see "Static text without a header" in the "block_html" "block"
    And I should not see "(new text block)"

  Scenario: Adding multiple instances of Text block on a page
    And I configure the "block_html" block
    And I set the field "Text block title" to "The Text block header"
    And I set the field "Content" to "Static text with a header"
    And I press "Save changes"
    And I add the "Text" block
    And I configure the "(new text block)" block
    And I set the field "Text block title" to "The second Text block header"
    And I set the field "Content" to "Second block contents"
    And I press "Save changes"
    And I log out
    Then I should see "Static text with a header" in the "The Text block header" "block"
    And I should see "Second block contents" in the "The second Text block header" "block"
