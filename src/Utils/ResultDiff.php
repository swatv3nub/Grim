<?php
namespace Grim\Utils;

class ResultDiff
{
    public static function diff($resultA, $resultB)
    {
        // Simple array diff for scan results
        return [
            'added' => array_diff_assoc($resultB, $resultA),
            'removed' => array_diff_assoc($resultA, $resultB)
        ];
    }
}
