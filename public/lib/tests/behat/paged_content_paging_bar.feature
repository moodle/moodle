@core @javascript
Feature: Paged content paging bar aria labels
  In order to provide correct navigation labels
  As a user
  I need pagination aria labels to update when changing pages

  Background:
    Given I log in as "admin"
    And I am on fixture page "/lib/tests/behat/fixtures/paged_content_paging_bar_testpage.php"
    And I wait until "//nav[@data-region='paging-bar']//a[normalize-space(.)='1']" "xpath_element" exists
    And I wait until "//nav[@data-region='paging-bar']//a[normalize-space(.)='2']" "xpath_element" exists

  Scenario: Numbered page links should have updated aria labels when paging
    Given the "aria-label" attribute of "//nav[@data-region='paging-bar']//a[normalize-space(.)='1']" "xpath_element" should contain "Current page, page 1"
    And the "aria-label" attribute of "//nav[@data-region='paging-bar']//a[normalize-space(.)='2']" "xpath_element" should contain "Go to page 2"
    When I click on "2" "link" in the "//nav[@data-region='paging-bar']" "xpath_element"
    Then the "aria-label" attribute of "//nav[@data-region='paging-bar']//a[normalize-space(.)='1']" "xpath_element" should contain "Go to page 1"
    And the "aria-label" attribute of "//nav[@data-region='paging-bar']//a[normalize-space(.)='2']" "xpath_element" should contain "Current page, page 2"
