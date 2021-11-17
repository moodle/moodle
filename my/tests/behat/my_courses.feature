@core @core_my
Feature: Run tests over my courses.

  Scenario: Admin can add new courses or manage them from my courses
    Given I am on the "My courses" page logged in as "admin"
    And I click on "Course management options" "link"
    And I click on "New course" "link"
    And I wait to be redirected
    Then I should see "Add a new course"
    And I am on the "My courses" page
    And I click on "Course management options" "link"
    And I click on "Manage courses" "link"
    And I should see "Course and category management"
