<?php

namespace Sauce\Sausage;

abstract class SauceTestCommon
{

    public static function RequireSauceConfig()
    {
        SauceConfig::LoadConfig();

        if (!defined('SAUCE_USERNAME') || !SAUCE_USERNAME) {
            throw new \Exception("SAUCE_USERNAME must be defined!");
        }

        if (!defined('SAUCE_API_KEY') || !SAUCE_API_KEY) {
            throw new \Exception("SAUCE_API_KEY must be defined!");
        }
    }

    public static function ReportStatus($session_id, $passed)
    {
        self::RequireSauceConfig();
        $api = new SauceAPI(SAUCE_USERNAME, SAUCE_API_KEY);
        $api->updateJob($session_id, array('passed'=>$passed));
    }

    public static function SpinAssert($msg, $test, $args=array(), $timeout=10)
    {
        $num_tries = 0;
        $result = false;
        while ($num_tries < $timeout && !$result) {
            try {
                $result = call_user_func_array($test, $args);
            } catch (\Exception $e) {
                $result = false;
            }

            if (!$result)
                sleep(1);

            $num_tries++;
        }

        $msg .= " (Failed after $num_tries tries)";

        return array($result, $msg);
    }
}
