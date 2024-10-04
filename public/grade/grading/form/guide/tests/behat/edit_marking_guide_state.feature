@gradingform @gradingform_guide
Feature: Teacher can edit a marking guide state
  In order to change marking guide back to draft
  As a teacher
  I need to be able to edit the marking guide status

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name     | advancedgradingmethod_submissions |
      | assign   | C1     | Assign 1 | guide                             |

  Scenario Outline: Marking guide state can be changed
    Given I am on the "Course 1" course page logged in as teacher1
    And I go to "Assign 1" advanced grading definition page
    And I set the following fields to these values:
      | Name                                 | Assign 1 marking guide    |
      | Description                          | Marking guide description |
    And I define the following marking guide:
      | Criterion name    | Description for students         | Description for markers         | Maximum score |
      | Grade Criteria 1  | Grade 1 description for students | Grade 1 description for markers | 70            |
      | Grade Criteria 2  | Grade 2 description for students | Grade 2 description for markers | 30            |
    When I press "<buttonname>"
    Then I <drafttextvisibility> see "Please note: the advanced grading form is not ready at the moment. Simple grading method will be used until the form has a valid status."
    And I <drafttextvisibility> see "Assign 1 marking guide Draft"
    And I <readytextvisibility> see "Assign 1 marking guide Ready for use"

    Examples:
      | buttonname                           | drafttextvisibility | readytextvisibility |
      | Save marking guide and make it ready | should not          | should              |
      | Save as draft                        | should              | should not          |
