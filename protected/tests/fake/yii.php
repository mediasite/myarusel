<?php

/**
 * Silly stub for phpStorm to correctly determine the class of `Yii::app()`
 */
class Yii extends YiiBase {

    /**
     * @return CWebApplication|CConsoleApplication
     */
    public static function app()
    {
        return parent::app();
    }
}