<?php

namespace App\Enums;

/**
 * @method static static Silent()
 * @method static static Action()
 */
final class LivenessType extends Enum
{
    const Silent =   'SILENT';
    const Action =   'ACTION';

    public static function getDescription($value): string
    {
        switch ($value) {
            case self::Silent:
                return 'Silent';
                break;
            case self::Action:
                return 'Action';
                break;
            default:
                return self::getKey($value);
        }
    }
}
