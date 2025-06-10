@mod @mod_forum @javascript
Feature: Guest and not logged users could see the option to add new post or reply
  In order to guide users to create an account
  As a guest or not logged user
  I want to see the option to add new post or reply

  Background:
    Given the following config values are set as admin:
      | enrol_guest | Yes |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher  | Teacher   | 1        | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And I am on the "Course 1" "enrolment methods" page logged in as teacher
    And I click on "Enable" "link" in the "Guest access" "table_row"

  Scenario Outline: As a not enrolled guest I don't see the option to add a new discussion
    Given the following "activities" exist:
      | activity | name       | course | idnumber | type   |
      | forum    | Forum      | C1     | forum    | <type> |
    And the following "mod_forum > discussions" exist:
      | user    | forum | name               | message                               |
      | teacher | forum | Forum discussion 1 | How awesome is this forum discussion? |
    And I log out
    And I am on "Course 1" course homepage
    When I press "Access as a guest"
    And I am on the "Forum" "forum activity" page
    Then I should not see "Add discussion topic"
    And I should see "Forum discussion 1"
    And I click on "Forum discussion 1" "link"
    And I should not see "Reply"

    Examples:
      | type       |
      | general    |
      | eachuser   |
      | qanda      |

  Scenario: As a not enrolled guest I don't see the option to add a new discussion in a single forum
    Given the following "activities" exist:
      | activity | name                      | course | idnumber | type   |
      | forum    | Forum (single discussion) | C1     | forum    | single |
    And I log out
    And I am on "Course 1" course homepage
    When I press "Access as a guest"
    And I am on the "Forum (single discussion)" "forum activity" page
    Then I should not see "Add discussion topic"
    And I should see "Forum (single discussion)"
    And I should not see "Reply"

  Scenario: As a not enrolled guest I don't see the option to add a new discussion in a blog type forum
    Given the following "activities" exist:
      | activity | name  | course | idnumber | type   |
      | forum    | Forum | C1     | forum    | blog   |
    And the following "mod_forum > discussions" exist:
      | user    | forum | name               | message                               |
      | teacher | forum | Forum discussion 1 | How awesome is this forum discussion? |
    And I log out
    And I am on "Course 1" course homepage
    When I press "Access as a guest"
    And I am on the "Forum" "forum activity" page
    Then I should not see "Add discussion topic"
    And I should see "Forum discussion 1"
    And I should not see "Reply"

  Scenario Outline: As an enrolled guest I see the option to add a new discussion
    Given I am on the "Course 1" "enrolment methods" page logged in as teacher
    And I click on "Enable" "link" in the "Self enrolment" "table_row"
    And the following "activities" exist:
      | activity | name       | course | idnumber | type   |
      | forum    | Forum      | C1     | forum    | <type> |
    And the following "mod_forum > discussions" exist:
      | user    | forum | name               | message                               |
      | teacher | forum | Forum discussion 1 | How awesome is this forum discussion? |
    And I log out
    And I am on "Course 1" course homepage
    When I press "Access as a guest"
    And I am on the "Forum" "forum activity" page
    Then I should see "Add discussion topic"
    And I click on "Add discussion topic" "link"
    And I should see "Sorry, guests are not allowed to post"
    And I click on "Cancel" "button"
    And I should see "Forum discussion 1"
    And I click on "Forum discussion 1" "link"
    And I should see "Reply"
    And I click on "Reply" "link"
    And I should see "Sorry, guests are not allowed to post"
    And I click on "Continue" "button"
    And I should see "Log in"

    Examples:
      | type       |
      | general    |
      | eachuser   |
      | qanda      |

  Scenario: As an enrolled guest I see the option to reply in a single forum
    Given I am on the "Course 1" "enrolment methods" page logged in as teacher
    And I click on "Enable" "link" in the "Self enrolment" "table_row"
    And the following "activities" exist:
      | activity | name                      | course | idnumber | type   |
      | forum    | Forum (single discussion) | C1     | forum    | single |
    And I log out
    And I am on "Course 1" course homepage
    When I press "Access as a guest"
    And I am on the "Forum (single discussion)" "forum activity" page
    And I should see "Forum (single discussion)"
    Then I should see "Reply"
    And I click on "Reply" "link"
    And I should see "Sorry, guests are not allowed to post"
    And I click on "Cancel" "button"
    And I should see "Reply"
    And I click on "Reply" "link"
    And I click on "Continue" "button"
    And I should see "Log in"

  Scenario: As an enrolled guest I see the option to reply in a blog type forum
    Given I am on the "Course 1" "enrolment methods" page logged in as teacher
    And I click on "Enable" "link" in the "Self enrolment" "table_row"
    And the following "activities" exist:
      | activity | name       | course | idnumber | type   |
      | forum    | Forum      | C1     | forum    | blog |
    And the following "mod_forum > discussions" exist:
      | user    | forum | name               | message                               |
      | teacher | forum | Forum discussion 1 | How awesome is this forum discussion? |
    And I log out
    And I am on "Course 1" course homepage
    When I press "Access as a guest"
    And I am on the "Forum" "forum activity" page
    Then I should see "Add discussion topic"
    And I click on "Add discussion topic" "link"
    And I should see "Sorry, guests are not allowed to post"
    And I click on "Cancel" "button"
    And I should see "Forum discussion 1"
    And I click on "Add discussion topic" "link"
    And I should see "Sorry, guests are not allowed to post"
    And I click on "Continue" "button"
    And I should see "Log in"
