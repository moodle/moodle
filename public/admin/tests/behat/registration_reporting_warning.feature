@core @core_admin @core_hub
Feature: Registered site reporting warning
  In order to know that my registered site has stopped reporting to moodle.org
  As an admin
  I need to see a persistent warning on the notifications page when reporting has paused

  Background:
    Given the site is registered with moodle.org
    And I log in as "admin"

  Scenario: A registered site reporting normally shows no warning
    When I navigate to "Notifications" in site administration
    Then I should not see "automatic registration reporting is paused"

  Scenario: A registered site with new registration fields pending shows no warning here
    # \core\hub\registration::registration_reminder() already redirects the admin away from this page
    # to the registration form under this exact same condition, so this cause is not covered here.
    Given the following config values are set as admin:
      | site_regupdateversion | 0 | hub |
    When I navigate to "Notifications" in site administration
    Then I should not see "automatic registration reporting is paused"

  Scenario: A site that is not publicly accessible shows no paused-reporting warning
    Given the following config values are set as admin:
      | site_is_public | 0 |
    And the scheduled task "\core\task\registration_cron_task" is disabled
    When I navigate to "Notifications" in site administration
    Then I should not see "automatic registration reporting is paused"

  Scenario: A registered site with the registration cron task disabled shows a warning
    Given the scheduled task "\core\task\registration_cron_task" is disabled
    When I navigate to "Notifications" in site administration
    Then I should see "automatic registration reporting is paused because the 'Site registration' scheduled task is disabled"
