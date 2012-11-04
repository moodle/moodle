@tool_behat
Feature: Set up the testing environment
  In order to execute automated acceptance tests
  As a moodle developer
  I need to use the test environment instead of the regular environment

  Scenario: Accessing the site
    When I am on homepage
    Then I should see "PHPUnit test site"
