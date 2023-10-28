@mod @mod_forum
Feature: Forum posts can be replied to in private
  In order to post feedback to my students
  As a Teacher
  I need to be able to reply privately to students

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Science 101 | C1 | 0 |
    And the following "activities" exist:
      | activity   | name                   | course  | idnumber  |
      | forum      | Study discussions      | C1      | forum     |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "mod_forum > discussions" exist:
      | user     | forum  | name                    | message                                        |
      | student1 | forum  | Answers to the homework | Here are the answers to last night's homework. |
    And the following forum replies exist in course "Science 101":
      | user     | forum             | discussion              | message                                                        | privatereplyto |
      | teacher1 | Study discussions | Answers to the homework |How about you and I have a meeting after class about plagiarism?| student1       |

  Scenario: As a teacher I can see my own response
    Given I am on the "Study discussions" "forum activity" page logged in as teacher1
    When I follow "Answers to the homework"
    Then I should see "How about you and I have a meeting after class about plagiarism?"

  Scenario: As a fellow teacher I can see the other teacher's response
    Given I am on the "Study discussions" "forum activity" page logged in as teacher2
    When I follow "Answers to the homework"
    Then I should see "How about you and I have a meeting after class about plagiarism?"

  Scenario: As the intended recipient I can see my own response
    Given I am on the "Study discussions" "forum activity" page logged in as student1
    When I follow "Answers to the homework"
    Then I should see "How about you and I have a meeting after class about plagiarism?"

  Scenario: As a non-privileged user I cannot see my own response
    Given I am on the "Study discussions" "forum activity" page logged in as student2
    When I follow "Answers to the homework"
    Then I should not see "How about you and I have a meeting after class about plagiarism?"
