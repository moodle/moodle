Feature: Invalid texting because background is not compatible
  Background:
    Given the following "course" exists:
      | fullname         | Course 1    |
      | shortname        | C1          |
      | category         | 0           |
      | numsections      | 3           |
    And the following "activities" exist:
      | activity | name              | intro                       | course  | idnumber | section | visible |
      | assign   | Activity sample 1 | Test assignment description | C1      | sample1  | 1       | 1       |
      | assign   | Activity sample 2 | Test assignment description | C1      | sample2  | 1       | 0       |
