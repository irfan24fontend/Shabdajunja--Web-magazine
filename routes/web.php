<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Site;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Writer;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/
Route::get('/', [Site\HomeController::class, 'index'])->name('home');

Route::get('/post/{post:slug}', [Site\PostController::class, 'show'])->name('post.show');
Route::post('/post/{post:slug}/comments', [Site\PostController::class, 'comment'])
    ->middleware('throttle:6,1')->name('post.comment');

Route::get('/category/{category:slug}', [Site\ArchiveController::class, 'category'])->name('category.show');
Route::get('/tag/{tag:slug}', [Site\ArchiveController::class, 'tag'])->name('tag.show');
Route::get('/author/{user}', [Site\ArchiveController::class, 'author'])->name('author.show');
Route::get('/search', [Site\ArchiveController::class, 'search'])->name('search');

/*
|--------------------------------------------------------------------------
| Login kora user (Breeze)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    // admin -> admin panel, baki sobai -> nijer lekhar list
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('writer.posts.index');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Editor theke lekhar vitore chhobi upload
    Route::post('/upload/image', [UploadController::class, 'image'])
        ->middleware('throttle:30,1')->name('upload.image');
});

/*
|--------------------------------------------------------------------------
| User: nijer lekha likhbe, review e pathabe (publish korte parbe na)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('writer')
    ->name('writer.')
    ->group(function () {
        Route::resource('posts', Writer\PostController::class)->except('show');
    });

/*
|--------------------------------------------------------------------------
| Admin panel (shudhu admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('posts', Admin\PostController::class)->except('show');
        Route::patch('posts/{post}/publish', [Admin\PostController::class, 'publish'])->name('posts.publish');

        Route::resource('categories', Admin\CategoryController::class)->except('show');
        Route::resource('tags', Admin\TagController::class)->except('show');

        Route::get('comments', [Admin\CommentController::class, 'index'])->name('comments.index');
        Route::patch('comments/{comment}/approve', [Admin\CommentController::class, 'approve'])->name('comments.approve');
        Route::delete('comments/{comment}', [Admin\CommentController::class, 'destroy'])->name('comments.destroy');

        Route::resource('users', Admin\UserController::class)->only(['index', 'edit', 'update', 'destroy']);
    });

require __DIR__ . '/auth.php';