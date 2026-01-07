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

class ServingRestriction extends \Google\Collection
{
  /**
   * The status is not known.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The ad was disapproved in this context.
   */
  public const STATUS_DISAPPROVAL = 'DISAPPROVAL';
  /**
   * The ad is pending review in this context.
   */
  public const STATUS_PENDING_REVIEW = 'PENDING_REVIEW';
  protected $collection_key = 'disapprovalReasons';
  protected $contextsType = ServingContext::class;
  protected $contextsDataType = 'array';
  protected $disapprovalType = Disapproval::class;
  protected $disapprovalDataType = '';
  protected $disapprovalReasonsType = Disapproval::class;
  protected $disapprovalReasonsDataType = 'array';
  /**
   * The status of the creative in this context (for example, it has been
   * explicitly disapproved or is pending review).
   *
   * @var string
   */
  public $status;

  /**
   * The contexts for the restriction.
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
   * Disapproval bound to this restriction. Only present if status=DISAPPROVED.
   * Can be used to filter the response of the creatives.list method.
   *
   * @param Disapproval $disapproval
   */
  public function setDisapproval(Disapproval $disapproval)
  {
    $this->disapproval = $disapproval;
  }
  /**
   * @return Disapproval
   */
  public function getDisapproval()
  {
    return $this->disapproval;
  }
  /**
   * Any disapprovals bound to this restriction. Only present if
   * status=DISAPPROVED. Can be used to filter the response of the
   * creatives.list method. Deprecated; use disapproval field instead.
   *
   * @deprecated
   * @param Disapproval[] $disapprovalReasons
   */
  public function setDisapprovalReasons($disapprovalReasons)
  {
    $this->disapprovalReasons = $disapprovalReasons;
  }
  /**
   * @deprecated
   * @return Disapproval[]
   */
  public function getDisapprovalReasons()
  {
    return $this->disapprovalReasons;
  }
  /**
   * The status of the creative in this context (for example, it has been
   * explicitly disapproved or is pending review).
   *
   * Accepted values: STATUS_UNSPECIFIED, DISAPPROVAL, PENDING_REVIEW
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServingRestriction::class, 'Google_Service_AdExchangeBuyerII_ServingRestriction');
