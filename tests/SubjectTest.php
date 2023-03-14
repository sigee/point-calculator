<?php


use PointCalculator\Subject;
use PHPUnit\Framework\TestCase;

class SubjectTest extends TestCase
{
    public function testGetName_shouldRemoveHTMLTagsFromName()
    {
        $subject = new Subject('<b>matematika</b>', 'közép', '95%');

        $this->assertEquals('matematika', $subject->getName());
    }

    public function testGetResultAsNumber_shouldRemoveTheLastPercentageSignFromResult()
    {
        $subject = new Subject('matematika', 'közép', '95%');

        $this->assertEquals('95', $subject->getResultAsNumber());
    }

    public function testGetResultAsNumber_shouldRemoveTheLastPercentageSignFromResult_whenThePercentageSignIsMistyped()
    {
        $subject = new Subject('matematika', 'közép', '955');

        $this->assertIsInt($subject->getResultAsNumber());
        $this->assertEquals(95, $subject->getResultAsNumber());
    }

    public function testGetResultAsNumber_shouldReturnTheValueAsInteger()
    {
        $subject = new Subject('matematika', 'közép', '95%');

        $this->assertIsInt($subject->getResultAsNumber());
    }

    public function testGetType_shouldReturnEmelt_whenTypeIsEmelt(){
        $subject = new Subject('matematika', 'emelt', '95%');

        $this->assertEquals('emelt', $subject->getType());
    }

    public function testGetType_shouldReturnKozep_whenTypeIsKozep(){
        $subject = new Subject('matematika', 'közép', '95%');

        $this->assertEquals('közép', $subject->getType());
    }

    public function testGetType_shouldReturnKozep_whenTypeIsNotEmelt(){
        $subject = new Subject('matematika', 'nem emelt', '95%');

        $this->assertEquals('közép', $subject->getType());
    }
}
