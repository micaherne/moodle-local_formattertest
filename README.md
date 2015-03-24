# moodle-local_formattertest

This is a test plugin to investigate whether it would be feasible to use MessageFormatter to
format strings as an additional method (as well as the existing {$a->blah} method).

The idea is that, if an $a data object is passed, but the string contains no placeholders using the {$a->blah}
syntax, we assume the string is in the ICU message format (as used by Java etc.) and we format the message using
that instead.

The benefits of this are:

* simpler format for basic values: we can use {blah} instead of {$a->blah}
* full power of the format, e.g.:
  *  plural formats: where numbers are passed, we can define the pluralisation (e.g. "{count, plural, one{# thing} other{# things}}"
   which will output "1 thing" if passed 1, "12 things" if passed 12. This is not possible with the existing get_string 
   interpolation format.
  * number formatting: where numbers are passed, MessageFormatter can format them by the locale used (e.g. "{count, number}" will 
    output "10,000" for some locales, "10.000" for others.
  * complex formats: "{0} resulted in {1,choice,0#no errors|1#single error|1<{1, number} errors}" (nicked from http://stackoverflow.com/questions/2366579/what-is-the-messageformatter-class-in-php-5-3-good-for)
  * simple array data: MessageFormat supports (as above) the use of array indices for replacements. The existing
    Moodle implementation doesn't allow this (there may be a good reason for this - I don't know :) )

## Performance
As far as I can see, the test for the existing format barely adds any time, and interpolating simple values is comparable in the two systems (where there are a lot of variables to interpolate, the MessageFormat version may actually be faster).
