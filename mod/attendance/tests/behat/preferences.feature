@mod @mod_attendance @mod_attendance_preferences
Feature: Teachers can't change status variables to have empty acronyms or descriptions
  In order to update status variables
  As a teacher
  I need to see an error notice below each acronym / description that I try to set to be empty

  Background:
    Given the following "courses" exist:
      | fullname | shortname | summary                             | category | timecreated   | timemodified  |
      | Course 1 | C1        | Prove the attendance activity works | 0        | ##yesterday## | ##yesterday## |
    And the following "users" exist:
      | username    | firstname | lastname |
      | student1    | Sam       | Student  |
      | teacher1    | Teacher   | One      |
    And the following "course enrolments" exist:
      | course | user     | role           | timestart     |
      | C1     | student1 | student        | ##yesterday## |
      | C1     | teacher1 | editingteacher | ##yesterday## |

    And the following "activity" exists:
      | activity | attendance            |
      | course   | C1                    |
      | idnumber | 00001                 |
      | name     | Attendancepreftest    |

  @javascript
  Scenario: Teachers can add status variables
    Given I am on the "Attendancepreftest" "mod_attendance > View" page logged in as "teacher1"
    And I click on "More" "link" in the ".secondary-navigation" "css_element"
    And I select "Status set" from secondary navigation
    # Set the second status acronym to be empty
    And I set the field with xpath "//*[@id='statusrow2']/td[2]/input" to ""
    # Set the second status description to be empty
    And I set the field with xpath "//*[@id='statusrow2']/td[3]/input" to ""
    # Set the second status grade to be empty
    And I set the field with xpath "//*[@id='statusrow2']/td[4]/input" to ""
    And I click on "Update" "button" in the "#preferencesform" "css_element"
    And I should see "Empty acronyms are not allowed" in the "//*[@id='statusrow2']/td[2]/p" "xpath_element"
    And I should see "Empty descriptions are not allowed" in the "//*[@id='statusrow2']/td[3]/p" "xpath_element"
    And I click on "Update" "button" in the "#preferencesform" "css_element"

    # Set the first status acronym to be empty
    And I set the field with xpath "//*[@id='statusrow1']/td[2]/input" to ""
    # Set the first status description to be empty
    And I set the field with xpath "//*[@id='statusrow1']/td[3]/input" to ""
    # Set the first status grade to be empty
    And I set the field with xpath "//*[@id='statusrow1']/td[4]/input" to ""
    # Set the third status acronym to be empty
    And I set the field with xpath "//*[@id='statusrow3']/td[2]/input" to ""
    # Set the third status description to be empty
    And I set the field with xpath "//*[@id='statusrow3']/td[3]/input" to ""
    # Set the third status grade to be empty
    And I set the field with xpath "//*[@id='statusrow3']/td[4]/input" to ""
    When I click on "Update" "button" in the "#preferencesform" "css_element"
    Then I should see "Empty acronyms are not allowed" in the "//*[@id='statusrow1']/td[2]/p" "xpath_element"
    And I should see "Empty descriptions are not allowed" in the "//*[@id='statusrow1']/td[3]/p" "xpath_element"
    And I should see "Empty acronyms are not allowed" in the "//*[@id='statusrow3']/td[2]/p" "xpath_element"
    And I should see "Empty descriptions are not allowed" in the "//*[@id='statusrow3']/td[3]/p" "xpath_element"
