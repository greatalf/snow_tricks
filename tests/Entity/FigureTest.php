<?php

namespace App\Tests\Entity;

use App\Entity\Figure;
use PHPUnit\Framework\TestCase;

class FigureTest extends TestCase
{
    /**
     * @test
     * @dataProvider validExtension
     */
    public function figure_head_visual_should_return_true_if_is_valid($extension)
    {
        $figure = new Figure;
        $figure->setHeadVisual('http://image-url' . $extension);
        
        $this->assertTrue($figure->isHeadVisualValid());
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
    public function setTitle_should_return_valid_value() 
    {
        $figure = new Figure;
        $value = 'My title';
        $figure->setTitle($value);
        $this->assertEquals($value, $figure->getTitle());
    }

    /**
     * @test
     */
    public function setContent_should_return_valid_value() 
    {
        $figure = new Figure;
        $value = 'My content';
        $figure->setContent($value);
        $this->assertEquals($value, $figure->getContent());
    }

    /**
     * @test
     */
    public function setCreatedAt_should_return_valid_value() 
    {
        $figure = new Figure;
        $value = new \Datetime();
        $figure->setCreatedAt($value);
        $this->assertEquals($value, $figure->getCreatedAt());
    }

    /**
     * @test
     */
    public function setModifiedAt_should_return_valid_value() 
    {
        $figure = new Figure;
        $value = new \Datetime();
        $figure->setModifiedAt($value);
        $this->assertEquals($value, $figure->getModifiedAt());
    }

    /**
     * @test
     */
    public function setSlug_should_return_valid_value() 
    {
        $figure = new Figure;
        $value = 'My slug';
        $figure->setSlug($value);
        $this->assertEquals($value, $figure->getSlug());
    }
}
