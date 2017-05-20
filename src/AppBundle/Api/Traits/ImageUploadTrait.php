<?php
namespace AppBundle\Api\Traits;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait ImageUploadTrait
 *
 * This trait should be used in a controller to add image upload functionality.
 *
 * @author Max Humme <max@humme.nl>
 */
trait ImageUploadTrait
{
    /**
     * Gets the UploadedFile for input with name $key.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $key
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private function getUploadedImage(Request $request, $key): UploadedFile
    {
        return $request->files->get($key);
    }

    /**
     * Gets the byte size of the $uploadedImage.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedImage
     * @return int
     */
    private function getUploadedImageByteSize(UploadedFile $uploadedImage): int
    {
        return filesize($uploadedImage->getPathName());
    }

    /**
     * Gets the MIME type of the $uploadedImage.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedImage
     * @return null|string
     */
    private function getUploadedImageMimeType(UploadedFile $uploadedImage): ?string
    {
        return $uploadedImage->getMimeType();
    }

    /**
     * Gets the original file name of $uploadedImage.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedImage
     * @return null|string
     */
    private function getUploadedImageOriginalFileName(UploadedFile $uploadedImage): ?string
    {
        return $uploadedImage->getClientOriginalName();
    }

    /**
     * Checks if $request has an uploaded image with name $key.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $key
     * @return bool
     */
    private function requestHasUploadedImage(Request $request, $key): bool
    {
        return $request->files->has($key);
    }

    /**
     * Moves the physical temporary $uploadedImage to the images directory.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedImage
     * @param string $fileName
     */
    private function storeUploadedImageFile(UploadedFile $uploadedImage, $fileName)
    {
        $uploadedImage->move($this->getParameter('images_directory'), $fileName);
    }
}
