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

class DatasetInputConfig extends \Google\Collection
{
  protected $collection_key = 'inputFiles';
  protected $inputFilesType = InputFile::class;
  protected $inputFilesDataType = 'array';

  /**
   * Files containing the sentence pairs to be imported to the dataset.
   *
   * @param InputFile[] $inputFiles
   */
  public function setInputFiles($inputFiles)
  {
    $this->inputFiles = $inputFiles;
  }
  /**
   * @return InputFile[]
   */
  public function getInputFiles()
  {
    return $this->inputFiles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatasetInputConfig::class, 'Google_Service_Translate_DatasetInputConfig');
