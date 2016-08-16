@block @block_html @core_block
Feature: Adding and configuring HTML blocks
  In order to have custom blocks on a page
  As admin
  I need to be able to create, configure and change HTML blocks

  @javascript
  Scenario: Configuring the HTML block with Javascript on
    Given I log in as "admin"
    And I am on site homepage
    When I click on "Turn editing on" "link" in the "Administration" "block"
    And I add the "HTML" block
    And I configure the "(new HTML block)" block
    And I set the field "Content" to "Static text without a header"
    And I press "Save changes"
    Then I should not see "(new HTML block)"
    And I configure the "block_html" block
    And I set the field "Block title" to "The HTML block header"
    And I set the field "Content" to "Static text with a header"
    And I press "Save changes"
    And "block_html" "block" should exist
    And "The HTML block header" "block" should exist
    And I should see "Static text with a header" in the "The HTML block header" "block"

  Scenario: Configuring the HTML block with Javascript off
    Given I log in as "admin"
    And I am on site homepage
    When I click on "Turn editing on" "link" in the "Administration" "block"
    And I add the "HTML" block
    And I configure the "(new HTML block)" block
    And I set the field "Content" to "Static text without a header"
    And I press "Save changes"
    Then I should not see "(new HTML block)"
    And I configure the "block_html" block
    And I set the field "Block title" to "The HTML block header"
    And I set the field "Content" to "Static text with a header"
    And I press "Save changes"
    And "block_html" "block" should exist
    And "The HTML block header" "block" should exist
    And I should see "Static text with a header" in the "The HTML block header" "block"
