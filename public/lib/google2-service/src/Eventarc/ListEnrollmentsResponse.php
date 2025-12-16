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

namespace Google\Service\Eventarc;

class ListEnrollmentsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $enrollmentsType = Enrollment::class;
  protected $enrollmentsDataType = 'array';
  /**
   * A page token that can be sent to `ListEnrollments` to request the next
   * page. If this is empty, then there are no more pages.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Unreachable resources, if any.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The requested Enrollments, up to the number specified in `page_size`.
   *
   * @param Enrollment[] $enrollments
   */
  public function setEnrollments($enrollments)
  {
    $this->enrollments = $enrollments;
  }
  /**
   * @return Enrollment[]
   */
  public function getEnrollments()
  {
    return $this->enrollments;
  }
  /**
   * A page token that can be sent to `ListEnrollments` to request the next
   * page. If this is empty, then there are no more pages.
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
   * Unreachable resources, if any.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListEnrollmentsResponse::class, 'Google_Service_Eventarc_ListEnrollmentsResponse');
