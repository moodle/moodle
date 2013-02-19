<?php
/*
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


  /**
   * The "tasks" collection of methods.
   * Typical usage is:
   *  <code>
   *   $tasksService = new Google_TasksService(...);
   *   $tasks = $tasksService->tasks;
   *  </code>
   */
  class Google_TasksServiceResource extends Google_ServiceResource {


    /**
     * Creates a new task on the specified task list. (tasks.insert)
     *
     * @param string $tasklist Task list identifier.
     * @param Google_Task $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string parent Parent task identifier. If the task is created at the top level, this parameter is omitted. Optional.
     * @opt_param string previous Previous sibling task identifier. If the task is created at the first position among its siblings, this parameter is omitted. Optional.
     * @return Google_Task
     */
    public function insert($tasklist, Google_Task $postBody, $optParams = array()) {
      $params = array('tasklist' => $tasklist, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Task($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the specified task. (tasks.get)
     *
     * @param string $tasklist Task list identifier.
     * @param string $task Task identifier.
     * @param array $optParams Optional parameters.
     * @return Google_Task
     */
    public function get($tasklist, $task, $optParams = array()) {
      $params = array('tasklist' => $tasklist, 'task' => $task);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Task($data);
      } else {
        return $data;
      }
    }
    /**
     * Clears all completed tasks from the specified task list. The affected tasks will be marked as
     * 'hidden' and no longer be returned by default when retrieving all tasks for a task list.
     * (tasks.clear)
     *
     * @param string $tasklist Task list identifier.
     * @param array $optParams Optional parameters.
     */
    public function clear($tasklist, $optParams = array()) {
      $params = array('tasklist' => $tasklist);
      $params = array_merge($params, $optParams);
      $data = $this->__call('clear', array($params));
      return $data;
    }
    /**
     * Moves the specified task to another position in the task list. This can include putting it as a
     * child task under a new parent and/or move it to a different position among its sibling tasks.
     * (tasks.move)
     *
     * @param string $tasklist Task list identifier.
     * @param string $task Task identifier.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string parent New parent task identifier. If the task is moved to the top level, this parameter is omitted. Optional.
     * @opt_param string previous New previous sibling task identifier. If the task is moved to the first position among its siblings, this parameter is omitted. Optional.
     * @return Google_Task
     */
    public function move($tasklist, $task, $optParams = array()) {
      $params = array('tasklist' => $tasklist, 'task' => $task);
      $params = array_merge($params, $optParams);
      $data = $this->__call('move', array($params));
      if ($this->useObjects()) {
        return new Google_Task($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns all tasks in the specified task list. (tasks.list)
     *
     * @param string $tasklist Task list identifier.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string dueMax Upper bound for a task's due date (as a RFC 3339 timestamp) to filter by. Optional. The default is not to filter by due date.
     * @opt_param bool showDeleted Flag indicating whether deleted tasks are returned in the result. Optional. The default is False.
     * @opt_param string updatedMin Lower bound for a task's last modification time (as a RFC 3339 timestamp) to filter by. Optional. The default is not to filter by last modification time.
     * @opt_param string completedMin Lower bound for a task's completion date (as a RFC 3339 timestamp) to filter by. Optional. The default is not to filter by completion date.
     * @opt_param string maxResults Maximum number of task lists returned on one page. Optional. The default is 100.
     * @opt_param bool showCompleted Flag indicating whether completed tasks are returned in the result. Optional. The default is True.
     * @opt_param string pageToken Token specifying the result page to return. Optional.
     * @opt_param string completedMax Upper bound for a task's completion date (as a RFC 3339 timestamp) to filter by. Optional. The default is not to filter by completion date.
     * @opt_param bool showHidden Flag indicating whether hidden tasks are returned in the result. Optional. The default is False.
     * @opt_param string dueMin Lower bound for a task's due date (as a RFC 3339 timestamp) to filter by. Optional. The default is not to filter by due date.
     * @return Google_Tasks
     */
    public function listTasks($tasklist, $optParams = array()) {
      $params = array('tasklist' => $tasklist);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Tasks($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the specified task. (tasks.update)
     *
     * @param string $tasklist Task list identifier.
     * @param string $task Task identifier.
     * @param Google_Task $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Task
     */
    public function update($tasklist, $task, Google_Task $postBody, $optParams = array()) {
      $params = array('tasklist' => $tasklist, 'task' => $task, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Task($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the specified task. This method supports patch semantics. (tasks.patch)
     *
     * @param string $tasklist Task list identifier.
     * @param string $task Task identifier.
     * @param Google_Task $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Task
     */
    public function patch($tasklist, $task, Google_Task $postBody, $optParams = array()) {
      $params = array('tasklist' => $tasklist, 'task' => $task, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Task($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes the specified task from the task list. (tasks.delete)
     *
     * @param string $tasklist Task list identifier.
     * @param string $task Task identifier.
     * @param array $optParams Optional parameters.
     */
    public function delete($tasklist, $task, $optParams = array()) {
      $params = array('tasklist' => $tasklist, 'task' => $task);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "tasklists" collection of methods.
   * Typical usage is:
   *  <code>
   *   $tasksService = new Google_TasksService(...);
   *   $tasklists = $tasksService->tasklists;
   *  </code>
   */
  class Google_TasklistsServiceResource extends Google_ServiceResource {


    /**
     * Creates a new task list and adds it to the authenticated user's task lists. (tasklists.insert)
     *
     * @param Google_TaskList $postBody
     * @param array $optParams Optional parameters.
     * @return Google_TaskList
     */
    public function insert(Google_TaskList $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_TaskList($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the authenticated user's specified task list. (tasklists.get)
     *
     * @param string $tasklist Task list identifier.
     * @param array $optParams Optional parameters.
     * @return Google_TaskList
     */
    public function get($tasklist, $optParams = array()) {
      $params = array('tasklist' => $tasklist);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_TaskList($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns all the authenticated user's task lists. (tasklists.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Token specifying the result page to return. Optional.
     * @opt_param string maxResults Maximum number of task lists returned on one page. Optional. The default is 100.
     * @return Google_TaskLists
     */
    public function listTasklists($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_TaskLists($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the authenticated user's specified task list. (tasklists.update)
     *
     * @param string $tasklist Task list identifier.
     * @param Google_TaskList $postBody
     * @param array $optParams Optional parameters.
     * @return Google_TaskList
     */
    public function update($tasklist, Google_TaskList $postBody, $optParams = array()) {
      $params = array('tasklist' => $tasklist, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_TaskList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the authenticated user's specified task list. This method supports patch semantics.
     * (tasklists.patch)
     *
     * @param string $tasklist Task list identifier.
     * @param Google_TaskList $postBody
     * @param array $optParams Optional parameters.
     * @return Google_TaskList
     */
    public function patch($tasklist, Google_TaskList $postBody, $optParams = array()) {
      $params = array('tasklist' => $tasklist, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_TaskList($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes the authenticated user's specified task list. (tasklists.delete)
     *
     * @param string $tasklist Task list identifier.
     * @param array $optParams Optional parameters.
     */
    public function delete($tasklist, $optParams = array()) {
      $params = array('tasklist' => $tasklist);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

/**
 * Service definition for Google_Tasks (v1).
 *
 * <p>
 * Lets you manage your tasks and task lists.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://code.google.com/apis/tasks/v1/using.html" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_TasksService extends Google_Service {
  public $tasks;
  public $tasklists;
  /**
   * Constructs the internal representation of the Tasks service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'tasks/v1/';
    $this->version = 'v1';
    $this->serviceName = 'tasks';

    $client->addService($this->serviceName, $this->version);
    $this->tasks = new Google_TasksServiceResource($this, $this->serviceName, 'tasks', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/tasks"], "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}, "parent": {"type": "string", "location": "query"}, "previous": {"type": "string", "location": "query"}}, "request": {"$ref": "Task"}, "response": {"$ref": "Task"}, "httpMethod": "POST", "path": "lists/{tasklist}/tasks", "id": "tasks.tasks.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/tasks", "https://www.googleapis.com/auth/tasks.readonly"], "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}, "task": {"required": true, "type": "string", "location": "path"}}, "id": "tasks.tasks.get", "httpMethod": "GET", "path": "lists/{tasklist}/tasks/{task}", "response": {"$ref": "Task"}}, "clear": {"scopes": ["https://www.googleapis.com/auth/tasks"], "path": "lists/{tasklist}/clear", "id": "tasks.tasks.clear", "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "POST"}, "move": {"scopes": ["https://www.googleapis.com/auth/tasks"], "parameters": {"task": {"required": true, "type": "string", "location": "path"}, "tasklist": {"required": true, "type": "string", "location": "path"}, "parent": {"type": "string", "location": "query"}, "previous": {"type": "string", "location": "query"}}, "id": "tasks.tasks.move", "httpMethod": "POST", "path": "lists/{tasklist}/tasks/{task}/move", "response": {"$ref": "Task"}}, "list": {"scopes": ["https://www.googleapis.com/auth/tasks", "https://www.googleapis.com/auth/tasks.readonly"], "parameters": {"dueMax": {"type": "string", "location": "query"}, "tasklist": {"required": true, "type": "string", "location": "path"}, "showDeleted": {"type": "boolean", "location": "query"}, "updatedMin": {"type": "string", "location": "query"}, "completedMin": {"type": "string", "location": "query"}, "maxResults": {"type": "string", "location": "query", "format": "int64"}, "showCompleted": {"type": "boolean", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "completedMax": {"type": "string", "location": "query"}, "showHidden": {"type": "boolean", "location": "query"}, "dueMin": {"type": "string", "location": "query"}}, "id": "tasks.tasks.list", "httpMethod": "GET", "path": "lists/{tasklist}/tasks", "response": {"$ref": "Tasks"}}, "update": {"scopes": ["https://www.googleapis.com/auth/tasks"], "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}, "task": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Task"}, "response": {"$ref": "Task"}, "httpMethod": "PUT", "path": "lists/{tasklist}/tasks/{task}", "id": "tasks.tasks.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/tasks"], "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}, "task": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Task"}, "response": {"$ref": "Task"}, "httpMethod": "PATCH", "path": "lists/{tasklist}/tasks/{task}", "id": "tasks.tasks.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/tasks"], "path": "lists/{tasklist}/tasks/{task}", "id": "tasks.tasks.delete", "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}, "task": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->tasklists = new Google_TasklistsServiceResource($this, $this->serviceName, 'tasklists', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/tasks"], "request": {"$ref": "TaskList"}, "response": {"$ref": "TaskList"}, "httpMethod": "POST", "path": "users/@me/lists", "id": "tasks.tasklists.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/tasks", "https://www.googleapis.com/auth/tasks.readonly"], "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}}, "id": "tasks.tasklists.get", "httpMethod": "GET", "path": "users/@me/lists/{tasklist}", "response": {"$ref": "TaskList"}}, "list": {"scopes": ["https://www.googleapis.com/auth/tasks", "https://www.googleapis.com/auth/tasks.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "maxResults": {"type": "string", "location": "query", "format": "int64"}}, "response": {"$ref": "TaskLists"}, "httpMethod": "GET", "path": "users/@me/lists", "id": "tasks.tasklists.list"}, "update": {"scopes": ["https://www.googleapis.com/auth/tasks"], "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "TaskList"}, "response": {"$ref": "TaskList"}, "httpMethod": "PUT", "path": "users/@me/lists/{tasklist}", "id": "tasks.tasklists.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/tasks"], "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "TaskList"}, "response": {"$ref": "TaskList"}, "httpMethod": "PATCH", "path": "users/@me/lists/{tasklist}", "id": "tasks.tasklists.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/tasks"], "path": "users/@me/lists/{tasklist}", "id": "tasks.tasklists.delete", "parameters": {"tasklist": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));

  }
}

class Google_Task extends Google_Model {
  public $status;
  public $kind;
  public $updated;
  public $parent;
  protected $__linksType = 'Google_TaskLinks';
  protected $__linksDataType = 'array';
  public $links;
  public $title;
  public $deleted;
  public $completed;
  public $due;
  public $etag;
  public $notes;
  public $position;
  public $hidden;
  public $id;
  public $selfLink;
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setParent($parent) {
    $this->parent = $parent;
  }
  public function getParent() {
    return $this->parent;
  }
  public function setLinks(/* array(Google_TaskLinks) */ $links) {
    $this->assertIsArray($links, 'Google_TaskLinks', __METHOD__);
    $this->links = $links;
  }
  public function getLinks() {
    return $this->links;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setDeleted($deleted) {
    $this->deleted = $deleted;
  }
  public function getDeleted() {
    return $this->deleted;
  }
  public function setCompleted($completed) {
    $this->completed = $completed;
  }
  public function getCompleted() {
    return $this->completed;
  }
  public function setDue($due) {
    $this->due = $due;
  }
  public function getDue() {
    return $this->due;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setNotes($notes) {
    $this->notes = $notes;
  }
  public function getNotes() {
    return $this->notes;
  }
  public function setPosition($position) {
    $this->position = $position;
  }
  public function getPosition() {
    return $this->position;
  }
  public function setHidden($hidden) {
    $this->hidden = $hidden;
  }
  public function getHidden() {
    return $this->hidden;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_TaskLinks extends Google_Model {
  public $type;
  public $link;
  public $description;
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setLink($link) {
    $this->link = $link;
  }
  public function getLink() {
    return $this->link;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
}

class Google_TaskList extends Google_Model {
  public $kind;
  public $title;
  public $updated;
  public $etag;
  public $id;
  public $selfLink;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_TaskLists extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_TaskList';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems(/* array(Google_TaskList) */ $items) {
    $this->assertIsArray($items, 'Google_TaskList', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_Tasks extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_Task';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems(/* array(Google_Task) */ $items) {
    $this->assertIsArray($items, 'Google_Task', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}
