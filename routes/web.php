<?php

use Illuminate\Support\Facades\Route;
use Hdaklue\Actioncrumb\Livewire\ActioncrumbDemo;

Route::get('/actioncrumb-demo', ActioncrumbDemo::class)->name('actioncrumb.demo');