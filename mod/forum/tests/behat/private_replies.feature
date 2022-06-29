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
      | activity   | name                   | intro                   | course  | idnumber  |
      | forum      | Study discussions      | Test forum description  | C1      | forum     |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "student1"
    And I am on "Science 101" course homepage
    And I add a new discussion to "Study discussions" forum with:
      | Subject | Answers to the homework                        |
      | Message | Here are the answers to last night's homework. |
    And I log out
    And I log in as "teacher1"
    And I am on "Science 101" course homepage
    And I reply "Answers to the homework" post from "Study discussions" forum with:
      | Message         | How about you and I have a meeting after class about plagiarism? |
      | Reply privately | 1                                                                |

  Scenario: As a teacher I can see my own response
    Given I follow "Study discussions"
    And I follow "Answers to the homework"
    Then I should see "How about you and I have a meeting after class about plagiarism?"

  Scenario: As a fellow teacher I can see the other teacher's response
    Given I log out
    And I log in as "teacher2"
    And I am on "Science 101" course homepage
    And I follow "Study discussions"
    When I follow "Answers to the homework"
    Then I should see "How about you and I have a meeting after class about plagiarism?"

  Scenario: As the intended recipient I can see my own response
    Given I log out
    And I log in as "student1"
    And I am on "Science 101" course homepage
    And I follow "Study discussions"
    When I follow "Answers to the homework"
    Then I should see "How about you and I have a meeting after class about plagiarism?"

  Scenario: As a non-privileged user I cannot see my own response
    Given I log out
    And I log in as "student2"
    And I am on "Science 101" course homepage
    And I follow "Study discussions"
    When I follow "Answers to the homework"
    Then I should not see "How about you and I have a meeting after class about plagiarism?"
