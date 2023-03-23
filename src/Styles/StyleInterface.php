<?php

declare(strict_types=1);

namespace Eclipxe\XlsxExporter\Styles;

/**
 * Interface StyleInterface
 */
interface StyleInterface
{
    public function setIndex(int $index): void;

    public function getIndex(): ?int;

    public function hasValues(): bool;

    /**
     * Set all values from the key-value array
     *
     * @param array<string, scalar|null> $values
     */
    public function setValues(array $values): void;

    /**
     * Return a key-values array with the properties of the class and its values
     *
     * @return array<string, scalar|null>
     */
    public function getValues(): array;

    /**
     * Get a string that represent the object,
     * two objects with the same data must retrieve the same hash
     */
    public function getHash(): string;

    /**
     * Get the object as xml element
     */
    public function asXML(): string;
}
