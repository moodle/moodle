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

namespace Google\Service\Walletobjects;

class PrivateText extends \Google\Model
{
  protected $bodyType = LocalizedString::class;
  protected $bodyDataType = '';
  protected $headerType = LocalizedString::class;
  protected $headerDataType = '';

  /**
   * @param LocalizedString
   */
  public function setBody(LocalizedString $body)
  {
    $this->body = $body;
  }
  /**
   * @return LocalizedString
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * @param LocalizedString
   */
  public function setHeader(LocalizedString $header)
  {
    $this->header = $header;
  }
  /**
   * @return LocalizedString
   */
  public function getHeader()
  {
    return $this->header;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateText::class, 'Google_Service_Walletobjects_PrivateText');
