<?php

namespace Kucbel\Scalar\Iterator;

use Iterator;
use Kucbel\Scalar\Validator\ValidatorInterface;

interface IteratorInterface extends ValidatorInterface, Iterator
{

}