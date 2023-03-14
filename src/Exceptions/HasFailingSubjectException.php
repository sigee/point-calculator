<?php

namespace PointCalculator\Exceptions;

use Exception;
use PointCalculator\Subject;

class HasFailingSubjectException extends Exception
{
    private Subject $subject;

    public function __construct(Subject $subject)
    {
        $this->subject = $subject;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }
}