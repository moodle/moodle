@mod @mod_forum
Feature: Posting to forums in a course with no groups behaves correctly

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name                   | course | idnumber     | groupmode |
      | forum      | Standard forum         | C1     | nogroups     | 0         |
      | forum      | Visible forum          | C1     | visgroups    | 2         |
      | forum      | Separate forum         | C1     | sepgroups    | 1         |

  Scenario: Teachers can post in standard forum
    Given I am on the "Standard forum" "forum activity" page logged in as teacher1
    When I click on "Add discussion topic" "link"
    Then I should not see "Post a copy to all groups"
    And I set the following fields to these values:
      | Subject | Teacher -> All participants |
      | Message | Teacher -> All participants |
    And I press "Post to forum"
    And I wait to be redirected
    And I should see "Teacher -> All participants"

  Scenario: Teachers can post in forum with separate groups
    Given I am on the "Separate forum" "forum activity" page logged in as teacher1
    When I click on "Add discussion topic" "link"
    Then I should not see "Post a copy to all groups"
    And I set the following fields to these values:
      | Subject | Teacher -> All participants |
      | Message | Teacher -> All participants |
    And I press "Post to forum"
    And I wait to be redirected
    And I should see "Teacher -> All participants"

  Scenario: Teachers can post in forum with visible groups
    Given I am on the "Visible forum" "forum activity" page logged in as teacher1
    When I click on "Add discussion topic" "link"
    Then I should not see "Post a copy to all groups"
    And I set the following fields to these values:
      | Subject | Teacher -> All participants |
      | Message | Teacher -> All participants |
    And I press "Post to forum"
    And I wait to be redirected
    And I should see "Teacher -> All participants"

  Scenario: Students can post in standard forum
    Given I am on the "Standard forum" "forum activity" page logged in as student1
    When I click on "Add discussion topic" "link"
    Then I should not see "Post a copy to all groups"
    And I set the following fields to these values:
      | Subject | Student -> All participants |
      | Message | Student -> All participants |
    And I press "Post to forum"
    And I wait to be redirected
    And I should see "Student -> All participants"

  Scenario: Students cannot post in forum with separate groups
    When I am on the "Separate forum" "forum activity" page logged in as student1
    Then I should see "You are not able to create a discussion because you are not a member of any group."
    And I should not see "Add discussion topic"

  Scenario: Students cannot post in forum with visible groups
    When I am on the "Visible forum" "forum activity" page logged in as student1
    Then I should see "You are not able to create a discussion because you are not a member of any group."
    And I should not see "Add discussion topic"
