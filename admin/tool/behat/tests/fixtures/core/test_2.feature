@behat_test @test2
Feature: Test feature 2
  In order to test behat.yml in phpunit
  As an user
  I need to be able to include this feature

  Scenario: I should not be included in normal execution.
    Given I pause scenario execution
