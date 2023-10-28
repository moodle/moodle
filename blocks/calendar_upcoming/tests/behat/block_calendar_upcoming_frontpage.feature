@block @block_calendar_upcoming
Feature: View a site event on the frontpage
  In order to view a site event
  As a teacher
  I can view the event in the upcoming events block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "blocks" exist:
      | blockname         | contextlevel | reference | pagetypepattern | defaultregion |
      | calendar_upcoming | System       | 1         | site-index      | side-pre      |

  @javascript
  Scenario: View a site event in the upcoming events block on the frontpage
    Given I log in as "admin"
    And I create a calendar event with form data:
      | id_eventtype | Site |
      | id_name | My Site Event |
    And I log out
    When I log in as "teacher1"
    And I am on site homepage
    Then I should see "My Site Event" in the "Upcoming events" "block"
