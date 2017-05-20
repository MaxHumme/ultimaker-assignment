Feature: Show a print
  In order know what a print looks like
  As a user
  I need to be able to view that print

  Background:
    Given there are users:
      | username  | apiToken     |
      | ultimaker | my-api-token |
    And there are prints:
      | user      | publicId | title          | description     | image      |
      | ultimaker | 12345678 | My first print | How nice it is. | image1.png |

  Scenario: Viewing a print
    Given I am authenticated as user "ultimaker"
    When I view the print with publicId "12345678"
    Then I should get the print returned
