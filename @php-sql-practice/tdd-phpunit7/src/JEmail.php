<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 9/7/2018
 * Time: 6:39 PM
 */

declare(strict_types=1);

namespace TDD;

class JEmail
{
    private $email;
    private function __construct(string $email) {
        $this->ensureIsValidEmail($email);
        $this->email = $email;
    }
    
    public static function fromString(string $email): self {
        return new self($email);
    }
    
    public function __toString(): string {
        return $this->email;
    }
    
    private function ensureIsValidEmail(string $email): void {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not a valid email address', $email)
            );
        }
    }
}