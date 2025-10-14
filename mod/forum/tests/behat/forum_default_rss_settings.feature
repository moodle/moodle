@mod @mod_forum @core_rss
Feature: Verify that default RSS settings for forums are correctly applied.
  In order to ensure consistent RSS configuration across new forums
  As an admin
  I need to verify that the default RSS values set at site level appear when creating a new forum.

  Background:
    Given the following "courses" exist:
      | fullname        | shortname |
      | RSS Test Course | RSS101    |
    And the following config values are set as admin:
      | enablerssfeeds       | 1 |
      | forum_enablerssfeeds | 1 |
      | forum_rsstype        | 1 |
      | forum_rssarticles    | 5 |

  Scenario: Admin sets default forum RSS values and verifies them on new forum creation
    Given I log in as "admin"
    When I am on "RSS101" course homepage with editing mode on
    And I add a forum activity to course "RSS101" section "New section"
    And I expand all fieldsets
    Then the field "RSS feed for this activity" matches value "Discussions"
    And the field "Number of RSS recent articles" matches value "5"
