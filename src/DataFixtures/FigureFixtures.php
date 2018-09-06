<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Figure;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Visual;

class FigureFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // 4 fake categories
        for($i = 1; $i <= 4; $i++)
        {
        	$category = new Category();
        	$category->setName($faker->word())
        			 ->setDescription($faker->paragraph());

        	$manager->persist($category);

        	// between 5 & 8 fake figures
        	for($j = 1; $j <= mt_rand(5, 8); $j++)
        	{
        		$content = '<p>';
        		$content .= join($faker->paragraphs(3), '</p><p>');
        		$content .= '</p>';

        		$figure = new Figure();
        		$figure->setTitle($faker->word)
        			   ->setContent($content)
        			   ->setCreatedAt($faker->dateTimeBetween('-6 months'))
        			   ->setCategory($category);
        		
        		$manager->persist($figure);

        		//Comments of figure
        		for($k = 1; $k <= mt_rand(2,5); $k++)
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
				
				//Visuals of figure
        		for($l = 1; $l <= mt_rand(4, 6); $l++)
        		{
        			$description = $faker->paragraph();
        			$now = new \DateTime();
        			$interval = $now->diff($figure->getCreatedAt());
        			$days = $interval->days;
					$minimum = '-' . $days . ' days';
					
					$rand = mt_rand(0, 1);
					if($rand == 0){$type = 'image';}
					else{$type = 'video';}


        			$visual = new Visual();
        			$visual->setTitle($faker->word)
							->setDescription($description)
							->setUrl($faker->imageUrl())
							->setType($type)
        					->setAddedAt($faker->dateTimeBetween($minimum))
        					->setFigure($figure);
        					
        			$manager->persist($visual);
        		}
        	}
        }

        $manager->flush();
    }
}
