Feature: Notify user about current temperature

  Scenario Outline: If temperature cycle check limit is not reached then properly notify a user about temperature
    Given Temperature have been checked "<temperatureHesBeenCheckedTimes>" times
    And Current temperature is "<currentTemperature>"
    When Execute temperature check process
    Then Temperature has been checked for city "Thessaloniki"
    And SMS "<expectedMessageText>" have been sent to "+30 6911111111"
    Examples:
      | temperatureHesBeenCheckedTimes | currentTemperature | expectedMessageText                                          |
      | 0                              |  21                | Zoran, temperature is more than 20C. It is 21C.              |
      | 0                              |  15                | Zoran, temperature is less than or equal to 20C. It is 15C.  |
      | 9                              |  25                | Zoran, temperature is more than 20C. It is 25C.              |
      | 9                              |  20                | Zoran, temperature is less than or equal to 20C. It is 20C.  |

  Scenario Outline: If temperature cycle check is reached then don't notify a user
    Given Temperature have been checked "<temperatureHesBeenCheckedTimes>" times
    When Execute temperature check process
    Then Temperature has not been checked
    And SMS have not been sent
    Examples:
      | temperatureHesBeenCheckedTimes |
      | 10                             |
      | 11                             |
      | 20                             |

  Scenario Outline: Temperature cycle check should be paused if cycle check is not the first one
    Given Temperature have been checked "<temperatureHesBeenCheckedTimes>" times
    When Execute temperature check process
    Then Execution pausing "<executionHasBeenPaused>" "<expectedPauseSec>"
    Examples:
      | temperatureHesBeenCheckedTimes | executionHasBeenPaused | expectedPauseSec |
      | 0                              | no                     |                  |
      | 1                              | yes                    | 600              |
      | 2                              | yes                    | 600              |
      | 9                              | yes                    | 600              |