# Change Log

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [1.1.3](https://github.com/Datamedrix/laravel-intl/compare/v1.1.2...v1.1.3) (2019-09-09)

### Chore

* **\*:** Dependencies updated to support laravel 6.0.


## [1.1.2](https://github.com/Datamedrix/laravel-intl/compare/v1.1.1...v1.1.2) (2019-05-28)


### Bug Fixes

* **LocaleManager:** Use application defined default locale when the system locale is set to 'C'! ([e35ecc5](https://github.com/Datamedrix/laravel-intl/commit/e35ecc5))



## [1.1.1](https://github.com/Datamedrix/laravel-intl/compare/v1.1.0...v1.1.1) (2019-05-16)


### Bug Fixes

* **composer:** Move helper file autoload definition to the correct section! ([750ced1](https://github.com/Datamedrix/laravel-intl/commit/750ced1))


# [1.1.0](https://github.com/Datamedrix/laravel-intl/compare/v1.0.0...v1.1.0) (2019-05-16)


### Features

* **helpers:** Add a lot of helper functions. ([18fa56f](https://github.com/Datamedrix/laravel-intl/commit/18fa56f))
* **service-provider:** Register default configuration and add them to the publishes list. ([e37122a](https://github.com/Datamedrix/laravel-intl/commit/e37122a))

# 1.0.0 (2019-05-15)

### Features

* **foundation:** Implement basic class to store common locale information. ([70e0d8e](https://github.com/Datamedrix/laravel-intl/commit/70e0d8e))
* **foundation:** Implement locale manager to set and get a locale to the laravel application container. ([e5a1c24](https://github.com/Datamedrix/laravel-intl/commit/e5a1c24))
* **Leo:** Add a dict.leo.org inspired helper class to translate and format date, datetime and numbers. ([8af7f68](https://github.com/Datamedrix/laravel-intl/commit/8af7f68))
* **Locale:** Add formatting information to the foundation class. ([13e6869](https://github.com/Datamedrix/laravel-intl/commit/13e6869))
* **Locale:** Add locale settings and get them from the config or system. ([2303185](https://github.com/Datamedrix/laravel-intl/commit/2303185))
* **LocaleManager:** Implement createLocale method to create a new locale from an ISO string and use the settings from the config. ([b1de032](https://github.com/Datamedrix/laravel-intl/commit/b1de032))
* **Middleware:** Add a http middleware to set the locale from the session. ([9cf7a8b](https://github.com/Datamedrix/laravel-intl/commit/9cf7a8b))
