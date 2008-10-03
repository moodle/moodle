<?php
/**
 * OO AJAX Implementation for PHP, contains HTML_AJAX_Action
 *
 * SVN Rev: $Id$
 *
 * @category  HTML
 * @package   AJAX
 * @author    Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright 2005-2008 Elizabeth Smith
 * @license   http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version   Release: 0.5.6
 * @link      http://htmlajax.org/HTML_AJAX/Using%20haSerializer
 */

/**
 * Require the response class and json serializer
 */
require_once 'HTML/AJAX/Response.php';
require_once 'HTML/AJAX/Serializer/JSON.php';

/**
 * Helper class to eliminate the need to write javascript functions to deal with data
 *
 * This class creates information that can be properly serialized and used by
 * the haaction serializer which eliminates the need for php users to write 
 * javascript for dealing with the information returned by an ajax method - 
 * instead the javascript is basically created for them
 *
 * @category  HTML
 * @package   AJAX
 * @author    Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright 2005-2008 Elizabeth Smith
 * @license   http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version   Release: 0.5.6
 * @link      http://htmlajax.org/HTML_AJAX/Using%20haSerializer
 */
class HTML_AJAX_Action extends HTML_AJAX_Response
{

    /**
     * Content type for the HAA response
     *
     * goofy but unique content type to tell the javascript which deserializer to use
     * overrides HTML_AJAX_Response
     *
     * @var string
     * @access public
     */
    var $contentType = 'application/html_ajax_action';

    /**
     * An array holding all the actions for the class
     *
     * these have numeric keys and each new action is added on the end, remember
     * these are executed in the order added
     *
     * @var array
     * @access private
     */
    var $_actions = array();

    /**
     * Prepends data to the attribute identified by id
     *
     * The data will be added to the beginning of the attribute identified by the id
     * sent, id must be unique
     *
     * $response->prependAttr('myid', 'class', 'red');
     * $response->prependAttr('myid', array('class' => 'red', 'innerHTML' => 'this is an error'));
     *
     * @param string       $id        id for a specific item on the page <div id="myid"></div>
     * @param string|array $attribute either an array of attribute/data pairs or a string attribute name
     * @param mixed        $data      should be NULL if attribute is an array, otherwise data you wish to set the attribute to
     *
     * @return void
     * @access public
     */
    function prependAttr($id, $attribute, $data = null)
    {
        if (!is_null($data)) {
            $attribute = array($attribute => $data);
        }
        $this->_actions[] = array(
            'action' => 'prepend',
            'id' => $id,
            'attributes' => $attribute,
            'data' => $data,
        );
        return;
    }

    /**
     * Appends data to the attribute identified by id
     *
     * The data will be added to the end of the attribute identified by the id
     * sent, id must be unique
     *
     * $response->appendAttr('myid', 'class', 'red');
     * $response->appendAttr('myid', array('class' => 'red', 'innerHTML' => 'this is an error'));
     *
     * @param string       $id        id for a specific item on the page <div id="myid"></div>
     * @param string|array $attribute either an array of attribute/data pairs or a string attribute name
     * @param mixed        $data      should be NULL if attribute is an array, otherwise data you wish to set the attribute to
     *
     * @return void
     * @access public
     */
    function appendAttr($id, $attribute, $data = null)
    {
        if (!is_null($data)) {
            $attribute = array($attribute => $data);
        }
        $this->_actions[] = array(
            'action' => 'append',
            'id' => $id,
            'attributes' => $attribute,
        );
        return;
    }

    /**
     * Assigns data to the attribute identified by id overwriting any previous values
     *
     * The data will be assigned to the attribute identified by the id
     * sent, id must be unique
     *
     * $response->assignAttr('myid', 'class', 'red');
     * $response->assignAttr('myid', array('class' => 'red', 'innerHTML' => 'this is an error'));
     *
     * @param string       $id        id for a specific item on the page <div id="myid"></div>
     * @param string|array $attribute either an array of attribute/data pairs or a string attribute name
     * @param mixed        $data      should be NULL if attribute is an array, otherwise data you wish to set the attribute to
     *
     * @return void
     * @access public
     */
    function assignAttr($id, $attribute, $data = null)
    {
        if (!is_null($data)) {
            $attribute = array($attribute => $data);
        }
        $this->_actions[] = array(
            'action' => 'assign',
            'id' => $id,
            'attributes' => $attribute,
        );
        return;
    }

    /**
     * Deletes or assigns a value of an empty string to an attribute
     *
     * You may send either a single attribute or an array of attributes to clear
     *
     * $response->clearAttr('myid', 'class');
     * $response->clearAttr('myid', array('class', 'innerHTML'));
     *
     * @param string       $id        id for a specific item on the page <div id="myid"></div>
     * @param string|array $attribute either an array of attribute/data pairs or a string attribute name
     *
     * @return void
     * @access public
     */
    function clearAttr($id, $attribute)
    {
        if (!is_array($attribute)) {
            $attribute = array($attribute);
        }
        $this->_actions[] = array(
            'action' => 'clear',
            'id' => $id,
            'attributes' => $attribute,
        );
        return;
    }

