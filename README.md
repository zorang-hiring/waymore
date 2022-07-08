## TASK
Task https://wayforms.routee.net/p94986542281/

## SYSTEM REQUIREMENTS

1. composer locally installed

## INSTALLATION

1. `composer install`

2. Create `.env` file

## EXECUTION

1. `docker build -t my-php-app .`
2. `docker run -it --rm --name my-running-app my-php-app`

To run without docker:
`php src/main.php`

## TESTS

Acceptance tests:
`vendor/bin/phpunit`

Unit Tests
`vendor/bin/behat`

Note: Test coverage is maximal, tests code coverage is probably ~100%.
Unfortunately I can't provide a report about it at the moment.