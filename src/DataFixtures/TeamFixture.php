<?php
namespace App\DataFixtures;
use App\Entity\Offer;
use App\Entity\Player;
use App\Entity\Team;
use App\Entity\Transaction;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class TeamFixture extends Fixture
{
    private ObjectManager $privateManager;
    private Generator $faker;

    public function __construct() {

        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->privateManager = $manager;
        for ($i = 0; $i < 50; $i++) {
            $team = $this->getQuote();
            $manager->persist($team);
            $transaction = new Transaction();
            $transaction->setAmount(
                $this->faker->randomFloat(min:1)
            );
            $transaction->setType("Opening Balance");
            $transaction->setTeam($team);
            $transaction->setCreatedAt(new DateTime());
            $manager->persist($transaction);
        }
        $manager->flush();

    }

    private function getQuote(): Team
    {
        $team = new Team(
            $this->faker->name(),
            $this->faker->country()
        );

        for ($i = 0; $i < $this->faker->randomElement([8,9,10,12,11]); $i++)
        {
            $player = new Player(
                $this->faker->firstName(gender: "male"),
                $this->faker->lastName()
            );
            $this->privateManager->persist($player);


            if($this->faker->randomElement([1,2,3,4]) == 3)
            {
                $offer = new Offer();
                $offer->setPrice($this->faker->randomNumber());
                $offer->setPlayer($player);
                $offer->setCreatedAt(  new DateTime());
                $this->privateManager->persist($offer);
            }


            $team->addPlayer($player);
        }
        return $team;
    }
}
