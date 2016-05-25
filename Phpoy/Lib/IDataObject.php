<?php
/**
 * Created by PhpStorm.
 * User: mofan
 * Date: 2016/5/24 0024
 * Time: 13:30
 */

namespace Phpoy\Lib;


interface IDataObject {
    /**
     * @return DataAccessor
     */
    public static function dataAccessor();

    /**
     * @return string
     */
    public static function table();

    /**
     * @return string
     */
    public static function primaryField();

    /**
     * 数据分组的依据
     * @return string[]
     */
    public static function keyFields();

    /**
     * @return array
     */
    public static function fieldType();
}