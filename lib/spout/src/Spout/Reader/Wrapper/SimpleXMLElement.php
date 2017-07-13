<?php

namespace Box\Spout\Reader\Wrapper;

use Box\Spout\Reader\Exception\XMLProcessingException;


/**
 * Class SimpleXMLElement
 * Wrapper around the built-in SimpleXMLElement. This class does not extend \SimpleXMLElement
 * because it its constructor is final... Instead, it is used as a passthrough.
 * @see \SimpleXMLElement
 *
 * @package Box\Spout\Reader\Wrapper
 */
class SimpleXMLElement
{
    use XMLInternalErrorsHelper;

    /** @var \SimpleXMLElement Instance of the wrapped SimpleXMLElement object */
    protected $simpleXMLElement;

    /**
     * Creates a new SimpleXMLElement object
     * @see \SimpleXMLElement::__construct
     *
     * @param string $xmlData A well-formed XML string
     * @throws \Box\Spout\Reader\Exception\XMLProcessingException If the XML string is not well-formed
     */
    public function __construct($xmlData)
    {
        $this->useXMLInternalErrors();

        try {
            $this->simpleXMLElement = new \SimpleXMLElement($xmlData);
        } catch (\Exception $exception) {
            // if the data is invalid, the constructor will throw an Exception
            $this->resetXMLInternalErrorsSetting();
            throw new XMLProcessingException($this->getLastXMLErrorMessage());
        }

        $this->resetXMLInternalErrorsSetting();
    }

    /**
     * Returns the attribute for the given name.
     *
     * @param string $name Attribute name
     * @param string|null|void $namespace An optional namespace for the retrieved attributes
     * @return string|null The attribute value or NULL if attribute not found
     */
    public function getAttribute($name, $namespace = null)
    {
        $isPrefix = ($namespace !== null);
        $attributes = $this->simpleXMLElement->attributes($namespace, $isPrefix);
        $attributeValue = $attributes->{$name};

        return ($attributeValue !== null) ? (string) $attributeValue : null;
    }

    /**
     * Creates a prefix/ns context for the next XPath query
     * @see \SimpleXMLElement::registerXPathNamespace
     *
     * @param string $prefix The namespace prefix to use in the XPath query for the namespace given in "namespace".
     * @param string $namespace The namespace to use for the XPath query. This must match a namespace in
     *                          use by the XML document or the XPath query using "prefix" will not return any results.
     * @return bool TRUE on success or FALSE on failure.
     */
    public function registerXPathNamespace($prefix, $namespace)
    {
        return $this->simpleXMLElement->registerXPathNamespace($prefix, $namespace);
    }

    /**
     * Runs XPath query on XML data
     * @see \SimpleXMLElement::xpath
     *
     * @param string $path An XPath path
     * @return SimpleXMLElement[]|bool an array of SimpleXMLElement objects or FALSE in case of an error.
     */
    public function xpath($path)
    {
        $elements = $this->simpleXMLElement->xpath($path);

        if ($elements !== false) {
            $wrappedElements = [];
            foreach ($elements as $element) {
                $wrappedElement = $this->wrapSimpleXMLElement($element);

                if ($wrappedElement !== null) {
                    $wrappedElements[] = $this->wrapSimpleXMLElement($element);
                }
            }

            $elements = $wrappedElements;
        }

        return $elements;
    }

    /**
     * Wraps the given element into an instance of the wrapper
     *
     * @param \SimpleXMLElement $element Element to be wrapped
     * @return SimpleXMLElement|null The wrapped element or NULL if the given element is invalid
     */
    protected function wrapSimpleXMLElement(\SimpleXMLElement $element)
    {
        $wrappedElement = null;
        $elementAsXML = $element->asXML();

        if ($elementAsXML !== false) {
            $wrappedElement = new SimpleXMLElement($elementAsXML);
        }

        return $wrappedElement;
    }

    /**
     * Remove all nodes matching the given XPath query.
     * It does not map to any \SimpleXMLElement function.
     *
     * @param string $path An XPath path
     * @return void
     */
    public function removeNodesMatchingXPath($path)
    {
        $nodesToRemove = $this->simpleXMLElement->xpath($path);

        foreach ($nodesToRemove as $nodeToRemove) {
            unset($nodeToRemove[0]);
        }
    }

    /**
     * Returns the first child matching the given tag name
     *
     * @param string $tagName
     * @return SimpleXMLElement|null The first child matching the tag name or NULL if none found
     */
    public function getFirstChildByTagName($tagName)
    {
        $doesElementExist = isset($this->simpleXMLElement->{$tagName});

        /** @var \SimpleXMLElement $realElement */
        $realElement = $this->simpleXMLElement->{$tagName};

        return $doesElementExist ? $this->wrapSimpleXMLElement($realElement) : null;
    }

    /**
     * Returns the immediate children.
     *
     * @return array The children
     */
    public function children()
    {
        $children = [];

        foreach ($this->simpleXMLElement->children() as $child) {
            $children[] = $this->wrapSimpleXMLElement($child);
        }

        return $children;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->simpleXMLElement->__toString();
    }
}
