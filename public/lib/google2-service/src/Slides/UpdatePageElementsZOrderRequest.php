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

namespace Google\Service\Slides;

class UpdatePageElementsZOrderRequest extends \Google\Collection
{
  /**
   * Unspecified operation.
   */
  public const OPERATION_Z_ORDER_OPERATION_UNSPECIFIED = 'Z_ORDER_OPERATION_UNSPECIFIED';
  /**
   * Brings the page elements to the front of the page.
   */
  public const OPERATION_BRING_TO_FRONT = 'BRING_TO_FRONT';
  /**
   * Brings the page elements forward on the page by one element relative to the
   * forwardmost one in the specified page elements.
   */
  public const OPERATION_BRING_FORWARD = 'BRING_FORWARD';
  /**
   * Sends the page elements backward on the page by one element relative to the
   * furthest behind one in the specified page elements.
   */
  public const OPERATION_SEND_BACKWARD = 'SEND_BACKWARD';
  /**
   * Sends the page elements to the back of the page.
   */
  public const OPERATION_SEND_TO_BACK = 'SEND_TO_BACK';
  protected $collection_key = 'pageElementObjectIds';
  /**
   * The Z-order operation to apply on the page elements. When applying the
   * operation on multiple page elements, the relative Z-orders within these
   * page elements before the operation is maintained.
   *
   * @var string
   */
  public $operation;
  /**
   * The object IDs of the page elements to update. All the page elements must
   * be on the same page and must not be grouped.
   *
   * @var string[]
   */
  public $pageElementObjectIds;

  /**
   * The Z-order operation to apply on the page elements. When applying the
   * operation on multiple page elements, the relative Z-orders within these
   * page elements before the operation is maintained.
   *
   * Accepted values: Z_ORDER_OPERATION_UNSPECIFIED, BRING_TO_FRONT,
   * BRING_FORWARD, SEND_BACKWARD, SEND_TO_BACK
   *
   * @param self::OPERATION_* $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return self::OPERATION_*
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * The object IDs of the page elements to update. All the page elements must
   * be on the same page and must not be grouped.
   *
   * @param string[] $pageElementObjectIds
   */
  public function setPageElementObjectIds($pageElementObjectIds)
  {
    $this->pageElementObjectIds = $pageElementObjectIds;
  }
  /**
   * @return string[]
   */
  public function getPageElementObjectIds()
  {
    return $this->pageElementObjectIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdatePageElementsZOrderRequest::class, 'Google_Service_Slides_UpdatePageElementsZOrderRequest');
