<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 10.07.18
 * Time: 17:45
 */

namespace cronfy\library\common\misc;

use cronfy\library\common\models\Library;

class LibraryRepository
{
    /**
     * @param $path
     * @return Library
     */
    public function getByPath($path)
    {
        $currentPid = null;
        $library = null;

        foreach ($path as $sid) {
            $library = $this->getByPidSid($currentPid, $sid);

            if (!$library) {
                break;
            }

            $currentPid = $library->id;
        }

        return $library;
    }

    /** @var integer[]  */
    protected $_idByPidSid = [];
    protected function getByPidSid($pid, $sid) {
        $key = md5(serialize([$pid, $sid]));

        $library = null;

        if (!array_key_exists($key, $this->_idByPidSid)) {
            /** @var Library $library */
            $library = Library::findOne(['pid' => $pid, 'sid' => $sid]);

            if ($library) {
                $this->_idByPidSid[$key] = $library->id;
                if (!@$this->_itemsById[$library->id]) {
                    $this->_itemsById[$library->id] = $library;
                }
            } else {
                $this->_idByPidSid[$key] = false;
            }
        }

        if ($libraryId = $this->_idByPidSid[$key]) {
            return $this->getById($libraryId);
        }

        return null;
    }

    /** @var Library[] */
    protected $_itemsById = [];
    /**
     * @param $id integer
     * @return Library
     */
    public function getById($id) {
        if (!array_key_exists($id, $this->_itemsById)) {
            $this->_itemsById[$id] = Library::findOne($id);
        }

        return $this->_itemsById[$id];
    }
    
    public function find() {
        return Library::find();
    }


}