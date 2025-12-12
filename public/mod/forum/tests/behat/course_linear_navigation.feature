@mod @mod_forum
Feature: Display the course linear navigation in the forum pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in forum pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname | format | enablelinearnav |
      | Course 1 | C1        | topics | 1               |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name   | course | idnumber |
      | page     | Page1  | C1     | page1    |
      | forum    | Forum1 | C1     | forum1   |
      | page     | Page2  | C1     | page2    |
    And the following "mod_forum > discussions" exist:
      | user    | forum  | name     | message               |
      | teacher | forum1 | Post one | Test post message one |
      | student | forum1 | Post two | Test post message two |
    And the following "mod_forum > posts" exist:
      | user    | parentsubject | subject                 | message                               |
      | student | Post one      | Reply 1 to discussion 1 | Discussion contents 1, second message |

  @javascript
  Scenario: As a student I should see the course linear navigation in forum pages that allow it
    Given I am on the "Forum1" "forum activity" page logged in as "student"
    Then the course linear navigation should be visible
    But I click on "Add discussion topic" "link"
    And the course linear navigation should be visible
    And I click on "Advanced" "button"
    And the course linear navigation should not be visible
    And I set the field "Subject" to "New message subject."
    And I set the field "Message" to "My new message content."
    And I press "Post to forum"
    And the course linear navigation should be visible
    And I follow "Post two"
    And the course linear navigation should be visible
    And I follow "Reply"
    And the course linear navigation should be visible
    And I click on "Advanced" "button"
    And the course linear navigation should not be visible
    And I set the field "Message" to "This is the reply text."
    And I press "Post to forum"
    And the course linear navigation should be visible

  @javascript
  Scenario: As a teacher I should see the course linear navigation in forum pages that allow it
    # The "Grade users" button that is displayed when Whole forum grading is enabled, opens a page in a fullscreen popup, so the
    # course linear navigation is still there, but it's not visible nor clickable. We decided to exclude this page from these tests
    # because we were expecting this popup to replace the content of the discussions page instead of opening it in a popup.
    Given I am on the "Forum1" "forum activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    And I follow "Post two"
    And the course linear navigation should be visible
    And I follow "Edit"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I follow "Delete"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I follow "Post one"
    And I follow "Split"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I navigate to "Subscriptions" in current page administration
    And the course linear navigation should not be visible
    And I set the field "Subscribers" to "Manage subscribers"
    And the course linear navigation should not be visible
    And I navigate to "Reports" in current page administration
    And the course linear navigation should not be visible
    And I navigate to "Export" in current page administration
    And the course linear navigation should not be visible
    # Add a discussion to trigger the subscription created event.
    And I am on the "Forum1" "forum activity" page
    And I click on "Add discussion topic" "link"
    And I set the following fields to these values:
      | Subject | student3 discussion |
      | Message | posted by student3  |
    And I press "Post to forum"
    And I navigate to "Logs" in current page administration
    And the course linear navigation should not be visible
    And I follow "Discussion subscription created"
    And I switch to a second window
    And the course linear navigation should not be visible

  @javascript
  Scenario: A user can navigate to previous and next activities from a forum activity page
    When I am on the "Forum1" "forum activity" page logged in as "student"
    Then I should see "Previous"
    And I should see "Next"
    And I click on "Next" "link" in the "sticky-footer" "region"
    And I should see "Page2" in the "page-header" "region"
    And I click on "Previous" "link" in the "sticky-footer" "region"
    # The Next/Previous links should be visible in the discussion page as well.
    And I follow "Post one"
    And I should see "Previous"
    And I should see "Next"
    And I click on "Previous" "link" in the "sticky-footer" "region"
    And I should see "Page1" in the "page-header" "region"
