<?php

/*
 * This file is part of the Digi Doo s.r.o. sshop project.
 *
 * (c) Digi Doo s.r.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PhoneNumberValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'phone');
        }

        if (!preg_match('/\+[0-9]{2,3} [0-9]{3} [0-9]{3} [0-9]{3}$/', $value, $matches) && !preg_match('/\+[0-9]{2,3}[0-9]{3}[0-9]{3}[0-9]{3}$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ phone }}', $value)
                ->addViolation();
        }
    }
}
