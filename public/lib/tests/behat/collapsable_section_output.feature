@core @javascript
Feature: Test collapsable section output module
  In order to show extra information to the user
  As a user
  I need to interact with the collapsable section output

  Background:
    # Get to the fixture page.
    Given I log in as "admin"
    And I am on fixture page "/lib/tests/behat/fixtures/collapsable_section_output_testpage.php"

  Scenario: Collapsable sections can be opened and closed
    Given I should not see "Dialog content"
    And I should not see "This is the closed section content." in the "closedsection" "region"
    And I should see "This is the open section content." in the "opensection" "region"
    When I click on "Expand" "button" in the "closedsection" "region"
    And I click on "Collapse" "button" in the "opensection" "region"
    Then I should see "This is the closed section content." in the "closedsection" "region"
    And I should not see "This is the open section content." in the "opensection" "region"

  Scenario: Collapsable sections content can have rich content inside
    When I click on "Expand" "button" in the "closedsection" "region"
    Then I should see "This is the closed section content." in the "closedsection" "region"
    And "Link" "link" should exist in the "closedsection" "region"
    And "Eye icon" "icon" should exist in the "closedsection" "region"

  Scenario: Collapsable sections HTML attributtes can be overriden
    When I click on "Expand" "button" in the "extraclasses" "region"
    And I click on "Expand" "button" in the "extraattributes" "region"
    Then ".extraclass" "css_element" should exist in the "extraclasses" "region"
    And "[data-foo='bar']" "css_element" should exist in the "extraattributes" "region"
    And "#myid" "css_element" should exist in the "extraattributes" "region"

  Scenario: Collapsable sections can have custom labels for expand and collapse
    When I click on "Custom expand" "button" in the "customlabels" "region"
    Then I should see "This is the custom labels content." in the "customlabels" "region"
    And I click on "Custom collapse" "button" in the "customlabels" "region"
    And I should not see "This is the custom labels content." in the "customlabels" "region"

  Scenario: Collapsable sections can be controlled via javascript
    # Toggle.
    Given I should not see "This is the javascript controls content." in the "jscontrols" "region"
    When I click on "Toggle" "button" in the "jscontrols" "region"
    Then I should see "This is the javascript controls content." in the "jscontrols" "region"
    And I click on "Toggle" "button" in the "jscontrols" "region"
    And I should not see "This is the javascript controls content." in the "jscontrols" "region"
    # Show and Hide.
    And  I click on "Show" "button" in the "jscontrols" "region"
    And I should see "This is the javascript controls content." in the "jscontrols" "region"
    And  I click on "Show" "button" in the "jscontrols" "region"
    And I should see "This is the javascript controls content." in the "jscontrols" "region"
    And I click on "Hide" "button" in the "jscontrols" "region"
    And I should not see "This is the javascript controls content." in the "jscontrols" "region"
    And I click on "Hide" "button" in the "jscontrols" "region"
    And I should not see "This is the javascript controls content." in the "jscontrols" "region"
    # Test state.
    And I click on "Test state" "button" in the "jscontrols" "region"
    And I should see "hidden" in the "state" "region"
    And  I click on "Show" "button" in the "jscontrols" "region"
    And I click on "Test state" "button" in the "jscontrols" "region"
    And I should see "visible" in the "state" "region"
    # Events.
    And I click on "Show" "button" in the "jscontrols" "region"
    And I should see "Last event: Section shown" in the "jscontrols" "region"
    And I click on "Hide" "button" in the "jscontrols" "region"
    And I should see "Last event: Section hidden" in the "jscontrols" "region"
