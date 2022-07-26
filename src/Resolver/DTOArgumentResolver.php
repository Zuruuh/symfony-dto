<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Adapter\ConstraintViolationAdapter;
use App\Attribute\DTO;
use App\DTO\ClassDTOInterface;
use App\DTO\DTOInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOArgumentResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {}

    public final function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return (new ($argument->getType())()) instanceof DTOInterface || !empty($argument->getAttributesOfType(DTO::class));
    }

    public final function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->request->all();
        $DTO = new ($argument->getType())();

        $propertyExtractor = new PropertyInfoExtractor([new ReflectionExtractor()]);
        $properties = $propertyExtractor->getProperties($DTO::class);

        $setReadonlyPropertiesClosure = function () use ($properties, $data): void {
            foreach ($properties as $property) {
                if (array_key_exists($property, $data)) {
                    $this->$property = $data[$property];
                }
            }
        };
        $setReadonlyPropertiesClosure->call($DTO);

        $errors = $this->validator->validate($DTO);
        if (!$errors->count()) {
            yield $DTO instanceof ClassDTOInterface ? $this->transformDTO($properties, $DTO) : $DTO;

            return;
        }

        $errors = ConstraintViolationAdapter::toArray($errors);
        throw new HttpException(
            Response::HTTP_BAD_REQUEST,
            json_encode(['form' => $errors]),
            null,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @param string[] $properties
     */
    private function transformDTO(array $properties, ClassDTOInterface $DTO): mixed
    {
        $object = new ($DTO::getClass())();
        $propertyAccessor = new PropertyAccessor();
        foreach ($properties as $property) {
            $propertyAccessor->setValue($object, $property, $DTO->$property);
        }

        return $object;
    }
}
