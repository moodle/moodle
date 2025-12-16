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

class AgentScript extends \Google\Model
{
  /**
   * Script file path on the host VM. To specify an interpreter, please add a
   * `#!`(also known as [shebang
   * line](https://en.wikipedia.org/wiki/Shebang_(Unix))) as the first line of
   * the file.(For example, to execute the script using bash, `#!/bin/bash`
   * should be the first line of the file. To execute the script using`Python3`,
   * `#!/usr/bin/env python3` should be the first line of the file.) Otherwise,
   * the file will by default be executed by `/bin/sh`.
   *
   * @var string
   */
  public $path;
  /**
   * Shell script text. To specify an interpreter, please add a `#!\n` at the
   * beginning of the text.(For example, to execute the script using bash,
   * `#!/bin/bash\n` should be added. To execute the script using`Python3`,
   * `#!/usr/bin/env python3\n` should be added.) Otherwise, the script will by
   * default be executed by `/bin/sh`.
   *
   * @var string
   */
  public $text;

  /**
   * Script file path on the host VM. To specify an interpreter, please add a
   * `#!`(also known as [shebang
   * line](https://en.wikipedia.org/wiki/Shebang_(Unix))) as the first line of
   * the file.(For example, to execute the script using bash, `#!/bin/bash`
   * should be the first line of the file. To execute the script using`Python3`,
   * `#!/usr/bin/env python3` should be the first line of the file.) Otherwise,
   * the file will by default be executed by `/bin/sh`.
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
   * Shell script text. To specify an interpreter, please add a `#!\n` at the
   * beginning of the text.(For example, to execute the script using bash,
   * `#!/bin/bash\n` should be added. To execute the script using`Python3`,
   * `#!/usr/bin/env python3\n` should be added.) Otherwise, the script will by
   * default be executed by `/bin/sh`.
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
class_alias(AgentScript::class, 'Google_Service_Batch_AgentScript');
