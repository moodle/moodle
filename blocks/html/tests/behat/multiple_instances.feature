@block @block_html
Feature: Adding and configuring HTML blocks
  In order to have one or multiple HTML blocks on a page
  As admin
  I need to be able to create, configure and change HTML blocks

  Background:
    Given I log in as "admin"
    When I click on "Turn editing on" "link" in the "Administration" "block"
    And I add the "HTML" block

  Scenario: Other users can not see HTML block that has not been configured
    Then "(new HTML block)" "block" should exist
    And I log out
    And "(new HTML block)" "block" should not exist
    And "block_html" "block" should not exist

  Scenario: Other users can see HTML block that has been configured even when it has no header
    And I configure the "(new HTML block)" block
    And I set the field "Content" to "Static text without a header"
    And I press "Save changes"
    Then I should not see "(new HTML block)"
    And I log out
    And I am on homepage
    And "block_html" "block" should exist
    And I should see "Static text without a header" in the "block_html" "block"
    And I should not see "(new HTML block)"

  Scenario: Adding multiple instances of HTML block on a page
    And I configure the "block_html" block
    And I set the field "Block title" to "The HTML block header"
    And I set the field "Content" to "Static text with a header"
    And I press "Save changes"
    And I add the "HTML" block
    And I configure the "(new HTML block)" block
    And I set the field "Block title" to "The second HTML block header"
    And I set the field "Content" to "Second block contents"
    And I press "Save changes"
    And I log out
    Then I should see "Static text with a header" in the "The HTML block header" "block"
    And I should see "Second block contents" in the "The second HTML block header" "block"
