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

namespace Google\Service\Bigquery;

class JsonOptions extends \Google\Model
{
  /**
   * Optional. The character encoding of the data. The supported values are
   * UTF-8, UTF-16BE, UTF-16LE, UTF-32BE, and UTF-32LE. The default value is
   * UTF-8.
   *
   * @var string
   */
  public $encoding;

  /**
   * Optional. The character encoding of the data. The supported values are
   * UTF-8, UTF-16BE, UTF-16LE, UTF-32BE, and UTF-32LE. The default value is
   * UTF-8.
   *
   * @param string $encoding
   */
  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }
  /**
   * @return string
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JsonOptions::class, 'Google_Service_Bigquery_JsonOptions');
