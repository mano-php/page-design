<?php

use ManoCode\Page\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('page', [Controllers\PageController::class, 'index']);
/**
 * 页面设计
 */
Route::resource('/page/design', Controllers\PagesDesignController::class);
/**
 * 页面分组
 */
Route::resource('/page/group', Controllers\PagesGroupController::class);

/**
 * 获取树状选择数据
 */
Route::get('/page/get-tree-group',[Controllers\PagesGroupController::class,'getTreeGroup']);

