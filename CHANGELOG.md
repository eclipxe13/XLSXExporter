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
