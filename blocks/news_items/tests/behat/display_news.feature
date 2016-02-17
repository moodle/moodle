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
    And I log in as "admin"
    And I create a course with:
      | Course full name | Course 1 |
      | Course short name | C1 |
      | News items to show | 5 |
    And I enrol "Teacher 1" user as "Teacher"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    When I add a new topic to "Announcements" forum with:
      | Subject | Discussion One |
      | Message | Not important |
    And I add a new topic to "Announcements" forum with:
      | Subject | Discussion Two |
      | Message | Not important |
    And I add a new topic to "Announcements" forum with:
      | Subject | Discussion Three |
      | Message | Not important |
    And I follow "Course 1"
    Then I should see "Discussion One" in the "Latest announcements" "block"
    And I should see "Discussion Two" in the "Latest announcements" "block"
    And I should see "Discussion Three" in the "Latest announcements" "block"
    And I follow "Edit settings"
    And I set the following fields to these values:
      | News items to show | 2 |
    And I press "Save and display"
    And I should not see "Discussion One" in the "Latest announcements" "block"
    And I should see "Discussion Two" in the "Latest announcements" "block"
    And I should see "Discussion Three" in the "Latest announcements" "block"
    And I follow "Edit settings"
    And I set the following fields to these values:
      | News items to show | 0 |
    And I press "Save and display"
    And "Latest announcements" "block" should not exist
