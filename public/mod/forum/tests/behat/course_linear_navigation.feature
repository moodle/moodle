@mod @mod_forum @core_course @core_courseformat @format_topics
Feature: Course linear navigation from forum activity
  In order to continue my course trajectory after participating in a forum
  As a learner
  I want to be able to click the Next and Previous activity links directly from the forum activity page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | s1       | Student   | 1        |
      | t1       | Teacher   | 1        |

  @javascript
  Scenario Outline: A user can go to the discussions list from a discussion forum page
    Given the following "courses" exist:
      | fullname | shortname | format | enablelinearnav |
      | Course1  | C1        | topics | <linearnav>     |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | s1      | C1     | student        |
      | t1      | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name   | course | idnumber |
      | page     | Page1  | C1     | page1    |
      | forum    | Forum1 | C1     | forum1   |
      | page     | Page2  | C1     | page2    |
    And the following "mod_forum > discussions" exist:
      | user | forum  | name     | message               |
      | t1   | forum1 | Post one | Test post message one |
      | s1   | forum1 | Post two | Test post message two |
    And the following "mod_forum > posts" exist:
      | user | parentsubject | subject                 | message                               |
      | s1   | Post one      | Reply 1 to discussion 1 | Discussion contents 1, second message |
    When I am on the "Forum1" "forum activity" page logged in as "s1"
    Then I should not see "Go to all discussions"
    And I <shouldnavbevisible> "Previous"
    And I <shouldnavbevisible> "Next"
    # The Go to all discussions link should be visible when we are in a discussion page.
    But I follow "Post one"
    And I should see "Go to all discussions" in the "sticky-footer" "region"
    And I <shouldnavbevisible> "Previous"
    And I <shouldnavbevisible> "Next"
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

  @javascript
  Scenario: A user can navigate to previous and next activities from a forum activity page
    Given the following "courses" exist:
      | fullname | shortname | format | enablelinearnav |
      | Course1  | C1        | topics | 1               |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | s1      | C1     | student        |
      | t1      | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name   | course | idnumber |
      | page     | Page1  | C1     | page1    |
      | forum    | Forum1 | C1     | forum1   |
      | page     | Page2  | C1     | page2    |
    And the following "mod_forum > discussions" exist:
      | user | forum  | name     | message               |
      | t1   | forum1 | Post one | Test post message one |
      | s1   | forum1 | Post two | Test post message two |
    And the following "mod_forum > posts" exist:
      | user | parentsubject | subject                 | message                               |
      | s1   | Post one      | Reply 1 to discussion 1 | Discussion contents 1, second message |
    When I am on the "Forum1" "forum activity" page logged in as "s1"
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
