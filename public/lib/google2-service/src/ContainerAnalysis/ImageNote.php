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

class ImageNote extends \Google\Model
{
  protected $fingerprintType = Fingerprint::class;
  protected $fingerprintDataType = '';
  /**
   * Required. Immutable. The resource_url for the resource representing the
   * basis of associated occurrence images.
   *
   * @var string
   */
  public $resourceUrl;

  /**
   * Required. Immutable. The fingerprint of the base image.
   *
   * @param Fingerprint $fingerprint
   */
  public function setFingerprint(Fingerprint $fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return Fingerprint
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Required. Immutable. The resource_url for the resource representing the
   * basis of associated occurrence images.
   *
   * @param string $resourceUrl
   */
  public function setResourceUrl($resourceUrl)
  {
    $this->resourceUrl = $resourceUrl;
  }
  /**
   * @return string
   */
  public function getResourceUrl()
  {
    return $this->resourceUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageNote::class, 'Google_Service_ContainerAnalysis_ImageNote');
