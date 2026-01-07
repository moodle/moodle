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

namespace Google\Service\CloudHealthcare;

class ImportMessagesRequest extends \Google\Model
{
  protected $gcsSourceType = GcsSource::class;
  protected $gcsSourceDataType = '';

  /**
   * Cloud Storage source data location and import configuration. The Cloud
   * Healthcare Service Agent requires the `roles/storage.objectViewer` Cloud
   * IAM roles on the Cloud Storage location.
   *
   * @param GcsSource $gcsSource
   */
  public function setGcsSource(GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportMessagesRequest::class, 'Google_Service_CloudHealthcare_ImportMessagesRequest');
