<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    /**
     * Permet de pouvoir utiliser un objet encoder qui n'est pas passable en injection de param de load()
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder){

        $this->encoder =$encoder;
    }

    public function load(ObjectManager $manager)
    {
       
        $faker=Factory::create("fr-FR");
        
        //Nous gérons les roles
        $adminRole = New Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser =new User();
        
        $adminUser->setFirstName('Maria')
                  ->setLastName('Chi')
                  ->setEmail('maria@chi.com')
                  ->setIntroduction($faker->sentence())
                  ->setDescription("<p>".join("</p> <p>", $faker->paragraphs(3)). "</p>")
                  ->setHash($this->encoder->encodePassword($adminUser,"password"))
                  ->setPicture("https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQuHA0rlzSE3yk5Yy1zoPgu6VofvKT55zaBaw&usqp=CAU")
                  ->addUserRole($adminRole); 
        $manager->persist($adminUser);

        
        
        $usersTab = [];
        $genreTab = ['male','female'];


         //Nous gérons les utilisateurs
            for($i=0; $i<=10; $i++){

                $user = new User();

                $genre= $faker->randomElement($genreTab);

                $picture = "https://randomuser.me/api/portraits/";
                $pictureId = $faker->numberBetween(0,99).'.jpg';
                
                $picture = $picture . ($genre == 'male' ? 'men/' : 'women/') . $pictureId;
              
                $hash = $this->encoder->encodePassword($user,'password');

                $user->setPicture($picture);
                $user->setFirstName($faker->firstName($genre));
                $user->setLastName($faker->lastName($genre));
                $user->setEmail($faker->email);
                $user->setIntroduction($faker->sentence(2));
                $user->setDescription("<p>".join("</p> <p>", $faker->paragraphs(3)). "</p>");
                $user->setHash($hash);
                
                $manager->persist($user);
                $usersTab[]=$user;
            }

         //Nous gérons les annonces
            for($i=0; $i<=30; $i++){

                $ad = new Ad();

                $title=$faker->sentence();
                $coverIMage=$faker->imageUrl(1000,350);
                $introduction=$faker->paragraph(2);
                $content= "<p>".join("</p> <p>", $faker->paragraphs(5)). "</p>";

                $ad->setTitle($title);
                $ad->setPrice(mt_rand(40, 200));
                $ad->setIntroduction($introduction);
                $ad->setContent($content);
                $ad->setCoverImage($coverIMage);
                $ad->setRooms(mt_rand(1,5));
                
                $adUser= $usersTab[mt_rand(0,count($usersTab)-1)];

                $ad->setAuthor($adUser);

                $manager->persist($ad);

                for($j=1; $j<=mt_rand(2,6); $j++){

                    $image = new Image();
                    $image->setAd($ad);
                    $image->setCaption($faker->sentence());
                    $image->setUrl($faker->imageUrl());

                    $manager->persist($image);
                }
                
                //Gestion des réservations
                /*for($k=1; $k<=mt_rand(2,5); $k++){

                    $booking = new Booking();

                    $createdAt = $faker->dateTimeBetween('-6 months');
                    $startDate = $faker->dateTimeBetween('-3 months');

                    $duration = mt_rand(3,10);
                    $enDate = (clone $startDate)->modify('+'.$duration.' day');

                    $amount = $ad->getPrice() * $duration;
                    $booker = $usersTab[mt_rand(0,count($usersTab)-1)];

                    $comment = $faker->paragraph();

                    $booking->setCreatedAt($createdAt);
                    $booking->setStartDate($startDate);
                    $booking->setEndDate($enDate);

                    $booking->setAmount($amount);
                    $booking->setAd($ad);
                    $booking->setBooker($booker);
                    $booking->setComment($comment);

                    $manager->persist($booking);
                }
               */
            }
         $manager->flush();
    }
}
