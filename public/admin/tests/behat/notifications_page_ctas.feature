@core @core_admin
Feature: From Moodle CTA cards on the notifications page
  In order to control the promotional content shown on the notifications page
  As an admin
  I need the "From Moodle" CTA cards to render, and be individually hideable via config

  Background:
    Given I log in as "admin"

  Scenario: All four "From Moodle" CTA cards are shown by default
    When I navigate to "Notifications" in site administration
    Then I should see "From Moodle"
    And I should see "Moodle Marketplace"
    And I should see "MoodleCloud"
    And I should see "Certified Moodle Partners"
    And I should see "Help decide what we build next"

  Scenario: Disabling a CTA card by key hides only that card
    Given the following config values are set as admin:
      | disablenotificationctas | ["moodlecloud"] |
    When I navigate to "Notifications" in site administration
    Then I should see "Moodle Marketplace"
    And I should not see "MoodleCloud"
    And I should see "Certified Moodle Partners"
    And I should see "Help decide what we build next"

  Scenario: Disabling all CTA cards by key hides the entire "From Moodle" section
    Given the following config values are set as admin:
      | disablenotificationctas | ["marketplace", "moodlecloud", "partners", "feedback"] |
    When I navigate to "Notifications" in site administration
    Then I should not see "From Moodle"

  Scenario: The feedback card is hidden once user feedback is already enabled
    Given the following config values are set as admin:
      | enableuserfeedback | 1 |
    When I navigate to "Notifications" in site administration
    Then I should see "From Moodle"
    And I should see "Moodle Marketplace"
    And I should not see "Help decide what we build next"
