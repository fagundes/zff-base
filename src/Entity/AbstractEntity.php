<?php

/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base\Entity;

use Zff\Base\Util\ConfiguratorTrait;
use Zff\Base\Util\FileMoveTrait;

/**
 * Abstract Entity
 *
 * @package ZffBase
 * @subpackage ZffBase_Model
 */
class AbstractEntity
{
    use ConfiguratorTrait;

    public function __construct($data = [])
    {
        $this->configure($data);
    }

    public function exchangeArray($data)
    {
        $this->configure($data);
    }
}
