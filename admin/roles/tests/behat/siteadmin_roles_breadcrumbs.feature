@core @core_admin @core_admin_roles @javascript
Feature: Verify the breadcrumbs in define roles site administration pages
  Whenever I navigate to define roles page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify breadcrumbs in manage roles tab
    Given I navigate to "Users > Permissions > Define roles" in site administration
    When "Define roles" "text" should exist in the ".breadcrumb" "css_element"
    Then "Permissions" "link" should exist in the ".breadcrumb" "css_element"

  Scenario Outline: Verify breadcrumbs in allow role tabs
    Given I navigate to "Users > Permissions > Define roles" in site administration
    When I click on "<allowlink>" "link"
    Then "Define roles" "text" should exist in the ".breadcrumb" "css_element"
    And "Permissions" "link" should exist in the ".breadcrumb" "css_element"

    Examples:
      | allowlink              |
      | Allow role assignments |
      | Allow role overrides   |
      | Allow role switches    |
      | Allow role to view     |

  Scenario: Verify breadcrumbs in new role page
    Given I navigate to "Users > Permissions > Define roles" in site administration
    And I click on "Add a new role" "button"
    Then "Define roles" "text" should exist in the ".breadcrumb" "css_element"
    And "Permissions" "link" should exist in the ".breadcrumb" "css_element"
