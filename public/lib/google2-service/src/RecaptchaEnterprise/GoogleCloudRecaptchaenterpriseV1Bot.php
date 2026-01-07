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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1Bot extends \Google\Model
{
  /**
   * Default unspecified type.
   */
  public const BOT_TYPE_BOT_TYPE_UNSPECIFIED = 'BOT_TYPE_UNSPECIFIED';
  /**
   * Software program that interacts with a site and performs tasks
   * autonomously.
   */
  public const BOT_TYPE_AI_AGENT = 'AI_AGENT';
  /**
   * Software that extracts specific data from sites for use.
   */
  public const BOT_TYPE_CONTENT_SCRAPER = 'CONTENT_SCRAPER';
  /**
   * Software that crawls sites and stores content for the purpose of efficient
   * retrieval, likely as part of a search engine.
   */
  public const BOT_TYPE_SEARCH_INDEXER = 'SEARCH_INDEXER';
  /**
   * Optional. Enumerated field representing the type of bot.
   *
   * @var string
   */
  public $botType;
  /**
   * Optional. Enumerated string value that indicates the identity of the bot,
   * formatted in kebab-case.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. Enumerated field representing the type of bot.
   *
   * Accepted values: BOT_TYPE_UNSPECIFIED, AI_AGENT, CONTENT_SCRAPER,
   * SEARCH_INDEXER
   *
   * @param self::BOT_TYPE_* $botType
   */
  public function setBotType($botType)
  {
    $this->botType = $botType;
  }
  /**
   * @return self::BOT_TYPE_*
   */
  public function getBotType()
  {
    return $this->botType;
  }
  /**
   * Optional. Enumerated string value that indicates the identity of the bot,
   * formatted in kebab-case.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1Bot::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1Bot');
