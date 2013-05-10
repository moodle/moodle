@core @core_course
Feature: Edit course settings
  In order to set the course according to my teaching needs
  As a teacher
  I need to edit the course settings

  @javascript
  Scenario: Edit course settings
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | summary | format |
      | Course 1 | C1 | <p>Course summary</p> | topics |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    When I follow "Edit settings"
    And I fill the moodle form with:
      | Course full name | Edited course fullname |
      | Course short name | Edited course shortname |
      | Course summary | Edited course summary |
    And I press "Save changes"
    And I follow "Edited course fullname"
    Then I should not see "Course 1"
    And I should not see "C1"
    And I should see "Edited course fullname"
    And I should see "Edited course shortname"
    And I follow "Edit settings"
    And the "Course full name" field should match "Edited course fullname" value
    And the "Course short name" field should match "Edited course shortname" value
    And the "Course summary" field should match "Edited course summary" value
    And I am on homepage
    And I should see "Edited course fullname"
