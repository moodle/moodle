@tool @tool_lp @javascript
Feature: View competencies
  In order to access competency information
  As a user
  I need to view user competencies

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname |
      | student1  | Student   | first    |
      | teacher1  | Teacher   | first    |
    And the following "system role assigns" exist:
      | user     | role           | contextlevel |
      | teacher1 | editingteacher | System       |
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/competency:planview | Allow      | editingteacher | System       |           |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "scales" exist:
      | name       | scale            |
      | Test Scale | Bad, Good, Great |
    And the following "core_competency > frameworks" exist:
      | shortname | idnumber | scale      |
      | Cookery   | cookery  | Test Scale |
      | Literacy  | literacy | Test Scale |
    And the following "core_competency > competencies" exist:
      | shortname      | idnumber       | description            | competencyframework |
      | Salads         | salads         | Salads are important   | cookery             |
      | Desserts       | desserts       | Desserts are important | cookery             |
      | Cakes          | cakes          | Cakes are important    | cookery             |
      | Reading        | reading        | Reading is important   | literacy            |
      | Writing        | writing        | Writing is important   | literacy            |
    And the following "core_competency > related_competencies" exist:
      | competency | relatedcompetency |
      | desserts   | cakes             |
    And the following "core_competency > plans" exist:
      | name     | description           | competencies            | user     |
      | Cookery  | Cookery is important  | salads, desserts, cakes | student1 |
      | Literacy | Literacy is important | reading, writing        | student1 |
    And the following "core_competency > course_competencies" exist:
      | course | competency |
      | C1     | salads     |
      | C1     | desserts   |
      | C1     | cakes      |
      | C1     | reading    |
      | C1     | writing    |
    And the following "core_competency > user_competency" exist:
      | competency | user     | grade |
      | salads     | student1 | Good  |
      | desserts   | student1 | Great |
      | cakes      | student1 | Great |
    And the following "core_competency > user_competency_courses" exist:
      | course | competency | user     | grade |
      | C1     | salads     | student1 | Good  |
      | C1     | desserts   | student1 | Great |
      | C1     | cakes      | student1 | Great |

  Scenario: Student view
    # Course competencies
    Given I am on the "C1" "tool_lp > course competencies" page logged in as "student1"
    Then I should see "You are proficient in 3 out of 5 competencies in this course"

    And "Salads are important" "tool_lp > competency description" should exist in the "Salads" "tool_lp > competency"
    And "Good" "tool_lp > competency grade" should exist in the "Salads" "tool_lp > competency"

    And "Desserts are important" "tool_lp > competency description" should exist in the "Desserts" "tool_lp > competency"
    And "Great" "tool_lp > competency grade" should exist in the "Desserts" "tool_lp > competency"

    And "Cakes are important" "tool_lp > competency description" should exist in the "Cakes" "tool_lp > competency"
    And "Great" "tool_lp > competency grade" should exist in the "Cakes" "tool_lp > competency"

    And "Reading is important" "tool_lp > competency description" should exist in the "Reading" "tool_lp > competency"
    And "Good" "tool_lp > competency grade" should not exist in the "Reading" "tool_lp > competency"
    And "Great" "tool_lp > competency grade" should not exist in the "Reading" "tool_lp > competency"
    And "Bad" "tool_lp > competency grade" should not exist in the "Reading" "tool_lp > competency"

    And "Writing is important" "tool_lp > competency description" should exist in the "Writing" "tool_lp > competency"
    And "Good" "tool_lp > competency grade" should not exist in the "Writing" "tool_lp > competency"
    And "Great" "tool_lp > competency grade" should not exist in the "Writing" "tool_lp > competency"
    And "Bad" "tool_lp > competency grade" should not exist in the "Writing" "tool_lp > competency"

    # Course competencies details
    And I click on "Desserts" "link" in the "Desserts" "tool_lp > competency"
    And I should see "Desserts are important"
    And "Yes" "tool_lp > competency page proficiency" should exist
    And "Great" "tool_lp > competency page rating" should exist

    # Course competencies summary
    And I click on "Cakes" "link"
    And I should see "Cakes are important"

    # Learning plans
    And I click on "Close" "button" in the "Cakes" "dialogue"
    And I follow "Profile" in the user menu
    And I click on "Learning plans" "link"
    And I should see "Cookery"
    And I should see "Literacy"

    # Learning plans details
    And I click on "Cookery" "link"
    And I should see "Cookery is important"
    And I should see "3 out of 3 competencies are proficient"

    And "Good" "tool_lp > learning plan rating" should exist in the "Salads" "tool_lp > learning plan"
    And "Yes" "tool_lp > learning plan proficiency" should exist in the "Salads" "tool_lp > learning plan"

    And "Great" "tool_lp > learning plan rating" should exist in the "Desserts" "tool_lp > learning plan"
    And "Yes" "tool_lp > learning plan proficiency" should exist in the "Desserts" "tool_lp > learning plan"

    And "Great" "tool_lp > learning plan rating" should exist in the "Cakes" "tool_lp > learning plan"
    And "Yes" "tool_lp > learning plan proficiency" should exist in the "Cakes" "tool_lp > learning plan"

    And I should not see "Literacy"
    And I should not see "Reading"
    And I should not see "Writing"

    # Learning plans competency details
    And I click on "Desserts" "link" in the "Desserts" "tool_lp > learning plan"
    And I should see "Desserts are important"
    And "Yes" "tool_lp > competency page proficiency" should exist
    And "Great" "tool_lp > competency page rating" should exist

    # Learning plans competency summary
    And I click on "Cakes cakes" "link"
    And I should see "Cakes are important"

  Scenario: Teacher view
    # Participant competencies
    Given I am on the "C1" "report_competency > breakdown" page logged in as "teacher1"
    Then I should see "Student first"
    And "Good" "report_competency > breakdown rating" should exist in the "Salads" "report_competency > breakdown"
    And "Great" "report_competency > breakdown rating" should exist in the "Desserts" "report_competency > breakdown"
    And "Great" "report_competency > breakdown rating" should exist in the "Cakes" "report_competency > breakdown"
    And "Not rated" "report_competency > breakdown rating" should exist in the "Reading" "report_competency > breakdown"
    And "Not rated" "report_competency > breakdown rating" should exist in the "Writing" "report_competency > breakdown"

    # Participant competencies details
    And I click on "Great" "report_competency > breakdown rating" in the "Desserts" "report_competency > breakdown"
    And "Yes" "tool_lp > competency page proficiency" should exist
    And "Great" "tool_lp > competency page rating" should exist

    # Participant competencies summary
    And I click on "Cakes" "tool_lp > competency page related competency"
    And I should see "Cakes are important"

    # Participant learning plans
    And I click on "Close" "button" in the "Cakes" "dialogue"
    And I click on "Close" "button" in the "User competency summary" "dialogue"
    And I navigate to course participants
    And I click on "Student first" "link"
    And I click on "Learning plans" "link"
    And I should see "Cookery"
    And I should see "Literacy"

    # Participant learning plans details
    And I click on "Cookery" "link"
    And I should see "Cookery is important"
    And I should see "3 out of 3 competencies are proficient"

    And "Good" "tool_lp > learning plan rating" should exist in the "Salads" "tool_lp > learning plan"
    And "Yes" "tool_lp > learning plan proficiency" should exist in the "Salads" "tool_lp > learning plan"

    And "Great" "tool_lp > learning plan rating" should exist in the "Desserts" "tool_lp > learning plan"
    And "Yes" "tool_lp > learning plan proficiency" should exist in the "Desserts" "tool_lp > learning plan"

    And "Great" "tool_lp > learning plan rating" should exist in the "Cakes" "tool_lp > learning plan"
    And "Yes" "tool_lp > learning plan proficiency" should exist in the "Cakes" "tool_lp > learning plan"

    And I should not see "Literacy"
    And I should not see "Reading"
    And I should not see "Writing"

    # Learning plans competency details
    And I click on "Desserts" "link"
    And I should see "Desserts are important"
    And "Yes" "tool_lp > competency page proficiency" should exist
    And "Great" "tool_lp > competency page rating" should exist

    # Learning plans competency summary
    And I click on "Cakes" "tool_lp > competency page related competency"
    And I should see "Cakes are important"
