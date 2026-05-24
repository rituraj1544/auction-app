<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('auctions:close-ended')->everyMinute();
