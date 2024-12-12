<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findBySearchInTitle(string $search): array {
        // Initialise un QueryBuilder pour la table 'recipe'
        $queryBuilder = $this->createQueryBuilder('recipe');

        // Crée une requête pour sélectionner les recettes
        $query = $queryBuilder->select('recipe')
            // Ajoute une condition pour chercher dans le titre
            ->where('recipe.title LIKE :search')
            // Définit la valeur du paramètre de recherche
            ->setParameter('search', '%'.$search.'%')
            // Génère la requête finale
            ->getQuery();

        // Retourne les résultats de la requête
        return $query->getResult();
    }

}
