# Upgrade guide from 1.x to 2.x

## PHP version

Version 2.x requires PHP 7.4 or higher.

## Namespace

Library namespace has been changed from `XLSXExporter` to `Eclipxe\XlsxExporter`.

## Types

- The library is now strict typed.
- Collections now uses variable-length argument list.

## Exceptions

- All exceptions are explicit and implement `Eclipxe\XlsxExporter\Exceptions\XlsxException` interface.
- Previous generic exception `XLSXException` has been removed.

## Custom autoloader

The custom autoloader has been removed, use `composer`.
