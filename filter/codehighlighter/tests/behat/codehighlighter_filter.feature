@filter @filter_codehighlighter
Feature: Render text content using a codehighliter filter
  To display code to be well-styled - I need to render text content.

  @javascript
  Scenario: Update admin profile description with a code content
    Given the "codehighlighter" filter is "on"
    And the following "user" exists:
      | username    | example                                                        |
      | description | <pre class="language-php"><code>$t = date();</code></language> |
    And I am on the Profile page logged in as "example"
    Then "//span[@class='token variable'][contains(text(),'$t')]" "xpath_element" should exist
    And "//span[@class='token operator'][contains(text(),'=')]" "xpath_element" should exist
    And "//span[@class='token punctuation'][contains(text(),'(')]" "xpath_element" should exist
    And "//span[@class='token punctuation'][contains(text(),')')]" "xpath_element" should exist
    And "//span[@class='token punctuation'][contains(text(),';')]" "xpath_element" should exist
