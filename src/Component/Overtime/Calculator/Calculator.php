<?php

namespace KejawenLab\Application\SemartHris\Component\Overtime\Calculator;

use KejawenLab\Application\SemartHris\Component\Overtime\Model\OvertimeCalculatorInterface;
use KejawenLab\Application\SemartHris\Component\Overtime\Model\OvertimeInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@kejawenlab.com>
 */
abstract class Calculator implements OvertimeCalculatorInterface
{
    /**
     * @var int
     */
    protected $workday;

    /**
     * @param int $workday
     */
    public function setWorkdayPerWeek(int $workday): void
    {
        $this->workday = $workday;
    }

    /**
     * @param OvertimeInterface $overtime
     *
     * @return float
     */
    protected function getOvertimeHours(OvertimeInterface $overtime): float
    {
        /** @var \DateTime $endHour */
        $endHour = $overtime->getEndHour();
        $startHour = $overtime->getStartHour();
        if ($endHour < $startHour) {
            $endHour->add(new \DateInterval('P1D'));
            $overtime->setOverday(true);
        }

        $delta = $overtime->getEndHour()->diff($startHour, true);
        $hours = $delta->h;
        $minutes = $delta->i;
        if (15 < $minutes) {//Minute adjustment
            if (15 < $minutes && 45 >= $minutes) {
                $hours += 0.5;
            } else {
                ++$hours;
            }
        }

        return $this->breakSub((float) $hours);
    }

    /**
     * @param float $hours
     *
     * @return float
     */
    private function breakSub(float $hours): float
    {
        $realHours = $hours;
        $flag = true;
        while ($flag) {
            if (4 <= $hours) {
                $realHours -= 0.5;
                $hours -= 4;

                $this->breakSub($hours);
            } else {
                $flag = false;
            }
        }

        return $realHours;
    }
}
