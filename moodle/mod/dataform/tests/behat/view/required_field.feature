@mod @mod_dataform @dataformview @dataformviewrequiredfield
Feature: View required field

    @javascript
    Scenario: View required field
        Given I run dataform scenario "view required field" with:
            | viewtype  | entrytemplate     |
            | aligned   | Entry template    |
            | csv       | Entry template    |
            | grid      | Entry template    |
            | interval  | Entry template    |
            | rss       | Item description  |
            | tabular   | Table design      |
