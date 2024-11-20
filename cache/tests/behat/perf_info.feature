@core @core_cache
Feature: Display cache information in performance info
  In order to investigate performance problems with caching
  As an administrator
  I need to be able to see cache information in perfinfo

  Background:
    Given the following config values are set as admin:
      | perfdebug | 15 |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |

  Scenario: Cache performance info displays
    When I am on the "C1" "Course" page logged in as "admin"
    # Confirm that the first cache info table is visible by checking an arbitrary row.
    Then I should see "default_application" in the "core/databasemeta" "table_row"
    # Don't specify the exact size as it may vary.
    And I should see "KB" in the "core/databasemeta" "table_row"
    # Confirm that the second cache info table is visible.
    And I should see "default_application" in the "cachestore_file" "table_row"
