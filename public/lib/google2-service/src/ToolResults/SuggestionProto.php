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

namespace Google\Service\ToolResults;

class SuggestionProto extends \Google\Model
{
  public const PRIORITY_unknownPriority = 'unknownPriority';
  public const PRIORITY_error = 'error';
  public const PRIORITY_warning = 'warning';
  public const PRIORITY_info = 'info';
  /**
   * Reference to a help center article concerning this type of suggestion.
   * Always set.
   *
   * @var string
   */
  public $helpUrl;
  protected $longMessageType = SafeHtmlProto::class;
  protected $longMessageDataType = '';
  /**
   * Relative importance of a suggestion. Always set.
   *
   * @var string
   */
  public $priority;
  /**
   * A somewhat human readable identifier of the source view, if it does not
   * have a resource_name. This is a path within the accessibility hierarchy, an
   * element with resource name; similar to an XPath.
   *
   * @var string
   */
  public $pseudoResourceId;
  protected $regionType = RegionProto::class;
  protected $regionDataType = '';
  /**
   * Reference to a view element, identified by its resource name, if it has
   * one.
   *
   * @var string
   */
  public $resourceName;
  /**
   * ID of the screen for the suggestion. It is used for getting the
   * corresponding screenshot path. For example, screen_id "1" corresponds to
   * "1.png" file in GCS. Always set.
   *
   * @var string
   */
  public $screenId;
  /**
   * Relative importance of a suggestion as compared with other suggestions that
   * have the same priority and category. This is a meaningless value that can
   * be used to order suggestions that are in the same category and have the
   * same priority. The larger values have higher priority (i.e., are more
   * important). Optional.
   *
   * @var 
   */
  public $secondaryPriority;
  protected $shortMessageType = SafeHtmlProto::class;
  protected $shortMessageDataType = '';
  /**
   * General title for the suggestion, in the user's language, without markup.
   * Always set.
   *
   * @var string
   */
  public $title;

  /**
   * Reference to a help center article concerning this type of suggestion.
   * Always set.
   *
   * @param string $helpUrl
   */
  public function setHelpUrl($helpUrl)
  {
    $this->helpUrl = $helpUrl;
  }
  /**
   * @return string
   */
  public function getHelpUrl()
  {
    return $this->helpUrl;
  }
  /**
   * Message, in the user's language, explaining the suggestion, which may
   * contain markup. Always set.
   *
   * @param SafeHtmlProto $longMessage
   */
  public function setLongMessage(SafeHtmlProto $longMessage)
  {
    $this->longMessage = $longMessage;
  }
  /**
   * @return SafeHtmlProto
   */
  public function getLongMessage()
  {
    return $this->longMessage;
  }
  /**
   * Relative importance of a suggestion. Always set.
   *
   * Accepted values: unknownPriority, error, warning, info
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * A somewhat human readable identifier of the source view, if it does not
   * have a resource_name. This is a path within the accessibility hierarchy, an
   * element with resource name; similar to an XPath.
   *
   * @param string $pseudoResourceId
   */
  public function setPseudoResourceId($pseudoResourceId)
  {
    $this->pseudoResourceId = $pseudoResourceId;
  }
  /**
   * @return string
   */
  public function getPseudoResourceId()
  {
    return $this->pseudoResourceId;
  }
  /**
   * Region within the screenshot that is relevant to this suggestion. Optional.
   *
   * @param RegionProto $region
   */
  public function setRegion(RegionProto $region)
  {
    $this->region = $region;
  }
  /**
   * @return RegionProto
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Reference to a view element, identified by its resource name, if it has
   * one.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * ID of the screen for the suggestion. It is used for getting the
   * corresponding screenshot path. For example, screen_id "1" corresponds to
   * "1.png" file in GCS. Always set.
   *
   * @param string $screenId
   */
  public function setScreenId($screenId)
  {
    $this->screenId = $screenId;
  }
  /**
   * @return string
   */
  public function getScreenId()
  {
    return $this->screenId;
  }
  public function setSecondaryPriority($secondaryPriority)
  {
    $this->secondaryPriority = $secondaryPriority;
  }
  public function getSecondaryPriority()
  {
    return $this->secondaryPriority;
  }
  /**
   * Concise message, in the user's language, representing the suggestion, which
   * may contain markup. Always set.
   *
   * @param SafeHtmlProto $shortMessage
   */
  public function setShortMessage(SafeHtmlProto $shortMessage)
  {
    $this->shortMessage = $shortMessage;
  }
  /**
   * @return SafeHtmlProto
   */
  public function getShortMessage()
  {
    return $this->shortMessage;
  }
  /**
   * General title for the suggestion, in the user's language, without markup.
   * Always set.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SuggestionProto::class, 'Google_Service_ToolResults_SuggestionProto');
