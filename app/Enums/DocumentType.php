<?php

namespace App\Enums;

/**
 * @method static static DrivingLicense()
 * @method static static SssID()
 * @method static static TinID()
 * @method static static VoteID()
 * @method static static UMID()
 */
final class DocumentType extends Enum
{
    const DrivingLicense = 0;
    const SssID = 1;
    const TinID = 2;
    const VoteID = 3;
    const UMID = 4;

    public static function getDescription($value): string
    {
        switch ($value) {
            case self::DrivingLicense:
                return 'Driving License';
                break;
            case self::SssID:
                return 'SSS ID';
                break;
            case self::TinID:
                return 'TIN ID';
                break;
            case self::VoteID:
                return 'Voter ID';
                break;
            case self::UMID:
                return 'UMID';
                break;
            default:
                return self::getKey($value);
        }
    }
}
