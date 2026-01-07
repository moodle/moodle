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

class GoogleCloudDialogflowCxV3ToolTLSConfig extends \Google\Collection
{
  protected $collection_key = 'caCerts';
  protected $caCertsType = GoogleCloudDialogflowCxV3ToolTLSConfigCACert::class;
  protected $caCertsDataType = 'array';

  /**
   * Required. Specifies a list of allowed custom CA certificates for HTTPS
   * verification.
   *
   * @param GoogleCloudDialogflowCxV3ToolTLSConfigCACert[] $caCerts
   */
  public function setCaCerts($caCerts)
  {
    $this->caCerts = $caCerts;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ToolTLSConfigCACert[]
   */
  public function getCaCerts()
  {
    return $this->caCerts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ToolTLSConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ToolTLSConfig');
