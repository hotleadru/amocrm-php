<?php

namespace AmoCRM\Events;

use AmoCRM\Models\AbstractModel;
use ArrayAccess;

abstract class AbstractEvent extends AbstractModel implements ArrayAccess, EventInterface
{

}
