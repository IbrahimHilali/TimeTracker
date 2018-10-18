<?php

/*
|--------------------------------------------------------------------------
| Web $this->>|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the $this->>rviceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes();
$this->redirect('/', '/home');
$this->get('/home', 'HomeController@index')->name('home');
//Home
$this->get('/management/invoice', 'HomeController@invoice')->name('invoice');
$this->get('/management/peakTime', 'HomeController@statisticalService')->name('peakTime');

$this->get('/home/upload', 'HomeController@getFile')->name('upload');
$this->post('/home/upload', 'HomeController@postFile');

//projects
$this->get('/projects', 'ProjectController@index');
$this->post('/projects', 'ProjectController@store');
$this->get('/projects/services', 'ProjectController@services');
//timers

$this->post('/projects/{id}/timers/stop', 'TimerController@stopRunning');
$this->post('/projects/{id}/timers', 'TimerController@store');
$this->get('/project/timers/active', 'TimerController@running');

