<?php

namespace App\DataFixtures;

use App\Entity\Visual;
use App\Entity\Figure;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

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

        	// between 6 & 8 fake figures
        	for($j = 1; $j <= mt_rand(6, 8); $j++)
        	{
        		$content = '<p>';
        		$content .= join($faker->paragraphs(3), '</p><p>');
				$content .= '</p>';

				$nb = $faker->numberBetween($min = 1, $max = 3);
				$title = $faker->sentence($nbWords = $nb, $variableNbWords = true);
				$title = str_replace('.', '', $title);
				
				$slug = str_replace(' ', '-', $title);
				$slug = str_replace('\'', '-', $slug);
				
        		$figure = new Figure();
        		$figure->setTitle($title)
        			   ->setContent($content)
        			   ->setCreatedAt($faker->dateTimeBetween('-6 months'))
					   ->setCategory($category)
					   ->setSlug($slug);
        		
				$manager->persist($figure);

				//Visuals of figure
				for($m = 1; $m <= mt_rand(2,5); $m++)
				{
					$visual = new Visual();
					
					$visual->setUrl($faker->visualUrl())
						  ->setCaption($faker->sentence())
						  ->setFigure($figure);

					$manager->persist($visual);
				}
        		//Comments of figure
        		for($k = 1; $k <= mt_rand(2,4); $k++)
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
