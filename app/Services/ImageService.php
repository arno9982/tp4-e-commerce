<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class ImageService
{
    /**
     * Dossier de stockage des images produits
     */
    private const PRODUCT_DISK = 'public';
    private const PRODUCT_PATH = 'products';
    private const THUMBNAIL_WIDTH = 300;
    private const THUMBNAIL_HEIGHT = 300;
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB

    /**
     * Valide et traite une image uploadée
     * 
     * @param UploadedFile $file Fichier uploadé
     * @return array ['url' => string, 'thumbnail' => string]
     * @throws Exception
     */
    public function processAndStore(UploadedFile $file): array
    {
        // 1. Valider le fichier
        $this->validateImage($file);

        // 2. Générer un nom unique
        $filename = $this->generateUniqueFilename($file);

        // 3. Optimiser et redimensionner l'image
        $imagePath = $this->optimizeImage($file, $filename);

        return [
            'url' => Storage::disk(self::PRODUCT_DISK)->url($imagePath),
            'path' => $imagePath,
        ];
    }

    /**
     * Valide l'image uploadée
     * 
     * @param UploadedFile $file
     * @throws Exception
     */
    private function validateImage(UploadedFile $file): void
    {
        // Vérifier le type MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new Exception('Format d\'image non autorisé. Formats acceptés: JPEG, PNG, WebP, GIF');
        }

        // Vérifier la taille du fichier
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new Exception('Le fichier est trop volumineux. Taille maximale: 5 MB');
        }

        // Vérifier que c'est une image valide et obtenir ses dimensions
        $imageInfo = @getimagesize($file->getRealPath());
        if (!$imageInfo) {
            throw new Exception('Le fichier n\'est pas une image valide');
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];

        if ($width < 100 || $height < 100) {
            throw new Exception("L'image doit faire au moins 100x100 pixels (votre image: {$width}x{$height} px)");
        }
    }

    /**
     * Génère un nom de fichier unique
     * 
     * @param UploadedFile $file
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $originalName = $file->getClientOriginalName();
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        // Nettoyer le nom et ajouter un hash unique
        $name = Str::slug($name, '-');
        $name = substr($name, 0, 50); // Limiter la longueur
        $hash = Str::random(8);
        
        return "{$name}-{$hash}.{$extension}";
    }

    /**
     * Optimise l'image et la stocke
     * 
     * @param UploadedFile $file
     * @param string $filename
     * @return string Chemin d'accès au fichier stocké
     */
    private function optimizeImage(UploadedFile $file, string $filename): string
    {
        $sourcePath = $file->getRealPath();
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Obtenir les dimensions originales
        $imageInfo = getimagesize($sourcePath);
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        
        // Déterminer les nouvelles dimensions (redimensionner si > 1200px)
        $maxWidth = 1200;
        $maxHeight = 1200;
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;
        
        if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
            // Calculer les proportions
            $ratio = $originalWidth / $originalHeight;
            if ($ratio > 1) {
                // Paysage
                $newWidth = $maxWidth;
                $newHeight = (int)($maxWidth / $ratio);
            } else {
                // Portrait
                $newHeight = $maxHeight;
                $newWidth = (int)($maxHeight * $ratio);
            }
        }

        // Créer la nouvelle image selon le format
        $sourceImage = null;
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            case 'webp':
                $sourceImage = imagecreatefromwebp($sourcePath);
                break;
            default:
                // Si format inconnu, copier le fichier sans traitement
                $path = self::PRODUCT_PATH . '/' . $filename;
                Storage::disk(self::PRODUCT_DISK)->putFileAs(
                    self::PRODUCT_PATH,
                    $file,
                    $filename
                );
                return $path;
        }

        if (!$sourceImage) {
            throw new Exception('Impossible de traiter l\'image');
        }

        // Créer la nouvelle image redimensionnée
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Préserver la transparence pour PNG et GIF
        if ($extension === 'png') {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        } elseif ($extension === 'gif') {
            $transparent = imagecolorallocate($newImage, 0, 0, 0);
            imagecolortransparent($newImage, $transparent);
        }

        // Redimensionner
        imagecopyresampled(
            $newImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Sauvegarder la nouvelle image
        $tempPath = tempnam(sys_get_temp_dir(), 'img_');
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($newImage, $tempPath, 85);
                break;
            case 'png':
                imagepng($newImage, $tempPath, 9);
                break;
            case 'gif':
                imagegif($newImage, $tempPath);
                break;
            case 'webp':
                imagewebp($newImage, $tempPath, 85);
                break;
        }

        // Stocker dans le disque public
        $path = self::PRODUCT_PATH . '/' . $filename;
        Storage::disk(self::PRODUCT_DISK)->put($path, file_get_contents($tempPath));

        // Nettoyer
        imagedestroy($sourceImage);
        imagedestroy($newImage);
        @unlink($tempPath);

        return $path;
    }

    /**
     * Supprime une ancienne image
     * 
     * @param string|null $imagePath
     * @return bool
     */
    public function deleteImage(?string $imagePath): bool
    {
        if (empty($imagePath)) {
            return true;
        }

        try {
            if (Storage::disk(self::PRODUCT_DISK)->exists($imagePath)) {
                Storage::disk(self::PRODUCT_DISK)->delete($imagePath);
            }
            return true;
        } catch (Exception $e) {
            \Log::warning('Erreur lors de la suppression d\'image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère l'URL publique d'une image
     * 
     * @param string|null $imagePath
     * @return string|null
     */
    public function getUrl(?string $imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        return Storage::disk(self::PRODUCT_DISK)->url($imagePath);
    }
}
