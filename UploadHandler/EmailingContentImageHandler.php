<?php

namespace App\Cdn\UploadHandler;

use App\Cdn\Model\CdnUpload;
use Intervention\Image\ImageManager;

class EmailingContentImageHandler implements UploadedImageHandlerInterface
{
    private ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function handle(CdnUpload $file)
    {
        $data = $this->imageManager->make($file->getLocalContent());
        $data->orientate();

        $canvas = $this->imageManager->canvas($data->getWidth(), $data->getHeight(), 'ffffff');
        $canvas->insert($data, 'top-left');

        $canvas->resize(1200, 1200, static function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $canvas->encode('jpg');
        $file->setStorageContent($canvas->getEncoded(), 'jpg');
    }
}
