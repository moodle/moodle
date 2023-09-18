<?php

declare(strict_types=1);

namespace OpenSpout\Writer\Common\Manager\Style;

use OpenSpout\Common\Entity\Style\Style;

/**
 * @internal
 */
abstract class AbstractStyleRegistry
{
    /** @var array<string, int> [SERIALIZED_STYLE] => [STYLE_ID] mapping table, keeping track of the registered styles */
    private array $serializedStyleToStyleIdMappingTable = [];

    /** @var array<int, Style> [STYLE_ID] => [STYLE] mapping table, keeping track of the registered styles */
    private array $styleIdToStyleMappingTable = [];

    public function __construct(Style $defaultStyle)
    {
        // This ensures that the default style is the first one to be registered
        $this->registerStyle($defaultStyle);
    }

    /**
     * Registers the given style as a used style.
     * Duplicate styles won't be registered more than once.
     *
     * @param Style $style The style to be registered
     *
     * @return Style the registered style, updated with an internal ID
     */
    public function registerStyle(Style $style): Style
    {
        $serializedStyle = $this->serialize($style);

        if (!$this->hasSerializedStyleAlreadyBeenRegistered($serializedStyle)) {
            $nextStyleId = \count($this->serializedStyleToStyleIdMappingTable);
            $style->markAsRegistered($nextStyleId);

            $this->serializedStyleToStyleIdMappingTable[$serializedStyle] = $nextStyleId;
            $this->styleIdToStyleMappingTable[$nextStyleId] = $style;
        }

        return $this->getStyleFromSerializedStyle($serializedStyle);
    }

    /**
     * @return Style[] List of registered styles
     */
    final public function getRegisteredStyles(): array
    {
        return array_values($this->styleIdToStyleMappingTable);
    }

    final public function getStyleFromStyleId(int $styleId): Style
    {
        return $this->styleIdToStyleMappingTable[$styleId];
    }

    /**
     * Serializes the style for future comparison with other styles.
     * The ID is excluded from the comparison, as we only care about
     * actual style properties.
     *
     * @return string The serialized style
     */
    final public function serialize(Style $style): string
    {
        return serialize($style);
    }

    /**
     * Returns whether the serialized style has already been registered.
     *
     * @param string $serializedStyle The serialized style
     */
    private function hasSerializedStyleAlreadyBeenRegistered(string $serializedStyle): bool
    {
        // Using isset here because it is way faster than array_key_exists...
        return isset($this->serializedStyleToStyleIdMappingTable[$serializedStyle]);
    }

    /**
     * Returns the registered style associated to the given serialization.
     *
     * @param string $serializedStyle The serialized style from which the actual style should be fetched from
     */
    private function getStyleFromSerializedStyle(string $serializedStyle): Style
    {
        $styleId = $this->serializedStyleToStyleIdMappingTable[$serializedStyle];

        return $this->styleIdToStyleMappingTable[$styleId];
    }
}
