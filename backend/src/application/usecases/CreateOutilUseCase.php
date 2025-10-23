<?php
declare(strict_types=1);

namespace App\application\usecases;

use App\application\ports\spi\OutilRepositoryInterface;
use App\domain\entities\Outil;

class CreateOutilUseCase
{
    public function __construct(
        private OutilRepositoryInterface $outilRepository
    ) {}

    public function execute(string $name, string $description, int $categoryId, int $modelId): Outil
    {
        $outil = new Outil(
            id: 0,
            categoryId: $categoryId,
            name: $name,
            brand: '',
            imageUrl: '',
            pricePerDay: null,
            description: $description,
            createdAt: new \DateTimeImmutable()
        );
        
        $this->outilRepository->save($outil);
        
        $outils = $this->outilRepository->findAll();
        foreach ($outils as $o) {
            if ($o->getName() === $name) {
                return $o;
            }
        }
        
        throw new \Exception('Erreur lors de la création de l\'outil');
    }
}
