<?php

namespace App\Classes;


use App\Enum\FileCategory;
use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileUpload
{
    public $file;

    public string $caption = '';

    public int $category;

    public string $path;

    public string $acceptableType;

    public string $maxUploadSize;

    public string $newName;

    public string $storageDisk;

    //TODO: remove exif image file! (security issue)

    public function setKey(string $key): static
    {
        $this->fileKey = $key;
        return $this;
    }

    public function setRequest($request): static
    {
        $this->file = $request->{$this->fileKey};
        return $this;
    }

    public function setCaption($caption): static
    {
        $this->caption = $caption;
        return $this;
    }

    public function setCategory(FileCategory $category): static
    {
        $this->category = $category->value;
        $this->path = $category->entity()['path'];
        $this->acceptableType = $category->entity()['types'];
        $this->maxUploadSize = $category->entity()['maxUploadSize'];
        $this->storageDisk = $category->entity()['fileSystem'];
        return $this;
    }

    /**
     * @throws Throwable
     */
    public function save(): Model|File
    {
        $this->checkIsCategorySet();

        $this->checkMimTypeOfFile();


        $this->checkMaxFileUploadFileSize();

        $this->newName = time() . '-' . $this->file->getClientOriginalName();

        Storage::disk($this->storageDisk)->putFileAs(
            $this->path,
            $this->file,
            $this->newName
        );

        return $this->saveOnDatabase();
    }

    /**
     * @throws Throwable
     */
    private function checkIsCategorySet(): void
    {
        throw_if(
            is_null($this->category),
            new \Exception('file category is not set! first use serCategory() method!')
        );
    }

    /**
     * @throws Throwable
     */
    private function checkMimTypeOfFile(): void
    {
        throw_if(
            !str_contains($this->acceptableType, $this->file->getClientMimeType()),
            new \Exception('file type is not acceptable!')
        );
    }

    /**
     * @throws Throwable
     */
    private function checkMaxFileUploadFileSize(): void
    {
        throw_if(
            ($this->file->getSize() > (int)$this->maxUploadSize),
            new \Exception('file size is bigger than limited size!')
        );
    }

    private function saveOnDatabase()
    {
        return File::create([
            'caption' => $this->caption,
            'path' => env('APP_URL') . $this->path . $this->newName,
            'extensions' => $this->file->getClientMimeType(),
            'hash' => md5($this->path . $this->newName),
            'original_name' => $this->file->getClientOriginalName(),
            'size' => $this->file->getSize(),
            'user_id' => Auth::user()->id,
            'file_category_id' => $this->category
        ]);
    }
}
