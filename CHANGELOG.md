# Litespeed Cache Changelog

## 1.2.3 - 2018-09-15
### Fixed
- Fix release. Fix PHP 7.2 incompatibility with sizeof on non-iterable arrays or objects.

## 1.2.2 - 2018-09-15
### Fixed
- Fix PHP 7.2 incompatibility with sizeof on non-iterable arrays or objects.

## 1.2.1 - 2018-05-10
### Fixed
- Change to URL over URI for getting the correct PURGE location, as the homepage URI is `__home__` and not `\`.

## 1.2.0 - 2018-05-10
### Added
- Added logging for pre-and-post-PURGE to allow for better debugging.

### Fixed
- Tidied up the coding standards.

## 1.1.7 - 2018-05-10
### Fixed
- Stop duplicate requests being fired when clearing per-URL.

## 1.1.6 - 2018-05-09
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
