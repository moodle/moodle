@mod @mod_forum @javascript @gradingform @advancedgrading
Feature: Create activity and set various advanced grading methods
  In order set various advanced grading methods
  As an admin I need to create a forum
 And check all grading options are accessible and save correctly


Background:
 Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
 And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
 And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
 And I log in as "admin"
 And I follow "Course 1"
 And I turn editing mode on


Scenario: Overall; No grade, Individual; No grade.
 Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test 2 forum |
      | Description | Test forum description |
      | Forum type | Standard forum for general use |
      | Overall forum participation | No grade |
      | Individual posts | No grade |
 When I follow "Test 2 forum"
 Then I expand "Advanced grading" node
 And "Overall forum participation" "link" should be visible

Scenario: Overall; Rubric, Individual;No grade
 Given I add a "Forum" to section "1" and I fill the form with:
       | Forum name | Test 2 forum |
       | Description | Test forum description |
       | Forum type | Standard forum for general use |
       | Overall forum participation | Rubric |
       | Individual posts | No grade |
 When I follow "Test 2 forum"
 Then I expand "Advanced grading" node
 And "Overall forum participation" "link" should be visible

Scenario: Overall; Marking guide, Individual; No grade
 Given I add a "Forum" to section "1" and I fill the form with:
     | Forum name | Test 2 forum |
     | Description | Test forum description |
     | Forum type | Standard forum for general use |
     | Overall forum participation | Marking guide |
     | Individual posts | No grade |
 When I follow "Test 2 forum"
 Then I expand "Advanced grading" node
 And "Overall forum participation" "link" should be visible

Scenario: Overall; No grade, Individual;Rubric
 Given I add a "Forum" to section "1" and I fill the form with:
    | Forum name | Test 2 forum |
    | Description | Test forum description |
    | Forum type | Standard forum for general use |
    | Overall forum participation | No grade |
    | Individual posts | Rubric |
 When I follow "Test 2 forum"
 Then I expand "Advanced grading" node
 And "Overall forum participation" "link" should be visible

Scenario:Overall; No grade, Individual; Rubric
 Given I add a "Forum" to section "1" and I fill the form with:
    | Forum name | Test 2 forum |
    | Description | Test forum description |
    | Forum type | Standard forum for general use |
    | Overall forum participation | No grade |
    | Individual posts | Rubric |
 When I follow "Test 2 forum"
 Then I expand "Advanced grading" node
 And "Overall forum participation" "link" should be visible

Scenario:Overall; Rubric, Individual; Marking guide
 Given I add a "Forum" to section "1" and I fill the form with:
    | Forum name | Test 2 forum |
    | Description | Test forum description |
    | Forum type | Standard forum for general use |
    | Overall forum participation | Rubric |
    | Individual posts | Marking guide |
 When I follow "Test 2 forum"
 Then I expand "Advanced grading" node
 And "Overall forum participation" "link" should be visible










