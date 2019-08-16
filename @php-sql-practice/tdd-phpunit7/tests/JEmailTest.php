<?php
/**
 * Created by PhpStorm.
 * User: julius
 * Date: 9/7/2018
 * Time: 6:41 PM
 */

declare(strict_types=1);

use TDD\JEmail;
use PHPUnit\Framework\TestCase;

class JEmailTest extends TestCase
{
    public function testCanBeCreatedFromValidEmailAddress(): void {
        $this->assertInstanceOf(
            JEmail::class,
            JEmail::fromString('julius@mail.com'));
    }

    public function testCannotBeCreatedFromInvalidEmail(): void {
        $this->expectException(InvalidArgumentException::class);
        JEmail::fromString('invalid');
    }

    public function testCanBeUsedAsString(): void {
        $this->assertEquals(
            'julius@mail.com',
            JEmail::fromString('julius@mail.com')
        );
    }
}