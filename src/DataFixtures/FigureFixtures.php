<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Figure;
use App\Entity\Category;
use App\Entity\Comment;

class FigureFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // 6 fake categories
        for($i = 1; $i <= 6; $i++)
        {
        	$category = new Category();
        	$category->setName($faker->word())
        			 ->setDescription($faker->paragraph());

        	$manager->persist($category);

        	// between 16 & 24 fake figures
        	for($j = 1; $j <= mt_rand(16, 24); $j++)
        	{
        		$content = '<p>';
        		$content .= join($faker->paragraphs(3), '</p><p>');
        		$content .= '</p>';

        		$figure = new Figure();
        		$figure->setTitle($faker->word)
        			   ->setContent($content)
        			   ->setImage($faker->imageUrl())
        			   ->setCreatedAt($faker->dateTimeBetween('-6 months'))
        			   ->setCategory($category);
        		
        		$manager->persist($figure);

        		//Comment of figure
        		for($k = 1; $k <= mt_rand(4,10); $k++)
        		{
        			$content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';
        			$now = new \DateTime();
        			$interval = $now->diff($figure->getCreatedAt());
        			$days = $interval->days;
        			$minimum = '-' . $days . ' days';


        			$comment = new Comment();
        			$comment->setAuthor($faker->name)
        					->setContent($content)
        					->setCreatedAt($faker->dateTimeBetween($minimum))
        					->setFigure($figure);
        					
        			$manager->persist($comment);
        		}
        	}
        }

        $manager->flush();
    }
}
