<?php

declare(strict_types=1);

namespace App\Traits;

use App\DTO\ClassDTOInterface;
use App\DTO\DTOInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait SerializeDTOTrait
{
    /** @param class-string<DTOInterface> $DTOClass */
    private function normalizeDataToDTO(string $DTOClass, mixed $data): DTOInterface
    {
        $DTO = new ($DTOClass)();
        if ($DTO instanceof ClassDTOInterface && $DTO::getClass() !== get_class($data)) {
            throw new InvalidArgumentException('Invalid data passed to DTO');
        }

        $propertyExtractor = new PropertyInfoExtractor([new ReflectionExtractor()]);
        $properties = $propertyExtractor->getProperties($DTOClass);

        $propertyAccessor = new PropertyAccessor();

        foreach ($properties as $property) {
            $value = $propertyAccessor->getValue($data, $property);
            (function () use ($property, $value) {
                $this->$property = $value;
            })->call($DTO);
        }

        return $DTO;
    }

    /** @param array{string, string} $headers */
    private function serializeDTO(DTOInterface $DTO, int $status = 200, array $headers = []): Response
    {
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);

        return new Response($serializer->serialize($DTO, 'json'), $status, ['Content-Type' => 'application/json', ...$headers]);
    }

    /**
     * @param class-string<DTOInterface> $DTOClass
     * @param array{string, string} $headers
     */
    private function returnDTO(string $DTOClass, mixed $data, int $status = 200, array $headers = []): Response
    {
        return $this->serializeDTO($this->normalizeDataToDTO($DTOClass, $data), $status, $headers);
    }
}
