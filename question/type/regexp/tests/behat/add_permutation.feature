@qtype @qtype_regexp
Feature: Test creating Regexp questions with the 'permutation' feature
  As a teacher
  In order to test my students
  I need to be able to create a Regexp question with the 'permutation' feature

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  @javascript
  Scenario: Create a Regexp question with the permutation feature French flag
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Regular expression short answer" question filling the form with:
      | Question name        | regexp-001                    |
      | Question text        | What are the colours of the French flag (in any order) |
      | Default mark         | 1                                         |
      | Case sensitivity     | Yes, case must match                      |
      | id_answer_0          | it's blue, white and red                  |
      | id_fraction_0        | 100%                                      |
      | id_feedback_0        | OK.                                       |
      | id_answer_1          | it's [[_blue_, _white_(,\| and) _red_]]   |
      | id_fraction_1        | 100%                                      |
    Then I should see "regexp-001"
    And I choose "Edit question" action for "regexp-001" in the question bank
    And I press "id_updatebutton"
    And I click on "Show/Hide alternate answers" "link"
    And I click on "id_showalternate" "button"
    Then I should see "Answer 2 (100%)"
    And I should see "it's blue, white, red"
    And I should see "it's blue, white and red"
    And I should see "it's blue, red, white"
    And I should see "it's blue, red and white"
    And I should see "it's white, red, blue"
    And I should see "it's white, red and blue"
    And I should see "it's white, blue, red"
    And I should see "it's white, blue and red"
    And I should see "it's red, blue, white"
    And I should see "it's red, blue and white"
    And I should see "it's red, white, blue"
    And I should see "it's red, white and blue"
    And I press "id_submitbutton"
    Then I should see "regexp-001"

  @javascript
  Scenario: Create a Regexp question with the permutation feature Proverb
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Regular expression short answer" question filling the form with:
      | Question name        | regexp-001                    |
      | Question text        | Quote the English proverb that is an encouragement to hard, diligent work. |
      | Default mark         | 1                                         |
      | Case sensitivity     | Yes, case must match                      |
      | id_answer_0          | Early to bed and early to rise makes a man healthy, wealthy and wise |
      | id_fraction_0        | 100%                                      |
      | id_feedback_0        | OK.                                       |
      | id_answer_1          | Early to [[_bed_ and early to _rise_]], makes a man [[_healthy_, _wealthy_ and _wise_]] |
      | id_fraction_1        | 100%                                      |
    Then I should see "regexp-001"
    And I choose "Edit question" action for "regexp-001" in the question bank
    And I press "id_updatebutton"
    And I click on "Show/Hide alternate answers" "link"
    And I click on "id_showalternate" "button"
    Then I should see "Answer 2 (100%)"
    And I should see "Early to bed and early to rise, makes a man healthy, wealthy and wise"
    And I should see "Early to bed and early to rise, makes a man healthy, wise and wealthy"
    And I should see "Early to bed and early to rise, makes a man wealthy, wise and healthy"
    And I should see "Early to bed and early to rise, makes a man wealthy, healthy and wise"
    And I should see "Early to bed and early to rise, makes a man wise, healthy and wealthy"
    And I should see "Early to bed and early to rise, makes a man wise, wealthy and healthy"
    And I should see "Early to rise and early to bed, makes a man healthy, wealthy and wise"
    And I should see "Early to rise and early to bed, makes a man healthy, wise and wealthy"
    And I should see "Early to rise and early to bed, makes a man wealthy, wise and healthy"
    And I should see "Early to rise and early to bed, makes a man wealthy, healthy and wise"
    And I should see "Early to rise and early to bed, makes a man wise, healthy and wealthy"
    And I should see "Early to rise and early to bed, makes a man wise, wealthy and healthy"
    And I press "id_submitbutton"
    Then I should see "regexp-001"
