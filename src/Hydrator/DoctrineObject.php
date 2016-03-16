<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */
namespace Zff\Base\Hydrator;

use DateTime;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as StdDoctrineHydrator;

/**
 * Improve doctrine date conversion
 *
 * @author Vinicius Fagundes <mvlacerda@gmail.com>
 */
class DoctrineObject extends StdDoctrineHydrator
{
    /**
     * Format to conversion dates
     * @var string
     */
    protected $dateFormat;

    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * Handle various type conversions that should be supported natively by Doctrine (like DateTime)
     *
     * @param  mixed  $value
     * @param  string $typeOfField
     * @return DateTime
     */
    protected function handleTypeConversions($value, $typeOfField)
    {
        switch ($typeOfField) {
            case 'datetimetz':
            case 'datetime':
            case 'time':
            case 'date':
                if ('' === $value) {
                    return null;
                }

                if (is_int($value)) {
                    $dateTime = new DateTime();
                    $dateTime->setTimestamp($value);
                    $value = $dateTime;
                } elseif (is_string($value)) {
                    $value = $this->getDateFormat() ?
                        \DateTime::createFromFormat($this->getDateFormat(), $value) :
                        new DateTime($value);
                }

                break;
            default:
        }

        return $value;
    }
}
