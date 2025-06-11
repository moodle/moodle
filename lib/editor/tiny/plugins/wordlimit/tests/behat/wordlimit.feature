@editor_tiny @tiny @tiny_wordlimit
Feature: TinyMCE wordlimit indicator in the statusbar
  To add words count to Atto editor, I need to use the wordcount button.

  Background:
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript
  Scenario: Do not display wordlimit in onlinesubmissions if no wordlimit is set
    Given the following "activity" exists:
      | activity                                      | assign                  |
      | course                                        | C1                      |
      | name                                          | Test assignment name    |
      | intro                                         | Submit your online text |
      | submissiondrafts                              | 0                       |
      | assignsubmission_onlinetext_enabled           | 1                       |
      | assignsubmission_onlinetext_wordlimit_enabled | 0                       |
      | assignsubmission_onlinetext_wordlimit         | 21                      |
      | assignsubmission_file_enabled                 | 0                       |
    And I am on the "Test assignment name" Activity page logged in as student1
    And I press "Add submission"
    And I wait until the page is ready
    Given I set the field "Online text" to "<p>this are exactly eight out of 21 words</p>"
    Then I should not see "Maximum word limit: 21" in the ".tox-statusbar" "css_element"

  @javascript
  Scenario: Display wordcount in onlinesubmissions if wordlimit is set
    Given the following "activity" exists:
      | activity                                      | assign                  |
      | course                                        | C1                      |
      | name                                          | Test assignment name    |
      | intro                                         | Submit your online text |
      | submissiondrafts                              | 0                       |
      | assignsubmission_onlinetext_enabled           | 1                       |
      | assignsubmission_onlinetext_wordlimit_enabled | 1                       |
      | assignsubmission_onlinetext_wordlimit         | 21                      |
      | assignsubmission_file_enabled                 | 0                       |
    And I am on the "Test assignment name" Activity page logged in as student1
    And I press "Add submission"
    And I wait until the page is ready
    Given I set the field "Online text" to "<p>this are exactly eight out of 21 words</p>"
    Then I should see "Maximum word limit: 21" in the ".tox-statusbar" "css_element"
    Given I set the field "Online text" to multiline:
    """
    <p>With this sentence the wordlimit of 21 is almost reached and I should see a visual indicator.</p>
    """
    Then I should see "Maximum word limit: 21" in the ".tox-statusbar" "css_element"

  @javascript
  Scenario: Display wordlimit in multiple essays inside quiz if wordlimit is sometimes set
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype | name   | questiontext                  | template | maxwordenabled | maxwordlimit |
      | Test questions   | essay | essay1 | Write not more than 111 words | editor   | 1              | 111          |
      | Test questions   | essay | essay2 | Write as much as you want Nr1 | editor   | 0              |              |
      | Test questions   | essay | essay3 | Write not more than 333 words | editor   | 1              | 333          |
      | Test questions   | essay | essay4 | Write as much as you want Nr2 | editor   | 0              |              |
      | Test questions   | essay | essay5 | Write as much as you want Nr3 | editor   | 0              |              |
      | Test questions   | essay | essay6 | Write not more than 666 words | editor   | 1              | 666          |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | essay1   | 1    |
      | essay2   | 1    |
      | essay3   | 1    |
      | essay4   | 1    |
      | essay5   | 2    |
      | essay6   | 2    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Section 1 | 1         | 0       |
      | Section 2 | 5         | 0       |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student1"
    And I press "Attempt quiz"
    And I wait until the page is ready
    Then I should see "Maximum word limit: 111"
    Then I should see "Maximum word limit: 333"
    And I click on "Question 5" "link"
    And I wait until the page is ready
    Then I should see "Maximum word limit: 666"

  @javascript
  Scenario: Display maximum wordlimit in essay question preview
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype | name   | questiontext                   | template | maxwordenabled | maxwordlimit |
      | Test questions   | essay | essay1 | Write not more than 1337 words | editor   | 1              | 1337         |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | essay1   | 1    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Section 1 | 1         | 0       |
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I click on "Edit question essay1" "link"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    Then I should see "Maximum word limit: 1337"
