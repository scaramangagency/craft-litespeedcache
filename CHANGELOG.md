# Solspace Freeform Changelog

## 1.1.6 - 2018-05-02
### Updated
- Update notes for LSCache location.

## 1.1.5 - 2018-05-02
### Updated
- Add note to explain the use of the Clear Litespeed Cache button.

## 1.1.4 - 2018-05-02
### Fixed
- Fix error on page save for global PURGE.

## 1.1.3 - 2018-05-02
### Fixed
- Fix `Clear caches per URL?` being permanently switched on. Fix error where plugin would not save settings.

## 1.1.2 - 2018-04-12
### Fixed
- Fix composer version number

## 1.1.1 - 2018-04-12
### Fixed
- Fix required craftcms/cms version


## 1.1.0 - 2018-04-11
### Added
- Added _Clear Caches by URL_ that will only PURGE caches based on what Craft thinks needs clearing when you save a page. If Craft thinks that template cache should be cleared, we will also fire a PURGE request to that page.
