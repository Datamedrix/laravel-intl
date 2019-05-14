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
use DMX\Application\Intl\Helper\LocaleStringConverter;
use DMX\Application\Intl\Exceptions\InvalidLocaleException;
use Illuminate\Contracts\Session\Session as SessionContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;
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
     * @var ConfigContract|MockObject
     */
    private $configMock;

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
     * @var array
     */
    private $defaultSettings = [];

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

        $availableLocales = \ResourceBundle::getLocales('');
        $this->dummyLocalString = $availableLocales[rand(0, count($availableLocales) - 1)];
        $this->dummyLocale = Locale::createFromISO15897String($this->dummyLocalString);
        $this->defaultSettings = array_merge(
            Locale::SETTINGS_TEMPLATE,
            [
                'decimalPoint' => localeconv()['decimal_point'],
                'thousandsSeparator' => localeconv()['thousands_sep'],
                'positiveSign' => localeconv()['positive_sign'],
                'negativeSign' => localeconv()['negative_sign'],
            ]
        );
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
            ->with(LocaleManager::SESSION_KEY, null)
        ;

        $details = LocaleStringConverter::explodeISO15897String(setlocale(LC_CTYPE, 0));

        $this->configMock
            ->expects($this->once())
            ->method('has')
            ->with('locale.settings.' . $details['language'] . (!empty($details['territory']) ? '-' . $details['territory'] : ''))
            ->willReturn(false)
        ;
        $this->configMock
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                ['locale.settings.' . $details['language'], null],
                ['locale.defaults.settings', null]
            )
            ->willReturnOnConsecutiveCalls(null, [])
        ;

        $locale = $this->manager->getCurrentLocale();
        // We check the values in lowercase, because on some environments (=YES WINDOWS) the locales do not follow the ISO specifications!
        $this->assertEquals(Str::lower(setlocale(LC_CTYPE, 0)), Str::lower((string) $locale));
        $this->assertEquals($this->defaultSettings, $locale->settings());
    }

    /**
     * Test.
     */
    public function testGetCurrentLocaleWithConfiguredSettings()
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
            ->with(LocaleManager::SESSION_KEY, null)
        ;

        $details = LocaleStringConverter::explodeISO15897String(setlocale(LC_CTYPE, 0));

        $this->configMock
            ->expects($this->once())
            ->method('has')
            ->with('locale.settings.' . $details['language'] . (!empty($details['territory']) ? '-' . $details['territory'] : ''))
            ->willReturn(true)
        ;
        $this->configMock
            ->expects($this->once())
            ->method('get')
            ->with('locale.settings.' . $details['language'] . (!empty($details['territory']) ? '-' . $details['territory'] : ''), null)
            ->willReturn(Locale::SETTINGS_TEMPLATE)
        ;

        $locale = $this->manager->getCurrentLocale();
        // We check the values in lowercase, because on some environments (=YES WINDOWS) the locales do not follow the ISO specifications!
        $this->assertEquals(Str::lower(setlocale(LC_CTYPE, 0)), Str::lower((string) $locale));
        $this->assertEquals($this->defaultSettings, $locale->settings());
    }

    /**
     * Test.
     */
    public function testGetCurrentLocaleWithConfiguredFallbackSettings()
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
            ->with(LocaleManager::SESSION_KEY, null)
        ;

        $details = LocaleStringConverter::explodeISO15897String(setlocale(LC_CTYPE, 0));

        $this->configMock
            ->expects($this->once())
            ->method('has')
            ->with('locale.settings.' . $details['language'] . (!empty($details['territory']) ? '-' . $details['territory'] : ''))
            ->willReturn(false)
        ;

        $testSettings = array_merge($this->defaultSettings, [
            'decimalPoint' => '?',
            'negativeSign' => -1,
            'foo' => 'BAR',
        ]);
        $this->configMock
            ->expects($this->once())
            ->method('get')
            ->with('locale.settings.' . $details['language'], null)
            ->willReturn($testSettings)
        ;

        $locale = $this->manager->getCurrentLocale();
        // We check the values in lowercase, because on some environments (=YES WINDOWS) the locales do not follow the ISO specifications!
        $this->assertEquals(Str::lower(setlocale(LC_CTYPE, 0)), Str::lower((string) $locale));
        $this->assertEquals($testSettings, $locale->settings());
    }

    /**
     * Test.
     */
    public function testGetCurrentLocaleWithConfiguredInvalidSettings()
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
            ->with(LocaleManager::SESSION_KEY, null)
        ;

        $details = LocaleStringConverter::explodeISO15897String(setlocale(LC_CTYPE, 0));

        $this->configMock
            ->expects($this->once())
            ->method('has')
            ->with('locale.settings.' . $details['language'] . (!empty($details['territory']) ? '-' . $details['territory'] : ''))
            ->willReturn(true)
        ;
        $this->configMock
            ->expects($this->once())
            ->method('get')
            ->with('locale.settings.' . $details['language'] . (!empty($details['territory']) ? '-' . $details['territory'] : ''), null)
            ->willReturn('foo')
        ;

        $locale = $this->manager->getCurrentLocale();
        // We check the values in lowercase, because on some environments (=YES WINDOWS) the locales do not follow the ISO specifications!
        $this->assertEquals(Str::lower(setlocale(LC_CTYPE, 0)), Str::lower((string) $locale));
        $this->assertEquals(array_merge($this->defaultSettings, ['foo']), $locale->settings());
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
            ->with(LocaleManager::SESSION_KEY, null)
            ->willReturn($this->dummyLocale)
        ;

        $this->configMock
            ->expects($this->never())
            ->method('has')
        ;
        $this->configMock
            ->expects($this->never())
            ->method('get')
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

        $this->configMock
            ->expects($this->never())
            ->method('has')
        ;
        $this->configMock
            ->expects($this->never())
            ->method('get')
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
