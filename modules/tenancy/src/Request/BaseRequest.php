<?php

namespace Module\Tenancy\Request;

use Module\Tenancy\Exception\ValidationFailedException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class BaseRequest
{
    public ?ParameterBag $attributes = null;

    /**
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param RequestStack $request
     * @throws ValidationFailedException
     */
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
        private readonly RequestStack $request,
    ) {
        $this->validate();
    }

    /**
     * @throws ValidationFailedException
     */
    public function validate(): void
    {
        $request = $this->request->getCurrentRequest();
        $this->serializer->deserialize(
            $request->getContent(),
            static::class,
            'json',
            [
                'object_to_populate' => $this
            ]
        );

        $this->setAttributes($request->attributes);

        $errors = $this->validator->validate($this);
        if ($errors->count())
        {
            throw new ValidationFailedException($errors);
        }
    }

    /**
     * @return ParameterBag|null
     */
    public function getAttributes(): ?ParameterBag
    {
        return $this->attributes;
    }

    /**
     * @param ParameterBag|null $attributes
     */
    public function setAttributes(?ParameterBag $attributes): void
    {
        $this->attributes = $attributes;
    }
}
