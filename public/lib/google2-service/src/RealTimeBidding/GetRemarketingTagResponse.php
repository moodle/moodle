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

namespace Google\Service\RealTimeBidding;

class GetRemarketingTagResponse extends \Google\Model
{
  /**
   * An HTML tag that can be placed on the advertiser's page to add users to a
   * user list. For more information and code samples on using snippets on your
   * website, refer to [Tag your site for
   * remarketing](https://support.google.com/google-ads/answer/2476688).
   *
   * @var string
   */
  public $snippet;

  /**
   * An HTML tag that can be placed on the advertiser's page to add users to a
   * user list. For more information and code samples on using snippets on your
   * website, refer to [Tag your site for
   * remarketing](https://support.google.com/google-ads/answer/2476688).
   *
   * @param string $snippet
   */
  public function setSnippet($snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return string
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetRemarketingTagResponse::class, 'Google_Service_RealTimeBidding_GetRemarketingTagResponse');
