<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService
{

    private $manager;

    public function __construct(EntityManagerInterface $Entitymanager)
    {
        $this->manager = $Entitymanager;
    }

    public function getStats()
    {
        $users = $this->getCountUsers();
        $ads = $this->getCountAds();
        $bookings = $this->getCountBookings();
        $comments = $this->getCountComments();

        return compact('users', 'ads', 'bookings', 'comments');
    }

    public function getAverageAds($order)
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
             FROM App\Entity\Comment c
             JOIN c.ad a
             JOIN a.author u
             GROUP BY a
             ORDER BY note ' . $order
        )->setMaxResults(5)
            ->getResult();
    }

    private function getCountUsers()
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    private function getCountAds()
    {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    private function getCountBookings()
    {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }

    private function getCountComments()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }
}
