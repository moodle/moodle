<?php
/**
 * LICENSE
 *
 * This file is part of CFPropertyList.
 *
 * The PHP implementation of Apple's PropertyList can handle XML PropertyLists
 * as well as binary PropertyLists. It offers functionality to easily convert
 * data between worlds, e.g. recalculating timestamps from unix epoch to apple
 * epoch and vice versa. A feature to automagically create (guess) the plist
 * structure from a normal PHP data structure will help you dump your data to
 * plist in no time.
 *
 * Copyright (c) 2018 Teclib'
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * ------------------------------------------------------------------------------
 * @author    Rodney Rehm <rodney.rehm@medialize.de>
 * @author    Christian Kruse <cjk@wwwtech.de>
 * @copyright Copyright © 2018 Teclib
 * @package   plist
 * @license   MIT
 * @link      https://github.com/TECLIB/CFPropertyList/
 * @link      http://developer.apple.com/documentation/Darwin/Reference/ManPages/man5/plist.5.html Property Lists
 * ------------------------------------------------------------------------------
 */

namespace CFPropertyList;

class IOException extends \Exception
{
  /**
   * Flag telling the File could not be found
   */
    const NOT_FOUND = 1;

  /**
   * Flag telling the File is not readable
   */
    const NOT_READABLE = 2;

  /**
   * Flag telling the File is not writable
   */
    const NOT_WRITABLE = 3;

  /**
   * Flag telling there was a read error
   */
    const READ_ERROR = 4;

  /**
   * Flag telling there was a read error
   */
    const WRITE_ERROR = 5;

  /**
   * Create new IOException
   * @param string $path Source of the problem
   * @param integer $type Type of the problem
   */
    public function __construct($path, $type = null)
    {
        parent::__construct($path, $type);
    }

  /**
   * Create new FileNotFound-Exception
   * @param string $path Source of the problem
   * @return IOException new FileNotFound-Exception
   */
    public static function notFound($path)
    {
        return new IOException($path, self::NOT_FOUND);
    }

  /**
   * Create new FileNotReadable-Exception
   * @param string $path Source of the problem
   * @return IOException new FileNotReadable-Exception
   */
    public static function notReadable($path)
    {
        return new IOException($path, self::NOT_READABLE);
    }

  /**
   * Create new FileNotWritable-Exception
   * @param string $path Source of the problem
   * @return IOException new FileNotWritable-Exception
   */
    public static function notWritable($path)
    {
        return new IOException($path, self::NOT_WRITABLE);
    }

  /**
   * Create new ReadError-Exception
   * @param string $path Source of the problem
   * @return IOException new ReadError-Exception
   */
    public static function readError($path)
    {
        return new IOException($path, self::READ_ERROR);
    }

  /**
   * Create new WriteError-Exception
   * @param string $path Source of the problem
   * @return IOException new WriteError-Exception
   */
    public static function writeError($path)
    {
        return new IOException($path, self::WRITE_ERROR);
    }
}
