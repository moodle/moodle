@core @core_cache
Feature: Display usage information for cache
  In order to investigate performance problems with caching
  As an administrator
  I need to be able to monitor the size of items in the cache

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |

  Scenario: Cache performance info displays
    When I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Plugins > Caching > Cache usage" in site administration

    # Check one row of the summary table. The actual total is currently 3.6MB so it's likely to
    # continue to be in the MB range.
    Then "default_application" row "Plugin" column of "usage_summary" table should contain "cachestore_file"
    And "default_application" row "Estimated total" column of "usage_summary" table should contain "MB"
    And "default_application" row "Actual usage (if known)" column of "usage_summary" table should contain "MB"

    # And one row of the main table. The totals are fixed to use the MB unit.
    And "core/config" row "Store name" column of "usage_main" table should contain "default_application"
    And "core/config" row "Plugin" column of "usage_main" table should contain "cachestore_file"
    And "core/config" row "Estimated total" column of "usage_main" table should contain "MB"

  Scenario: Sample option works
    When I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Plugins > Caching > Cache usage" in site administration
    And I set the field "samples" to "1000"
    And I press "Update"

    Then the field "samples" matches value "1000"
    And "usage_summary" "table" should exist
    And "usage_main" "table" should exist
