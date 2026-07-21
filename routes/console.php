<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('scm:sync-weather')
    ->hourlyAt(0)
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

Schedule::command('scm:sync-currency')
    ->hourlyAt(10)
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

Schedule::command('scm:sync-news')
    ->cron('20 */6 * * *')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

Schedule::command('scm:recalculate-risks')
    ->hourlyAt(30)
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
