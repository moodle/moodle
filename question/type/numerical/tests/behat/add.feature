@qtype @qtype_numerical
Feature: Test creating a Numerical question
  As a teacher
  In order to test my students
  I need to be able to create a Numerical question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  Scenario: Create a Numerical question
    When I add a "Numerical" question filling the form with:
      | Question name                      | Numerical-001                          |
      | Question text                      | What is the average of 4, 5, 6 and 10? |
      | Default mark                       | 1                                      |
      | General feedback                   | The average is 6.25                    |
      | id_answer_0                        | 6.25                                   |
      | id_tolerance_0                     | 0.05                                   |
      | id_fraction_0                      | 100%                                   |
      | id_answer_1                        | 2#25                                   |
      | id_tolerance_1                     | 0#05                                   |
      | id_fraction_1                      | 0%                                     |
      | id_answer_2                        | 5,1                                    |
      | id_tolerance_2                     | 0                                      |
      | id_fraction_2                      | 100%                                   |
    Then I should see "Numerical-001"
