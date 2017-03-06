<?php
/**
 * Created by PhpStorm.
 *
 * combo-outfit (by Duc-Anh LE)
 *
 * User: ducanh-ki
 * Date: 3/6/17
 * Time: 11:25 AM
 */

namespace CreativeDelta\User\View;


use Zend\View\Helper\AbstractHelper;
use Zend\View\Renderer\PhpRenderer;

class FacebookHelper extends AbstractHelper
{

    const scripts = [

    ];


    public function configure(PhpRenderer $view)
    {
        foreach (self::scripts as $script) {
            $view->headScript()->prependScript($view->basePath() . $script);
        }
    }
}