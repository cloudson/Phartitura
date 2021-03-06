<?php

namespace Cloudson\Phartitura\Project; 

class VersionHeap extends \SplMaxHeap
{
    const HIGHER = 1; 
    const LOWER = -1;
    const EQUAL = 0;

    public function compare($version1, $version2)
    {
        if ($version2['version'] == 'dev-master') {
            return self::HIGHER;
        } elseif ($version1['version'] == 'dev-master') {
            return self::LOWER;
        }

        $date1 = new \DateTime($version1['time']);
        $date2 = new \DateTime($version2['time']);
        if ($date1->getTimestamp() === $date2->getTimestamp()) {
            return version_compare($version1['version'], $version2['version']); 
        }

        return $date1->getTimestamp() > $date2->getTimestamp() ? self::HIGHER : self::LOWER;
    }
}