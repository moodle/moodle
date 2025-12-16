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

namespace Google\Service;

use Google\Client;

/**
 * Service definition for WorkspaceEvents (v1).
 *
 * <p>
 * The Google Workspace Events API lets you subscribe to events and manage
 * change notifications across Google Workspace applications.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/workspace/events" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class WorkspaceEvents extends \Google\Service
{
  /** On their own behalf, apps in Google Chat can see, add, update, and remove members from conversations and spaces. */
  const CHAT_APP_MEMBERSHIPS =
      "https://www.googleapis.com/auth/chat.app.memberships";
  /** On their own behalf, apps in Google Chat can see all messages and their associated reactions and message content. */
  const CHAT_APP_MESSAGES_READONLY =
      "https://www.googleapis.com/auth/chat.app.messages.readonly";
  /** On their own behalf, apps in Google Chat can create conversations and spaces and see or update their metadata (including history settings and access settings). */
  const CHAT_APP_SPACES =
      "https://www.googleapis.com/auth/chat.app.spaces";
  /** Private Service: https://www.googleapis.com/auth/chat.bot. */
  const CHAT_BOT =
      "https://www.googleapis.com/auth/chat.bot";
  /** See, add, update, and remove members from conversations and spaces in Google Chat. */
  const CHAT_MEMBERSHIPS =
      "https://www.googleapis.com/auth/chat.memberships";
  /** View members in Google Chat conversations.. */
  const CHAT_MEMBERSHIPS_READONLY =
      "https://www.googleapis.com/auth/chat.memberships.readonly";
  /** See, compose, send, update, and delete messages as well as their message content; add, see, and delete reactions to messages.. */
  const CHAT_MESSAGES =
      "https://www.googleapis.com/auth/chat.messages";
  /** See, add, and delete reactions as well as their reaction content to messages in Google Chat. */
  const CHAT_MESSAGES_REACTIONS =
      "https://www.googleapis.com/auth/chat.messages.reactions";
  /** View reactions as well as their reaction content to messages in Google Chat. */
  const CHAT_MESSAGES_REACTIONS_READONLY =
      "https://www.googleapis.com/auth/chat.messages.reactions.readonly";
  /** See messages as well as their reactions and message content in Google Chat. */
  const CHAT_MESSAGES_READONLY =
      "https://www.googleapis.com/auth/chat.messages.readonly";
  /** Create conversations and spaces and see or update metadata (including history settings and access settings) in Google Chat. */
  const CHAT_SPACES =
      "https://www.googleapis.com/auth/chat.spaces";
  /** View chat and spaces in Google Chat. */
  const CHAT_SPACES_READONLY =
      "https://www.googleapis.com/auth/chat.spaces.readonly";
  /** See, edit, create, and delete all of your Google Drive files. */
  const DRIVE =
      "https://www.googleapis.com/auth/drive";
  /** See, edit, create, and delete only the specific Google Drive files you use with this app. */
  const DRIVE_FILE =
      "https://www.googleapis.com/auth/drive.file";
  /** View and manage metadata of files in your Google Drive. */
  const DRIVE_METADATA =
      "https://www.googleapis.com/auth/drive.metadata";
  /** See information about your Google Drive files. */
  const DRIVE_METADATA_READONLY =
      "https://www.googleapis.com/auth/drive.metadata.readonly";
  /** See and download all your Google Drive files. */
  const DRIVE_READONLY =
      "https://www.googleapis.com/auth/drive.readonly";
  /** Create, edit, and see information about your Google Meet conferences created by the app.. */
  const MEETINGS_SPACE_CREATED =
      "https://www.googleapis.com/auth/meetings.space.created";
  /** Read information about any of your Google Meet conferences. */
  const MEETINGS_SPACE_READONLY =
      "https://www.googleapis.com/auth/meetings.space.readonly";

  public $message;
  public $operations;
  public $subscriptions;
  public $tasks;
  public $tasks_pushNotificationConfigs;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the WorkspaceEvents service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://workspaceevents.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://workspaceevents.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'workspaceevents';

    $this->message = new WorkspaceEvents\Resource\Message(
        $this,
        $this->serviceName,
        'message',
        [
          'methods' => [
            'stream' => [
              'path' => 'v1/message:stream',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->operations = new WorkspaceEvents\Resource\Operations(
        $this,
        $this->serviceName,
        'operations',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->subscriptions = new WorkspaceEvents\Resource\Subscriptions(
        $this,
        $this->serviceName,
        'subscriptions',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/subscriptions',
              'httpMethod' => 'POST',
              'parameters' => [
                'validateOnly' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'delete' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'allowMissing' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
                'etag' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'validateOnly' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/subscriptions',
              'httpMethod' => 'GET',
              'parameters' => [
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'validateOnly' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'reactivate' => [
              'path' => 'v1/{+name}:reactivate',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->tasks = new WorkspaceEvents\Resource\Tasks(
        $this,
        $this->serviceName,
        'tasks',
        [
          'methods' => [
            'cancel' => [
              'path' => 'v1/{+name}:cancel',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'historyLength' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
              ],
            ],'subscribe' => [
              'path' => 'v1/{+name}:subscribe',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->tasks_pushNotificationConfigs = new WorkspaceEvents\Resource\TasksPushNotificationConfigs(
        $this,
        $this->serviceName,
        'pushNotificationConfigs',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/{+parent}',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'configId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'delete' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/{+parent}/pushNotificationConfigs',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkspaceEvents::class, 'Google_Service_WorkspaceEvents');
