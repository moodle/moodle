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

namespace Google\Service\CloudDomains;

class ImportDomainRequest extends \Google\Model
{
  /**
   * Required. The domain name. Unicode domain names must be expressed in
   * Punycode format.
   *
   * @var string
   */
  public $domainName;
  /**
   * Set of labels associated with the `Registration`.
   *
   * @var string[]
   */
  public $labels;

  /**
   * Required. The domain name. Unicode domain names must be expressed in
   * Punycode format.
   *
   * @param string $domainName
   */
  public function setDomainName($domainName)
  {
    $this->domainName = $domainName;
  }
  /**
   * @return string
   */
  public function getDomainName()
  {
    return $this->domainName;
  }
  /**
   * Set of labels associated with the `Registration`.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportDomainRequest::class, 'Google_Service_CloudDomains_ImportDomainRequest');
