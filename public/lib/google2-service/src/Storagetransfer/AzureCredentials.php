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

namespace Google\Service\Storagetransfer;

class AzureCredentials extends \Google\Model
{
  /**
   * Required. Azure shared access signature (SAS). For more information about
   * SAS, see [Grant limited access to Azure Storage resources using shared
   * access signatures (SAS)](https://docs.microsoft.com/en-
   * us/azure/storage/common/storage-sas-overview).
   *
   * @var string
   */
  public $sasToken;

  /**
   * Required. Azure shared access signature (SAS). For more information about
   * SAS, see [Grant limited access to Azure Storage resources using shared
   * access signatures (SAS)](https://docs.microsoft.com/en-
   * us/azure/storage/common/storage-sas-overview).
   *
   * @param string $sasToken
   */
  public function setSasToken($sasToken)
  {
    $this->sasToken = $sasToken;
  }
  /**
   * @return string
   */
  public function getSasToken()
  {
    return $this->sasToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AzureCredentials::class, 'Google_Service_Storagetransfer_AzureCredentials');
