<?php

namespace App\Repository;

use App\Entity\TranslationPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TranslationPrice>
 */
class TranslationPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranslationPrice::class);
    }

    /**
     * Find price by languages (case-insensitive)
     */
    public function findByLanguages(string $fromLanguage, string $toLanguage): ?TranslationPrice
    {
        // Normalize input (trim and lowercase for comparison)
        $fromLanguageNormalized = strtolower(trim($fromLanguage));
        $toLanguageNormalized = strtolower(trim($toLanguage));
        
        // Get all prices and filter in PHP for case-insensitive matching
        // This is more reliable than database-specific functions
        $prices = $this->findAll();
        
        foreach ($prices as $price) {
            $priceFromLang = strtolower(trim($price->getFromLanguage() ?? ''));
            $priceToLang = strtolower(trim($price->getToLanguage() ?? ''));
            
            if ($priceFromLang === $fromLanguageNormalized && $priceToLang === $toLanguageNormalized) {
                return $price;
            }
        }
        
        return null;
    }

    /**
     * Find all prices ordered by creation date
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('tp')
            ->orderBy('tp.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

