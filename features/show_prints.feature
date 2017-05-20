Feature: Show prints
  In order to know which prints I have
  As a user
  I need to be able to view my prints

  Background:
    Given there are users:
      | username  | apiToken     |
      | ultimaker | my-api-token |
    And there are prints:
      | user      | publicId | title              | description           | image      |
      | ultimaker | 12345678 | My first print     | How nice it is.       | image1.png |
      | ultimaker | 14725836 | And another print  | Which is just a test. | image2.png |
      | ultimaker | 74185296 | Look at this print | Now look back to me.  | image3.png |

  Scenario: Viewing my prints
    Given I am authenticated as user "ultimaker"
    When I view my prints
    Then I should get the prints returned
