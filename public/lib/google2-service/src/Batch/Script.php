<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Batch;

class Script extends \Google\Model
{
  /**
   * The path to a script file that is accessible from the host VM(s). Unless
   * the script file supports the default `#!/bin/sh` shell interpreter, you
   * must specify an interpreter by including a [shebang
   * line](https://en.wikipedia.org/wiki/Shebang_(Unix) as the first line of the
   * file. For example, to execute the script using bash, include `#!/bin/bash`
   * as the first line of the file. Alternatively, to execute the script using
   * Python3, include `#!/usr/bin/env python3` as the first line of the file.
   *
   * @var string
   */
  public $path;
  /**
   * The text for a script. Unless the script text supports the default
   * `#!/bin/sh` shell interpreter, you must specify an interpreter by including
   * a [shebang line](https://en.wikipedia.org/wiki/Shebang_(Unix) at the
   * beginning of the text. For example, to execute the script using bash,
   * include `#!/bin/bash\n` at the beginning of the text. Alternatively, to
   * execute the script using Python3, include `#!/usr/bin/env python3\n` at the
   * beginning of the text.
   *
   * @var string
   */
  public $text;

  /**
   * The path to a script file that is accessible from the host VM(s). Unless
   * the script file supports the default `#!/bin/sh` shell interpreter, you
   * must specify an interpreter by including a [shebang
   * line](https://en.wikipedia.org/wiki/Shebang_(Unix) as the first line of the
   * file. For example, to execute the script using bash, include `#!/bin/bash`
   * as the first line of the file. Alternatively, to execute the script using
   * Python3, include `#!/usr/bin/env python3` as the first line of the file.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * The text for a script. Unless the script text supports the default
   * `#!/bin/sh` shell interpreter, you must specify an interpreter by including
   * a [shebang line](https://en.wikipedia.org/wiki/Shebang_(Unix) at the
   * beginning of the text. For example, to execute the script using bash,
   * include `#!/bin/bash\n` at the beginning of the text. Alternatively, to
   * execute the script using Python3, include `#!/usr/bin/env python3\n` at the
   * beginning of the text.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Script::class, 'Google_Service_Batch_Script');
