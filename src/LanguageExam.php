<?php

namespace PointCalculator;

class LanguageExam
{
    private string $language;
    private string $type;

    public function __construct($language, $type)
    {
        $this->language = $language;
        $this->type = $type;
    }

    public function isGreaterThan(LanguageExam $other): bool
    {
        if ($this->language === $other->language && $this->type === 'C1' && $other->type === 'B2') {
            return true;
        }
        return false;
    }

    public function getExtraPoints(): int
    {
        switch ($this->type) {
            case 'B2':
                return 28;
            case 'C1':
                return 40;
            default:
                return 0;
        }
    }
}