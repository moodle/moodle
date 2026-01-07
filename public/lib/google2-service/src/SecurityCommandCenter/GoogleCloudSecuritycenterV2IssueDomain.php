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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2IssueDomain extends \Google\Model
{
  /**
   * Unspecified domain category.
   */
  public const DOMAIN_CATEGORY_DOMAIN_CATEGORY_UNSPECIFIED = 'DOMAIN_CATEGORY_UNSPECIFIED';
  /**
   * Issues in the AI domain.
   */
  public const DOMAIN_CATEGORY_AI = 'AI';
  /**
   * Issues in the code domain.
   */
  public const DOMAIN_CATEGORY_CODE = 'CODE';
  /**
   * Issues in the container domain.
   */
  public const DOMAIN_CATEGORY_CONTAINER = 'CONTAINER';
  /**
   * Issues in the data domain.
   */
  public const DOMAIN_CATEGORY_DATA = 'DATA';
  /**
   * Issues in the identity and access domain.
   */
  public const DOMAIN_CATEGORY_IDENTITY_AND_ACCESS = 'IDENTITY_AND_ACCESS';
  /**
   * Issues in the vulnerability domain.
   */
  public const DOMAIN_CATEGORY_VULNERABILITY = 'VULNERABILITY';
  /**
   * Issues in the threat domain.
   */
  public const DOMAIN_CATEGORY_THREAT = 'THREAT';
  /**
   * The domain category of the issue.
   *
   * @var string
   */
  public $domainCategory;

  /**
   * The domain category of the issue.
   *
   * Accepted values: DOMAIN_CATEGORY_UNSPECIFIED, AI, CODE, CONTAINER, DATA,
   * IDENTITY_AND_ACCESS, VULNERABILITY, THREAT
   *
   * @param self::DOMAIN_CATEGORY_* $domainCategory
   */
  public function setDomainCategory($domainCategory)
  {
    $this->domainCategory = $domainCategory;
  }
  /**
   * @return self::DOMAIN_CATEGORY_*
   */
  public function getDomainCategory()
  {
    return $this->domainCategory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2IssueDomain::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2IssueDomain');
