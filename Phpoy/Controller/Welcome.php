<?php
/**
 * Created by PhpStorm.
 * User: mofan
 * Date: 2016/5/23 0023
 * Time: 17:54
 */

namespace Phpoy\Controller;


use Phpoy\Lib\Controller;

class Welcome extends Controller{

    public function indexAction() {

        return 'welcome';
    }

}