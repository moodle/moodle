@mod @mod_forum @core_course @core_courseformat @format_topics
Feature: Course linear navigation from forum activity
  In order to continue my course trajectory after participating in a forum
  As a learner
  I want to be able to click the Next and Previous activity links directly from the forum activity page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student  | Student   | 1        |
      | teacher  | Teacher   | 1        |

  @javascript
  Scenario Outline: A user can go to the discussions list from a discussion forum page
    Given the following "courses" exist:
      | fullname | shortname | format | enablelinearnav |
      | Course1  | C1        | topics | <linearnav>     |
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
      | user | forum  | name     | message               |
      | teacher   | forum1 | Post one | Test post message one |
      | student   | forum1 | Post two | Test post message two |
    And the following "mod_forum > posts" exist:
      | user | parentsubject | subject                 | message                               |
      | student   | Post one      | Reply 1 to discussion 1 | Discussion contents 1, second message |
    When I am on the "Forum1" "forum activity" page logged in as "student"
    Then I should not see "Go to all discussions"
    And I <shouldnavbevisible> "Previous"
    And I <shouldnavbevisible> "Next"
    # The Go to all discussions link should be visible when we are in a discussion page.
    But I follow "Post one"
    And I should see "Go to all discussions" in the "sticky-footer" "region"
    And I <shouldnavbevisible> "Previous" in the "sticky-footer" "region"
    And I <shouldnavbevisible> "Next" in the "sticky-footer" "region"
    # Clicking the Go to all discussions link should take us to the discussions list page.
    And I click on "Go to all discussions" "link" in the "sticky-footer" "region"
    And I should see "Forum1" in the "page-header" "region"
    And I should see "Post one" in the "page-content" "region"
    And I should see "Post two" in the "page-content" "region"
    And I should not see "Go to all discussions"

    Examples:
      | linearnav | shouldnavbevisible |
      | 0         | should not see     |
      | 1         | should see         |
