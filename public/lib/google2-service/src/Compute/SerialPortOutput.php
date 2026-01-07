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

namespace Google\Service\Compute;

class SerialPortOutput extends \Google\Model
{
  /**
   * [Output Only] The contents of the console output.
   *
   * @var string
   */
  public $contents;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#serialPortOutput for serial port output.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] The position of the next byte of content, regardless of
   * whether the content exists, following the output returned in the `contents`
   * property. Use this value in the next request as the start parameter.
   *
   * @var string
   */
  public $next;
  /**
   * Output only. [Output Only] Server-defined URL for this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The starting byte position of the output that was returned. This should
   * match the start parameter sent with the request. If the serial console
   * output exceeds the size of the buffer (1 MB), older output is overwritten
   * by newer content. The output start value will indicate the byte position of
   * the output that was returned, which might be different than the `start`
   * value that was specified in the request.
   *
   * @var string
   */
  public $start;

  /**
   * [Output Only] The contents of the console output.
   *
   * @param string $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return string
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#serialPortOutput for serial port output.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * [Output Only] The position of the next byte of content, regardless of
   * whether the content exists, following the output returned in the `contents`
   * property. Use this value in the next request as the start parameter.
   *
   * @param string $next
   */
  public function setNext($next)
  {
    $this->next = $next;
  }
  /**
   * @return string
   */
  public function getNext()
  {
    return $this->next;
  }
  /**
   * Output only. [Output Only] Server-defined URL for this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The starting byte position of the output that was returned. This should
   * match the start parameter sent with the request. If the serial console
   * output exceeds the size of the buffer (1 MB), older output is overwritten
   * by newer content. The output start value will indicate the byte position of
   * the output that was returned, which might be different than the `start`
   * value that was specified in the request.
   *
   * @param string $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SerialPortOutput::class, 'Google_Service_Compute_SerialPortOutput');
