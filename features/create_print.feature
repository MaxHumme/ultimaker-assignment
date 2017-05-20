Feature: Create a print
  In order add a print to my prints
  As a user
  I need to be able to create a print

  Background:
    Given there are users:
      | username  | apiToken     |
      | ultimaker | my-api-token |

  Scenario: Creating a print
    Given I am authenticated as user "ultimaker"
    When I create a print with title "Print it baby!", description "Just do it." and image "image4.png"
    Then I should get the new print returned
    And the print should be returned when I view my prints
