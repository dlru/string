Multibyte string object representation
======

## Usage

`namespace DL\Main;`

### Construction and basic usage

	$so = new String('string', 'ascii'); // String and encoding
	$so = new String('строка'); // UTF-8 is default encoding
	echo $so->value(); // "строка"
	echo $so; // Also "строка"

### Length and size (bytes)

	$so = new String('строка');
	echo $so->length(); // 6 (characters)
	echo $so->size(); // 12 (bytes)

### Append and prepend

	$so = new String('строка');
	echo $so->append('@'); // "строка@"
	echo $so->prepend('#'); // "#строка@"
	echo $so->size(); // 14 (bytes) Understand? :)

### Character access

	$so = new String('строка');
	echo $so->char(0); // "c"
	echo $so->char(4); // "к"
	echo $so->char(-2); // Also "к" :)

### Substings

	$so = new String('строка'); echo $so->substr(1, 2); // "тр"
	$so = new String('строка'); echo $so->substr(2, -1); // "рок"
	$so = new String('строка'); echo $so->substr(1); // "трока"

### Case manipulation

	$so = new String('Это строка');
	echo $so->upper(); // "ЭТО СТРОКА!"
	echo $so->lower(); // "это строка!"
	echo $so->ucWords(); // "Это Строка!"
	echo $so->lower()->ucFirst(); // "Это строка"

### In "foreach"

	$result = '';
	$so = new String('строка');
	foreach ($so as $key => $char) {
	    $result .= $char . ':' . $key .'-';
	}
	echo $result; // "с:0-т:1-р:2-о:3-к:4-а:5-"

### Array access

	$so = new String('строка');
	$so[] = '!'; echo $so; // "строка!"
	unset($so[1]); echo $so; // "срока!"
	$so[2] = 'а'; echo $so; // "срака!" Just an example :)

### Spaghetti

	$so = new String('Строка!');
	echo $so->upper()->substr(3, -2); // "ОК"
	echo $so->lower()->ucFirst(); // "Ок"
	echo $so->append('!'); // "Ок!"
	echo $so->concat(' да?')->lower()->ucWords(); // "Ок! Да?"

### Miscellaneous

	$so = new String('Строка!');
	echo $so->convert('Windows-1251'); // Windows-1251 encoded $so's
	echo $so->encoding(); // Now returns "Windows-1251"
	$so->convert('UTF-8'); // Rollback string to UTF-8

## Copyright and license

Copyright (c) 2001-2013, DesignLab, LLC. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
* Neither the name of the DesignLab, LLC nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
