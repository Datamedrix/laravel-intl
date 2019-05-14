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

namespace DMX\Application\Intl\Tests\Unit\Helper;

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use DMX\Application\Intl\Helper\LocaleStringConverter;

class LocaleStringConverterTest extends TestCase
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
    public function testCreateISO15897String(string $isoString, string $IETFTag, string $language, ?string $territory, ?string $codeSet, ?string $modifier)
    {
        $this->assertEquals($isoString, LocaleStringConverter::createISO15897String($language, $territory, $codeSet, $modifier));
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
        $this->assertEquals($IETFTag, LocaleStringConverter::createIETFLanguageTag($language, $territory));
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
    public function testExplodeISO15897String(string $isoString, string $IETFTag, string $language, ?string $territory, ?string $codeSet, ?string $modifier)
    {
        $this->assertEquals([
                'language' => Str::lower($language),
                'territory' => !empty($territory) ? Str::upper($territory) : null,
                'codeSet' => $codeSet,
                'modifier' => $modifier,
            ],
            LocaleStringConverter::explodeISO15897String($isoString)
        );
    }

    /**
     * Test.
     */
    public function testExplodeISO15897StringThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $locale = LocaleStringConverter::explodeISO15897String('');
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
    public function testExplodeIETFLanguageTag(string $isoString, string $IETFTag, string $language, ?string $territory, ?string $codeSet, ?string $modifier)
    {
        $this->assertEquals([
            'language' => Str::lower($language),
            'territory' => !empty($territory) ? Str::upper($territory) : null,
            'codeSet' => null,
            'modifier' => null,
        ],
            LocaleStringConverter::explodeIETFLanguageTag($IETFTag)
        );
    }

    /**
     * Test.
     */
    public function testExplodeIETFLanguageTagThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $locale = LocaleStringConverter::explodeIETFLanguageTag('');
    }
}
