<?php

class Images
{
    public static function save($targetDirectory, $formName, $id): bool
    {
        $dir = 'public/img/' . $targetDirectory;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tmpName = @$_FILES[$formName]['tmp_name'];
        $imageError = @$_FILES[$formName]['error'];

        if ($imageError === UPLOAD_ERR_OK) {
            $uniqueName = $id . '.png';
            move_uploaded_file($tmpName, $dir . $uniqueName);

            return true;
        }

        return false;
    }

    public static function delete($targetDirectory, $id): bool
    {
        $filePath = 'public/img/' . $targetDirectory . $id . '.png';
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }

    public static function saveFolder(string $prefix, string $folderName, string $postValue): array
    {
        $files_name = array();

        $targetDirectory = 'public/img/' . rtrim($prefix, '/') . '/' . $folderName . '/';
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        foreach ($_FILES[$postValue]['name'] as $key => $imageName) {
            $tmpName = $_FILES[$postValue]['tmp_name'][$key];
            $imageError = $_FILES[$postValue]['error'][$key];

            if ($imageError === UPLOAD_ERR_OK) {
                $uniqueName = 'img-' . $key . '.png';
                $files_name[] = $uniqueName;

                move_uploaded_file($tmpName, $targetDirectory . $uniqueName);
            }
        }
        return $files_name;
    }

    public static function deleteFolder(string $prefix, string $folderName): void
    {
        $dir = 'public/img/' . rtrim($prefix, '/') . '/' . $folderName . '/';
        $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()) rmdir($file->getPathname());
            else unlink($file->getPathname());
        }
        rmdir($dir);
    }
}