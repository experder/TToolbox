PHP Regular Expressions
=======================
https://www.php.net/manual/en/book.pcre.php

`[a-z0-9]` matches all (lowercase) letters and numbers  
`[0-9]{2,3}` matches all numbers from 10 to 999 (and 00 to 09)  
`^A` matches an A at the very beginning  
`A$` matches an A at the very end  

Meta-characters
---------------
http://php.net/manual/en/regexp.reference.meta.php

`.*?` lazy match 0 or more any characters  
`.+` greedy match 1 or more any characters

Escape sequences
----------------
http://php.net/manual/en/regexp.reference.escape.php

`\b` word boundary  
`\R` line break: matches \n, \r and \r\n  
`\s` any whitespace character  
`\W` any "non-word" character  

Pattern Modifiers
-----------------
http://php.net/manual/en/reference.pcre.pattern.modifiers.php

`i` PCRE_CASELESS ("...letters in the pattern match both upper and lower case letters.")  
`s` PCRE_DOTALL ("...a dot metacharacter in the pattern matches all characters, including newlines.")  
