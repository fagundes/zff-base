<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace ZffTest\Base\Util\Assets;

use Zff\Base\Util\ConfiguratorTrait;

/**
 * Class ConfigurableClass
 * @package ZffTest\Base\Util\Assets
 */
class ConfigurableClass
{
    use ConfiguratorTrait;

    protected $x;

    protected $y;

    protected $z;

    /**
     * ConfigurableClass constructor.
     *
     * @param array $options
     *
     * @throws \Exception
     */
    public function __construct(array $options = [])
    {
        $this->configure($options);
    }


    /**
     * @return mixed
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param mixed $x
     *
     * @return ConfigurableClass
     */
    public function setX($x)
    {
        $this->x = $x;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param mixed $y
     *
     * @return ConfigurableClass
     */
    public function setY($y)
    {
        $this->y = $y;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getZ()
    {
        return $this->z;
    }

    /**
     * @param mixed $z
     *
     * @return ConfigurableClass
     */
    public function setZ($z)
    {
        $this->z = $z;
        return $this;
    }
}
