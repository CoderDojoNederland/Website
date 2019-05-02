<?php

declare(strict_types=1);

namespace CoderDojo\WebsiteBundle\Service;

use Carbon\Carbon;
use CoderDojo\WebsiteBundle\Entity\Club100;

class NextDonationFinder
{
    private const DONATION_MONTHS = [
        Club100::INTERVAL_YEARLY => [6],
        Club100::INTERVAL_SEMI_YEARLY => [4, 10],
        Club100::INTERVAL_QUARTERLY => [1, 4, 7, 10]
    ];

    public const FIRST_DONATION_THRESHOLD = [
        Club100::INTERVAL_YEARLY => 183, // 365 / 2,
        Club100::INTERVAL_SEMI_YEARLY => 91, // 365 / 2 / 2,
        Club100::INTERVAL_QUARTERLY => 46, // 365 / 4 / 2
    ];

    /**
     * @param Club100 $member
     *
     * @return \DateTime
     */
    public static function findNextDonation(Club100 $member): \DateTime
    {
        $interval = $member->getInterval();
        $schedule = self::DONATION_MONTHS[$interval];

        $today = Carbon::today();
        $currentMonth = $today->month;
        $currentYear = $today->year;

        $nextYear = $currentYear;
        $nextMonth = null;

        foreach($schedule as $month) {
            if ($month > $currentMonth) {
                $nextMonth = $month;
                break;
            }
        }

        if ($nextMonth === null) {
            $nextMonth = $schedule[0];
            $nextYear++;
        }

        return Carbon::create($nextYear, $nextMonth, 1)->startOfDay();
    }

    /**
     * @param Club100 $member
     *
     * @return bool
     */
    public static function shouldSendFirstRequest(Club100 $member): bool
    {
        $next = self::findNextDonation($member);
        $diff = $next->diff(Carbon::today()->startOfDay());

        return $diff->days > self::FIRST_DONATION_THRESHOLD[$member->getInterval()];
    }
}
