<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        for($i = 1; $i<30; $i ++){
            $ad = new Ad();
                $title = $faker->sentence(6);
                $CoverImage = $faker->imageUrl(1000, 350);
                $Introduction = $faker->paragraph(2);
                $Content = '<p>'. join('</p><p>', $faker->paragraphs(5)). '</p>';

            $ad->setTitle($title)
                ->setCoverImage($CoverImage)
                ->setIntroduction($Introduction)
                ->setContent($Content)
                ->setPrice(mt_rand(40,350))
                ->setRooms(mt_rand(1,6));

            for($j = 1; $j<= mt_rand(2, 5); $j++){
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);

                $manager->persist($image);

            }
            
            $manager->persist($ad);
        }
        $manager->flush();
    }
}
