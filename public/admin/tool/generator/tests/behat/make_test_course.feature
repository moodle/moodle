@core @core_course @tool_generator
Feature: Admins can create test courses
  In order to create testing information
  As an admin
  I need to create testing courses quickly

  @javascript
  Scenario: 'Auto-enrol admin in new courses' setting when creating a test course as admin
    Given I log in as "admin"
    And the following config values are set as admin:
      | enroladminnewcourse | 0 |
    And I navigate to "Development > Make test course" in site administration
    And I set the following fields to these values:
      | Size of course    | XS                      |
      | Course full name  | Fake course for testing |
      | Course short name | fake                    |
    And I press "Create course"
    And I click on "Continue" "link"
    And I navigate to course participants
    Then I should not see "Teacher"
    And I should not see "Nothing to display"
    And the following config values are set as admin:
      | enroladminnewcourse | 1 |
    And I navigate to "Courses > Add a new course" in site administration
    And I navigate to "Development > Make test course" in site administration
    And I set the following fields to these values:
      | Size of course    | XS                          |
      | Course full name  | New fake course for testing |
      | Course short name | newfake                     |
    And I press "Create course"
    And I click on "Continue" "link"
    And I navigate to course participants
    And I should see "Teacher"
