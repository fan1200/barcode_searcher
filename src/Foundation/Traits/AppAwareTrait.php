<?php


namespace BarcodeSearcher\Foundation\Traits;


use Illuminate\Contracts\Foundation\Application;

trait AppAwareTrait
{
    protected Application $app;

    public function getApp(): Application
    {
        return $this->app;
    }

    public function setApp(Application $app): void
    {
        $this->app = $app;
    }
}