TCPDF supports TrueTypeUnicode (UTF-8 Unicode), TrueType, Type1, CID-0 and Core (standard) fonts.
All TCPDF fonts must be embedded on the PDF document, unless you are using standard fonts whith Unicode mode disabled.

The PDF Core (standard) fonts are:

    * courier : Courier
    * courierb : Courier Bold
    * courierbi : Courier Bold Italic
    * courieri : Courier Italic
    * helvetica : Helvetica
    * helveticab : Helvetica Bold
    * helveticabi : Helvetica Bold Italic
    * helveticai : Helvetica Italic
    * symbol : Symbol
    * times : Times New Roman
    * timesb : Times New Roman Bold
    * timesbi : Times New Roman Bold Italic
    * timesi : Times New Roman Italic
    * zapfdingbats : Zapf Dingbats

Setting up a font for usage with TCPDF requires the following steps:

   1. Generate the font's metrics file.
          * For Type1 font files this first step is not necessary because the AFM file is usually shipped with the font. In case you have only a metric file in PFM format, use the pfm2afm utility (fonts/utils/pfm2afm.exe) to get the AFM file. If you own a Type1 font in ASCII format (.pfa), you can convert it to binary format with Type 1 utilities.
          * For TrueTypeUnicode or TrueType font files, use the the provided ttf2ufm utility (fonts/utils/ttf2ufm.exe):

            $ ttf2ufm -a -F myfont.ttf

   2. Run makefont.php script.
          * For TrueTypeUnicode:

            $ php -q makefont.php myfont.ttf myfont.ufm

          * For TrueType:

            $ php -q makefont.php myfont.ttf myfont.afm

          * For Type1:

            $ php -q makefont.php myfont.pfb myfont.afm

      You may also specify additional parameters:

      MakeFont(string $fontfile, string $fmfile [, boolean $embedded [, $enc="cp1252" [, $patch=array()]]])

          * $fontfile : Path to the .ttf or .pfb file.
          * $fmfile : Path to the .afm file for Type1 and TrueType or .ufm for TrueTypeUnicode.
          * $embedded : Set to false to not embed the font, true otherwise (default).
          * $enc : Name of the encoding table to use. Default value: cp1252. Omit this parameter for TrueType Unicode, OpenType Unicode and symbolic fonts like Symbol or ZapfDingBats. The encoding defines the association between a code (from 0 to 255) and a character. The first 128 are fixed and correspond to ASCII. The encodings are stored in .map files. Those available are:
                o cp1250 (Central Europe)
                o cp1251 (Cyrillic)
                o cp1252 (Western Europe)
                o cp1253 (Greek)
                o cp1254 (Turkish)
                o cp1255 (Hebrew)
                o cp1257 (Baltic)
                o cp1258 (Vietnamese)
                o cp874 (Thai)
                o iso-8859-1 (Western Europe)
                o iso-8859-2 (Central Europe)
                o iso-8859-4 (Baltic)
                o iso-8859-5 (Cyrillic)
                o iso-8859-7 (Greek)
                o iso-8859-9 (Turkish)
                o iso-8859-11 (Thai)
                o iso-8859-15 (Western Europe)
                o iso-8859-16 (Central Europe)
                o koi8-r (Russian)
                o koi8-u (Ukrainian)
            Of course, the font must contain the characters corresponding to the chosen encoding. The encodings which begin with cp are those used by Windows; Linux systems usually use ISO.
          * $patch : Optional modification of the encoding. Empty by default. This parameter gives the possibility to alter the encoding. Sometimes you may want to add some characters. For instance, ISO-8859-1 does not contain the euro symbol. To add it at position 164, pass array(164=>'Euro').

   3. Copy the resulting .php, .z and .ctg.z (if available) files to the TCPDF fonts directory.

   4. Rename php font files variations for bold and italic using the following schema:
          * [basic-font-name]b.php for bold variation
          * [basic-font-name]i.php for oblique variation
          * [basic-font-name]bi.php for bold oblique variation

   5. Convert all fonts filenames to lowercase.
