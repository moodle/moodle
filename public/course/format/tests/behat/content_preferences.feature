@core @core_course @core_courseformat
Feature: Course content collapsed user preferences
  In order to quickly access the course content
  As a user
  I need to keep the course sections collapsed when I return to the course.

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
      | numsections      | 5        |
      | initsections     | 1        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
      | book     | Activity sample 2 |                             | C1     | sample2  | 2       |
      | choice   | Activity sample 3 | Test choice description     | C1     | sample3  | 3       |
      | assign   | Activity sample 4 | Test assignment description | C1     | sample1  | 4       |
      | assign   | Activity sample 5 | Test assignment description | C1     | sample1  | 5       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Course content preferences
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I should see "Section 1" in the "region-main" "region"
    And I should see "Activity sample 1" in the "region-main" "region"
    And I should see "Section 2" in the "region-main" "region"
    And I should see "Activity sample 2" in the "region-main" "region"
    And I should see "Section 3" in the "region-main" "region"
    And I should see "Activity sample 3" in the "region-main" "region"
    And I click on "Collapse" "link" in the "Section 1" "section"
    When I reload the page
    Then I should see "Section 1" in the "region-main" "region"
    And I should not see "Activity sample 1" in the "region-main" "region"
    And I should see "Section 2" in the "region-main" "region"
    And I should see "Activity sample 2" in the "region-main" "region"
    And I should see "Section 3" in the "region-main" "region"
    And I should see "Activity sample 3" in the "region-main" "region"
    And I click on "Collapse" "link" in the "Section 2" "section"
    And I reload the page
    And I should see "Section 1" in the "region-main" "region"
    And I should not see "Activity sample 1" in the "region-main" "region"
    And I should see "Section 2" in the "region-main" "region"
    And I should not see "Activity sample 2" in the "region-main" "region"
    And I should see "Section 3" in the "region-main" "region"
    And I should see "Activity sample 3" in the "region-main" "region"
    And I click on "Collapse" "link" in the "Section 3" "section"
    And I reload the page
    And I should see "Section 1" in the "region-main" "region"
    And I should not see "Activity sample 1" in the "region-main" "region"
    And I should see "Section 2" in the "region-main" "region"
    And I should not see "Activity sample 2" in the "region-main" "region"
    And I should see "Section 3" in the "region-main" "region"
    And I should not see "Activity sample 3" in the "region-main" "region"
    And I click on "Expand" "link" in the "Section 2" "section"
    And I click on "Expand" "link" in the "Section 3" "section"
    And I reload the page
    And I should see "Section 1" in the "region-main" "region"
    And I should not see "Activity sample 1" in the "region-main" "region"
    And I click on "Collapse" "link" in the "Section 4" "section"
    And I turn editing mode on
    And I delete section "1"
    And I click on "Delete" "button" in the ".modal" "css_element"
    And I should not see "Activity sample 1" in the "region-main" "region"
    And I should see "Activity sample 2" in the "region-main" "region"
    And I should see "Activity sample 3" in the "region-main" "region"
    And I should not see "Activity sample 4" in the "region-main" "region"
    And I should see "Activity sample 5" in the "region-main" "region"
