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

namespace Google\Service\Gmail;

class WatchRequest extends \Google\Collection
{
  /**
   * Only get push notifications for message changes relating to labelIds
   * specified.
   */
  public const LABEL_FILTER_ACTION_include = 'include';
  /**
   * Get push notifications for all message changes except those relating to
   * labelIds specified.
   */
  public const LABEL_FILTER_ACTION_exclude = 'exclude';
  /**
   * Only get push notifications for message changes relating to labelIds
   * specified.
   */
  public const LABEL_FILTER_BEHAVIOR_include = 'include';
  /**
   * Get push notifications for all message changes except those relating to
   * labelIds specified.
   */
  public const LABEL_FILTER_BEHAVIOR_exclude = 'exclude';
  protected $collection_key = 'labelIds';
  /**
   * Filtering behavior of `labelIds list` specified. This field is deprecated
   * because it caused incorrect behavior in some cases; use
   * `label_filter_behavior` instead.
   *
   * @deprecated
   * @var string
   */
  public $labelFilterAction;
  /**
   * Filtering behavior of `labelIds list` specified. This field replaces
   * `label_filter_action`; if set, `label_filter_action` is ignored.
   *
   * @var string
   */
  public $labelFilterBehavior;
  /**
   * List of label_ids to restrict notifications about. By default, if
   * unspecified, all changes are pushed out. If specified then dictates which
   * labels are required for a push notification to be generated.
   *
   * @var string[]
   */
  public $labelIds;
  /**
   * A fully qualified Google Cloud Pub/Sub API topic name to publish the events
   * to. This topic name **must** already exist in Cloud Pub/Sub and you
   * **must** have already granted gmail "publish" permission on it. For
   * example, "projects/my-project-identifier/topics/my-topic-name" (using the
   * Cloud Pub/Sub "v1" topic naming format). Note that the "my-project-
   * identifier" portion must exactly match your Google developer project id
   * (the one executing this watch request).
   *
   * @var string
   */
  public $topicName;

  /**
   * Filtering behavior of `labelIds list` specified. This field is deprecated
   * because it caused incorrect behavior in some cases; use
   * `label_filter_behavior` instead.
   *
   * Accepted values: include, exclude
   *
   * @deprecated
   * @param self::LABEL_FILTER_ACTION_* $labelFilterAction
   */
  public function setLabelFilterAction($labelFilterAction)
  {
    $this->labelFilterAction = $labelFilterAction;
  }
  /**
   * @deprecated
   * @return self::LABEL_FILTER_ACTION_*
   */
  public function getLabelFilterAction()
  {
    return $this->labelFilterAction;
  }
  /**
   * Filtering behavior of `labelIds list` specified. This field replaces
   * `label_filter_action`; if set, `label_filter_action` is ignored.
   *
   * Accepted values: include, exclude
   *
   * @param self::LABEL_FILTER_BEHAVIOR_* $labelFilterBehavior
   */
  public function setLabelFilterBehavior($labelFilterBehavior)
  {
    $this->labelFilterBehavior = $labelFilterBehavior;
  }
  /**
   * @return self::LABEL_FILTER_BEHAVIOR_*
   */
  public function getLabelFilterBehavior()
  {
    return $this->labelFilterBehavior;
  }
  /**
   * List of label_ids to restrict notifications about. By default, if
   * unspecified, all changes are pushed out. If specified then dictates which
   * labels are required for a push notification to be generated.
   *
   * @param string[] $labelIds
   */
  public function setLabelIds($labelIds)
  {
    $this->labelIds = $labelIds;
  }
  /**
   * @return string[]
   */
  public function getLabelIds()
  {
    return $this->labelIds;
  }
  /**
   * A fully qualified Google Cloud Pub/Sub API topic name to publish the events
   * to. This topic name **must** already exist in Cloud Pub/Sub and you
   * **must** have already granted gmail "publish" permission on it. For
   * example, "projects/my-project-identifier/topics/my-topic-name" (using the
   * Cloud Pub/Sub "v1" topic naming format). Note that the "my-project-
   * identifier" portion must exactly match your Google developer project id
   * (the one executing this watch request).
   *
   * @param string $topicName
   */
  public function setTopicName($topicName)
  {
    $this->topicName = $topicName;
  }
  /**
   * @return string
   */
  public function getTopicName()
  {
    return $this->topicName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WatchRequest::class, 'Google_Service_Gmail_WatchRequest');
