@editor @editor_tiny @filter @filter_codehighlighter
Feature: Render text content using a codehighliter filter
  To display code to be well-styled - I need to render text content.

  @javascript
  Scenario: Update admin profile description with a code content
    Given the "codehighlighter" filter is "on"
    And I log in as "admin"
    And I follow "Profile" in the user menu
    When I click on "Edit profile" "link" in the "region-main" "region"
    And I click on "//span[@class='tox-mbtn__select-label'][contains(text(), 'Insert')]" "xpath_element"
    And I click on "//div[@class='tox-collection__item-label'][contains(text(), 'Code sample...')]" "xpath_element"
    And I set the field with xpath "//div[@class='tox-selectfield']/select" to "PHP"
    And I set the field with xpath "//textarea" to "<pre class=\"language-php\"><code>$t = date();</code></pre>"
    And I click on "//button[@class='tox-button'][contains(text(), 'Save')]" "xpath_element"
    And I click on "Update profile" "button"
    Then I should see "Changes saved"
    And "//span[@class='token variable'][contains(text(),'$t')]" "xpath_element" should exist
    And "//span[@class='token operator'][contains(text(),'=')]" "xpath_element" should exist
    And "//span[@class='token punctuation'][contains(text(),'(')]" "xpath_element" should exist
    And "//span[@class='token punctuation'][contains(text(),')')]" "xpath_element" should exist
    And "//span[@class='token punctuation'][contains(text(),';')]" "xpath_element" should exist
