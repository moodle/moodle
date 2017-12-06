@mod @mod_dataform @dataformview @dataformviewsubmissionbuttons
Feature: View submission buttons

    @javascript
    Scenario: View submission buttons
        Given I run dataform scenario "view submission buttons" with:
            | viewtype  | actor     |
            | aligned   | student1  |
            | csv       | student1  |
            | grid      | student1  |
            | interval  | student1  |
            | rss       | student1  |
            | tabular   | student1  |
