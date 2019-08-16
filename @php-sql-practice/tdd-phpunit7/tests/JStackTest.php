<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 9/8/2018
 * Time: 2:53 PM
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class JStackTest extends TestCase
{
    public function testEmpty(): array {
        $stack = [];
        $this->assertEmpty($stack);
        return $stack;
    }

    /**
     * @depends testEmpty
     * @param array $stack
     * @return array
     */
    public function testPush(array $stack): array {
        array_push($stack, 'red ninja');
        $this->assertSame(
            'red ninja',
            $stack[(count($stack) - 1)]
        );
        $this->assertNotEmpty($stack);
        return $stack;
    }

    /**
     * @depends testPush
     * @param $stack
     * @return void
     */
    public function testPop(array $stack): void {
        $this->assertSame('red ninja', array_pop($stack));
        $this->assertSame(0, count($stack));
    }

    #region multiple @depends practice
    public function testProducerOne(): string {
        $this->assertTrue(true);
        return 'one';
    }
    public function testProducerTwo(): string {
        $this->assertTrue(true);
        return 'two';
    }
    /**
     * @depends testProducerOne
     * @depends testProducerTwo
     * @param string $a
     * @param string $b
     */
    public function testConsumer(string $a, string $b): void {
        $this->assertSame('one', $a);
        $this->assertSame('two', $b);
    }
    #endregion
}