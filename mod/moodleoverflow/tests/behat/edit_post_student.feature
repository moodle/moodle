@mod @mod_moodleoverflow
Feature: Students can edit or delete their moodleoverflow posts within a set time limit
  In order to refine moodleoverflow posts
  As a user
  I need to edit or delete my moodleoverflow posts within a certain period of time after posting

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity       | name                     | intro                            | course  | idnumber       |
      | moodleoverflow | Test moodleoverflow name | Test moodleoverflow description  | C1      | moodleoverflow |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test moodleoverflow name" moodleoverflow with:
      | Subject | Moodleoverflow post subject |
      | Message | This is the body |

  Scenario: Edit moodleoverflow post
    Given I follow "Moodleoverflow post subject"
    And I follow "Edit"
    When I set the following fields to these values:
      | Subject | Edited post subject |
      | Message | Edited post body |
    And I press "Save changes"
    And I wait to be redirected
    Then I should see "Edited post subject"
    And I should see "Edited post body"

  Scenario: Delete moodleoverflow post
    Given I follow "Moodleoverflow post subject"
    When I follow "Delete"
    And I press "Continue"
    Then I should not see "Moodleoverflow post subject"
