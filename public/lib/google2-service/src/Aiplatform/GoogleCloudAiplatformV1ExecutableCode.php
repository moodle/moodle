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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ExecutableCode extends \Google\Model
{
  /**
   * Unspecified language. This value should not be used.
   */
  public const LANGUAGE_LANGUAGE_UNSPECIFIED = 'LANGUAGE_UNSPECIFIED';
  /**
   * Python >= 3.10, with numpy and simpy available.
   */
  public const LANGUAGE_PYTHON = 'PYTHON';
  /**
   * Required. The code to be executed.
   *
   * @var string
   */
  public $code;
  /**
   * Required. Programming language of the `code`.
   *
   * @var string
   */
  public $language;

  /**
   * Required. The code to be executed.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Required. Programming language of the `code`.
   *
   * Accepted values: LANGUAGE_UNSPECIFIED, PYTHON
   *
   * @param self::LANGUAGE_* $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return self::LANGUAGE_*
   */
  public function getLanguage()
  {
    return $this->language;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExecutableCode::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExecutableCode');
