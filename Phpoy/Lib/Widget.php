<?php
/**
 * Created by PhpStorm.
 * User: mofan
 * Date: 2016/5/23 0023
 * Time: 23:26
 */

namespace Phpoy\Lib;


abstract class Widget extends Controller {

    protected $cacheHandler;
    protected $cacheExpire = 0;

    protected static function viewPath() {
        return PROJECT_PATH . '/View/Widget';
    }

    /**
     * make cacheable
     * @param string $sAction
     * @param array $args
     * @return string
     */
    public function run($sAction, $args = []) {
        $cacheHandler = $this->getCacheHandler();
        if (null !== $cacheHandler) {
            $cacheKey = $this->getCacheKey();
            $cache = $cacheHandler->get($cacheKey);
            if ($cache != '') {
                return $cache;
            }
            $s = parent::run($sAction, $args);
            if ($s != '') {
                $cacheHandler->set($cacheKey, $s, $this->cacheExpire);
            }
            return $s;
        } else {
            return parent::run($sAction, $args);
        }
    }

    /**
     * @return mixed
     */
    protected function getCacheHandler() {
        return $this->cacheHandler;
    }

    protected function getCacheKey() {
        return get_class($this);
    }
}