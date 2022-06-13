<?php

namespace Ahmedtaha\FileUploader\Services\Concrete;

use Ahmedtaha\FileUploader\Services\Interfaces\IFileUploader;
use File;
use Image;
use JetBrains\PhpStorm\Pure;

class FileUploader implements IFileUploader
{

    private $file, $directory, $width, $height;

    /**
     * @return FileUploader
     */
    static public function getInstance(): FileUploader
    {
        return new self;
    }

    /**
     * @param $file
     * @param $directory
     * @param null $width
     * @param null $height
     * @return $this
     */
    public function setParams($file, $directory, $width = NULL, $height = NULL): FileUploader
    {
        $this->file      = $file;
        $this->width     = $width;
        $this->height    = $height;
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return string|null
     * @throws \Exception
     */
    public function upload(): ?string
    {
        if (!File::isDirectory($this->directory)) {
            File::makeDirectory($this->directory, 0777, true, true);
        }

        if (is_file($this->file)) {
            $fileMimeType = $this->file->getClientmimeType();
            $imageCheck   = explode('/' , $fileMimeType);

            if ($imageCheck[0] === 'image') {
                $allowedImagesMimeTypes = ['image/jpeg','image/jpg','image/png','image/webp'] ;

                if(!in_array($fileMimeType, $allowedImagesMimeTypes, true)){
                    throw new \RuntimeException('Make sure file mime type is in '. implode(',', $allowedImagesMimeTypes));
                }
                return $this->uploadImage() ;
            }else{
                $allowedMimeTypes = [
                    'application/pdf'  ,
                    'application/msword' ,
                    'application/excel',
                    'application/vnd.ms-excel',
                    'application/vnd.msexcel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'video/mp4',
                    'video/x-flv',
                    'video/MP2T',
                    'video/3gpp',
                    'video/quicktime',
                    'video/x-msvideo',
                    'video/x-ms-wmv'
                ] ;
                if(!in_array($fileMimeType, $allowedMimeTypes, true)){
                    throw new \RuntimeException('Make sure file mime type is in ' . implode(',', $allowedMimeTypes),);
                }
                return  $this->uploadFile();
            }
        } else {
            return $this->uploadBase64();
        }
    }

    /**
     * @return string|null
     */
    public function uploadFile(): ?string
    {
        try {
            $name = time() . '_' . random_int(1111, 9999) . '.' . $this->file->getClientOriginalExtension();
            $this->file->move(base_path().'/public/'.$this->directory,$name);
            return $name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @return string|null
     */
    public function uploadImage(): ?string
    {
        try {
            $name = time() . '_' . random_int(1111, 9999) . '.' . $this->file->getClientOriginalExtension();
            $img  = Image::make($this->file);
            if (NULL !== $this->width) {
                $img->resize($this->width, $this->height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $img->save("$this->directory/$name");
            return $name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @return string|null
     */
    public function uploadBase64(): ?string
    {
        try {
            $name = time() . random_int(1000000, 9999999) . '.png';
            $img  = Image::make(base64_decode($this->file));

            if (NULL !== $this->width && NULL !== $this->height) {
                $img->resize($this->width, $this->height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $img->save("$this->directory/$name");
            return $name;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
