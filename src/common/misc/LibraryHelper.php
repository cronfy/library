<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 24.02.18
 * Time: 15:16
 */

namespace cronfy\library\common\misc;

use cronfy\library\common\models\Library;

class LibraryHelper
{
    /**
     * @deprecated use LibraryRepository
     * @param $path
     * @return Library
     */
    public static function getByPath($path)
    {
        $currentPid = null;
        $library = null;
        foreach ($path as $sid) {
            if (!$library = Library::findOne(['pid' => $currentPid, 'sid' => $sid])) {
                return null;
            }
            $currentPid = $library->id;
        }
        return $library;
    }
}
