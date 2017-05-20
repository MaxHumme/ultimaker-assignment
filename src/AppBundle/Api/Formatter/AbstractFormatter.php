<?php
namespace AppBundle\Api\Formatter;

/**
 * Abstract class AbstractFormatter
 *
 * We use formatters to format our entities into an associative array, which we can use as input for our Json response.
 *
 * @author Max Humme <max@humme.nl>
 */
abstract class AbstractFormatter
{
    /**
     * Renders the formatter and returns output in associative array form.
     *
     * @return mixed[]
     */
    abstract public function render(): array;
}
