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

namespace Google\Service\Translate;

class ImportDataRequest extends \Google\Model
{
  protected $inputConfigType = DatasetInputConfig::class;
  protected $inputConfigDataType = '';

  /**
   * Required. The config for the input content.
   *
   * @param DatasetInputConfig $inputConfig
   */
  public function setInputConfig(DatasetInputConfig $inputConfig)
  {
    $this->inputConfig = $inputConfig;
  }
  /**
   * @return DatasetInputConfig
   */
  public function getInputConfig()
  {
    return $this->inputConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportDataRequest::class, 'Google_Service_Translate_ImportDataRequest');
