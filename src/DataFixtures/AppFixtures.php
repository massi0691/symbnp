<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('FR-fr');
        $roleAdmin = new Role();
        $roleAdmin->setTitle('ROLE_ADMIN');
        $manager->persist($roleAdmin);
        $adminUser = new User();
        $adminUser->setFirstName('Massinissa')
            ->setLastName('AIT SALAH')
            ->setEmail('massi9106@live.fr')
            ->setHash($this->hasher->hashPassword($adminUser, 'password'))
            ->setPicture('https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/6b/6b5f4a1c76831ddfd11a884b4049dc2c22d855e1_full.jpg')
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>' . join('</p></p>', $faker->paragraphs(3)) . '</p>')
            ->addUserRole($roleAdmin);

        $manager->persist($adminUser);

        // manage users
        $users = [];
        $genres = ['male', 'female'];
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $pic = "https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';
            if ($genre === "male") {
                $pic = $pic . 'men/' . $pictureId;
            } else {
                $pic = $pic . 'women/' . $pictureId;
            }
            $hash = $this->hasher->hashPassword($user, 'password');
            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName())
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence)
                ->setDescription('<p>' . join('</p></p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($hash)
                ->setPicture($pic);
            $manager->persist($user);
            $users[] = $user;
        }

        // manage ads
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();
            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p></p>', $faker->paragraphs(5)) . '</p>';
            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($users[mt_rand(0, sizeof($users) - 1)]);

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);
            }

            // Manage reservation

            for ($k = 1; $k <= mt_rand(0, 10); $k++) {
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');

                // Manage the end date
                $duration = mt_rand(3, 10);
                $endDate = (clone $startDate)->modify("+$duration days");

                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) - 1)];
                $comment = $faker->paragraph();

                $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setCreatedAt($createdAt)
                    ->setAmount($amount)
                    ->setComment($comment)
                ;


                $manager->persist($booking);
            }
            $manager->persist($ad);
        }

        $manager->flush();
    }
}
