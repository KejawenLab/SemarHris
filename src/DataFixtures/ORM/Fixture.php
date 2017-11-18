<?php

namespace KejawenLab\Application\SemartHris\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture as Base;
use Doctrine\Common\Persistence\ObjectManager;
use KejawenLab\Application\SemartHris\Component\User\Model\UserInterface;
use KejawenLab\Application\SemartHris\Util\SettingUtil;
use KejawenLab\Application\SemartHris\Util\StringUtil;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@kejawenlab.com>
 */
abstract class Fixture extends Base
{
    const REF_KEY = 'ref';

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    /**
     * @return string
     */
    abstract protected function getFixtureFilePath(): string;

    /**
     * @return mixed
     */
    abstract protected function createNew();

    /**
     * @return string
     */
    abstract protected function getReferenceKey(): string;

    public function load(ObjectManager $manager)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($this->getData() as $data) {
            $entity = $this->createNew();
            foreach ($data as $key => $value) {
                if (self::REF_KEY === $key) {
                    $this->setReference(StringUtil::uppercase(sprintf('%s#%s', $this->getReferenceKey(), $value)), $entity);
                } else {
                    if (false !== strpos($value, self::REF_KEY)) {
                        $value = $this->getReference(StringUtil::uppercase(str_replace('ref:', '', $value)));
                    }

                    if (is_string($value) && false !== strpos($value, 'date:')) {
                        $value = \DateTime::createFromFormat(SettingUtil::get(SettingUtil::DATE_FORMAT), str_replace('date:', '', $value));
                    }

                    if (is_string($value) && false !== strpos($value, 'year')) {
                        $value = \DateTime::createFromFormat(SettingUtil::get(SettingUtil::DATE_FORMAT), sprintf('%s-%s', str_replace('year:', '', $value), date('Y')));
                    }

                    if (is_string($value) && false !== strpos($value, 'hour')) {
                        $value = \DateTime::createFromFormat(SettingUtil::get(SettingUtil::HOUR_FORMAT), str_replace('hour:', '', $value));
                    }

                    $accessor->setValue($entity, $key, $value);
                }
            }

            $manager->persist($entity);

            if ($entity instanceof UserInterface) {
                $this->output->writeln('<info>User baru telah dibuat!!!</info>');
                $this->output->writeln(sprintf('<comment>Username: %s</comment>', $entity->getUsername()));
                $this->output->writeln(sprintf('<comment>Password: %s</comment>', SettingUtil::get(SettingUtil::DEFAULT_PASSWORD)));
            }
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    protected function getData(): array
    {
        $path = sprintf('%s/data/%s', $this->container->getParameter('kernel.project_dir'), $this->getFixtureFilePath());

        return Yaml::parse(file_get_contents($path));
    }
}
