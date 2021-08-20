@core @core_course
Feature: Course index depending on role
  In order to quickly access the course structure
  As a user
  I need to see the current course structure in the course index.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
      | numsections      | 4        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
      | book     | Activity sample 2 | Test book description       | C1     | sample2  | 2       |
      | choice   | Activity sample 3 | Test choice description     | C1     | sample3  | 3       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  Scenario: Course index is present on course and activities.
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    Then I should see "Open course index drawer"
    And I follow "Activity sample 1"
    And I should see "Open course index drawer"

  @javascript
  Scenario: Course index as a teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Side panel" "button"
    When I click on "Open course index drawer" "button"
    And I click on "Topic 1" "link" in the "courseindex-content" "region"
    And I click on "Topic 2" "link" in the "courseindex-content" "region"
    And I click on "Topic 3" "link" in the "courseindex-content" "region"
    Then I should see "Topic 1" in the "courseindex-content" "region"
    And I should see "Topic 2" in the "courseindex-content" "region"
    And I should see "Topic 3" in the "courseindex-content" "region"
    And I should see "Activity sample 1" in the "courseindex-content" "region"
    And I should see "Activity sample 2" in the "courseindex-content" "region"
    And I should see "Activity sample 3" in the "courseindex-content" "region"

  @javascript
  Scenario: Teacher can see hiden activities and sections
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I hide section "2"
    And I open "Activity sample 3" actions menu
    And I click on "Hide" "link" in the "Activity sample 3" activity
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Side panel" "button"
    When I click on "Open course index drawer" "button"
    And I click on "Topic 1" "link" in the "courseindex-content" "region"
    And I click on "Topic 2" "link" in the "courseindex-content" "region"
    And I click on "Topic 3" "link" in the "courseindex-content" "region"
    Then I should see "Topic 1" in the "courseindex-content" "region"
    And I should see "Topic 2" in the "courseindex-content" "region"
    And I should see "Topic 3" in the "courseindex-content" "region"
    And I should see "Activity sample 1" in the "courseindex-content" "region"
    And I should see "Activity sample 2" in the "courseindex-content" "region"
    And I should see "Activity sample 3" in the "courseindex-content" "region"

  @javascript
  Scenario: Students can only see visible activies and sections
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I hide section "2"
    And I open "Activity sample 3" actions menu
    And I click on "Hide" "link" in the "Activity sample 3" activity
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "Side panel" "button"
    When I click on "Open course index drawer" "button"
    And I click on "Topic 1" "link" in the "courseindex-content" "region"
    And I click on "Topic 3" "link" in the "courseindex-content" "region"
    Then I should see "Topic 1" in the "courseindex-content" "region"
    And I should not see "Topic 2" in the "courseindex-content" "region"
    And I should see "Topic 3" in the "courseindex-content" "region"
    And I should see "Activity sample 1" in the "courseindex-content" "region"
    And I should not see "Activity sample 2" in the "courseindex-content" "region"
    And I should not see "Activity sample 3" in the "courseindex-content" "region"

  @javascript
  Scenario: Delete an activity as a teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Side panel" "button"
    When I delete "Activity sample 2" activity
    And I click on "Open course index drawer" "button"
    And I click on "Topic 1" "link" in the "courseindex-content" "region"
    And I click on "Topic 2" "link" in the "courseindex-content" "region"
    Then I should not see "Activity sample 2" in the "courseindex-content" "region"

  @javascript
  Scenario: Highlight sections are represented in the course index.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Side panel" "button"
    And I turn section "2" highlighting on
    # Current section is only marked visually in the course index.
    And the "class" attribute of "#courseindex-content [data-for='section'][data-number='2']" "css_element" should contain "current"
    When I turn section "1" highlighting on
    And I click on "Open course index drawer" "button"
    # Current section is only marked visually in the course index.
    Then the "class" attribute of "#courseindex-content [data-for='section'][data-number='1']" "css_element" should contain "current"
