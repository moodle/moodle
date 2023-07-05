@block @block_news_items
Feature: Latest announcements block displays the course latest news
  In order to be aware of the course announcements
  As a user
  I need to see the latest announcements block in the main course page

  @javascript
  Scenario: Latest course announcements are displayed and can be configured
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | newsitems |
      | Course 1 | C1        | 0        | 5         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "blocks" exist:
      | blockname  | contextlevel | reference | pagetypepattern | defaultregion |
      | news_items | Course       | C1        | course-view-*   | side-pre      |
    And I am on the "Course 1" Course page logged in as teacher1
    When I add a new topic to "Announcements" forum with:
      | Subject | Discussion One |
      | Message | Not important |
    And I add a new topic to "Announcements" forum with:
      | Subject | Discussion Two |
      | Message | Not important |
    And I add a new topic to "Announcements" forum with:
      | Subject | Discussion Three |
      | Message | Not important |
    And I am on "Course 1" course homepage
    Then I should see "Discussion One" in the "Latest announcements" "block"
    And I should see "Discussion Two" in the "Latest announcements" "block"
    And I should see "Discussion Three" in the "Latest announcements" "block"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Number of announcements | 2 |
    And I press "Save and display"
    And I should not see "Discussion One" in the "Latest announcements" "block"
    And I should see "Discussion Two" in the "Latest announcements" "block"
    And I should see "Discussion Three" in the "Latest announcements" "block"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Number of announcements | 0 |
    And I press "Save and display"
    And "Latest announcements" "block" should not exist
