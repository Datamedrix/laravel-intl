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

class LocaleTest extends TestCase
{
    /**
     * Test data provider.
     *
     * @return array
     */
    public function getTestData(): array
    {
        return [
            //ISO, IETF, language, territory, code set, modifier
            ['en', 'en', 'en', null, null, null],
            ['en_GB', 'en-GB', 'en', 'GB', null, null],
            ['en_GB.UTF-8', 'en-GB', 'en', 'GB', 'UTF-8', null],
            ['de_AT.UTF-8@euro', 'de-AT', 'de', 'AT', 'UTF-8', 'euro'],
            ['de_AT@euro', 'de-AT', 'DE', 'at', null, 'euro'],
            ['en', 'en', 'EN', null, null, null],
            ['en_GB', 'en-GB', 'EN', 'gb', null, null],
            ['en_GB.utf16', 'en-GB', 'EN', 'gb', 'utf16', null],
            ['en_US.iso-1234@fooBar', 'en-US', 'EN', 'us', 'iso-1234', 'fooBar'],
        ];
    }

    /**
     * Test.
     *
     * @param string      $isoString
     * @param string      $IETFTag
     * @param string      $language
     * @param string|null $territory
     * @param string|null $codeSet
     * @param string|null $modifier
     * @dataProvider getTestData
     */
    public function testGetter(string $isoString, string $IETFTag, string $language, ?string $territory, ?string $codeSet, ?string $modifier)
    {
        $locale = new Locale($language, $territory, $codeSet, $modifier);

        $this->assertEquals(Str::lower($language), $locale->language());
        $this->assertEquals(Str::upper($territory), $locale->territory());
        $this->assertEquals($codeSet, $locale->codeSet());
        $this->assertEquals($modifier, $locale->modifier());
        $this->assertEquals($isoString, $locale->toISO15897String());
        $this->assertEquals($isoString, (string) $locale);
        $this->assertEquals($IETFTag, $locale->toIETFLanguageTag());
    }

    /**
     * Test.
     *
     * @param string      $isoString
     * @param string      $IETFTag
     * @param string      $language
     * @param string|null $territory
     * @param string|null $codeSet
     * @param string|null $modifier
     * @dataProvider getTestData
     */
    public function testCreateISO15897String(string $isoString, string $IETFTag, string $language, ?string $territory, ?string $codeSet, ?string $modifier)
    {
        $this->assertEquals($isoString, Locale::createISO15897String($language, $territory, $codeSet, $modifier));
    }

    /**
     * Test.
     *
     * @param string      $isoString
     * @param string      $IETFTag
     * @param string      $language
     * @param string|null $territory
     * @param string|null $codeSet
     * @param string|null $modifier
     * @dataProvider getTestData
     */
    public function testCreateIETFLanguageTag(string $isoString, string $IETFTag, string $language, ?string $territory, ?string $codeSet, ?string $modifier)
    {
        $this->assertEquals($IETFTag, Locale::createIETFLanguageTag($language, $territory));
    }

    /**
     * Test.
     */
    public function testToISO15897String()
    {
        $locale = new Locale('fr', 'FR', null, null);

        $this->assertEquals('fr_FR', $locale->toISO15897String());
        $this->assertEquals('fr_FR', $locale->toISO15897String(true, false));
        $this->assertEquals('fr_FR', $locale->toISO15897String(false, true));
        $this->assertEquals('fr_FR', $locale->toISO15897String(true, true));

        $locale = new Locale('fr', 'FR', 'utf8', 'euro');

        $this->assertEquals('fr_FR.utf8@euro', $locale->toISO15897String());
        $this->assertEquals('fr_FR@euro', $locale->toISO15897String(true, false));
        $this->assertEquals('fr_FR.utf8', $locale->toISO15897String(false, true));
        $this->assertEquals('fr_FR', $locale->toISO15897String(true, true));
    }

    /**
     * Test.
     *
     * @param string      $isoString
     * @param string      $IETFTag
     * @param string      $language
     * @param string|null $territory
     * @param string|null $codeSet
     * @param string|null $modifier
     * @dataProvider getTestData
     */
    public function testCreateFromISO15897String(string $isoString, string $IETFTag, string $language, ?string $territory, ?string $codeSet, ?string $modifier)
    {
        $locale = Locale::createFromISO15897String($isoString);

        $this->assertEquals(Str::lower($language), $locale->language());
        $this->assertEquals(Str::upper($territory), $locale->territory());
        $this->assertEquals($codeSet, $locale->codeSet());
        $this->assertEquals($modifier, $locale->modifier());
        $this->assertEquals($isoString, $locale->toISO15897String());
        $this->assertEquals($isoString, (string) $locale);
        $this->assertEquals($IETFTag, $locale->toIETFLanguageTag());
    }

    /**
     * Test.
     */
    public function testCreateFromISO15897StringThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $locale = Locale::createFromISO15897String('');
    }

    /**
     * Test.
     *
     * @param string      $isoString
     * @param string      $IETFTag
     * @param string      $language
     * @param string|null $territory
     * @param string|null $codeSet
     * @param string|null $modifier
     * @dataProvider getTestData
     */
    public function testCreateFromIETFLanguageTag(string $isoString, string $IETFTag, string $language, ?string $territory, ?string $codeSet, ?string $modifier)
    {
        $locale = Locale::createFromIETFLanguageTag($IETFTag);

        $this->assertEquals(Str::lower($language), $locale->language());
        $this->assertEquals(Str::upper($territory), $locale->territory());
        $this->assertEmpty($locale->codeSet());
        $this->assertEmpty($locale->modifier());
        $this->assertEquals($IETFTag, $locale->toIETFLanguageTag());
    }

    /**
     * Test.
     */
    public function testCreateFromIETFLanguageTagThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $locale = Locale::createFromIETFLanguageTag('');
    }
}
