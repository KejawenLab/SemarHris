<?php

namespace KejawenLab\Application\SemartHris\Component\Skill\Model;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@kejawenlab.com>
 */
interface SkillGroupInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return null|SkillGroupInterface
     */
    public function getParent(): ? self;

    /**
     * @param SkillGroupInterface|null $group
     */
    public function setParent(?self $group): void;

    /**
     * @return string
     */
    public function getName(): string;
}
