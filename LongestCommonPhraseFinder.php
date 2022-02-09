<?php

class LongestCommonPhraseFinder
{
    private array $excludedPhrases;

    public function __construct(array $excludedPhrases = [])
    {
        $this->excludedPhrases = $excludedPhrases;
    }

    public function find(array $values): ?string
    {
        $longestPhrases = [];
        $valueCombinations = $this->createValueCombinations($values);
        foreach ($valueCombinations as $valueCombination) {
            if (!$valueCombination) {

                continue;
            }

            $_longestPhrase = $this->getLongestPhrase($valueCombination);
            if (!$_longestPhrase || in_array($_longestPhrase, $this->excludedPhrases)) {

                continue;
            }

            $longestPhrases[$_longestPhrase] = count($valueCombination);
        }

        $maxCount = max($longestPhrases);
        $longestPhrase = null;
        foreach ($longestPhrases as $_longestPhrase => $occurrenceCount) {
            if ($maxCount != $occurrenceCount) {

                continue;
            }

            if (empty($longestPhrase)) {
                $longestPhrase = $_longestPhrase;

                continue;
            }

            if (mb_strlen($_longestPhrase) > mb_strlen($longestPhrase)) {
                $longestPhrase = $_longestPhrase;
            }
        }

        return $longestPhrase;
    }

    public function getLongestPhrase(array $values): string
    {
        if (count($values) == 1) {

            return array_pop($values);
        }

        $subWords = [];
        $wordsInValues = array_map(function($string) {

            return explode(' ', $string);
        }, $values);

        if (count($wordsInValues) > 1 && count($wordsInValues[0]) > 0) {
            for ($i = 0; $i < count($wordsInValues[0]); $i++) {
                for ($j = 0; $j < count($wordsInValues[0])-$i+1; $j++) {
                    $_subWords = array_slice($wordsInValues[0], $i, $i+$j-$i);
                    $foundWordsInAllList = true;
                    foreach ($wordsInValues as $words) {
                        if (!$this->isSubList($_subWords, $words)) {
                            $foundWordsInAllList = false;

                            break;
                        }
                    }

                    if ($j > count($subWords) && $foundWordsInAllList) {
                        $subWords = array_slice($wordsInValues[0], $i, $i+$j-$i);
                    }
                }
            }
        }

        return implode(' ', $subWords);
    }

    protected function createValueCombinations($array): array
    {
        // initialize by adding the empty set
        $results = [[]];
        foreach ($array as $element) {
            foreach ($results as $combination) {
                array_push($results, array_merge([$element], $combination));
            }
        }

        return $results;
    }

    protected function isSubList(array $source, array $target): bool
    {
        return array_intersect($source, $target) == $source;
    }

    protected function zip(array $array1, array $array2): array
    {
        $result = [];
        for($i = 0; $i < min(count($array1), count($array2)); $i++) {
            $result[$i] = [$array1[$i], $array2[$i]];
        }

        return $result;
    }
}
