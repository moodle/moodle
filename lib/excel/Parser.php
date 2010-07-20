<?php
/**
*  Class for parsing Excel formulas
*
*  License Information:
*
*    Spreadsheet::WriteExcel:  A library for generating Excel Spreadsheets
*    Copyright (C) 2002 Xavier Noguer xnoguer@rezebra.com
*
*    This library is free software; you can redistribute it and/or
*    modify it under the terms of the GNU Lesser General Public
*    License as published by the Free Software Foundation; either
*    version 2.1 of the License, or (at your option) any later version.
*
*    This library is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
*    Lesser General Public License for more details.
*
*    You should have received a copy of the GNU Lesser General Public
*    License along with this library; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
* @const ADD token identifier for character "+"
*/
define('ADD',"+");

/**
* @const SUB token identifier for character "-"
*/
define('SUB',"-");

/**
* @const EQUAL token identifier for character "="
*/
define('EQUAL',"=");

/**
* @const MUL token identifier for character "*"
*/
define('MUL',"*");

/**
* @const DIV token identifier for character "/"
*/
define('DIV',"/");

/**
* @const OPEN token identifier for character "("
*/
define('OPEN',"(");

/**
* @const CLOSE token identifier for character ")"
*/
define('CLOSE',")");

/**
* @const COMA token identifier for character ","
*/
define('COMA',",");

