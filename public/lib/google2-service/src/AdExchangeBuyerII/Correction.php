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

namespace Google\Service\AdExchangeBuyerII;

class Correction extends \Google\Collection
{
  /**
   * The correction type is unknown. Refer to the details for more information.
   */
  public const TYPE_CORRECTION_TYPE_UNSPECIFIED = 'CORRECTION_TYPE_UNSPECIFIED';
  /**
   * The ad's declared vendors did not match the vendors that were detected. The
   * detected vendors were added.
   */
  public const TYPE_VENDOR_IDS_ADDED = 'VENDOR_IDS_ADDED';
  /**
   * The ad had the SSL attribute declared but was not SSL-compliant. The SSL
   * attribute was removed.
   */
  public const TYPE_SSL_ATTRIBUTE_REMOVED = 'SSL_ATTRIBUTE_REMOVED';
  /**
   * The ad was declared as Flash-free but contained Flash, so the Flash-free
   * attribute was removed.
   */
  public const TYPE_FLASH_FREE_ATTRIBUTE_REMOVED = 'FLASH_FREE_ATTRIBUTE_REMOVED';
  /**
   * The ad was not declared as Flash-free but it did not reference any flash
   * content, so the Flash-free attribute was added.
   */
  public const TYPE_FLASH_FREE_ATTRIBUTE_ADDED = 'FLASH_FREE_ATTRIBUTE_ADDED';
  /**
   * The ad did not declare a required creative attribute. The attribute was
   * added.
   */
  public const TYPE_REQUIRED_ATTRIBUTE_ADDED = 'REQUIRED_ATTRIBUTE_ADDED';
  /**
   * The ad did not declare a required technology vendor. The technology vendor
   * was added.
   */
  public const TYPE_REQUIRED_VENDOR_ADDED = 'REQUIRED_VENDOR_ADDED';
  /**
   * The ad did not declare the SSL attribute but was SSL-compliant, so the SSL
   * attribute was added.
   */
  public const TYPE_SSL_ATTRIBUTE_ADDED = 'SSL_ATTRIBUTE_ADDED';
  /**
   * Properties consistent with In-banner video were found, so an In-Banner
   * Video attribute was added.
   */
  public const TYPE_IN_BANNER_VIDEO_ATTRIBUTE_ADDED = 'IN_BANNER_VIDEO_ATTRIBUTE_ADDED';
  /**
   * The ad makes calls to the MRAID API so the MRAID attribute was added.
   */
  public const TYPE_MRAID_ATTRIBUTE_ADDED = 'MRAID_ATTRIBUTE_ADDED';
  /**
   * The ad unnecessarily declared the Flash attribute, so the Flash attribute
   * was removed.
   */
  public const TYPE_FLASH_ATTRIBUTE_REMOVED = 'FLASH_ATTRIBUTE_REMOVED';
  /**
   * The ad contains video content.
   */
  public const TYPE_VIDEO_IN_SNIPPET_ATTRIBUTE_ADDED = 'VIDEO_IN_SNIPPET_ATTRIBUTE_ADDED';
  protected $collection_key = 'details';
  protected $contextsType = ServingContext::class;
  protected $contextsDataType = 'array';
  /**
   * Additional details about what was corrected.
   *
   * @var string[]
   */
  public $details;
  /**
   * The type of correction that was applied to the creative.
   *
   * @var string
   */
  public $type;

  /**
   * The contexts for the correction.
   *
   * @param ServingContext[] $contexts
   */
  public function setContexts($contexts)
  {
    $this->contexts = $contexts;
  }
  /**
   * @return ServingContext[]
   */
  public function getContexts()
  {
    return $this->contexts;
  }
  /**
   * Additional details about what was corrected.
   *
   * @param string[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The type of correction that was applied to the creative.
   *
   * Accepted values: CORRECTION_TYPE_UNSPECIFIED, VENDOR_IDS_ADDED,
   * SSL_ATTRIBUTE_REMOVED, FLASH_FREE_ATTRIBUTE_REMOVED,
   * FLASH_FREE_ATTRIBUTE_ADDED, REQUIRED_ATTRIBUTE_ADDED,
   * REQUIRED_VENDOR_ADDED, SSL_ATTRIBUTE_ADDED,
   * IN_BANNER_VIDEO_ATTRIBUTE_ADDED, MRAID_ATTRIBUTE_ADDED,
   * FLASH_ATTRIBUTE_REMOVED, VIDEO_IN_SNIPPET_ATTRIBUTE_ADDED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Correction::class, 'Google_Service_AdExchangeBuyerII_Correction');
