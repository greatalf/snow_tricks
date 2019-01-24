<?php

namespace App\Tests\Entity;

use App\Entity\Visual;
use PHPUnit\Framework\TestCase;
use App\Controller\TricksController;

class VisualTest extends TestCase
{
    /**
     * @test
     * @dataProvider validExtension
     */
    public function isImage_should_return_true_if_image_is_valid($extension)
    {
        $visual = new Visual;
        $visual->setUrl('http://image-url' . $extension);
        
        $this->assertTrue($visual->isImage());
    }

    public function validExtension()
    {
        return [
            ['.jpg'],
            ['.jpeg'],
            ['.png'],
            ['.aspx']
        ];
    }

    /**
     * @test
     */
    public function isImage_should_return_false_if_image_is_not_valid()
    {
        $visual = new Visual;
        $visual->setUrl('http://image-url.xxx');

        $this->assertFalse($visual->isImage());
    }

    /**
     * @test
     */
    public function isVideo_should_return_true_if_video_kind_egal_1()
    {
        $visual = new Visual;
        $visual->setVisualKind(1);

        $this->assertTrue($visual->isVideo());
    }

    /**
     * @test
     */
    public function setUrl_should_return_valid_value() 
    {
        $visual = new Visual;
        $value = 'https://myUrl.com';
        $visual->setUrl($value);
        $this->assertSame($value, $visual->getUrl());
    }

    /**
     * @test
     */
    public function setCaption_should_return_valid_value() 
    {
        $visual = new Visual;
        $value = 'My caption';
        $visual->setCaption($value);
        $this->assertSame($value, $visual->getCaption());
    }

    /**
     * @test
     */
    public function setVisualKind_should_return_valid_value() 
    {
        $visual = new Visual;
        $value = 1;
        $visual->setVisualKind($value);
        $this->assertSame($value, $visual->getVisualKind());
    }
}
