<?php

namespace Ahmedtaha\FileUploader;

use Illuminate\Support\ServiceProvider;
use Ahmedtaha\FileUploader\Commands\FileUploaderCommand;

class FileUploaderServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->singleton('command.ahmedtaha.artisan-file:upload', function ($app) {
            return $app[FileUploaderCommand::class];
        });

        $this->commands('command.ahmedtaha.artisan-file:upload');
    }
}
