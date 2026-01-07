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

namespace Google\Service\ContainerAnalysis;

class SecretLocation extends \Google\Model
{
  protected $fileLocationType = GrafeasV1FileLocation::class;
  protected $fileLocationDataType = '';

  /**
   * The secret is found from a file.
   *
   * @param GrafeasV1FileLocation $fileLocation
   */
  public function setFileLocation(GrafeasV1FileLocation $fileLocation)
  {
    $this->fileLocation = $fileLocation;
  }
  /**
   * @return GrafeasV1FileLocation
   */
  public function getFileLocation()
  {
    return $this->fileLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecretLocation::class, 'Google_Service_ContainerAnalysis_SecretLocation');
