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

namespace Google\Service\DatabaseMigrationService;

class RulesFile extends \Google\Model
{
  /**
   * Required. The text content of the rules that needs to be converted.
   *
   * @var string
   */
  public $rulesContent;
  /**
   * Required. The filename of the rules that needs to be converted. The
   * filename is used mainly so that future logs of the import rules job contain
   * it, and can therefore be searched by it.
   *
   * @var string
   */
  public $rulesSourceFilename;

  /**
   * Required. The text content of the rules that needs to be converted.
   *
   * @param string $rulesContent
   */
  public function setRulesContent($rulesContent)
  {
    $this->rulesContent = $rulesContent;
  }
  /**
   * @return string
   */
  public function getRulesContent()
  {
    return $this->rulesContent;
  }
  /**
   * Required. The filename of the rules that needs to be converted. The
   * filename is used mainly so that future logs of the import rules job contain
   * it, and can therefore be searched by it.
   *
   * @param string $rulesSourceFilename
   */
  public function setRulesSourceFilename($rulesSourceFilename)
  {
    $this->rulesSourceFilename = $rulesSourceFilename;
  }
  /**
   * @return string
   */
  public function getRulesSourceFilename()
  {
    return $this->rulesSourceFilename;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RulesFile::class, 'Google_Service_DatabaseMigrationService_RulesFile');
