<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('run:campaigns')->everyMinute();


//Schedule::command('run:crm-lead-reply')->everyThreeMinutes();
//Schedule::command('run:followups')->everyFiveMinutes();
//Schedule::command('run:campaign-replies')->everyFiveMinutes();
