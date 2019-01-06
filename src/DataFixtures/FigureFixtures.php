<?php

namespace App\DataFixtures;

use App\Entity\Visual;
use App\Entity\User;
use App\Entity\Figure;
use App\Entity\Avatar;
use App\Entity\Comment;
use App\Entity\Category;
use App\ToolDevice\Slugification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FigureFixtures extends Fixture
{
	private $encoder;

	public function __construct(UserPasswordEncoderInterface $encoder)
	{
		$this->encoder = $encoder;
	}

    public function load(ObjectManager $manager)
    {
		$faker = \Faker\Factory::create('fr_FR');
		
		//Gestion des users
		$users = [];
		$genres = ['male', 'female'];

		for($i = 1; $i<= 10; $i++)
		{
			$user = new User();
			$genre = $faker->randomElement($genres);

			$picture = 'https://randomuser.me/api/portraits/';
			$pictureId = $faker->numberBetween(1, 99) . '.jpg';

			$picture .= ($genre == "male" ? 'men/' : 'women/') . $pictureId; 

			$password = $this->encoder->encodePassword($user, 'azertyuiop');
			$slugificator = new Slugification();

			$user->setFirstName($faker->firstname)
				 ->setLastName($faker->lastname)
				 ->setEmail($faker->email)
				 ->setUsername($faker->username)
				 ->setSlug($slugificator->slugify($user->getFirstName() . ' ' . $user->getLastName()))
				 ->setDescription('<p>' . join( '</p><p>', $faker->paragraphs(3)) . '</p>')
				 ->setPassword($password)
				 ->setConfirmed(1)
				 ->setToken(md5(uniqid()));

				 $avatar = new Avatar;

				 $avatar->setName($picture)
						->setUser($user);

				$manager->persist($avatar);

			$manager->persist($user);
			$users[] = $user;

		}

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
				
				$user = $users[mt_rand(0, count($users) - 1)];

        		$figure->setTitle($title)
        			   ->setContent($content)
        			   ->setCreatedAt($faker->dateTimeBetween('-6 months'))
					   ->setCategory($category)
					   ->setSlug($slug)
					   ->setHeadVisual($faker->imageUrl())
					   ->setAuthor($user);
        		
				$manager->persist($figure);

				//Visuals of figure
				for($m = 1; $m <= mt_rand(20,50); $m++)
				{
					$visual = new Visual();
					
					$visual->setUrl($faker->imageUrl())
						  ->setCaption($faker->sentence())
						  ->setFigure($figure);

					$manager->persist($visual);
				}
        		//Comments of figure
        		for($k = 1; $k <= mt_rand(12,20); $k++)
        		{
        			$content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';
        			$now = new \DateTime();
        			$interval = $now->diff($figure->getCreatedAt());
        			$days = $interval->days;
        			$minimum = '-' . $days . ' days';

					$user = $users[mt_rand(0, count($users) - 1)];

        			$comment = new Comment();
        			$comment->setAuthor($user)
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
