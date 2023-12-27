<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle("The Dark Knight");
        $movie->setReleaseYear(2008);
        $movie->setDescription("Batman");
        $movie->setImagePath("https://cdn1.epicgames.com/undefined/offer/batman-arkham-knight_promo-2048x1152-ed2be22b3f24f446534b90b122ed560d.jpg");
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));
        $manager->persist($movie);

        $movie2 = new Movie();
        $movie2->setTitle("Avengers: Endgame");
        $movie2->setReleaseYear(2019);
        $movie2->setDescription("Avengers");
        $movie2->setImagePath("https://static.posters.cz/image/750/plotno-avengers-endgame-from-the-ashes-i80989.jpg");
        $movie2->addActor($this->getReference('actor_3'));
        $movie2->addActor($this->getReference('actor_4'));
        $manager->persist($movie2);

        $manager->flush();
    }
}
