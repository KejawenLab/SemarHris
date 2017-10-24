<?php

namespace KejawenLab\Application\SemartHris\Component\Holiday\Model;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@kejawenlab.com>
 */
interface HolidayInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return \DateTimeInterface
     */
    public function getHolidayDate(): \DateTimeInterface;
}
