@core @core_blog
Feature: Blog links in user profiles
  In order to navigate to personal and site blog entries
  As a user
  I need to access blog links from my profile

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | testuser | Test      | User     | moodle@example.com |
    And the following "core_blog > entries" exist:
      | subject       | body                     | user     |
      | Blog post one | User 1 blog post content | testuser |

  Scenario: A user can navigate to personal and site blog entries from their profile
    Given I am on the "testuser" "user > profile" page logged in as testuser
    Then I should see "View my blog entries" in the ".profile_tree" "css_element"
    And I should see "View site blog entries" in the ".profile_tree" "css_element"
    When I follow "View my blog entries"
    Then I should see "User blog: Test User"
    And I should see "Blog post one"
    And I am on the "testuser" "user > profile" page
    When I follow "View site blog entries"
    Then I should see "Site blog"
    And I should see "Blog post one"

  Scenario: Another user sees a neutral blog entries label when viewing a user profile
    Given the following "users" exist:
      | username  | firstname | lastname | email               |
      | otheruser | Other     | User     | other@example.com   |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user      | course | role    |
      | testuser  | C1     | student |
      | otheruser | C1     | student |
    And I am on the "testuser" "user > profile" page logged in as otheruser
    Then I should see "Test User"
    And I should see "View blog entries" in the ".profile_tree" "css_element"
    And I should not see "View my blog entries"
    And I should see "View site blog entries" in the ".profile_tree" "css_element"

  Scenario: Blog links are not displayed when blogs are disabled
    Given the following config values are set as admin:
      | enableblogs | 0 |
    When I am on the "testuser" "user > profile" page logged in as testuser
    Then I should not see "View my blog entries"
    And I should not see "View site blog entries"
