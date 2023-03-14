<?php

namespace PointCalculator;

use Exception;
use PointCalculator\Exceptions\HasFailingSubjectException;
use PointCalculator\Exceptions\MissingRequiredSubjectForProgrammeException;
use PointCalculator\Exceptions\MissingRequiredSubjectsException;
use PointCalculator\Exceptions\MissingElectivesSubjectForProgrammeException;

class Calculator
{
    const REQUIRED_SUBJECTS = ['magyar nyelv és irodalom', 'történelem', 'matematika'];
    const GRADUATION_RESULTS = 'erettsegi-eredmenyek';
    const EXTRA_POINTS = 'tobbletpontok';

    function calculate($data): string
    {
        try {
            $config = $this->getConfig($data['valasztott-szak']);
            $basePoints = $this->calculateBasePoints($data, $config);
            $extraPoints = $this->calculateExtraPoints($data[self::GRADUATION_RESULTS], $data[self::EXTRA_POINTS]);
            return sprintf('%d (%d alappont + %d többletpont)', ($basePoints + $extraPoints), $basePoints, $extraPoints);
        } catch (MissingRequiredSubjectsException $e) {
            return 'hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt';
        } catch (MissingRequiredSubjectForProgrammeException $e) {
            return 'hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgy hiánya miatt';
        } catch (MissingElectivesSubjectForProgrammeException $e) {
            return 'hiba, nem lehetséges a pontszámítás a kötelezően választható érettségi tárgy hiánya miatt';
        } catch (HasFailingSubjectException $e) {
            return sprintf('hiba, nem lehetséges a pontszámítás a %s tárgyból elért 20%% alatti eredmény miatt', $e->getSubject()->getName());
        } catch (Exception $e) {
            return 'Unexpected Error';
        }
    }

    private function providedAllRequiredSubjects($graduationResults): bool
    {
        foreach (self::REQUIRED_SUBJECTS as $subject) {
            $notFound = true;
            foreach ($graduationResults as $result) {
                if ($result['nev'] == $subject) {
                    $notFound = false;
                }
            }
            if ($notFound) {
                return false;
            }
        }
        return true;
    }

    private function getFailedSubject($graduationResults): ?Subject
    {
        foreach ($graduationResults as $result) {
            $subject = new Subject($result['nev'], $result['tipus'], $result['eredmeny']);
            if ($subject->getResultAsNumber() < 20) {
                return $subject;
            }
        }
        return null;
    }

    /**
     * @throws MissingRequiredSubjectsException
     * @throws HasFailingSubjectException
     * @throws MissingRequiredSubjectForProgrammeException
     * @throws MissingElectivesSubjectForProgrammeException
     */
    private function calculateBasePoints($data, $config): int
    {
        $graduationResults = $data[self::GRADUATION_RESULTS];
        if (!$this->providedAllRequiredSubjects($graduationResults)) {
            throw new MissingRequiredSubjectsException();
        }
        if (!is_null($failedSubject = $this->getFailedSubject($graduationResults))) {
            throw new HasFailingSubjectException($failedSubject);
        }
        if (!$this->providedRequiredSubjectForProgramme($graduationResults, $config)) {
            throw new MissingRequiredSubjectForProgrammeException();
        }
        if (!$this->providedElectivesSubjectForProgramme($graduationResults, $config)) {
            throw new MissingElectivesSubjectForProgrammeException();
        }

        $requiredSubjectForProgramme = $this->getRequiredSubjectForProgramme($graduationResults, $config);
        $basePoints = $requiredSubjectForProgramme->getResultAsNumber();
        $electivesSubjectsForProgramme = $this->getElectivesSubjectsForProgramme($graduationResults, $config);
        $bestElectivesSubjectForProgrammeResult = $this->getBestElectivesSubjectForProgrammeResult($electivesSubjectsForProgramme);
        $basePoints += $bestElectivesSubjectForProgrammeResult;

        return ($basePoints * 2);
    }

    private function calculateExtraPoints($graduationResults, $extraPoints): int
    {
        $extraPoint = $this->calculateExtraPointsByGraduationResults($graduationResults);
        $languageExams = $this->getBestLanguageExams($extraPoints);
        foreach ($languageExams as $exam) {
            $extraPoint += $exam->getExtraPoints();
        }

        return min($extraPoint, 100);
    }

    private function calculateExtraPointsByGraduationResults($graduationResults): int
    {
        $extraPoint = 0;
        foreach ($graduationResults as $result) {
            if ($result['tipus'] === 'emelt') {
                $extraPoint += 50;
            }
        }
        return $extraPoint;
    }

    private function getBestLanguageExams($extraPoints): array
    {
        $languageExams = [];
        foreach ($extraPoints as $extra) {
            $languageExam = new LanguageExam($extra['nyelv'], $extra['tipus']);
            if (!array_key_exists($extra['nyelv'], $languageExams)) {
                $languageExams[$extra['nyelv']] = $languageExam;
            } else if ($languageExam->isGreaterThan($languageExams[$extra['nyelv']])) {
                $languageExams[$extra['nyelv']] = $languageExam;
            }
        }
        return $languageExams;
    }

    private function getRequiredSubjectForProgramme($graduationResults, $config): ?Subject
    {
        foreach ($graduationResults as $result) {
            if ($result['nev'] === $config['kotelezo']['nev']) {
                if (!$config['kotelezo']['tipus'] || $result['tipus'] === $config['kotelezo']['tipus']) {
                    return new Subject($result['nev'], $result['tipus'], $result['eredmeny']);
                }
            }
        }
        return null;
    }

    /**
     * @return Subject[]
     */
    private function getElectivesSubjectsForProgramme($graduationResults, $config): array
    {
        return array_map(function($subject) {
            return new Subject($subject['nev'], $subject['tipus'], $subject['eredmeny']);
        }, array_filter($graduationResults, function ($result) use ($config) {
            foreach ($config['valaszthato'] as $electives) {
                if ($result['nev'] === $electives['nev']) {
                    if (!$electives['tipus'] || $result['tipus'] === $electives['tipus']) {
                        return true;
                    }
                }
            }
            return false;
        }));
    }

    private function providedRequiredSubjectForProgramme($graduationResults, $config): bool
    {
        return !is_null($this->getRequiredSubjectForProgramme($graduationResults, $config));
    }

    private function getConfig($selectedProfession)
    {
        $allConfig = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
        $egyetem = $selectedProfession['egyetem'];
        $kar = $selectedProfession['kar'];
        $szak = $selectedProfession['szak'];
        return $allConfig['egyetemek'][$egyetem]['karok'][$kar]['szakok'][$szak];
    }

    private function providedElectivesSubjectForProgramme($graduationResults, $config): bool
    {
        return count($this->getElectivesSubjectsForProgramme($graduationResults, $config)) > 0;
    }

    /**
     * @param Subject[] $electivesSubjects
     */
    private function getBestElectivesSubjectForProgrammeResult(array $electivesSubjects): int
    {
        $bestElectivesSubjectResult = 0;
        foreach ($electivesSubjects as $subject) {
            $result = $subject->getResultAsNumber();
            if ($result > $bestElectivesSubjectResult) {
                $bestElectivesSubjectResult = $result;
            }
        }
        return $bestElectivesSubjectResult;
    }
}
