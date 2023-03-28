<?php

namespace Contentify;

/**
 * Simple helper class that allows to declare text content as raw content,
 * which means it should not be escaped with htmlentities() to allow use of HTML code.
 *
 * ATTENTION: Use with care, as this might allow XSS attacks!
 */
class Raw
{

    /**
     * The value to print
     *
     * @var mixed
     */
    protected $value;

    /**
     * Constructor call
     * 
     * @param mixed $value       The value to print
     * @param mixed $escapeValue Another value that's going to be auto escaped
     */
    public function __construct($value, $escapeValue = null)
    {
        if ($escapeValue !== null) {
            $this->value = (string) $value.e($escapeValue);
        } else {
            $this->value = (string) $value;
        }
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
