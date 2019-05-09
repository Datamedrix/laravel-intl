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

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use DMX\Application\Intl\Locale;
use DMX\Application\Intl\LocaleManager;
use PHPUnit\Framework\MockObject\MockObject;
use DMX\Application\Intl\Exceptions\InvalidLocaleException;
use Illuminate\Contracts\Session\Session as SessionContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class LocaleManagerTest extends TestCase
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
     * @var LocaleManager
     */
    private $manager;

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

        $this->manager = new LocaleManager($this->appMock, $this->sessionMock);

        $availableLocales = \ResourceBundle::getLocales('');
        $this->dummyLocalString = $availableLocales[rand(0, count($availableLocales) - 1)];
        $this->dummyLocale = Locale::createFromISO15897String($this->dummyLocalString);
    }

    /**
     * Test.
     */
    public function testSetLocale()
    {
        $this->appMock
            ->expects($this->once())
            ->method('setLocale')
            ->with($this->dummyLocale->language())
        ;

        $this->manager->setLocale($this->dummyLocale);
        $this->assertEquals($this->dummyLocale->toISO15897String(), setlocale(LC_CTYPE, 0));
    }

    /**
     * Test.
     */
    public function testSetLocaleThrowsInvalidLocaleException()
    {
        $this->expectException(InvalidLocaleException::class);
        $this->manager->setLocale(new Locale('darth_Vader'));
    }

    /**
     * Test.
     */
    public function testGetCurrentLocale()
    {
        $this->sessionMock
            ->expects($this->once())
            ->method('has')
            ->with(LocaleManager::SESSION_KEY)
            ->willReturn(false)
        ;

        $this->sessionMock
            ->expects($this->never())
            ->method('get')
            ->with(LocaleManager::SESSION_KEY, setlocale(LC_CTYPE, 0))
        ;

        // We check the values in lowercase, because on some environments (=YES WINDOWS) the locales do not follow the ISO specifications!
        $this->assertEquals(Str::lower(setlocale(LC_CTYPE, 0)), Str::lower((string) $this->manager->getCurrentLocale()));
    }

    /**
     * Test.
     */
    public function testGetCurrentLocaleWhenSetInSession()
    {
        $this->sessionMock
            ->expects($this->once())
            ->method('has')
            ->with(LocaleManager::SESSION_KEY)
            ->willReturn(true)
        ;

        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with(LocaleManager::SESSION_KEY, setlocale(LC_CTYPE, 0))
            ->willReturn($this->dummyLocale->toISO15897String())
        ;

        $this->assertEquals((string) $this->dummyLocale, (string) $this->manager->getCurrentLocale());
        $this->assertEquals((string) $this->dummyLocale, (string) $this->manager->getCurrentLocale());
    }

    /**
     * Test.
     */
    public function testGetCurrentLocaleWhenSet()
    {
        $this->manager->setLocale($this->dummyLocale);

        $this->sessionMock
            ->expects($this->never())
            ->method('has')
            ->with(LocaleManager::SESSION_KEY)
        ;

        $this->sessionMock
            ->expects($this->never())
            ->method('get')
            ->with(LocaleManager::SESSION_KEY, setlocale(LC_CTYPE, 0))
        ;

        $this->assertEquals((string) $this->dummyLocale, (string) $this->manager->getCurrentLocale());
    }

    /**
     * Test.
     */
    public function testSetLocalAnPutIntoSession()
    {
        $this->appMock
            ->expects($this->once())
            ->method('setLocale')
            ->with($this->dummyLocale->language())
        ;

        $this->sessionMock
            ->expects($this->once())
            ->method('put')
            ->with(LocaleManager::SESSION_KEY, $this->dummyLocale->toISO15897String())
        ;

        $this->manager->setLocaleAnPutIntoSession($this->dummyLocale);
        $this->assertEquals($this->dummyLocale->toISO15897String(), setlocale(LC_CTYPE, 0));
    }
}
