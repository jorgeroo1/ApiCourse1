<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\DragonTreasureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DragonTreasureRepository::class)]
#[ApiResource(operations: [
    //estas son las operaciones que saldran en mi API platforms
    //las acciones son clases que tienen sus propios parametros usables
    //si usamos el short Name me cambiaria la URL tambien
    new Get(
        //esta me eliminara la accion por defecto (el otro normalizationContext) y ahora la accion read se llamara DragonTreasure:item:get
        normalizationContext: [
            'groups' => ['DragonTreasure:read', 'DragonTreasure:item:get'],
        ]
    ),
    new GetCollection(),
    new Post(),
    new Patch(),
    new Delete()
],
    normalizationContext: ['groups' => ['DragonTreasure:read']],
   denormalizationContext: ['groups' => ['DragonTreasure:write']],
paginationItemsPerPage: 5
)]
#[ApiResource(
    uriTemplate: '/users/{user_id}/dragon_treasure.{_format}',
    operations: [new GetCollection()],
    //el uriVariables es para que Api Platform sepa que user_id es un id de la clase user
    //si no la url seria users/{user_id} en vez de users/4
    uriVariables: [
        'user_id' => new Link(
            //propiedad que está compartida (variable dragonTreasures en el entity User) y clase a la que referencia
            fromProperty: 'dragonTreasures',
            fromClass: User::class,
        )
    ],
    normalizationContext: ['groups' => ['DragonTreasure:read']]

)]
//para filtrar los propiedades, es decir, se puede elegir si solo mostrar el name o el value
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(SearchFilter::class, properties: ['owner.username' => 'partial'])]
class DragonTreasure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    //hay que poner arriba y aqui si la propiedad es de lectura o escritura o ambas
    #[Groups(['DragonTreasure:read', 'DragonTreasure:write', 'user:read', 'user:write'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    //validacion que obliga a que el campo name sea obligatorio
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50, maxMessage: 'Only 2-50 characters allowed.')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = "";
    /**
     * Prueba de comentario
     */
    #[ORM\Column]
    #[Groups(['DragonTreasure:read', 'DragonTreasure:write', 'user:read', 'user:write'])]
    #[ApiFilter(RangeFilter::class)]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $value;

    #[ORM\Column]
    #[Groups(['DragonTreasure:read', 'DragonTreasure:write', 'user:write'])]
    #[ApiFilter(NumericFilter::class)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThanOrEqual(10)]
    private ?int $coolFactor = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;
    #[Groups(['DragonTreasure:read'])]
    #[ORM\Column]
    #[ApiFilter(BooleanFilter::class)]
    private bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'dragonTreasures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['DragonTreasure:item:get', 'DragonTreasure:write'])]
    #[Assert\Valid]
    private ?User $owner = null;

    public function __construct(){
        //para lectura esto si que lo permite pero no para escritura.
        //si la variable que le pasamos al constructor tiene el mismo name que la propiedad
        //en nuestra entidad (name) no habria problema
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }
//    public function getValueDiscount(): ?int
//    {
//        return $this->value;
//    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }
//    public function setValueDiscount(int $value): static
//    {
//        $this->value = $value;
//
//        return $this;
//    }

    public function getCoolFactor(): ?int
    {
        return $this->coolFactor;
    }

    public function setCoolFactor(int $coolFactor): static
    {
        $this->coolFactor = $coolFactor;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
