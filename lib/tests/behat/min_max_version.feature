@core
Feature: Check for minimum or maximimum version of Moodle
    In order adapt acceptance tests for different versions of Moodle
    As a developer
    I should be able to skip tests according to the Moodle version present on a site

  Scenario: Minimum version too low
    Given the site is running Moodle version 99.0 or higher
    # The following steps should not be executed. If they are, the test will fail.
    When I log in as "admin"
    Then I should not see "Home"

  Scenario: Maximum version too high
    Given the site is running Moodle version 3.0 or lower
    # The following steps should not be executed. If they are, the test will fail.
    When I log in as "admin"
    Then I should not see "Home"

  Scenario: Minimum version OK
    Given the site is running Moodle version 3.0 or higher
    When I log in as "admin"
    Then I should see "Home"

  Scenario: Maximum version OK
    Given the site is running Moodle version 99.0 or lower
    When I log in as "admin"
    Then I should see "Home"
