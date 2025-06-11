<?php

namespace IMSGlobal\LTI\ToolProvider;

/**
 * Class to represent a content-item object
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.2
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
#[\AllowDynamicProperties]
class ContentItem
{

/**
 * Media type for LTI launch links.
 */
    const LTI_LINK_MEDIA_TYPE = 'application/vnd.ims.lti.v1.ltilink';

/**
 * Class constructor.
 *
 * @param string $type Class type of content-item
 * @param ContentItemPlacement $placementAdvice  Placement object for item (optional)
 * @param string $id   URL of content-item (optional)
 */
    function __construct($type, $placementAdvice = null, $id = null)
    {

        $this->{'@type'} = $type;
        if (is_object($placementAdvice) && (count(get_object_vars($placementAdvice)) > 0)) {
            $this->placementAdvice = $placementAdvice;
        }
        if (!empty($id)) {
            $this->{'@id'} = $id;
        }

    }

/**
 * Set a URL value for the content-item.
 *
 * @param string $url  URL value
 */
    public function setUrl($url)
    {

        if (!empty($url)) {
            $this->url = $url;
        } else {
            unset($this->url);
        }

    }

/**
 * Set a media type value for the content-item.
 *
 * @param string $mediaType  Media type value
 */
    public function setMediaType($mediaType)
    {

        if (!empty($mediaType)) {
            $this->mediaType = $mediaType;
        } else {
            unset($this->mediaType);
        }

    }

/**
 * Set a title value for the content-item.
 *
 * @param string $title  Title value
 */
    public function setTitle($title)
    {

        if (!empty($title)) {
            $this->title = $title;
        } else if (isset($this->title)) {
            unset($this->title);
        }

    }

/**
 * Set a link text value for the content-item.
 *
 * @param string $text  Link text value
 */
    public function setText($text)
    {

        if (!empty($text)) {
            $this->text = $text;
        } else if (isset($this->text)) {
            unset($this->text);
        }

    }

/**
 * Wrap the content items to form a complete application/vnd.ims.lti.v1.contentitems+json media type instance.
 *
 * @param mixed $items An array of content items or a single item
 * @return string
 */
    public static function toJson($items)
    {
/*
        $data = array();
        if (!is_array($items)) {
            $data[] = json_encode($items);
        } else {
            foreach ($items as $item) {
                $data[] = json_encode($item);
            }
        }
        $json = '{ "@context" : "http://purl.imsglobal.org/ctx/lti/v1/ContentItem", "@graph" : [' . implode(", ", $data) . '] }';
*/
        $obj = new \stdClass();
        $obj->{'@context'} = 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem';
        if (!is_array($items)) {
          $obj->{'@graph'} = array();
          $obj->{'@graph'}[] = $items;
        } else {
          $obj->{'@graph'} = $items;
        }

        return json_encode($obj);

    }

}
