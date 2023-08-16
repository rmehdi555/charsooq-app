<?php

if (!function_exists('generateRandomNumber')) {
    function generateRandomNumber($length = 4): int
    {
        $intMin = (10 ** $length) / 10;
        $intMax = (10 ** $length) - 1;

        return mt_rand($intMin, $intMax);
    }
}

if (!function_exists('checkPhoneNumber')) {
    function checkPhoneNumber($cell_number): string
    {
        $cell_number = convertToEnglishDigit($cell_number);
        return str_replace('+98', '0', $cell_number);
    }
}

if (!function_exists('getCompleteIpAddr')) {
    function getCompleteIpAddr(): string
    {
        $ip = '';
        if (!empty($_SERVER['HTTP_AR_REAL_IP'])) {
            $ip .= 'HTTP_AR_REAL_IP => ' . $_SERVER['HTTP_AR_REAL_IP'] . ' | ';
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip .= 'HTTP_X_FORWARDED_FOR => ' . $_SERVER['HTTP_X_FORWARDED_FOR'] . ' | ';
        }

        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip .= 'HTTP_X_REAL_IP => ' . $_SERVER['HTTP_X_REAL_IP'] . ' | ';
        }

        if (!empty($_SERVER['SERVER_ADDR'])) {
            $ip = 'SERVER_ADDR => ' . $_SERVER['SERVER_ADDR'] . ' | ';
        }

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip .= 'REMOTE_ADDR => ' . $_SERVER['REMOTE_ADDR'] . ' | ';
        }

        $ip = rtrim($ip, ' | ');
        return $ip;
    }
}

if (!function_exists('generateTransferCode')) {
    function generateTransferCode($length = 15): int
    {
        $intMin = (10 ** $length) / 10;
        $intMax = (10 ** $length) - 1;

        return mt_rand($intMin, $intMax);
    }
}

if (!function_exists('extractRegionAndExchangeType')) {
    function extractRegionAndExchangeType($link)
    {
        $urlDetail = ['region' => 1, 'exchangeType' => 1];

        if (strpos($link, 'co.uk'))
            $urlDetail = ['region' => 3, 'exchangeType' => 2];
        elseif (strpos($link, '.fr'))
            $urlDetail = ['region' => 3, 'exchangeType' => 4];
        elseif (strpos($link, '.de'))
            $urlDetail = ['region' => 3, 'exchangeType' => 4];
        elseif (strpos($link, '.ae'))
            $urlDetail = ['region' => 2, 'exchangeType' => 3];
        elseif (strpos($link, '.tr'))
            $urlDetail = ['region' => 4, 'exchangeType' => 6];
        elseif (strpos($link, '.ca'))
            $urlDetail = ['region' => 1, 'exchangeType' => 5];

        return $urlDetail;
    }
}

if (!function_exists('truncate')) {
    function truncate($input, $maxWords, $maxChars)
    {
        $words = preg_split('/\s+/', $input);
        $words = array_slice($words, 0, $maxWords);
        $words = array_reverse($words);

        $chars = 0;
        $truncated = array();

        while (count($words) > 0) {
            $fragment = trim(array_pop($words));
            $chars += strlen($fragment);

            if ($chars > $maxChars) break;

            $truncated[] = $fragment;
        }

        $result = implode(' ', $truncated);
        if ($input == $result) {
            $ret = $input;
        } else {
            $ret = $result . '...';
        }
        return $ret;
    }
}


if (!function_exists('extractAsinAmazon')) {
    function extractAsinAmazon($url)
    {
        $pattern = '/(dp\/)([A-Z0-9]{10})/';
        preg_match($pattern, $url, $matches);
        if (isset($matches[2]))
            return $matches[2];
        return false;
    }
}
