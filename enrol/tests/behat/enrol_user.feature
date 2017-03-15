@enrol
Feature: User can be enrolled into a course
  In order to let them participate in course activities
  As an admin
  I must be able to enrol users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Studie    | One      | student1@example.com |
    And the following "courses" exist:
      | fullname   | shortname |
      | Course 001 | C001      |
    And I log in as "admin"
    And I am on course index
    And I am on "Course 001" course homepage

  Scenario: User can be enrolled without javascript
    When I enrol "Studie One" user as "Student"
    And I navigate to "Enrolled users" node in "Course administration > Users"
    Then I should see "Studie One"

  @javascript
  Scenario: User can be enrolled with javascript enrol element
    When I enrol "Studie One" user as "Student"
    And I navigate to "Enrolled users" node in "Course administration > Users"
    Then I should see "Studie One"
