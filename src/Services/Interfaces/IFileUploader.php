<?php

namespace Ahmedtaha\FileUploader\Services\Interfaces;

use Ahmedtaha\FileUploader\Services\Concrete\FileUploader;

interface IFileUploader
{
    public static function getInstance(): FileUploader;

    public function setParams($file, $directory, $width = NULL, $height = NULL): FileUploader;

    public function upload(): ?string;

    public function uploadFile(): ?string;

    public function uploadBase64(): ?string;

    public function uploadImage(): ?string;
}