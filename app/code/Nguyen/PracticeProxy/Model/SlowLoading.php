<?php
namespace Nguyen\PracticeProxy\Model;

class SlowLoading
{
    public function __construct()
    {
        // ... Do something resource intensive
        sleep(3);
    }

    public function getValue()
    {
        return 'SlowLoading value';
    }
}
