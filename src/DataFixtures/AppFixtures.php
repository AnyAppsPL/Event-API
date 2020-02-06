<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const NUMBER_OF_FAKE_USERS = 10;
    private const NUMBER_OF_FAKE_COMPANIES = 10;
    private const NUMBER_OF_FAKE_EVENTS = 10;


    public Generator $faker;
    /**
     * @var UserPasswordEncoder
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->faker = Factory::create();
        $this->encoder = $encoder;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
        $this->loadCompany($manager);
        $this->loadEvent($manager);

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadUser(ObjectManager $manager): void
    {

        for ($i = 0; $i < self::NUMBER_OF_FAKE_USERS; $i++) {
            $user = new User();
            $user
                ->setUsername($this->faker->userName)
                ->setEmail($this->faker->email)
                ->setPassword($this->encoder->encodePassword($user, 'Jakieshaslo!2'))
                ->setFirstName($this->faker->firstName)
                ->setLastName($this->faker->lastName)
                ->setCreatedAt($this->faker->dateTime);

            $manager->persist($user);

            $this->addReference('user_' . $i, $user);
        }
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadCompany(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::NUMBER_OF_FAKE_COMPANIES; $i++) {

            /** @var User $user */
            $user = $this->getReference('user_' . $i);

            $company = new Company();

            $company
                ->setName($this->faker->company)
                ->setCity($this->faker->city)
                ->setPhone($this->faker->phoneNumber)
                ->setAddress($this->faker->address)
                ->addEmployee($user);

            $manager->persist($company);

            $this->addReference('company_' . $i, $company);
        }
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadEvent(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::NUMBER_OF_FAKE_EVENTS; $i++) {

            /** @var User $user */
            $user = $this->getReference('user_' . $i);

            /** @var \DateTime $fakeDateTime */
            $fakeDateTime = $this->faker->dateTime;

            $event = new Event();
            $event
                ->setName($this->faker->name . ' ' . $this->faker->company . ' event')
                ->setCreatedAt($fakeDateTime)
                ->setDescription('Lorem Ipsum is simply dummy text of the printing and typesetting industry')
                ->setHappenedAt($fakeDateTime)
                ->setCreatedBy($user);

            $manager->persist($event);
        }
    }
}
