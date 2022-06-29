@format @format_weeks
Feature: The current week should be highlighted in the course.
  In order to know which is the current week
  As a user
  I need to see the current week highlighted

  Scenario: Highlight first week
    Given the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | startdate |
      | Course 1 | C1        | weeks  | 0             | 5           | ##yesterday## |
    When I am on the "C1" "Course" page logged in as "admin"
    Then I should see "This week" in the "#section-1" "css_element"

  Scenario: Highlight next week
    Given the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | startdate |
      | Course 1 | C1        | weeks  | 0             | 5           | ##monday last week## |
    When I am on the "C1" "Course" page logged in as "admin"
    Then I should see "This week" in the "#section-2" "css_element"
