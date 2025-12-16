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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3GcsDestination extends \Google\Model
{
  /**
   * Required. The Google Cloud Storage URI for the exported objects. A URI is
   * of the form: `gs://bucket/object-name-or-prefix` Whether a full object
   * name, or just a prefix, its usage depends on the Dialogflow operation.
   *
   * @var string
   */
  public $uri;

  /**
   * Required. The Google Cloud Storage URI for the exported objects. A URI is
   * of the form: `gs://bucket/object-name-or-prefix` Whether a full object
   * name, or just a prefix, its usage depends on the Dialogflow operation.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3GcsDestination::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3GcsDestination');
