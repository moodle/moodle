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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataProfileResultProfile extends \Google\Collection
{
  protected $collection_key = 'fields';
  protected $fieldsType = GoogleCloudDataplexV1DataProfileResultProfileField::class;
  protected $fieldsDataType = 'array';

  /**
   * Output only. List of fields with structural and profile information for
   * each field.
   *
   * @param GoogleCloudDataplexV1DataProfileResultProfileField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileResultProfileField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataProfileResultProfile::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProfileResultProfile');
