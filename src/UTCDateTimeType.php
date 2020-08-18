<?php
namespace Lamansky\Doctrine;
use Carbon\Carbon;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Translates Carbon objects to UTC when persisted to the database, and
 * converts them back to the local PHP timezone when retrieved.
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/working-with-datetime.html
 */
class UTCDateTimeType extends DateTimeType {
    private static $utc;
    private static $local;

    protected static function getUtcTimezone () : \DateTimeZone {
        return self::$utc
            ? self::$utc
            : self::$utc = new \DateTimeZone('UTC');
    }

    protected static function getLocalTimezone () : \DateTimeZone {
        return self::$local
            ? self::$local
            : self::$local = new \DateTimeZone(date_default_timezone_get());
    }

    /**
     * @return mixed
     */
    public function convertToDatabaseValue ($value, AbstractPlatform $platform) {
        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtcTimezone());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * @throws \Doctrine\DBAL\Types\ConversionException
     * @return mixed
     */
    public function convertToPHPValue ($value, AbstractPlatform $platform) {
        if ($value === null || $value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTime) {
            return Carbon::instance($value);
        }

        $converted = Carbon::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::getUtcTimezone()
        );

        if (!$converted) {
            throw \Doctrine\DBAL\Types\ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        $converted->setTimezone(self::getLocalTimezone());

        return $converted;
    }
}
