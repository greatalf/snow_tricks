<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class HomeController extends AbstractController
{ 
    /**
     *@Route("/", name="home")
     */
    public function home(FigureRepository $repo)
    {        
        $figures = $repo->findAll();

        $encoders = new JsonEncoder();
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer([$normalizers, $encoders]);
        // $sessionVars = $this->get('session')->get("_security_main");
        // dump($serializer->deserialize($sessionVars, Session::class, 'json'));

        // echo '<pre>';
        // echo gettype($sessionVars);
        // echo '</pre>';
        // dump($sessionVars->session);
        // dump($sessionVars);
        // die;
        // unserialize($sessionVars);
        // echo substr('a:1:{s:14:"_security_main";s:2554:"C:74:"Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken":2465:{a:3:{i:0;N;i:1;s:4:"main";i:2;s:2424:"a:4:{i:0;O:15:"App\Entity\User":15:{s:19:"\x00App\Entity\User\x00id";i:11;s:22:"\x00App\Entity\User\x00email";s:25:"avril.laurent974@yahoo.fr";s:25:"\x00App\Entity\User\x00username";s:5:"great";s:25:"\x00App\Entity\User\x00password";s:60:"$2y$13$2LjUl7b96k/2QdSIYqC9P.1DT2gjk1ESy3UAGVYXyY0V6bqVEytrC";s:16:"confirm_password";N;s:24:"\x00App\Entity\User\x00figures";O:33:"Doctrine\ORM\PersistentCollection":2:{s:13:"\x00*\x00collection";O:43:"Doctrine\Common\Collections\ArrayCollection":1:{s:53:"\x00Doctrine\Common\Collections\ArrayCollection\x00elements";a:0:{}}s:14:"\x00*\x00initialized";b:0;}s:25:"\x00App\Entity\User\x00comments";O:33:"Doctrine\ORM\PersistentCollection":2:{s:13:"\x00*\x00collection";O:43:"Doctrine\Common\Collections\ArrayCollection":1:{s:53:"\x00Doctrine\Common\Collections\ArrayCollection\x00elements";a:0:{}}s:14:"\x00*\x00initialized";b:0;}s:21:"\x00App\Entity\User\x00slug";s:5:"great";s:28:"\x00App\Entity\User\x00description";s:25:"Je suis un hard learner!!";s:26:"\x00App\Entity\User\x00firstName";s:5:"AVRIL";s:25:"\x00App\Entity\User\x00lastName";s:9:"Laurentos";s:23:"\x00App\Entity\User\x00avatar";C:17:"App\Entity\Avatar":1044:{a:3:{i:0;i:13;i:1;s:36:"135de3780f2b3071451d7ad85559c4c6.png";i:2;O:15:"App\Entity\User":15:{s:19:"\x00App\Entity\User\x00id";i:11;s:22:"\x00App\Entity\User\x00email";s:25:"avril.laurent974@yahoo.fr";s:25:"\x00App\Entity\User\x00username";s:5:"great";s:25:"\x00App\Entity\User\x00password";s:60:"$2y$13$2LjUl7b96k/2QdSIYqC9P.1DT2gjk1ESy3UAGVYXyY0V6bqVEytrC";s:16:"confirm_password";N;s:24:"\x00App\Entity\User\x00figures";r:9;s:25:"\x00App\Entity\User\x00comments";r:13;s:21:"\x00App\Entity\User\x00slug";s:5:"great";s:28:"\x00App\Entity\User\x00description";s:25:"Je suis un hard learner!!";s:26:"\x00App\Entity\User\x00firstName";s:5:"AVRIL";s:25:"\x00App\Entity\User\x00lastName";s:9:"Laurentos";s:23:"\x00App\Entity\User\x00avatar";r:21;s:26:"\x00App\Entity\User\x00userRoles";O:33:"Doctrine\ORM\PersistentCollection":2:{s:13:"\x00*\x00collection";O:43:"Doctrine\Common\Collections\ArrayCollection":1:{s:53:"\x00Doctrine\Common\Collections\ArrayCollection\x00elements";a:0:{}}s:14:"\x00*\x00initialized";b:0;}s:22:"\x00App\Entity\User\x00token";s:43:"QKoZYkYYsmLe89kfu7C3ET4FiGSOqlxiI_Eyz1LLc0o";s:26:"\x00App\Entity\User\x00confirmed";b:1;}}}s:26:"\x00App\Entity\User\x00userRoles";r:38;s:22:"\x00App\Entity\User\x00token";s:43:"QKoZYkYYsmLe89kfu7C3ET4FiGSOqlxiI_Eyz1LLc0o";s:26:"\x00App\Entity\User\x00confirmed";b:1;}i:1;b:1;i:2;a:1:{i:0;O:41:"Symfony\Component\Security\Core\Role\Role":1:{s:47:"\x00Symfony\Component\Security\Core\Role\Role\x00role";s:9:"ROLE_USER";}}i:3;a:0:{}}";}}";}', 2589, 2820);
        // die;
        // var_dump($serializer->deserialize($sessionVars, Session::class, $encoders));
        // die();

        return $this->render('tricks/home.html.twig', ['figures' => $figures]);
    }
}
