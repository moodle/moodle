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

class Publisher extends \Google\Model
{
  /**
   * Provides information about the authority of the issuing party to release
   * the document, in particular, the party's constituency and responsibilities
   * or other obligations.
   *
   * @var string
   */
  public $issuingAuthority;
  /**
   * Name of the publisher. Examples: 'Google', 'Google Cloud Platform'.
   *
   * @var string
   */
  public $name;
  /**
   * The context or namespace. Contains a URL which is under control of the
   * issuing party and can be used as a globally unique identifier for that
   * issuing party. Example: https://csaf.io
   *
   * @var string
   */
  public $publisherNamespace;

  /**
   * Provides information about the authority of the issuing party to release
   * the document, in particular, the party's constituency and responsibilities
   * or other obligations.
   *
   * @param string $issuingAuthority
   */
  public function setIssuingAuthority($issuingAuthority)
  {
    $this->issuingAuthority = $issuingAuthority;
  }
  /**
   * @return string
   */
  public function getIssuingAuthority()
  {
    return $this->issuingAuthority;
  }
  /**
   * Name of the publisher. Examples: 'Google', 'Google Cloud Platform'.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The context or namespace. Contains a URL which is under control of the
   * issuing party and can be used as a globally unique identifier for that
   * issuing party. Example: https://csaf.io
   *
   * @param string $publisherNamespace
   */
  public function setPublisherNamespace($publisherNamespace)
  {
    $this->publisherNamespace = $publisherNamespace;
  }
  /**
   * @return string
   */
  public function getPublisherNamespace()
  {
    return $this->publisherNamespace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Publisher::class, 'Google_Service_ContainerAnalysis_Publisher');
