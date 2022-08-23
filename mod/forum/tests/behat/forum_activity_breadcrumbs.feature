@mod @mod_forum @javascript
Feature: A teacher or admin can view breadcrumbs in the reply, delete, split, edit and add discussion advanced pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher  | Teacher   | Tom      | teacher@example.com   |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher  | C1     | editingteacher |
    And the following "activity" exists:
      | course   | C1                             |
      | activity | forum                          |
      | name     | Test forum name                |

  Scenario: A teacher views add discussion topics advanced page
    Given I am on the "Test forum name" "forum activity" page logged in as teacher
    And I click on "Add discussion topic" "link"
    When I click on "Advanced" "button"
    And I should see "Add discussion topic"
    Then I should see "Add discussion topic" in the ".breadcrumb" "css_element"
    And I should see "Test forum name" in the ".breadcrumb" "css_element"

  Scenario: A teacher adds posts and then verifies the breadcrumbs in the links
    Given I am on the "Course 1" course page logged in as teacher
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject one |
      | Message | Test post message one |
    And I reply "Test post subject one" post from "Test forum name" forum with:
      | Subject                 | Reply 1 to discussion 1               |
      | Message                 | Discussion contents 1, second message |
    And I should see "Settings" in the "//div[@class='ml-auto dropdown']" "xpath_element"
    When I follow "Edit"
    Then I should see "Edit discussion topic"
    And I should see "Edit discussion topic" in the ".breadcrumb" "css_element"
    And I should see "Test post subject one" in the ".breadcrumb" "css_element"
    And I click on "Cancel" "button"
    And I follow "Delete"
    And I should not see "Test forum description"
    And I should see "Delete" in the ".breadcrumb" "css_element"
    And I click on "Cancel" "button"
    And I follow "Reply"
    And I click on "Advanced" "button"
    And I should not see "Test forum description"
    And I should see "Reply to discussion"
    And I should see "Add reply" in the ".breadcrumb" "css_element"
    And I click on "Cancel" "button"
    And I follow "Split"
    And I should not see "Test forum description"
    And I should see "Split discussion" in the ".breadcrumb" "css_element"
    And I click on "Cancel" "button"
