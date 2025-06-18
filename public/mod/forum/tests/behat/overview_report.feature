@mod @mod_forum
Feature: Testing overview integration in mod_forum
  In order to summarize the forums
  As a user
  I need to be able to see the forum overview

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | trackforums |
      | s1       | Username  | 1        | 1           |
      | s2       | Username  | 2        | 0           |
      | s3       | Username  | 3        | 0           |
      | s4       | Username  | 4        | 0           |
      | t1       | Teacher   | T        | 1           |
    And the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 1         |
    And "4" "course enrolments" exist with the following data:
      | user   | s[count] |
      | course | C1       |
      | role   | student  |
    And the following "course enrolments" exist:
      | user | course | role           |
      | t1   | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name           | course | idnumber | duedate              | type     | forcesubscribe | trackingtype |
      | forum    | Due date       | C1     | forum1   | ##1 Jan 2040 08:00## | qanda    | 0              | 0            |
      | forum    | No discussions | C1     | forum2   | ##tomorrow noon##    | eachuser | 2              | 0            |
      | forum    | Unread posts   | C1     | forum3   |                      | general  | 3              | 1            |
    And the following "mod_forum > discussions" exist:
      | user | forum       | name              | message                 |
      | t1   | forum3      | Test discussion 1 | Test post message one   |
      | s1   | forum3      | Test discussion 2 | Test post message two   |
      | s2   | forum3      | Test discussion 3 | Test post message three |
    And the following "mod_forum > posts" exist:
      | user | parentsubject     | subject                 | message                 |
      | s1   | Test discussion 1 | Reply 1 to discussion 1 | Discussion contents 1.1 |
      | s3   | Test discussion 1 | Reply 2 to discussion 1 | Discussion contents 1.2 |

  Scenario: The forum overview report should generate log events
    Given I am on the "Course 1" "course > activities > forum" page logged in as "t1"
    When I am on the "Course 1" "course" page logged in as "t1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'forum'"

  @javascript
  Scenario: Teachers can see relevant columns in the forum overview
    When I am on the "Course 1" "course > activities > forum" page logged in as "t1"
    # Check columns. Posts = 3 discussions + 2 replies.
    Then the following should exist in the "Table listing all Forum activities" table:
      | Name           | Forum type                       | Track | Subscribed | Digest type | Discussions | Posts |
      | Due date       | Q and A forum                    |       |            |             | 0           | 0     |
      | No discussions | Each person posts one discussion |       |            |             | 0           | 0     |
      | Unread posts   | Standard forum for general use   |       |            |             | 3           | 5     |
    And I should not see "Tomorrow" in the "forum_overview_collapsible" "region"
    # Check Subscribe toggle works.
    And I click on "input[data-type=forum-subscription-toggle]" "css_element" in the "No discussions" "table_row"
    And I follow "No discussions"
    And I should see "Subscribe to forum"
    And I am on the "Course 1" "course > activities > forum" page
    And I click on "input[data-type=forum-subscription-toggle]" "css_element" in the "No discussions" "table_row"
    And I follow "No discussions"
    And I should see "Unsubscribe from forum"
    # Check Tracking toggle works.
    And I am on the "Course 1" "course > activities > forum" page
    And I click on "input[data-type=forum-track-toggle]" "css_element" in the "Unread posts" "table_row"
    And I follow "Unread posts"
    And I navigate to "Track unread posts" in current page administration
    And I navigate to "Don't track unread posts" in current page administration
    And I should not see "Don't track unread posts "
    And I am on the "Course 1" "course > activities > forum" page
    And I click on "input[data-type=forum-track-toggle]" "css_element" in the "Unread posts" "table_row"
    And I follow "Unread posts"
    And I navigate to "Don't track unread posts" in current page administration

  @javascript
  Scenario: Students can see relevant columns in the forum overview
    When I am on the "Course 1" "course > activities > forum" page logged in as "s1"
    # Check columns. Posts = 3 discussions + 2 replies.
    Then the following should exist in the "Table listing all Forum activities" table:
      | Name           | Due date       | Track | Subscribed | Digest type | Discussions | Posts |
      | Due date       | 1 January 2040 |       |            |             | 0           | 0     |
      | No discussions | Tomorrow       |       |            |             | 0           | 0     |
      | Unread posts   | -              |       |            |             | 3           | 5     |
    And I should not see "Forum type" in the "forum_overview_collapsible" "region"
    # Check Subscribe toggle works.
    And I click on "input[data-type=forum-subscription-toggle]" "css_element" in the "No discussions" "table_row"
    And I follow "No discussions"
    And I should see "Subscribe to forum"
    And I am on the "Course 1" "course > activities > forum" page
    And I click on "input[data-type=forum-subscription-toggle]" "css_element" in the "No discussions" "table_row"
    And I follow "No discussions"
    And I should see "Unsubscribe from forum"
    # Check Tracking toggle works.
    And I am on the "Course 1" "course > activities > forum" page
    And I click on "input[data-type=forum-track-toggle]" "css_element" in the "Unread posts" "table_row"
    And I follow "Unread posts"
    And I navigate to "Track unread posts" in current page administration
    And I navigate to "Don't track unread posts" in current page administration
    And I should not see "Don't track unread posts "
    And I am on the "Course 1" "course > activities > forum" page
    And I click on "input[data-type=forum-track-toggle]" "css_element" in the "Unread posts" "table_row"
    And I follow "Unread posts"
    And I navigate to "Don't track unread posts" in current page administration

  Scenario: The forum index redirect to the activities overview
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Activities" block
    And I click on "Forums" "link" in the "Activities" "block"
    Then I should see "An overview of all activities in the course"
    And I should see "Name" in the "forum_overview_collapsible" "region"
    And I should see "Discussions" in the "forum_overview_collapsible" "region"
    And I should see "Posts" in the "forum_overview_collapsible" "region"
