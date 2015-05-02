<?php

namespace app\models\exceptions;

class FormError extends ExceptionBase
{
    /** @var \string[] */
    protected $messages;

    /**
     * @param string|string[] $messages
     */
    public function __construct($messages)
    {
        if (is_array($messages)) {
            $this->messages = $messages;
        } else {
            $this->messages = [$messages];
        }
        parent::__construct(implode("\n", $this->messages));
    }

    /**
     * @return string[]
     */
    public function getMessages()
    {
        return $this->messages;
    }
}