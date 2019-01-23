<?php

namespace App\Tests\Controller;

use App\Entity\Visual;
use PHPUnit\Framework\TestCase;

class TricksControllerTest extends TestCase
{
    /**
     * @test
     */
    public function isImage_should_return_true_if_image_is_valid()
    {
        $visual = new Visual;
        $visual->setUrl('http://coresites-cdn.factorymedia.com/onboardfr/wp-content/uploads/2015/11/wpid-Getting-Up-Snowboard-How-To-22-680x453.jpg');
        // $extTable = ['.jpg', '.jpeg', '.png', 'aspx'];
        // $extensionJpgPng = (substr($visual->getUrl(), strlen($visual->getUrl())-4));
        $this->assertTrue($visual->isImage());
    }
}
