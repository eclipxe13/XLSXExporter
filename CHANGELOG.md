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
