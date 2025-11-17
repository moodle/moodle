@mod @mod_feedback
Feature: Preview feedback questions
  In order to view the feedback questions
  As a teacher
  I need to be able to preview them

  Background:
    Given the following "users" exist:
      | username       | firstname   | lastname |
      | student        | Student     | User     |
      | editingteacher | Editing     | Teacher  |
      | teacher        | NonEditing  | Teacher  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user           | course | role           |
      | student        | C1     | student        |
      | editingteacher | C1     | editingteacher |
      | teacher        | C1     | teacher        |
    And the following "activities" exist:
      | activity   | name           | course | idnumber    |
      | feedback   | Music history  | C1     | feedback0   |
    And the following "mod_feedback > question" exists:
      | activity      | feedback0                         |
      | name          | What is your favourite instrument |
      | questiontype  | multichoice                       |
      | label         | instrument1                       |
      | values        | Drums\nGuitar\nHurdy-gurdy        |

  Scenario: Students cannot see the Preview questions button
    When I am on the "Music history" "feedback activity" page logged in as student
    Then I should not see "Preview questions"

  Scenario: Non-editing teachers can see the Preview questions button
    When I am on the "Music history" "feedback activity" page logged in as teacher
    Then I should see "Preview questions"
    And I follow "Preview questions"
    And I should see "What is your favourite instrument"

  Scenario: Editing teachers can see the Preview questions button
    When I am on the "Music history" "feedback activity" page logged in as editingteacher
    Then I should see "Preview questions"
    And I follow "Preview questions"
    And I should see "What is your favourite instrument"

  @javascript @accessibility
  Scenario: Feedback questionnaire pages should be accessible
    Given the following "mod_feedback > question" exists:
      | activity      | feedback0                               |
      | name          | What type of guitar is your favourite?  |
      | questiontype  | multichoice                             |
      | label         | whatguitar                              |
      | dependitem    | instrument1                             |
      | dependvalue   | Guitar                                  |
      | values        | Electric\nAcoustic\nBass                |
    When I am on the "Music history" "feedback activity" page logged in as editingteacher
    And I navigate to "Questions" in current page administration
    Then the "region-main" "region" should meet accessibility standards with "best-practice" extra tests
    And I am on the "Music history" "feedback activity" page logged in as student
    And I follow "Answer the questions"
    And the "region-main" "region" should meet accessibility standards with "best-practice" extra tests
