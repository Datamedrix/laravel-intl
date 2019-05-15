<?php
/**
 * ----------------------------------------------------------------------------
 * This code is part of an application or library developed by Datamedrix and
 * is subject to the provisions of your License Agreement with
 * Datamedrix GmbH.
 *
 * @copyright (c) 2019 Datamedrix GmbH
 * ----------------------------------------------------------------------------
 * @author Christian Graf <c.graf@datamedrix.com>
 */

declare(strict_types=1);

namespace DMX\Application\Intl\Tests\Unit;

use PHPUnit\Framework\TestCase;
use DMX\Application\Intl\Factory;

class FactoryTest extends TestCase
{
    /**
     * Test.
     */
    public function testCreateIntlDateFormatter()
    {
        $this->assertInstanceOf(\IntlDateFormatter::class, Factory::createIntlDateFormatter('en'));
    }

    /**
     * Test.
     */
    public function testCreateIntlNumberFormatter()
    {
        $this->assertInstanceOf(\NumberFormatter::class, Factory::createIntlNumberFormatter('en'));
    }
}