    /**
     * create a dom node via javascript
     *
     * higher level dom manipulation - creates a new node to insert into the dom
     * You can control where the new node is inserted with two things, the insertion
     * type and the id/  The type should be append, prepend, insertBefore, or insertAfter
     *
     * The id is a sibling node - like a div in the same div you want to add more to
     * If you choose to append or prepend a node it will be placed at the beginning
     * or end of the node with the id you send. If you choose insertBefore or
     * InsertAfter it will be put right before or right after the node you specified.
     * You can send an array of attributes to apply to the new node as well,
     * so you don't have to create it and then assign Attributes.
     *
     * <code>
     * $response->createNode('myid', 'div');
     * $response->createNode('submit', 'input',
     *   array('id' => 'key',
     *         'name' => 'key',
     *         'type' => 'hidden',
     *         'value' => $id),
     *   'insertBefore');
     * <code>
     *
     * @param string $id         id for a specific item on the page <div id="myid"></div>
     * @param string $tag        html node to create
     * @param array  $attributes array of attribute -> data to fill the node with
     * @param string $type       append|prepend|insertBefore|insertAfter default is append
     *
     * @return void
     * @access public
     */
    function createNode($id, $tag, $attributes, $type = 'append')
    {
        $types = array('append', 'prepend', 'insertBefore', 'insertAfter');
        if (!in_array($type, $types)) {
            $type = 'append';
        }
        settype($attributes, 'array');
        $this->_actions[] = array(
            'action' => 'create',
            'id' => $id,
            'tag' => $tag,
            'attributes' => $attributes,
            'type' => $type,
        );
        return;
    }

    /**
     * Replace a dom node via javascript
     *
     * higher level dom manipulation - replaces one node with another
     * This can be used to replace a div with a form for inline editing
     * use innerHtml attribute to change inside text
     *
     * $response->replaceNode('myid', 'div', array('innerHTML' => 'loading complete'));
     * $response->replaceNode('mydiv', 'form', array('innerHTML' => $form));
     *
     * @param string $id         id for a specific item on the page <div id="myid"></div>
     * @param string $tag        html node to create
     * @param array  $attributes array of attribute -> data to fill the node with
     *
     * @return void
     * @access public
     */
    function replaceNode($id, $tag, $attributes)
    {
        settype($attributes, 'array');
        $this->_actions[] = array(
            'action' => 'replace',
            'id' => $id,
            'tag' => $tag,
            'attributes' => $attributes,
        );
        return;
    }

    /**
     * Delete a dom node via javascript
     *
     * $response->removeNode('myid');
     * $response->removeNode(array('mydiv', 'myform'));
     *
     * @param string $id id for a specific item on the page <div id="myid"></div>
     *
     * @return void
     * @access public
     */
    function removeNode($id)
    {
        $this->_actions[] = array(
            'action' => 'remove',
            'id' => $id,
        );
        return;
    }

    /**
     * Send a string to a javascript eval
     *
     * This will send the data right to the eval javascript function, it will NOT
     * allow you to dynamically add a javascript function for use later on because
     * it is constrined by the eval function
     *
     * @param string $data string to pass to the alert javascript function
     *
     * @return void
     * @access public
     */
    function insertScript($data)
    {
        $this->_actions[] = array(
            'action' => 'script',
            'data' => $data,
        );
        return;
    }

    /**
     * Send a string to a javascript alert
     *
     * This will send the data right to the alert javascript function
     *
     * @param string $data string to pass to the alert javascript function
     *
     * @return void
     * @access public
     */
    function insertAlert($data)
    {
        $this->_actions[] = array(
            'action' => 'alert',
            'data' => $data,
        );
        return;
    }

    /**
     * Returns the serialized content of the response class
     *
     * we actually use the json serializer underneath, so we send the actions array
     * to the json serializer and return the data
     *
     * @return string serialized response content
     * @access public
     */
    function getPayload()
    {
        $serializer = new HTML_AJAX_Serializer_JSON();
        return $serializer->serialize($this->_actions);
    }

    /**
     * Adds all the actions from one response object to another, feature request
     * #6635 at pear.php.net
     *
     * @param object &$instance referenced HTML_AJAX_Action object
     *
     * @return array
     * @access public
     */
    function combineActions(&$instance)
    {
        $this->_actions = array_merge($this->_actions, $instance->retrieveActions());
    }

    /**
     * to follow proper property access we need a way to retrieve the private
     * actions array
     *
     * @return  array
     * @access public
     */
    function retrieveActions()
    {
        return $this->_actions;
    }
}
?>
