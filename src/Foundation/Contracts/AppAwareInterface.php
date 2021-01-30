<?php


namespace BarcodeSearcher\Foundation\Contracts;



use Illuminate\Contracts\Foundation\Application;

interface AppAwareInterface
{
    public function getApp(): Application;
    public function setApp(Application $application): void;
}