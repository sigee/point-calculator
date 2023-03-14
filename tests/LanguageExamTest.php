<?php

namespace PointCalculator;

use PHPUnit\Framework\TestCase;

class LanguageExamTest extends TestCase
{
    public function testIsGreaterThan_shouldReturnFalse_whenLanguagesAreDifferent()
    {
        $languageExam = new LanguageExam('angol', 'B2');
        $languageExam2 = new LanguageExam('német', 'C1');

        $this->assertFalse($languageExam->isGreaterThan($languageExam2));
    }

    public function testIsGreaterThan_shouldReturnFalse_whenTypesAreTheSame()
    {
        $languageExam = new LanguageExam('angol', 'B2');
        $languageExam2 = new LanguageExam('angol', 'B2');

        $this->assertFalse($languageExam->isGreaterThan($languageExam2));
    }

    public function testIsGreaterThan_shouldReturnFalse_whenItIsSmaller()
    {
        $languageExam = new LanguageExam('angol', 'B2');
        $languageExam2 = new LanguageExam('angol', 'C1');

        $this->assertFalse($languageExam->isGreaterThan($languageExam2));
    }

    public function testIsGreaterThan_shouldReturnTrue_whenItIsGreater()
    {
        $languageExam = new LanguageExam('angol', 'C1');
        $languageExam2 = new LanguageExam('angol', 'B2');

        $this->assertTrue($languageExam->isGreaterThan($languageExam2));
    }

    public function testGetExtraPoints_shouldReturn28_whenTypeIsB2()
    {
        $languageExam = new LanguageExam('angol', 'B2');

        $this->assertEquals(28, $languageExam->getExtraPoints());
    }

    public function testGetExtraPoints_shouldReturn40_whenTypeIsC1()
    {
        $languageExam = new LanguageExam('angol', 'C1');

        $this->assertEquals(40, $languageExam->getExtraPoints());
    }
}
