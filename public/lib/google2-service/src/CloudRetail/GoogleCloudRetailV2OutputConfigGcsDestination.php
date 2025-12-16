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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2OutputConfigGcsDestination extends \Google\Model
{
  /**
   * Required. The output uri prefix for saving output data to json files. Some
   * mapping examples are as follows: output_uri_prefix sample output(assuming
   * the object is foo.json) ========================
   * ============================================= gs://bucket/
   * gs://bucket/foo.json gs://bucket/folder/ gs://bucket/folder/foo.json
   * gs://bucket/folder/item_ gs://bucket/folder/item_foo.json
   *
   * @var string
   */
  public $outputUriPrefix;

  /**
   * Required. The output uri prefix for saving output data to json files. Some
   * mapping examples are as follows: output_uri_prefix sample output(assuming
   * the object is foo.json) ========================
   * ============================================= gs://bucket/
   * gs://bucket/foo.json gs://bucket/folder/ gs://bucket/folder/foo.json
   * gs://bucket/folder/item_ gs://bucket/folder/item_foo.json
   *
   * @param string $outputUriPrefix
   */
  public function setOutputUriPrefix($outputUriPrefix)
  {
    $this->outputUriPrefix = $outputUriPrefix;
  }
  /**
   * @return string
   */
  public function getOutputUriPrefix()
  {
    return $this->outputUriPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2OutputConfigGcsDestination::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2OutputConfigGcsDestination');
