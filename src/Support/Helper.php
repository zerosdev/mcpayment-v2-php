<?php

namespace ZerosDev\MCPayment\Support;

class Helper
{
    public static function dateIso8601(int $timestamp)
    {
        return date('Y-m-d\TH:i:s.vP', $timestamp);
    }
}
