<?php
namespace AppBundle\App;

/**
 * Class JsonArrayComparator
 *
 * To be used as a singleton (inject through container).
 *
 * @author Max Humme <max@humme.nl>
 */
final class JsonArrayComparator
{
    /**
     * Compares two arrays.
     *
     * When $array1 has a value set to null, this method will ignore the difference if $array2 does have a value for
     * that same $key.
     * Works with multidimensional arrays.
     *
     * @param mixed[] $baseArray
     * @param mixed[] $arrayToCompare
     * @param string $keyString
     * @param mixed[] $diff
     * @return mixed[]
     */
    public function compareJsonArrays(array $baseArray, $arrayToCompare, $keyString = "", $diff = [])
    {
        // Prepare $diff on first call. Will be filled and returned when method is done.
        // We store differing, missing and extra keys, to be able to give meaningful feedback based on the result of
        // this method.
        if (is_null($diff)) {
            $diff = [
                'differingKeys' => [],
                'missingKeys' => [],
                'extraKeys' => []
            ];
        }

        // Visited keys to be able to check for extra keys in $baseArray later.
        $visitedKeys = [];

        // search for differing and missing keys
        foreach ($arrayToCompare as $key => $value) {
            $visitedKeys[] = $key;
            $newKeyString = $this->newKeystring($keyString, $key);

            // $arrayToCompare's $key does not exist in $baseArray
            if (!array_key_exists($key, $baseArray)) {
                $diff['missingKeys'][] = $newKeyString;
            // $arrayToCompare[$key] is an array, but $baseArray[$key] is not
            } elseif (is_array($value)) {
                if (!is_array($baseArray[$key])) {
                    $diff['differingKeys'][$newKeyString] = ['is' => $baseArray[$key], 'expected' => $value];
                } else {
                    // both arrays have an array for $key, so compare recursively...
                    $diff = $this->compareJsonArrays($baseArray[$key], $value, $newKeyString, $diff);
                }
            // $arrayToCompare[$key] not an array, but $baseArray[$key] is
            } elseif (is_array($baseArray[$key])) {
                $diff['differingKeys'][$newKeyString] = ['is' => $baseArray[$key], 'expected' => $value];
            // both arrays have a value for $key, so compare...
            } else {
                if (!is_null($value) && $value != $baseArray[$key]) {
                    $diff['differingKeys'][$newKeyString] = ['is' => $baseArray[$key], 'expected' => $value];
                }
            }
        }

        // All keys of $arrayToCompare are visited, now search for extra keys in $baseArray
        foreach ($baseArray as $key => $value) {
            $newKeyString = $this->newKeystring($keyString, $key);
            if (!in_array($key, $visitedKeys)) {
                $diff['extraKeys'][$newKeyString] = $value;
            }
        }

        return $diff;
    }

    /**
     * Checks if $array1 and $array2 are equal.
     *
     * @param mixed[] $array1
     * @param mixed[] $array2
     * @return bool
     */
    public function jsonArraysAreEqual(array $array1, array $array2)
    {
        return empty($this->compareJsonArrays($array1, $array2));
    }

    /**
     * Checks if $array1 contains $array2.
     *
     * @param mixed[] $array1
     * @param mixed[] $array2
     * @return bool
     */
    public function jsonArrayContainsJsonArray(array $array1, array $array2)
    {
        if ($this->jsonArraysAreEqual($array1, $array2)) {
            return true;
        }

        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if ($this->jsonArrayContainsJsonArray($value, $array2)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns a new key string based on $keyString and $key.
     *
     * @param string $keyString
     * @param string $key
     * @return string
     */
    private function newKeystring($keyString, $key)
    {
        $newKeyString = $keyString;
        if ($newKeyString != "") {
            $newKeyString .= '.';
        }

        return $newKeyString.$key;
    }
}
