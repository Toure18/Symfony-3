<?php



namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use  Faker;


class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const Actor = [
        'andrew-lincoln',
        'norman-reedus',
        'lauren-cohan',
        'danai-gurira'
    ];

    public function getDependencies()

    {
        return [ProgramFixtures::class];
    }


    public function load(ObjectManager $manager)
    {

        $faker  =  Faker\Factory::create('fr_FR');

        for ($i = 0; $i <= 50; $i++){
            $randNumber = rand(0, 5);
            $actor = new Actor();
            $actor->setName($faker->name);
            $actor->addProgram($this->getReference("program_$randNumber"));
            $manager->persist($actor);
        }
        $manager->flush();
    }
}