<?php

namespace Application\Validator;

use InvalidArgumentException;
use Zend\Validator\AbstractValidator;

class MultipleInArray extends AbstractValidator
{

    // Validation failure message IDs.
    public const VALUE_IS_NOT_ARRAY = 'valueIsNotArray';
    public const VALUE_IS_NOT_IN_HAYSTACK = 'valueIsNotInHaystack';

    /**
     * Available validator options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::VALUE_IS_NOT_ARRAY => 'The value is not array',
        self::VALUE_IS_NOT_IN_HAYSTACK => 'The value is not in haystack',
    ];

    /**
     * @param array $options
     *
     * @return AbstractValidator
     */
    public function setOptions($options = [])
    {
        if (isset($options['haystack'])) {
            $this->setHaystack($options['haystack']);
        } else {
            throw new InvalidArgumentException(
                'Missing option array "haystack"'
            );
        }

        return parent::setOptions($options);
    }

    /**
     * @param array $haystack
     */
    public function setHaystack(array $haystack)
    {
        $this->options['haystack'] = $haystack;
    }

    /**
     * @return array
     */
    public function getHaystack()
    {
        return $this->options['haystack'];
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        if (!\is_array($value)) {
            $this->error(self::VALUE_IS_NOT_ARRAY);
            return false;
        }

        $haystack = $this->options['haystack'];

        foreach ($value as $needle) {
            if (!\in_array($needle, $haystack, false)) {
                $this->error(self::VALUE_IS_NOT_IN_HAYSTACK);
                return false;
            }
        }

        return true;
    }
}
