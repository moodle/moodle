@mod @mod_lesson
Feature: In Dashboard, a student can see their current status on all lessons with an upcoming due date
  In order to know my status on a lesson
  As a student
  I need to see it in Dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name             | intro                   | deadline   | retake | course | idnumber |
      | lesson   | Test lesson name | Test lesson description | 1893481200 | 1      | C1     | lesson1  |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

  Scenario: A completed lesson with only questions that allows multiple attempts
    Given I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | Cat is an amphibian |
      | id_answer_editor_0 | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I select "Question" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    When I am on homepage
    Then I should see "You have lessons that are due"
    And I should see "Completed, You can re-attempt this lesson"

  Scenario: A completed lesson with only questions that does not allow multiple attempts
    Given  I follow "Test lesson name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Re-takes allowed | 0 |
    And I press "Save and display"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | Cat is an amphibian |
      | id_answer_editor_0 | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I select "Question" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    When I am on homepage
    Then I should not see "You have lessons that are due"

  Scenario: A completed lesson with only content pages that allows multiple attempts
    Given I follow "Test lesson name"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
    And I set the following fields to these values:
      | Page title | Second page name |
      | Page contents | Second page contents |
      | id_answer_editor_0 | Previous page |
      | id_jumpto_0 | Previous page |
      | id_answer_editor_1 | End of lesson |
      | id_jumpto_1 | End of lesson |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "End of lesson"
    When I am on homepage
    Then I should see "You have lessons that are due"
    And I should see "Completed, You can re-attempt this lesson"

  Scenario: A completed lesson with only content pages that does not allow multiple attempts
    Given I follow "Test lesson name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Re-takes allowed | 0 |
    And I press "Save and display"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
    And I set the following fields to these values:
      | Page title | Second page name |
      | Page contents | Second page contents |
      | id_answer_editor_0 | Previous page |
      | id_jumpto_0 | Previous page |
      | id_answer_editor_1 | End of lesson |
      | id_jumpto_1 | End of lesson |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "End of lesson"
    When I am on homepage
    Then I should not see "You have lessons that are due"

  Scenario: An incomplete lesson with only questions.
    Given I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | Cat is an amphibian |
      | id_answer_editor_0 | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I select "Question" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    When I am on homepage
    Then I should see "You have lessons that are due"
    And I should see "Lesson has been started, but not yet completed"

  Scenario: An incomplete lesson with only content pages.
    Given I follow "Test lesson name"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
    And I set the following fields to these values:
      | Page title | Second page name |
      | Page contents | Second page contents |
      | id_answer_editor_0 | Previous page |
      | id_jumpto_0 | Previous page |
      | id_answer_editor_1 | End of lesson |
      | id_jumpto_1 | End of lesson |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    When I am on homepage
    Then I should see "You have lessons that are due"
    And I should see "Lesson has been started, but not yet completed"

  Scenario: A lesson with only questions that has not been started.
    Given I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | Cat is an amphibian |
      | id_answer_editor_0 | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I select "Question" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out
    When I log in as "student1"
    Then I should see "You have lessons that are due"
    And I should see "No attempts have been made on this lesson"

  Scenario: A lesson with only content pages that has not been started.
    Given I follow "Test lesson name"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
    And I set the following fields to these values:
      | Page title | Second page name |
      | Page contents | Second page contents |
      | id_answer_editor_0 | Previous page |
      | id_jumpto_0 | Previous page |
      | id_answer_editor_1 | End of lesson |
      | id_jumpto_1 | End of lesson |
    And I press "Save page"
    And I log out
    When I log in as "student1"
    Then I should see "You have lessons that are due"
    And I should see "No attempts have been made on this lesson"

  Scenario: Viewing the status for multiple lessons in multiple courses
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 2 | C2 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C2 | editingteacher |
      | student1 | C2 | student |
    And the following "activities" exist:
      | activity | name               | intro                   | deadline   | retake | course | idnumber |
      | lesson   | Test lesson name 2 | Test lesson description | 1893481200 | 1      | C1     | lesson1  |
      | lesson   | Test lesson name 3 | Test lesson description | 1893481200 | 1      | C2     | lesson1  |
    And I turn editing mode off
    And I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question |
      | Page contents | D035 M00d13 r0x0rz j00 b0x0rs? |
      | id_answer_editor_0 | True |
      | id_answer_editor_1 | False |
    And I press "Save page"
    And I follow "Course 1"
    And I follow "Test lesson name 2"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question |
      | Page contents | D035 M00d13 r0x0rz j00 b0x0rs? |
      | id_answer_editor_0 | True |
      | id_answer_editor_1 | False |
    And I press "Save page"
    And I am on homepage
    And I follow "Course 2"
    And I follow "Test lesson name 3"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | D035 M00d13 r0x0rz j00 b0x0rs? |
      | id_answer_editor_0 | True |
      | id_answer_editor_1 | False |
    And I press "Save page"
    And I select "Question" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | D035 M00d13 r0x0rz j00 b0x0rs? |
      | id_answer_editor_0 | True |
      | id_answer_editor_1 | False |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "D035 M00d13 r0x0rz j00 b0x0rs?"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I am on homepage
    And I follow "Course 2"
    And I follow "Test lesson name 3"
    And I should see "D035 M00d13 r0x0rz j00 b0x0rs?"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    When I am on homepage
    Then I should see "You have lessons that are due" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' coursebox ' ) and contains(normalize-space(.), 'Course 1')]/div[contains( normalize-space(.), 'You have lessons that are due ' )]" "xpath_element"
    And I should see "You have lessons that are due" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' coursebox ' ) and contains(normalize-space(.), 'Course 2')]/div[contains( normalize-space(.), 'You have lessons that are due ' )]" "xpath_element"
    And I should see "Lesson has been started, but not yet completed" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' overview ' ) and descendant-or-self::a[.='Test lesson name 3']]" "xpath_element"
    And I should see "Completed, You can re-attempt this lesson" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' overview ' ) and descendant-or-self::a[.='Test lesson name']]" "xpath_element"
    And I should see "No attempts have been made on this lesson" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' overview ' ) and descendant-or-self::a[.='Test lesson name 2']]" "xpath_element"
