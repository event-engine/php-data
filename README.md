# EventEngine\Data

Generate Immutable Objects with ease!

![Value Object Template vo_string](https://event-engine.io/api/img/vo_string.gif)

## Installation

```bash
composer require event-engine/php-data
```

## Versions
- 1.x uses method return type hints to detect ImmutableRecord property types
    - Use this version in PHP 7.2 - PHP 7.3 environments
- 2.x makes use of PHP 7.4 property type hints 
    - Use this version in  >= PHP 7.4 environments
    
## PHPStorm Templates

The `EventEngine\Data` package contains a set of live templates specifically designed to work together with the `EventEngine\Data\ImmutableRecord`. 

You can import the templates by following official [PHPStorm instructions](https://www.jetbrains.com/help/phpstorm/sharing-live-templates.html).


Please find the `settings.zip` [here](https://github.com/event-engine/php-data/blob/master/.env/PHPStorm/settings.zip).

## Usage

Usage is described in the [documentation](https://event-engine.io/api/immutable_state.html)

## No Tests?

Looks strange, right? A stable open source package without a single test can only be a joke ... 
You're right! The situation isn't ideal. However, this is a low level package used by [event-engine/php-json-schema](https://github.com/event-engine/php-json-schema).
The tests included there also cover the implementation of this package. Anyway, a pull request that adds some tests is always welcome!
