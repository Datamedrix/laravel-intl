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

use DMX\Application\Intl\Leo;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;
use DMX\Application\Intl\Locale;
use DMX\Application\Intl\LocaleManager;
use PHPUnit\Framework\MockObject\MockObject;
use Illuminate\Contracts\Session\Session as SessionContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class LeoTest extends TestCase
{
    /**
     * @var ApplicationContract|MockObject
     */
    private $appMock;

    /**
     * @var SessionContract|MockObject
     */
    private $sessionMock;

    /**
     * @var ConfigContract|MockObject
     */
    private $configMock;

    /**
     * @var LocaleManager
     */
    private $manager;

    /**
     * @var Leo
     */
    private $leo;

    /**
     * @var string
     */
    private $dummyLocalString;

    /**
     * @var Locale
     */
    private $dummyLocale;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->appMock = $this->getMockBuilder(ApplicationContract::class)->disableOriginalConstructor()->getMock();
        $this->sessionMock = $this->getMockBuilder(SessionContract::class)->disableOriginalConstructor()->getMock();
        $this->configMock = $this->getMockBuilder(ConfigContract::class)->disableOriginalConstructor()->getMock();

        $this->manager = new LocaleManager($this->appMock, $this->sessionMock, $this->configMock);
        $this->dummyLocalString = 'de_AT.utf8';
        $this->dummyLocale = Locale::createFromISO15897String($this->dummyLocalString);

        $this->manager->setLocale($this->dummyLocale);
        $this->leo = new Leo($this->manager);
    }

    /**
     * Test.
     */
    public function testFormatDate()
    {
        $carbon = Carbon::now();

        $this->assertEquals($carbon->format('d.m.y'), $this->leo->formatDate($carbon));
        $this->assertEquals($carbon->format('d.m.Y, H:i'), $this->leo->formatDate($carbon, 'datetime'));
    }

    /**
     * Test.
     */
    public function testFormatTime()
    {
        $carbon = Carbon::now();

        $this->assertEquals($carbon->format('H:i:s'), $this->leo->formatTime($carbon));
    }

    /**
     * Test.
     */
    public function testFormatNumber()
    {
        $this->assertEquals('23', $this->leo->formatNumber(23));
        $this->assertEquals('-23', $this->leo->formatNumber(-23));
        $this->assertEquals('100.000.234', $this->leo->formatNumber(100000234));
        $this->assertEquals('-100.000.234', $this->leo->formatNumber(-100000234));
        $this->assertEquals('100.000.234,457', $this->leo->formatNumber(100000234.456789));
        $this->assertEquals('-100.000.234,457', $this->leo->formatNumber(-100000234.456789));
    }

    /**
     * Test.
     */
    public function testFormatCurrency()
    {
        $this->assertEquals('23,00 €', $this->leo->formatCurrency(23));
        $this->assertEquals('-23,00 €', $this->leo->formatCurrency(-23));
        $this->assertEquals('100.000.234,00 €', $this->leo->formatCurrency(100000234));
        $this->assertEquals('-100.000.234,00 €', $this->leo->formatCurrency(-100000234));
        $this->assertEquals('100.000.234,46 €', $this->leo->formatCurrency(100000234.456789));
    }
}
