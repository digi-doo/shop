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

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ContactFormValidator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function validate($data)
    {
        $violations = $this->validator->validate($data['fullName'], [
            new NotBlank(),
        ]);

        if (count($violations) === 0) {
            $violations = $this->validator->validate($data['email'], [
                new NotBlank(),
                new Email(),
            ]);
        }

        if (count($violations) === 0) {
            $violations = $this->validator->validate($data['message'], [
                new NotBlank(),
            ]);
        }

        if (count($violations) === 0) {
            $violations = $this->validator->validate($data['recaptcha'], [
                new RecaptchaTrue(),
            ]);
        }

        $errors = [];
        if (count($violations) === 0) {
            return $errors;
        }

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }
}
