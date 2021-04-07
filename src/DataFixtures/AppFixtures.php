<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        //création d'utilisateur

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Test')
            ->setLastName('Sym')
            ->setEmail('test@sym.com')
            ->setHash($this->encoder->encodePassword($adminUser, 'pass'))
            ->setIntroduction("Salut c'est moi le créateur du site je vous souhaite la bienvenue!")
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(2)) . '</p>')
            ->addUserRole($adminRole);
        $manager->persist($adminUser);



        $users = [];
        $genres = ['male', 'female'];

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $description = '<p>' . join('</p><p>', $faker->paragraphs(2)) . '</p>';

            $genre = $faker->numberBetween(1, 99);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');


            $user->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription($description)
                ->setHash($hash);

            $manager->persist($user);
            $users[] = $user;
        }

        // creation d'annonce 
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            //$coverImage = "https://picsum.photos/1000/350?random=" . $i;
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            $userAd = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($userAd);

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();
                $js = $j + 500;
                $image->setUrl("https://picsum.photos/750/550?random=" . $js)
                    ->setCaption($faker->sentence())
                    ->setAd($ad);

                $manager->persist($image);
            }

            for ($k = 0; $k < mt_rand(0, 10); $k++) {
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-8 months');
                $startDate = $faker->dateTimeBetween('-5 month');
                $duration = mt_rand(3, 10);
                $endDate = (clone $startDate)->modify("+$duration days");
                $amout = $ad->getPrice() * $duration;

                $booker = $users[mt_rand(0, count($users) - 1)];

                $booking->setBooker($booker)
                    ->setCreatedAt($createdAt)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setAmount($amout)
                    ->setComment($faker->paragraph())
                    ->setAd($ad);

                $manager->persist($booking);

                if (mt_rand(0, 1)) {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                        ->setRating(mt_rand(1, 5))
                        ->setAuthor($booker)
                        ->setAd($ad);

                    $manager->persist($comment);
                }
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
