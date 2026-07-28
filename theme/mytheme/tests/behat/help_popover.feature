@theme_boost
Feature: Using the help popover
  As a user who wants to use the help popover
  The help popover must be accessible

  Background:
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name                | Revision | Content    | Summary     | Status   |
      | This site policy    |          | full text2 | short text2 | active   |

  @javascript @accessibility
  Scenario: Checking the policies link in the footer popover
    Given I am on site homepage
    And I click on "Continue" "link"
    When I click on "Show footer" "button" in the "page-footer" "region"
    Then I should see "Policies" in the "page-footer" "region"
    And the page should meet accessibility standards with "best-practice" extra tests
