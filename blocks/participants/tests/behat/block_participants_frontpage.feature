@block @block_participants
Feature: People Block used on frontpage
  In order to view participants in a site
  As a admin
  I can add the people block to the front page

  Background:
    Given the following "users" exist:
      | username    | firstname | lastname | email            |
      | student1    | Sam       | Student  | student1@asd.com |
    And I log in as "admin"
    And I navigate to "Turn editing on" node in "Front page settings"
    And I add the "People" block
    And I log out

  Scenario: Admin can view site participants link
    When I log in as "admin"
    Then "People" "block" should exist
    And I should see "Participants" in the "People" "block"

  Scenario: Student can not follow participants link on frontpage
    When I log in as "student1"
    Then "People" "block" should not exist
