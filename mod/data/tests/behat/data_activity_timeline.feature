@mod @mod_data
Feature: Students can view upcoming data activities in the timeline block
  In order for student to see upcoming data activities in timeline block
  As a teacher
  I should be able to set the availability dates of data activities

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |

  @javascript
  Scenario Outline: Student can view upcoming data activities in the timeline block
    Given the following "activities" exist:
      | activity | course | name       | id_timeavailablefrom_enabled | timeavailablefrom | id_timeavailableto_enabled | timeavailableto     |
      | data     | C1     | DB Past    | 1                            | <pastfrom>        | 1                          | <pastto>            |
      | data     | C1     | DB Future  | 1                            | <futurefrom>      | 1                          | <futureto>          |
      | data     | C1     | DB No date | 0                            |                   | 0                          |                     |
    # Confirm that student can see future but not past db activity in the timeline block
    When I log in as "student 1"
    Then I should not see "DB Past" in the "Timeline" "block"
    # Also confirm that student can't see db activity where availability is disabled
    And I should not see "DB No Date" in the "Timeline" "block"
    And I should see "DB Future" in the "Timeline" "block"
    # Confirm link works and redirects to db activity
    And I click on "DB Future" "link" in the "Timeline" "block"
    And the activity date in "DB Future" should contain "Opens:"
    And the activity date in "DB Future" should contain "<futurefrom>%A, %d %B %Y, %I:%M##"
    And the activity date in "DB Future" should contain "Closes:"
    And the activity date in "DB Future" should contain "<futureto>%A, %d %B %Y, %I:%M##"

    Examples:
      | pastfrom         | pastto                | futurefrom           | futureto                  |
      | ##1 month ago##  | ##yesterday##         | ##tomorrow##         | ##tomorrow +1day##        |
      | ##yesterday##    | ##yesterday +3hours## | ##tomorrow noon##    | ##tomorrow noon +3hours## |
      | ##6 months ago## | ##1 week ago##        | ##tomorrow +5days##  | ##tomorrow +6days##       |