/**
* Class for parsing Excel formulas
*
* @author Xavier Noguer <xnoguer@rezebra.com>
* @package Spreadsheet_WriteExcel
*/
class Parser
  {
/**
* The class constructor
*
* @param integer $byte_order The byte order (Little endian or Big endian) of the architecture
                             (optional). 1 => big endian, 0 (default) => little endian. 
*/
  function Parser($byte_order = 0)
    {
    $this->_current_char  = 0;        // The index of the character we are currently looking at.
    $this->_current_token = '';       // The token we are working on.
    $this->_formula       = "";       // The formula to parse.
    $this->_lookahead     = '';       // The character ahead of the current char.
    $this->_parse_tree    = '';       // The parse tree to be generated.
    $this->_initialize_hashes();      // Initialize the hashes: ptg's and function's ptg's
    $this->_byte_order = $byte_order; // Little Endian or Big Endian
    $this->_func_args  = 0;           // Number of arguments for the current function
    $this->_volatile   = 0;
    }

/**
* Initialize the ptg and function hashes. 
*/
  function _initialize_hashes()
    {
    // The Excel ptg indices
    $this->ptg = array(
        'ptgExp'       => 0x01,
        'ptgTbl'       => 0x02,
        'ptgAdd'       => 0x03,
        'ptgSub'       => 0x04,
        'ptgMul'       => 0x05,
        'ptgDiv'       => 0x06,
        'ptgPower'     => 0x07,
        'ptgConcat'    => 0x08,
        'ptgLT'        => 0x09,
        'ptgLE'        => 0x0A,
        'ptgEQ'        => 0x0B,
        'ptgGE'        => 0x0C,
        'ptgGT'        => 0x0D,
        'ptgNE'        => 0x0E,
        'ptgIsect'     => 0x0F,
        'ptgUnion'     => 0x10,
        'ptgRange'     => 0x11,
        'ptgUplus'     => 0x12,
        'ptgUminus'    => 0x13,
        'ptgPercent'   => 0x14,
        'ptgParen'     => 0x15,
        'ptgMissArg'   => 0x16,
        'ptgStr'       => 0x17,
        'ptgAttr'      => 0x19,
        'ptgSheet'     => 0x1A,
        'ptgEndSheet'  => 0x1B,
        'ptgErr'       => 0x1C,
        'ptgBool'      => 0x1D,
        'ptgInt'       => 0x1E,
        'ptgNum'       => 0x1F,
        'ptgArray'     => 0x20,
        'ptgFunc'      => 0x21,
        'ptgFuncVar'   => 0x22,
        'ptgName'      => 0x23,
        'ptgRef'       => 0x24,
        'ptgArea'      => 0x25,
        'ptgMemArea'   => 0x26,
        'ptgMemErr'    => 0x27,
        'ptgMemNoMem'  => 0x28,
        'ptgMemFunc'   => 0x29,
        'ptgRefErr'    => 0x2A,
        'ptgAreaErr'   => 0x2B,
        'ptgRefN'      => 0x2C,
        'ptgAreaN'     => 0x2D,
        'ptgMemAreaN'  => 0x2E,
        'ptgMemNoMemN' => 0x2F,
        'ptgNameX'     => 0x39,
        'ptgRef3d'     => 0x3A,
        'ptgArea3d'    => 0x3B,
        'ptgRefErr3d'  => 0x3C,
        'ptgAreaErr3d' => 0x3D,
        'ptgArrayV'    => 0x40,
        'ptgFuncV'     => 0x41,
        'ptgFuncVarV'  => 0x42,
        'ptgNameV'     => 0x43,
        'ptgRefV'      => 0x44,
        'ptgAreaV'     => 0x45,
        'ptgMemAreaV'  => 0x46,
        'ptgMemErrV'   => 0x47,
        'ptgMemNoMemV' => 0x48,
        'ptgMemFuncV'  => 0x49,
        'ptgRefErrV'   => 0x4A,
        'ptgAreaErrV'  => 0x4B,
        'ptgRefNV'     => 0x4C,
        'ptgAreaNV'    => 0x4D,
        'ptgMemAreaNV' => 0x4E,
        'ptgMemNoMemN' => 0x4F,
        'ptgFuncCEV'   => 0x58,
        'ptgNameXV'    => 0x59,
        'ptgRef3dV'    => 0x5A,
        'ptgArea3dV'   => 0x5B,
        'ptgRefErr3dV' => 0x5C,
        'ptgAreaErr3d' => 0x5D,
        'ptgArrayA'    => 0x60,
        'ptgFuncA'     => 0x61,
        'ptgFuncVarA'  => 0x62,
        'ptgNameA'     => 0x63,
        'ptgRefA'      => 0x64,
        'ptgAreaA'     => 0x65,
        'ptgMemAreaA'  => 0x66,
        'ptgMemErrA'   => 0x67,
        'ptgMemNoMemA' => 0x68,
        'ptgMemFuncA'  => 0x69,
        'ptgRefErrA'   => 0x6A,
        'ptgAreaErrA'  => 0x6B,
        'ptgRefNA'     => 0x6C,
        'ptgAreaNA'    => 0x6D,
        'ptgMemAreaNA' => 0x6E,
        'ptgMemNoMemN' => 0x6F,
        'ptgFuncCEA'   => 0x78,
        'ptgNameXA'    => 0x79,
        'ptgRef3dA'    => 0x7A,
        'ptgArea3dA'   => 0x7B,
        'ptgRefErr3dA' => 0x7C,
        'ptgAreaErr3d' => 0x7D
        );

    // Thanks to Michael Meeks and Gnumeric for the initial arg values.
    //
    // The following hash was generated by "function_locale.pl" in the distro.
    // Refer to function_locale.pl for non-English function names.
    //
    // The array elements are as follow:
    // ptg:   The Excel function ptg code.
    // args:  The number of arguments that the function takes:
    //           >=0 is a fixed number of arguments.
    //           -1  is a variable  number of arguments.
    // class: The reference, value or array class of the function args.
    // vol:   The function is volatile.
    //
    $this->_functions = array(
          // function                  ptg  args  class  vol
          'COUNT'           => array(   0,   -1,    0,    0 ),
          'IF'              => array(   1,   -1,    1,    0 ),
          'ISNA'            => array(   2,    1,    1,    0 ),
          'ISERROR'         => array(   3,    1,    1,    0 ),
          'SUM'             => array(   4,   -1,    0,    0 ),
          'AVERAGE'         => array(   5,   -1,    0,    0 ),
          'MIN'             => array(   6,   -1,    0,    0 ),
          'MAX'             => array(   7,   -1,    0,    0 ),
          'ROW'             => array(   8,   -1,    0,    0 ),
          'COLUMN'          => array(   9,   -1,    0,    0 ),
          'NA'              => array(  10,    0,    0,    0 ),
          'NPV'             => array(  11,   -1,    1,    0 ),
          'STDEV'           => array(  12,   -1,    0,    0 ),
          'DOLLAR'          => array(  13,   -1,    1,    0 ),
          'FIXED'           => array(  14,   -1,    1,    0 ),
          'SIN'             => array(  15,    1,    1,    0 ),
          'COS'             => array(  16,    1,    1,    0 ),
          'TAN'             => array(  17,    1,    1,    0 ),
          'ATAN'            => array(  18,    1,    1,    0 ),
          'PI'              => array(  19,    0,    1,    0 ),
          'SQRT'            => array(  20,    1,    1,    0 ),
          'EXP'             => array(  21,    1,    1,    0 ),
          'LN'              => array(  22,    1,    1,    0 ),
          'LOG10'           => array(  23,    1,    1,    0 ),
          'ABS'             => array(  24,    1,    1,    0 ),
          'INT'             => array(  25,    1,    1,    0 ),
          'SIGN'            => array(  26,    1,    1,    0 ),
          'ROUND'           => array(  27,    2,    1,    0 ),
          'LOOKUP'          => array(  28,   -1,    0,    0 ),
          'INDEX'           => array(  29,   -1,    0,    1 ),
          'REPT'            => array(  30,    2,    1,    0 ),
          'MID'             => array(  31,    3,    1,    0 ),
          'LEN'             => array(  32,    1,    1,    0 ),
          'VALUE'           => array(  33,    1,    1,    0 ),
          'TRUE'            => array(  34,    0,    1,    0 ),
          'FALSE'           => array(  35,    0,    1,    0 ),
          'AND'             => array(  36,   -1,    0,    0 ),
          'OR'              => array(  37,   -1,    0,    0 ),
          'NOT'             => array(  38,    1,    1,    0 ),
          'MOD'             => array(  39,    2,    1,    0 ),
          'DCOUNT'          => array(  40,    3,    0,    0 ),
          'DSUM'            => array(  41,    3,    0,    0 ),
          'DAVERAGE'        => array(  42,    3,    0,    0 ),
          'DMIN'            => array(  43,    3,    0,    0 ),
          'DMAX'            => array(  44,    3,    0,    0 ),
          'DSTDEV'          => array(  45,    3,    0,    0 ),
          'VAR'             => array(  46,   -1,    0,    0 ),
          'DVAR'            => array(  47,    3,    0,    0 ),
          'TEXT'            => array(  48,    2,    1,    0 ),
          'LINEST'          => array(  49,   -1,    0,    0 ),
          'TREND'           => array(  50,   -1,    0,    0 ),
          'LOGEST'          => array(  51,   -1,    0,    0 ),
          'GROWTH'          => array(  52,   -1,    0,    0 ),
          'PV'              => array(  56,   -1,    1,    0 ),
          'FV'              => array(  57,   -1,    1,    0 ),
          'NPER'            => array(  58,   -1,    1,    0 ),
          'PMT'             => array(  59,   -1,    1,    0 ),
          'RATE'            => array(  60,   -1,    1,    0 ),
          'MIRR'            => array(  61,    3,    0,    0 ),
          'IRR'             => array(  62,   -1,    0,    0 ),
          'RAND'            => array(  63,    0,    1,    1 ),
          'MATCH'           => array(  64,   -1,    0,    0 ),
          'DATE'            => array(  65,    3,    1,    0 ),
          'TIME'            => array(  66,    3,    1,    0 ),
          'DAY'             => array(  67,    1,    1,    0 ),
          'MONTH'           => array(  68,    1,    1,    0 ),
          'YEAR'            => array(  69,    1,    1,    0 ),
          'WEEKDAY'         => array(  70,   -1,    1,    0 ),
          'HOUR'            => array(  71,    1,    1,    0 ),
          'MINUTE'          => array(  72,    1,    1,    0 ),
          'SECOND'          => array(  73,    1,    1,    0 ),
          'NOW'             => array(  74,    0,    1,    1 ),
          'AREAS'           => array(  75,    1,    0,    1 ),
          'ROWS'            => array(  76,    1,    0,    1 ),
          'COLUMNS'         => array(  77,    1,    0,    1 ),
          'OFFSET'          => array(  78,   -1,    0,    1 ),
          'SEARCH'          => array(  82,   -1,    1,    0 ),
          'TRANSPOSE'       => array(  83,    1,    1,    0 ),
          'TYPE'            => array(  86,    1,    1,    0 ),
          'ATAN2'           => array(  97,    2,    1,    0 ),
          'ASIN'            => array(  98,    1,    1,    0 ),
          'ACOS'            => array(  99,    1,    1,    0 ),
          'CHOOSE'          => array( 100,   -1,    1,    0 ),
          'HLOOKUP'         => array( 101,   -1,    0,    0 ),
          'VLOOKUP'         => array( 102,   -1,    0,    0 ),
          'ISREF'           => array( 105,    1,    0,    0 ),
          'LOG'             => array( 109,   -1,    1,    0 ),
          'CHAR'            => array( 111,    1,    1,    0 ),
          'LOWER'           => array( 112,    1,    1,    0 ),
          'UPPER'           => array( 113,    1,    1,    0 ),
          'PROPER'          => array( 114,    1,    1,    0 ),
          'LEFT'            => array( 115,   -1,    1,    0 ),
          'RIGHT'           => array( 116,   -1,    1,    0 ),
          'EXACT'           => array( 117,    2,    1,    0 ),
          'TRIM'            => array( 118,    1,    1,    0 ),
          'REPLACE'         => array( 119,    4,    1,    0 ),
          'SUBSTITUTE'      => array( 120,   -1,    1,    0 ),
          'CODE'            => array( 121,    1,    1,    0 ),
          'FIND'            => array( 124,   -1,    1,    0 ),
          'CELL'            => array( 125,   -1,    0,    1 ),
          'ISERR'           => array( 126,    1,    1,    0 ),
          'ISTEXT'          => array( 127,    1,    1,    0 ),
          'ISNUMBER'        => array( 128,    1,    1,    0 ),
          'ISBLANK'         => array( 129,    1,    1,    0 ),
          'T'               => array( 130,    1,    0,    0 ),
          'N'               => array( 131,    1,    0,    0 ),
          'DATEVALUE'       => array( 140,    1,    1,    0 ),
          'TIMEVALUE'       => array( 141,    1,    1,    0 ),
          'SLN'             => array( 142,    3,    1,    0 ),
          'SYD'             => array( 143,    4,    1,    0 ),
          'DDB'             => array( 144,   -1,    1,    0 ),
          'INDIRECT'        => array( 148,   -1,    1,    1 ),
          'CALL'            => array( 150,   -1,    1,    0 ),
          'CLEAN'           => array( 162,    1,    1,    0 ),
          'MDETERM'         => array( 163,    1,    2,    0 ),
          'MINVERSE'        => array( 164,    1,    2,    0 ),
          'MMULT'           => array( 165,    2,    2,    0 ),
          'IPMT'            => array( 167,   -1,    1,    0 ),
          'PPMT'            => array( 168,   -1,    1,    0 ),
          'COUNTA'          => array( 169,   -1,    0,    0 ),
          'PRODUCT'         => array( 183,   -1,    0,    0 ),
          'FACT'            => array( 184,    1,    1,    0 ),
          'DPRODUCT'        => array( 189,    3,    0,    0 ),
          'ISNONTEXT'       => array( 190,    1,    1,    0 ),
          'STDEVP'          => array( 193,   -1,    0,    0 ),
          'VARP'            => array( 194,   -1,    0,    0 ),
          'DSTDEVP'         => array( 195,    3,    0,    0 ),
          'DVARP'           => array( 196,    3,    0,    0 ),
          'TRUNC'           => array( 197,   -1,    1,    0 ),
          'ISLOGICAL'       => array( 198,    1,    1,    0 ),
          'DCOUNTA'         => array( 199,    3,    0,    0 ),
          'ROUNDUP'         => array( 212,    2,    1,    0 ),
          'ROUNDDOWN'       => array( 213,    2,    1,    0 ),
          'RANK'            => array( 216,   -1,    0,    0 ),
          'ADDRESS'         => array( 219,   -1,    1,    0 ),
          'DAYS360'         => array( 220,   -1,    1,    0 ),
          'TODAY'           => array( 221,    0,    1,    1 ),
          'VDB'             => array( 222,   -1,    1,    0 ),
          'MEDIAN'          => array( 227,   -1,    0,    0 ),
          'SUMPRODUCT'      => array( 228,   -1,    2,    0 ),
          'SINH'            => array( 229,    1,    1,    0 ),
          'COSH'            => array( 230,    1,    1,    0 ),
          'TANH'            => array( 231,    1,    1,    0 ),
          'ASINH'           => array( 232,    1,    1,    0 ),
          'ACOSH'           => array( 233,    1,    1,    0 ),
          'ATANH'           => array( 234,    1,    1,    0 ),
          'DGET'            => array( 235,    3,    0,    0 ),
          'INFO'            => array( 244,    1,    1,    1 ),
          'DB'              => array( 247,   -1,    1,    0 ),
          'FREQUENCY'       => array( 252,    2,    0,    0 ),
          'ERROR.TYPE'      => array( 261,    1,    1,    0 ),
          'REGISTER.ID'     => array( 267,   -1,    1,    0 ),
          'AVEDEV'          => array( 269,   -1,    0,    0 ),
          'BETADIST'        => array( 270,   -1,    1,    0 ),
          'GAMMALN'         => array( 271,    1,    1,    0 ),
          'BETAINV'         => array( 272,   -1,    1,    0 ),
          'BINOMDIST'       => array( 273,    4,    1,    0 ),
          'CHIDIST'         => array( 274,    2,    1,    0 ),
          'CHIINV'          => array( 275,    2,    1,    0 ),
          'COMBIN'          => array( 276,    2,    1,    0 ),
          'CONFIDENCE'      => array( 277,    3,    1,    0 ),
          'CRITBINOM'       => array( 278,    3,    1,    0 ),
          'EVEN'            => array( 279,    1,    1,    0 ),
          'EXPONDIST'       => array( 280,    3,    1,    0 ),
          'FDIST'           => array( 281,    3,    1,    0 ),
          'FINV'            => array( 282,    3,    1,    0 ),
          'FISHER'          => array( 283,    1,    1,    0 ),
          'FISHERINV'       => array( 284,    1,    1,    0 ),
          'FLOOR'           => array( 285,    2,    1,    0 ),
          'GAMMADIST'       => array( 286,    4,    1,    0 ),
          'GAMMAINV'        => array( 287,    3,    1,    0 ),
          'CEILING'         => array( 288,    2,    1,    0 ),
          'HYPGEOMDIST'     => array( 289,    4,    1,    0 ),
          'LOGNORMDIST'     => array( 290,    3,    1,    0 ),
          'LOGINV'          => array( 291,    3,    1,    0 ),
          'NEGBINOMDIST'    => array( 292,    3,    1,    0 ),
          'NORMDIST'        => array( 293,    4,    1,    0 ),
          'NORMSDIST'       => array( 294,    1,    1,    0 ),
          'NORMINV'         => array( 295,    3,    1,    0 ),
          'NORMSINV'        => array( 296,    1,    1,    0 ),
          'STANDARDIZE'     => array( 297,    3,    1,    0 ),
          'ODD'             => array( 298,    1,    1,    0 ),
          'PERMUT'          => array( 299,    2,    1,    0 ),
          'POISSON'         => array( 300,    3,    1,    0 ),
          'TDIST'           => array( 301,    3,    1,    0 ),
          'WEIBULL'         => array( 302,    4,    1,    0 ),
          'SUMXMY2'         => array( 303,    2,    2,    0 ),
          'SUMX2MY2'        => array( 304,    2,    2,    0 ),
          'SUMX2PY2'        => array( 305,    2,    2,    0 ),
          'CHITEST'         => array( 306,    2,    2,    0 ),
          'CORREL'          => array( 307,    2,    2,    0 ),
          'COVAR'           => array( 308,    2,    2,    0 ),
          'FORECAST'        => array( 309,    3,    2,    0 ),
          'FTEST'           => array( 310,    2,    2,    0 ),
          'INTERCEPT'       => array( 311,    2,    2,    0 ),
          'PEARSON'         => array( 312,    2,    2,    0 ),
          'RSQ'             => array( 313,    2,    2,    0 ),
          'STEYX'           => array( 314,    2,    2,    0 ),
          'SLOPE'           => array( 315,    2,    2,    0 ),
          'TTEST'           => array( 316,    4,    2,    0 ),
          'PROB'            => array( 317,   -1,    2,    0 ),
          'DEVSQ'           => array( 318,   -1,    0,    0 ),
          'GEOMEAN'         => array( 319,   -1,    0,    0 ),
          'HARMEAN'         => array( 320,   -1,    0,    0 ),
          'SUMSQ'           => array( 321,   -1,    0,    0 ),
          'KURT'            => array( 322,   -1,    0,    0 ),
          'SKEW'            => array( 323,   -1,    0,    0 ),
          'ZTEST'           => array( 324,   -1,    0,    0 ),
          'LARGE'           => array( 325,    2,    0,    0 ),
          'SMALL'           => array( 326,    2,    0,    0 ),
          'QUARTILE'        => array( 327,    2,    0,    0 ),
          'PERCENTILE'      => array( 328,    2,    0,    0 ),
          'PERCENTRANK'     => array( 329,   -1,    0,    0 ),
          'MODE'            => array( 330,   -1,    2,    0 ),
          'TRIMMEAN'        => array( 331,    2,    0,    0 ),
          'TINV'            => array( 332,    2,    1,    0 ),
          'CONCATENATE'     => array( 336,   -1,    1,    0 ),
          'POWER'           => array( 337,    2,    1,    0 ),
          'RADIANS'         => array( 342,    1,    1,    0 ),
          'DEGREES'         => array( 343,    1,    1,    0 ),
          'SUBTOTAL'        => array( 344,   -1,    0,    0 ),
          'SUMIF'           => array( 345,   -1,    0,    0 ),
          'COUNTIF'         => array( 346,    2,    0,    0 ),
          'COUNTBLANK'      => array( 347,    1,    0,    0 ),
          'ROMAN'           => array( 354,   -1,    1,    0 )
          );
    }

/**
* Convert a token to the proper ptg value.
*
* @param mixed $token The token to convert.
*/
  function _convert($token)
    {
    if(is_numeric($token))
        {
        return($this->_convert_number($token));
        }
    // match references like A1
    elseif(preg_match("/^([A-I]?[A-Z])(\d+)$/",$token))
        {
        return($this->_convert_ref2d($token));
        }
    // match ranges like A1:B2
    elseif(preg_match("/^([A-I]?[A-Z])(\d+)\:([A-I]?[A-Z])(\d+)$/",$token))
        {
        return($this->_convert_range2d($token));
        }
    // match ranges like A1..B2
    elseif(preg_match("/^([A-I]?[A-Z])(\d+)\.\.([A-I]?[A-Z])(\d+)$/",$token))
        {
        return($this->_convert_range2d($token));
        }
    elseif(isset($this->ptg[$token])) // operators (including parentheses)
        {
        return(pack("C", $this->ptg[$token]));
        }
    elseif(preg_match("/[A-Z0-9�-�\.]+/",$token))
        {
        return($this->_convert_function($token,$this->_func_args));
        }
    // if it's an argument, ignore the token (the argument remains)
    elseif($token == 'arg')
        {
        $this->_func_args++;
        return('');
        }
    die("Unknown token $token");
    }

/**
* Convert a number token to ptgInt or ptgNum
*
* @param mixed $num an integer or double for conersion to its ptg value
*/
  function _convert_number($num)
    {
    // Integer in the range 0..2**16-1
    if ((preg_match("/^\d+$/",$num)) and ($num <= 65535)) {
        return pack("Cv", $this->ptg['ptgInt'], $num);
        }
    else // A float
        {
        if($this->_byte_order) // if it's Big Endian
            {
            $num = strrev($num);
            }
        return pack("Cd", $this->ptg['ptgNum'], $num);
        }
    }

/**
* Convert a function to a ptgFunc or ptgFuncVarV depending on the number of
* args that it takes.
*
* @param string  $token    The name of the function for convertion to ptg value.
* @param integer $num_args The number of arguments the function recieves.
*/
  function _convert_function($token, $num_args)
    {
    $this->_func_args = 0; // re initialize the number of arguments
    $args     = $this->_functions[$token][1];
    $volatile = $this->_functions[$token][3];

    if($volatile) {
        $this->_volatile = 1;
        }
    // Fixed number of args eg. TIME($i,$j,$k).
    if ($args >= 0)
        {
        return(pack("Cv", $this->ptg['ptgFuncV'], $this->_functions[$token][0]));
        }
    // Variable number of args eg. SUM($i,$j,$k, ..).
    if ($args == -1) {
        return(pack("CCv", $this->ptg['ptgFuncVarV'], $num_args, $this->_functions[$token][0]));
        }
    }

/**
* Convert an Excel range such as A1:D4 to a ptgRefV.
*
* @param string $range An Excel range in the A1:A2 or A1..A2 format.
*/
  function _convert_range2d($range)
    {
    $class = 2; // as far as I know, this is magick.

    // Split the range into 2 cell refs
    if(preg_match("/^([A-I]?[A-Z])(\d+)\:([A-I]?[A-Z])(\d+)$/",$range)) {
        list($cell1, $cell2) = explode(':', $range);
        }
    elseif(preg_match("/^([A-I]?[A-Z])(\d+)\.\.([A-I]?[A-Z])(\d+)$/",$range)) {
        list($cell1, $cell2) = explode('..', $range);
        }
    else {
        die("Unknown range separator");
        }

    // Convert the cell references
    list($row1, $col1) = $this->_cell_to_packed_rowcol($cell1);
    list($row2, $col2) = $this->_cell_to_packed_rowcol($cell2);

    // The ptg value depends on the class of the ptg.
    if ($class == 0) {
        $ptgArea = pack("C", $this->ptg['ptgArea']);
        }
    elseif ($class == 1) {
        $ptgArea = pack("C", $this->ptg['ptgAreaV']);
        }
    elseif ($class == 2) {
        $ptgArea = pack("C", $this->ptg['ptgAreaA']);
        }
    else{
        die("Unknown class ");
        }

    return($ptgArea . $row1 . $row2 . $col1. $col2);
    }

/**
* Convert an Excel reference such as A1, $B2, C$3 or $D$4 to a ptgRefV.
*
* @param string $cell An Excel cell reference
*/
  function _convert_ref2d($cell)
    {
    $class = 2; // as far as I know, this is magick.

    // Convert the cell reference
    list($row, $col) = $this->_cell_to_packed_rowcol($cell);

    // The ptg value depends on the class of the ptg.
    if ($class == 0) {
        $ptgRef = pack("C", $this->ptg['ptgRef']);
        }
    elseif ($class == 1) {
        $ptgRef = pack("C", $this->ptg['ptgRefV']);
        }
    elseif ($class == 2) {
        $ptgRef = pack("C", $this->ptg['ptgRefA']);
        }
    else{
        die("Unknown class ");
        }
    return $ptgRef.$row.$col;
    }

/**
* pack() row and column into the required 3 byte format.
*
* @param string $cell The Excel cell reference to be packed
*/
  function _cell_to_packed_rowcol($cell)
    {
    list($row, $col, $row_rel, $col_rel) = $this->_cell_to_rowcol($cell);
    if ($col >= 256) {
        die("Column in: $cell greater than 255 ");
        }
    if ($row >= 16384) {
        die("Row in: $cell greater than 16384 ");
        }

    // Set the high bits to indicate if row or col are relative.
    $row    |= $col_rel << 14;
    $row    |= $row_rel << 15;

    $row     = pack('v', $row);
    $col     = pack('C', $col);

    return (array($row, $col));
    }

/**
* Convert an Excel cell reference such as A1 or $B2 or C$3 or $D$4 to a zero
* indexed row and column number. Also returns two boolean values to indicate
* whether the row or column are relative references.
*
* @param string $cell The Excel cell reference in A1 format.
*/
  function _cell_to_rowcol($cell)
    {
    preg_match('/(\$)?([A-I]?[A-Z])(\$)?(\d+)/',$cell,$match);
    // return absolute column if there is a $ in the ref
    $col_rel = empty($match[1]) ? 1 : 0;
    $col_ref = $match[2];
    $row_rel = empty($match[3]) ? 1 : 0;
    $row     = $match[4];

    // Convert base26 column string to a number.
    $expn   = strlen($col_ref) - 1;
    $col    = 0;
    for($i=0; $i < strlen($col_ref); $i++)
    {
        $col += (ord($col_ref{$i}) - ord('A') + 1) * pow(26, $expn);
        $expn--;
    }

    // Convert 1-index to zero-index
    $row--;
    $col--;

    return(array($row, $col, $row_rel, $col_rel));
    }

/**
* Advance to the next valid token.
*/
  function _advance()
    {
    $i = $this->_current_char;
    // eat up white spaces
    if($i < strlen($this->_formula))
        {
        while($this->_formula{$i} == " ")
            {
            $i++;
            }
        if($i < strlen($this->_formula) - 1)
            {
            $this->_lookahead = $this->_formula{$i+1};
            }
        $token = "";
        }
    while($i < strlen($this->_formula))
        {
        $token .= $this->_formula{$i};
        if($this->_match($token) != '')
            {
            if($i < strlen($this->_formula) - 1)
                {
                $this->_lookahead = $this->_formula{$i+1};
                }
            $this->_current_char = $i + 1;
            $this->_current_token = $token;
            return(1);
            }
        $this->_lookahead = $this->_formula{$i+2};
        $i++;
        }
    //die("Lexical error ".$this->_current_char);
    }

/**
* Checks if it's a valid token.
*
* @param mixed $token The token to check.
*/
  function _match($token)
    {
    switch($token)
        {
        case ADD:
            return($token);
            break;
        case SUB:
            return($token);
            break;
        case MUL:
            return($token);
            break;
        case DIV:
            return($token);
            break;
        case OPEN:
            return($token);
            break;
        case CLOSE:
            return($token);
            break;
        case COMA:
            return($token);
            break;
        default:
	    // if it's a reference
            if(preg_match("/^[A-I]?[A-Z][0-9]+$/i",$token) and
	       !preg_match("/[0-9]/",$this->_lookahead) and
               ($this->_lookahead != ':') and ($this->_lookahead != '.'))
                {
                return($token);
                }
            // if it's a range (A1:A2)
            elseif(preg_match("/^[A-I]?[A-Z][0-9]+:[A-I]?[A-Z][0-9]+$/i",$token) and
	           !preg_match("/[0-9]/",$this->_lookahead))
	        {
		return($token);
		}
            // if it's a range (A1..A2)
            elseif(preg_match("/^[A-I]?[A-Z][0-9]+\.\.[A-I]?[A-Z][0-9]+$/i",$token) and
	           !preg_match("/[0-9]/",$this->_lookahead))
	        {
		return($token);
		}
            elseif(is_numeric($token) and !is_numeric($token.$this->_lookahead))
                {
                return($token);
                }
            // if it's a function call
            elseif(preg_match("/^[A-Z0-9�-�\.]+$/i",$token) and ($this->_lookahead == "("))

	        {
		return($token);
		}
            return '';
        }
    }

/**
* The parsing method. It parses a formula.
*
* @access public
* @param string $formula The formula to parse, without the initial equal sign (=).
*/
  function parse($formula)
    {
    $this->_current_char = 0;
    $this->_formula      = $formula;
    $this->_lookahead    = $formula{1};
    $this->_advance();
    $this->_parse_tree   = $this->_expression();
    }

/**
* It parses a expression. It assumes the following rule:
* Expr -> Term [("+" | "-") Term]
*
* @return mixed The parsed ptg'd tree
*/
  function _expression()
    {
    $result = $this->_term();
    while ($this->_current_token == ADD or $this->_current_token == SUB)
        {
        if ($this->_current_token == ADD)
            {
            $this->_advance();
            $result = $this->_create_tree('ptgAdd', $result, $this->_term());
            }
        else 
            {
            $this->_advance();
            $result = $this->_create_tree('ptgSub', $result, $this->_term());
            }
        }
    return $result;
    }

/**
* This function just introduces a ptgParen element in the tree, so that Excel
* doesn't get confused when working with a parenthesized formula afterwards.
*
* @see _fact
* @return mixed The parsed ptg'd tree
*/
  function _parenthesized_expression()
    {
    $result = $this->_create_tree('ptgParen', $this->_expression(), '');
    return($result);
    }

/**
* It parses a term. It assumes the following rule:
* Term -> Fact [("*" | "/") Fact]
*
* @return mixed The parsed ptg'd tree
*/
  function _term()
    {
    $result = $this->_fact();
    while ($this->_current_token == MUL || $this->_current_token == DIV)
        {
        if ($this->_current_token == MUL)
            {
            $this->_advance();
            $result = $this->_create_tree('ptgMul', $result, $this->_fact());
            }
        else 
            {
            $this->_advance();
            $result = $this->_create_tree('ptgDiv', $result, $this->_fact());
            }
        }
    return($result);
    }

/**
* It parses a factor. It assumes the following rule:
* Fact -> ( Expr )
*       | CellRef
*       | CellRange
*       | Number
*       | Function
*
* @return mixed The parsed ptg'd tree
*/
  function _fact()
    {
    if ($this->_current_token == OPEN)
        {
        $this->_advance();         // eat the "("
        $result = $this->_parenthesized_expression();//$this->_expression();

        if ($this->_current_token != CLOSE) {
            die("')' token expected.");
            }
        $this->_advance();         // eat the ")"
        return($result);
        }
    // if it's a reference
    if (preg_match("/^[A-I]?[A-Z][0-9]+$/i",$this->_current_token))
        {
        $result = $this->_create_tree($this->_current_token, '', '');
        $this->_advance();
        return($result);
        }
    // if it's a range
    elseif (preg_match("/^[A-I]?[A-Z][0-9]+:[A-I]?[A-Z][0-9]+$/i",$this->_current_token) or
            preg_match("/^[A-I]?[A-Z][0-9]+\.\.[A-I]?[A-Z][0-9]+$/i",$this->_current_token))
        {
        $result = $this->_current_token;
        $this->_advance();
        return($result);
        }
    elseif (is_numeric($this->_current_token))
        {
        $result = $this->_create_tree($this->_current_token, '', '');
        $this->_advance();
        return($result);
        }
    // if it's a function call
    elseif (preg_match("/^[A-Z0-9�-�\.]+$/i",$this->_current_token))
        {
        $result = $this->_func();
        return($result);
        }
    die("Sintactic error: ".$this->_current_token.", lookahead: ".
        $this->_lookahead.", current char: ".$this->_current_char);
    }

/**
* It parses a function call. It assumes the following rule:
* Func -> ( Expr [,Expr]* )
*
*/
  function _func()
    {
    $num_args = 0; // number of arguments received
    $function = $this->_current_token;
    $this->_advance();
    $this->_advance();         // eat the "("
    while($this->_current_token != ')')
        {
        if($num_args > 0)
            {
            if($this->_current_token == COMA) {
                $this->_advance();  // eat the ","
                }
            else {
                die("Sintactic error: coma expected $num_args");
                }
            $result = $this->_create_tree('arg', $result, $this->_expression());
            }
        else {
            $result = $this->_create_tree('arg', '', $this->_expression());
            }
        $num_args++;
        }
    $args = $this->_functions[$function][1];
    // If fixed number of args eg. TIME($i,$j,$k). Check that the number of args is valid.
    if (($args >= 0) and ($args != $num_args))
        {
        die("Incorrect number of arguments in function $function() ");
        }

    $result = $this->_create_tree($function, $result, '');
    $this->_advance();         // eat the ")"
    return($result);
    }

/**
* Creates a tree. In fact an array which may have one or two arrays (sub-trees)
* as elements.
*
* @param mixed $value The value of this node.
* @param mixed $left  The left array (sub-tree) or a final node.
* @param mixed $right The right array (sub-tree) or a final node.
*/
  function _create_tree($value, $left, $right)
    {
    return array('value' => $value, 'left' => $left, 'right' => $right);
    }

/**
* Builds a string containing the tree in reverse polish notation (What you 
* would use in a HP calculator stack).
* The following tree:
* 
*    +
*   / \
*  2   3
*
* produces: "23+"
*
* The following tree:
*
*    +
*   / \
*  3   *
*     / \
*    6   A1
*
* produces: "36A1*+"
*
* In fact all operands, functions, references, etc... are written as ptg's
*
* @access public
* @param array $tree The optional tree to convert.
*/
  function to_reverse_polish($tree = array())
    {
    $polish = ""; // the string we are going to return
    if (empty($tree)) // If it's the first call use _parse_tree
        {
        $tree = $this->_parse_tree;
        }
    if (is_array($tree['left']))
        {
        $polish .= $this->to_reverse_polish($tree['left']);
        }
    elseif($tree['left'] != '') // It's a final node
        {
        $polish .= $this->_convert($tree['left']); //$tree['left'];
        }
    if (is_array($tree['right']))
        {
        $polish .= $this->to_reverse_polish($tree['right']);
        }
    elseif($tree['right'] != '') // It's a final node
        {
        $polish .= $this->_convert($tree['right']);
        }
    $polish .= $this->_convert($tree['value']);
    return $polish;
    }
  }
?>
