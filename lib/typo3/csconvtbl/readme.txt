Charset Conversion Tables


The files in this directory contains conversion tables from a number of charsets (corresponding to the filenames) to UNICODE.
This is used by the TYPO3 class "t3lib_cs" which uses these tables when asked to convert a string from one charset to another.

Conversion tables are reproduced from http://www.microsoft.com/globaldev/reference/cphome.mspx
Found overview on this page: http://www.i18nguy.com/unicode/codepages.html
A mirror of Czyborra's pages: http://aspell.net/charsets/

Further a lot of mapping tables are found here as well:
http://www.unicode.org/Public/MAPPINGS/





PARSING:
The conversion table files are parsed linie by linie, extracting by either of these formulars:

Syntax 1:
[Local charset value, hex] = U+[UNICODE hex number] : [descriptive text, ignored]

Example:
A0 = U+00A0 : NO-BREAK SPACE
(eg. iso-8859-1.tbl)

Syntax 2:
0x[Local charset value, hex]	0x[UNICODE hex number]		[descriptive text, ignored]

Example:
0xA0  0x00A0 	 NO-BREAK SPACE
(eg. big5.tbl)


Lines beginning with "#" or empty lines are ignored.
The syntax is auto-detected based on the first line found in the file.
Syntax 2 is directly from http://www.unicode.org/Public/MAPPINGS/ and you can probably take any charmap there and just copy into th csconvtbl/ folder and it will be ready for use.















INDEX:

iso-8859-1.tbl
ISO Character Set 8859-1 (Latin 1)
http://www.microsoft.com/globaldev/reference/iso/28591.htm

iso-8859-2.tbl
ISO Character Set 8859-2 (Latin 2)
http://www.microsoft.com/globaldev/reference/iso/28592.htm

iso-8859-3.tbl
ISO Character Set 8859-3 (Latin 3)
http://www.microsoft.com/globaldev/reference/iso/28593.htm

iso-8859-4.tbl
ISO Character Set 8859-4 (Baltic)
http://www.microsoft.com/globaldev/reference/iso/28594.htm

iso-8859-5.tbl
ISO Character Set 8859-5 (Cyrillic)
http://www.microsoft.com/globaldev/reference/iso/28595.htm

iso-8859-6.tbl
ISO Character Set 8859-6 (Arabic)
http://www.microsoft.com/globaldev/reference/iso/28596.htm

iso-8859-7.tbl
ISO Character Set 8859-7 (Greek)
http://www.microsoft.com/globaldev/reference/iso/28597.htm

iso-8859-8.tbl
ISO Character Set 8859-8 (Hebrew)
http://www.microsoft.com/globaldev/reference/iso/28598.htm

iso-8859-9.tbl
ISO Character Set 8859-9 (Turkish)
http://www.microsoft.com/globaldev/reference/iso/28599.htm

iso-8859-10.tbl
ISO Character Set 8859-10
http://www.unicode.org/Public/MAPPINGS/ISO8859/8859-10.TXT

iso-8859-11.tbl
ISO Character Set 8859-11 (Thai)
http://czyborra.com/charsets/iso8859.html
http://aspell.net/charsets/iso8859.html
http://www.unicode.org/Public/MAPPINGS/ISO8859/8859-11.TXT

iso-8859-13.tbl
ISO Character Set 8859-13 (Lithuanian)
http://www.unicode.org/Public/MAPPINGS/ISO8859/8859-13.TXT

iso-8859-14.tbl
ISO Character Set 8859-14 (Celtic)
http://www.unicode.org/Public/MAPPINGS/ISO8859/8859-10.TXT

iso-8859-15.tbl
ISO Character Set 8859-15 (Latin 9)
http://www.microsoft.com/globaldev/reference/iso/28605.htm

iso-8859-16.tbl
ISO Character Set 8859-16 (Romanian)
http://www.unicode.org/Public/MAPPINGS/ISO8859/8859-16.TXT

windows-1250.tbl
Microsoft Windows Codepage : 1250 (Central Europe)
http://www.microsoft.com/globaldev/reference/sbcs/1250.htm

windows-1251.tbl
Microsoft Windows Codepage : 1251 (Cyrillic)
http://www.microsoft.com/globaldev/reference/sbcs/1251.htm

windows-1252.tbl
Microsoft Windows Codepage : 1252 (Latin I)
http://www.microsoft.com/globaldev/reference/sbcs/1252.htm

windows-1253.tbl
Microsoft Windows Code Page : 1253 (Greek)
http://www.microsoft.com/globaldev/reference/sbcs/1253.htm

windows-1254.tbl
Microsoft Windows Codepage : 1254 (Turkish)
http://www.microsoft.com/globaldev/reference/sbcs/1254.htm

windows-1255.tbl
Microsoft Windows Codepage : 1255 (Hebrew)
http://www.microsoft.com/globaldev/reference/sbcs/1255.htm

windows-1256.tbl
Microsoft Windows Codepage : 1256 (Arabic)
http://www.microsoft.com/globaldev/reference/sbcs/1256.htm

windows-1257.tbl
Microsoft Windows Codepage : 1257 (Baltic)
http://www.microsoft.com/globaldev/reference/sbcs/1257.htm

windows-1258.tbl
Microsoft Windows Codepage : 1258 (Viet Nam)
http://www.microsoft.com/globaldev/reference/sbcs/1258.htm

windows-874.tbl
Microsoft Windows Codepage : 874 (Thai)
http://www.microsoft.com/globaldev/reference/sbcs/874.htm

shift_jis.tbl
Microsoft Windows Codepage : 932 (Japanese Shift-JIS)
http://www.microsoft.com/globaldev/reference/dbcs/932.htm
(Multibyte)

gb2312.tbl
Microsoft Windows Codepage : 936 (Simplified Chinese GBK)
gb2312 936 Chinese Simplified (GB2312)
gb_2312-80 936 Chinese Simplified (GB2312)
http://www.microsoft.com/globaldev/reference/dbcs/936.htm
(Multibyte)
Note: this is a MS-specific superset of the real GB2312

euc-kr.tbl
Microsoft Windows Codepage : 949 (Korean EUC-KR)
http://www.microsoft.com/globaldev/reference/dbcs/932.htm
(Multibyte)
Note: this is a MS-specific superset of the real EUC-KR

big5.tbl
Microsoft Windows Codepage : 950 (Traditional Chinese Big5)
http://www.microsoft.com/globaldev/reference/dbcs/950.htm
(Multibyte)
Note: this is a MS-specific superset of the real Big5


koi8-r.tbl
Cyrillic (Russian)
http://www.unicode.org/Public/MAPPINGS/VENDORS/MISC/KOI8-R.TXT

