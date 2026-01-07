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

namespace Google\Service\Dns;

class ResponsePolicyRulesListResponse extends \Google\Collection
{
  protected $collection_key = 'responsePolicyRules';
  /**
   * This field indicates that more results are available beyond the last page
   * displayed. To fetch the results, make another list request and use this
   * value as your page token. This lets you retrieve the complete contents of a
   * very large collection one page at a time. However, if the contents of the
   * collection change between the first and last paginated list request, the
   * set of all elements returned are an inconsistent view of the collection.
   * You can't retrieve a consistent snapshot of a collection larger than the
   * maximum page size.
   *
   * @var string
   */
  public $nextPageToken;
  protected $responsePolicyRulesType = ResponsePolicyRule::class;
  protected $responsePolicyRulesDataType = 'array';

  /**
   * This field indicates that more results are available beyond the last page
   * displayed. To fetch the results, make another list request and use this
   * value as your page token. This lets you retrieve the complete contents of a
   * very large collection one page at a time. However, if the contents of the
   * collection change between the first and last paginated list request, the
   * set of all elements returned are an inconsistent view of the collection.
   * You can't retrieve a consistent snapshot of a collection larger than the
   * maximum page size.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * The Response Policy Rule resources.
   *
   * @param ResponsePolicyRule[] $responsePolicyRules
   */
  public function setResponsePolicyRules($responsePolicyRules)
  {
    $this->responsePolicyRules = $responsePolicyRules;
  }
  /**
   * @return ResponsePolicyRule[]
   */
  public function getResponsePolicyRules()
  {
    return $this->responsePolicyRules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResponsePolicyRulesListResponse::class, 'Google_Service_Dns_ResponsePolicyRulesListResponse');
