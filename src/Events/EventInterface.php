<?php

namespace AmoCRM\Events;

interface EventInterface
{
    /**
     * @param array $options
     * @return array
     */
    public function send($options);
}
