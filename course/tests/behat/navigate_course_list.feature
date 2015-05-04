@core @core_course
Feature: Browse course list and return back from enrolment page
  In order to navigate between course list consistently
  As a user
  I need to be able to return back from enrolment page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | 1 | user1@example.com |
      | user2 | User | 2 | user2@example.com |
    And the following "categories" exist:
      | name | category | idnumber |
      | Sample category | 0 | CAT1 |
    And the following "courses" exist:
      | fullname      | shortname | category |
      | Sample course | C1        | 0        |
      | Course 1      | COURSE1   | CAT1     |

  @javascript
  Scenario: A user can return to the category page from enrolment page
    When I log in as "user2"
    And I click on "Courses" "link" in the "Navigation" "block"
    And I follow "Miscellaneous"
    And I follow "Sample course"
    And I press "Continue"
    Then I should see "Courses" in the ".breadcrumb-nav" "css_element"
    And I click on "Courses" "link" in the ".breadcrumb-nav" "css_element"
    And I follow "Sample category"
    And I follow "Course 1"
    And I press "Continue"
    And I should see "Sample category" in the ".breadcrumb-nav" "css_element"

  @javascript
  Scenario: A user can return to the previous page from enrolment page by clicking navigation links
    When I log in as "user2"
    And I follow "Preferences" in the user menu
    And I follow "Edit profile"
    And I expand "Courses" node
    And I expand "Sample category" node
    And I follow "Course 1"
    And I press "Continue"
    Then I should see "Edit profile" in the ".breadcrumb-nav" "css_element"

  @javascript
  Scenario: User can return to the choice activity from enrolment page
    Given the following "roles" exist:
      | name                   | shortname | description      | archetype      |
      | Non-enrolled           | custom1   | My custom role 1 | user           |
    And the following "role assigns" exist:
      | user  | role           | contextlevel | reference |
      | user1 | custom1        | Course       | C1        |
    And the following "activities" exist:
      | activity   | name        | intro                         | course | idnumber    |
      | choice     | Test choice | Test choice description       | C1     | choice1     |
    And I log in as "admin"
    And I set the following system permissions of "Non-enrolled" role:
      | capability | permission |
      | moodle/course:view | Allow |
    And I log out
    When I log in as "user1"
    And I click on "Courses" "link" in the "Navigation" "block"
    And I follow "Miscellaneous"
    And I follow "Sample course"
    And I follow "Test choice"
    And I should see "Sorry, only enrolled users are allowed to make choices."
    And I press "Enrol me in this course"
    And I press "Continue"
    Then I should see "Test choice" in the ".breadcrumb-nav" "css_element"
