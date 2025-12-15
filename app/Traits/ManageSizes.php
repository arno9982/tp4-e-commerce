<?php

namespace App\Traits;

/**
 * Trait pour gérer les tailles des produits selon leur catégorie
 */
trait ManageSizes
{
    /**
     * Définit les tailles disponibles par catégorie
     */
    public static function getSizesByCategory(string $categoryName): array
    {
        $sizes = [
            'ROBES & JUPES' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'T-SHIRTS & HAUTS' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'JEANS & PANTALONS' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'VESTES & MANTEAUX' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'CHAUSSURES FEMME' => ['35', '36', '37', '38', '39', '40', '41', '42'],
            'CHAUSSURES HOMME' => ['38', '39', '40', '41', '42', '43', '44', '45', '46'],
            'ACCESSOIRES' => [],
        ];

        return $sizes[$categoryName] ?? [];
    }

    /**
     * Vérifie si une taille est valide pour une catégorie
     */
    public static function isValidSize(string $categoryName, string $size): bool
    {
        return in_array($size, self::getSizesByCategory($categoryName));
    }

    /**
     * Filtre les tailles invalides
     */
    public static function filterValidSizes(string $categoryName, array $sizes): array
    {
        return array_filter($sizes, fn($size) => self::isValidSize($categoryName, $size));
    }

    /**
     * Retourne les tailles par type
     */
    public static function getSizeType(string $categoryName): string
    {
        $clothing = ['ROBES & JUPES', 'T-SHIRTS & HAUTS', 'JEANS & PANTALONS', 'VESTES & MANTEAUX'];
        $shoes = ['CHAUSSURES FEMME', 'CHAUSSURES HOMME'];

        if (in_array($categoryName, $clothing)) {
            return 'clothing';
        } elseif (in_array($categoryName, $shoes)) {
            return 'shoes';
        }
        return 'none';
    }
}
