# Version 1.5.8
- Improve README file:
    - Install instructions.
    - Fix "bug" in example.
    - Expand example tiwh `use` sentences and better readability.
    - Fix badges.
- Fix build instructions on CONTRIBUTING.
- Use PHP 7.2, 7.3, 7.4 on Travis-CI.
- Fix date formats that where using a single `"y"` instead of double `"y"`.
- Fix `FORMAT_DATE_TIME7`: minutes where not displayed and milliseconds error.

# Version 1.5.7
- Fix bug when creating a zip archive using a temporary empty file, some php versions return
  `ZipArchive::ER_NOZIP (19)`, to fix it the library open the archive using flag `ZipArchive::OVERWRITE`.
  This bug could be caused by new libzip version 1.6.0.

# Version 1.5.6
- Add ResultProvider to use a DBAL\Result as the source of a Provider
- ResultProvider does not implement its own methods, it simply extends ProviderIterator
- RecordsetProvider does not use its own implementation, now it extends ProviderIterator
  since the DBAL\Ŗecordset objects implements the \IteratorAggregate interface
- ProviderInterface has a method count, make it extends \Countable
- Styles\Alignment write wrapText attribute if it was set, was only written if was true
- Remove docblock for casting \DBAL\Result->getIterator to \Iterator (fixed in upstream)

# Version 1.5.5
- Fix bug when the array in the ProviderArray has non consecutive keys
- Improve ProviderIterator, use count() method if the $iterator parameter is an instance of \Countable
- Set composer specific versions, remove scrutinizer/ocular from dev, install only on inside travis build
- Minor changes in typos, dockblocks and code style

# Version 1.5.4
- Fix bad namespace used on some tests files

# Version 1.5.3
- Enable php 7.1 due engineworks-dbal works again
- Drop coveralls
- Fix sensiolabs badge
- Improve README.md
- Add CoC and contributing guide lines
- Changed sensiolabs project code

# Version 1.5.2
- Fix code following recommendations from scrutinizer, disallow duplication
- Allow fail travis on version 7.1 due engineworks-dbal

# Version 1.5.1
- Now depends on eclipxe13/engineworks-progress-status to report progress
- Add `const DateConverter::PRECISION_TIME = 6` to define seconds precision of `1/8600` as `0.000012`
- Move code from `source/` to `src/` following `php-pds/skeleton`
- Move code from `tests/classes/` to `tests/`
- Travis: include 7.1, run parallel-lint, run php-cs-fixer
- Git: update .gitattributes and .gitignore
- Remove autoloader.php, use composer please
- Increase code coverage
- Improve documentation

# Version 1.4.2
- Add accounting format (FORMAT_ACCOUNTING & FORMAT_ACCOUNTING_00)
- Upgrade to PHP CS Fixer version 2.0.0
- Copyright 2017

# Version 1.4.1
- When exporting using DBAL boundle the headers order must remain
  Check your code for possible breaks
- Fix bug that not all styles were included

# Version 1.3.2
- Fix bug were all sheets were selected
- Stop using FORMAT_YESNO for booleans, this format requiere numerical value
- Small optimizations and fixed from scrutinizer

# Version 1.3.1
- XLSXExporter::passtru must be static function

# Version 1.3.0
- Create bundle with eclipxe/engineworks-dbal
- Create two helper methods into XLSXExporter: save and passtru
- Improve coding standards and project files
